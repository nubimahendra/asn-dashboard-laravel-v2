<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FonnteToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteController extends Controller
{
    public function index()
    {
        $token = FonnteToken::where('is_active', true)->latest()->first();
        return view('admin.chat.api.index', compact('token'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        // Deactivate old tokens (optional, or just reuse single row)
        FonnteToken::where('is_active', true)->update(['is_active' => false]);

        FonnteToken::create([
            'token' => $request->token,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Token Fonnte berhasil disimpan.');
    }

    public function checkConnection()
    {
        $tokenRecord = FonnteToken::where('is_active', true)->latest()->first();

        if (!$tokenRecord) {
            return response()->json(['status' => 'error', 'message' => 'Token belum dikonfigurasi.'], 400);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $tokenRecord->token,
            ])->post('https://api.fonnte.com/device');

            $data = $response->json();

            // Log for debugging
            Log::info('Fonnte Check Connection Response:', $data);

            if ($response->successful()) {
                // Fonnte usually returns database details or status
                // Example response: {"device_status":"connect","name":"MyBot","quota":"...","status":true}
                // Adjust based on actual Fonnte response structure

                $deviceStatus = $data['device_status'] ?? 'Unknown';
                $name = $data['name'] ?? 'Unknown Device';
                $status = $data['status'] ?? false;

                if ($status && ($deviceStatus === 'connect' || $deviceStatus === 'connected')) {
                    return response()->json([
                        'status' => 'success',
                        'message' => "Koneksi Berhasil! Nomor WA: {$data['device']} Status: Connected.",
                        'data' => $data
                    ]);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Terhubung ke Fonnte, tetapi status perangkat belum connect. Status: ' . $deviceStatus
                    ]);
                }
            } else {
                return response()->json(['status' => 'error', 'message' => 'Gagal menghubungi server Fonnte.'], 500);
            }

        } catch (\Exception $e) {
            Log::error('Fonnte Connection Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }
}
