<?php

namespace App\Http\Controllers;

use App\Imports\PegawaiImport;
use App\Jobs\ProcessPegawaiImport;
use App\Models\StgPegawaiImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class PegawaiImportController extends Controller
{
    /**
     * Display the import page
     */
    public function index()
    {
        return view('pegawai.import.index');
    }

    /**
     * Handle file upload and import to staging
     */
    public function upload(Request $request)
    {
        // Check if file was uploaded
        if (!$request->hasFile('file')) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada file yang diupload',
            ], 422);
        }

        // Check if file upload was successful
        if (!$request->file('file')->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'File gagal diupload. Periksa ukuran file (max 10MB) dan format file.',
            ], 422);
        }

        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10000', // Max 10MB (10240 KB)
        ]);

        try {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $filename = time() . '_' . $originalName;

            // Store file
            $path = $file->storeAs('imports/pegawai', $filename);

            // Import to staging table
            Excel::import(new PegawaiImport($filename), $file);

            // Count imported records
            $recordCount = StgPegawaiImport::where('source_file', $filename)
                ->count();

            // Dispatch queue job for processing
            ProcessPegawaiImport::dispatch($filename);

            return response()->json([
                'success' => true,
                'message' => 'File berhasil diupload dan sedang diproses',
                'filename' => $filename,
                'record_count' => $recordCount,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload file: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get import history
     */
    public function history()
    {
        $imports = StgPegawaiImport::select('source_file', 'imported_at')
            ->selectRaw('COUNT(*) as total_rows')
            ->selectRaw('SUM(CASE WHEN is_processed = 1 THEN 1 ELSE 0 END) as processed_rows')
            ->selectRaw('SUM(CASE WHEN processing_error IS NOT NULL THEN 1 ELSE 0 END) as error_rows')
            ->groupBy('source_file', 'imported_at')
            ->orderBy('imported_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($import) {
                $status = 'Menunggu';
                if ($import->error_rows > 0) {
                    $status = 'Gagal';
                } elseif ($import->processed_rows == $import->total_rows) {
                    $status = 'Selesai';
                } elseif ($import->processed_rows > 0) {
                    $status = 'Diproses';
                }

                return [
                    'filename' => $import->source_file,
                    'uploaded_at' => $import->imported_at->format('d/m/Y H:i:s'),
                    'total_rows' => $import->total_rows,
                    'processed_rows' => $import->processed_rows,
                    'error_rows' => $import->error_rows,
                    'status' => $status,
                    'progress' => $import->total_rows > 0
                        ? round(($import->processed_rows / $import->total_rows) * 100, 2)
                        : 0,
                ];
            });

        return response()->json($imports);
    }

    /**
     * Get status of specific import
     */
    public function status($filename)
    {
        $import = StgPegawaiImport::where('source_file', $filename)
            ->selectRaw('COUNT(*) as total_rows')
            ->selectRaw('SUM(CASE WHEN is_processed = 1 THEN 1 ELSE 0 END) as processed_rows')
            ->selectRaw('SUM(CASE WHEN processing_error IS NOT NULL THEN 1 ELSE 0 END) as error_rows')
            ->first();

        if (!$import) {
            return response()->json(['error' => 'Import not found'], 404);
        }

        $status = 'Menunggu';
        if ($import->error_rows > 0) {
            $status = 'Gagal';
        } elseif ($import->processed_rows == $import->total_rows) {
            $status = 'Selesai';
        } elseif ($import->processed_rows > 0) {
            $status = 'Diproses';
        }

        return response()->json([
            'total_rows' => $import->total_rows,
            'processed_rows' => $import->processed_rows,
            'error_rows' => $import->error_rows,
            'status' => $status,
            'progress' => $import->total_rows > 0
                ? round(($import->processed_rows / $import->total_rows) * 100, 2)
                : 0,
        ]);
    }
}
