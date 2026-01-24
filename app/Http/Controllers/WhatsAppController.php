<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\SnapshotPegawai;
use App\Models\Faq;
use App\Models\ChatMessage;
use App\Models\FonnteToken;
use Illuminate\Support\Facades\Log;

class WhatsAppController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $sender = $request->input('sender'); // Format Fonnte: 62812xxx
        $message = $request->input('message'); // Text message

        // 1. Normalisasi Nomor HP untuk pencarian di database
        // Asumsi DB menyimpan format '0812xxx'
        $normalizedPhone = $this->normalizePhoneNumber($sender);

        // 2. Cari Identitas Pengirim
        $pegawai = SnapshotPegawai::where('no_hp', $normalizedPhone)->first();
        $namaUser = $pegawai ? $pegawai->nama_pegawai : 'Bosku';

        // Simpan Pesan Masuk (Logging)
        // Kita simpan nomor asli '628xxx' atau '08xxx' terserah preferensi, 
        // tapi sebaiknya konsisten dengan identifier Fonnte (sender asli).
        ChatMessage::create([
            'sender_number' => $sender,
            'message' => $message,
            'direction' => 'in',
            'is_handled_by_bot' => false,
            'is_read' => false,
        ]);

        // 3. Pencarian Jawaban FAQ
        $faq = Faq::findByKeyword($message)->first();

        // 4. Penyusunan Pesan (Anti-Spam & Personal)
        if ($faq) {
            // Sapaan personal
            $replyMessage = "*Halo Bpk/Ibu $namaUser*, berikut jawaban untuk pertanyaan Anda:\n\n" . $faq->answer;

            // Tandai auto-reply
            $handledByBot = true;

            // Optional: Update pesan masuk tadi jadi 'handled'
            ChatMessage::where('sender_number', $sender)->latest()->first()->update([
                'is_handled_by_bot' => true,
                'is_read' => true
            ]);

        } else {
            // Fallback response
            $replyMessage = "Halo *$namaUser*, mohon maaf saya belum menemukan info terkait hal tersebut. Percakapan ini akan dialihkan ke Admin.";

            // Tetap dikirim oleh bot, tapi status 'handled' bisa false agar admin cek, 
            // atau true karena bot sudah merespon "Maaf". 
            // Sesuai request sebelumnya: "Baru" di dashboard -> berarti pesan masuknya is_handled_by_bot = false.
            // Pesan balasan ini tetap kita simpan.
            $handledByBot = true;
        }

        // 5. Kirim via Fonnte
        // Ambil token aktif
        $tokenRecord = FonnteToken::where('is_active', true)->latest()->first();

        if ($tokenRecord && $tokenRecord->token) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => $tokenRecord->token,
                ])->post('https://api.fonnte.com/send', [
                            'target' => $sender,
                            'message' => $replyMessage,
                            'countryCode' => '62', // optional default
                        ]);

                // Logging response fonnte jika perlu
                // Log::info('Fonnte Response: ' . $response->body());

            } catch (\Exception $e) {
                Log::error("Gagal mengirim pesan ke Fonnte: " . $e->getMessage());
            }
        } else {
            Log::warning("Token Fonnte belum disetting.");
        }

        // Simpan Pesan Keluar (Bot Reply)
        ChatMessage::create([
            'sender_number' => $sender,
            'message' => $replyMessage,
            'direction' => 'out',
            'is_handled_by_bot' => $handledByBot,
            'is_read' => true,
        ]);

        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Normalisasi nomor HP dari format 628... ke 08...
     */
    private function normalizePhoneNumber($phone)
    {
        // Hapus karakter non-angka
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Jika diawali 62, ganti dengan 0
        if (Str::startsWith($phone, '62')) {
            $phone = '0' . substr($phone, 2);
        }

        return $phone;
    }
}
