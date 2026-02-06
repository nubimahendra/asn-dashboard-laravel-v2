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
            'file' => 'required|file|mimes:xlsx,xls|max:10000', // Max 10MB (10240 KB)
        ]);

        try {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $filename = time() . '_' . $originalName;

            // Store file
            $path = $file->storeAs('imports/pegawai', $filename);

            // Create import instance to track errors
            $import = new PegawaiImport($filename);

            // Import to staging table
            try {
                Excel::import($import, $file);
            } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                $failures = $e->failures();
                $errorMessages = [];

                foreach ($failures as $failure) {
                    $errorMessages[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal pada beberapa baris',
                    'errors' => $errorMessages,
                    'error_count' => count($errorMessages)
                ], 422);
            }

            // Check for errors during import
            $errors = $import->getErrors();
            $failures = $import->getFailures();

            if (!empty($errors)) {
                $errorMessages = array_map(function ($error) {
                    return $error['message'];
                }, $errors);

                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi error saat import file',
                    'errors' => $errorMessages,
                    'error_count' => count($errorMessages)
                ], 500);
            }

            if (!empty($failures)) {
                $errorMessages = [];
                foreach ($failures as $failure) {
                    $errorMessages[] = "Baris {$failure['row']}: " . implode(', ', $failure['errors']);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Beberapa baris gagal validasi',
                    'errors' => $errorMessages,
                    'error_count' => count($errorMessages)
                ], 422);
            }

            // Count imported records
            $recordCount = StgPegawaiImport::where('source_file', $filename)
                ->count();

            if ($recordCount == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data yang berhasil diimport. Periksa format file dan pastikan ada data di dalamnya.',
                ], 422);
            }

            // Dispatch queue job for processing
            ProcessPegawaiImport::dispatch($filename);

            return response()->json([
                'success' => true,
                'message' => 'File berhasil diupload dan sedang diproses',
                'filename' => $filename,
                'record_count' => $recordCount,
            ]);

        } catch (\Exception $e) {
            \Log::error('Upload error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload file: ' . $e->getMessage(),
                'error_detail' => [
                    'type' => get_class($e),
                    'file' => basename($e->getFile()),
                    'line' => $e->getLine()
                ]
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
            ->selectRaw('SUM(CASE WHEN processing_error IS NOT NULL THEN 1 ELSE 0 END) as processing_error_rows')
            ->selectRaw('SUM(CASE WHEN import_error IS NOT NULL THEN 1 ELSE 0 END) as import_error_rows')
            ->groupBy('source_file', 'imported_at')
            ->orderBy('imported_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($import) {
                $totalErrors = $import->processing_error_rows + $import->import_error_rows;

                $status = 'Menunggu';
                if ($totalErrors > 0) {
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
                    'import_error_rows' => $import->import_error_rows,
                    'processing_error_rows' => $import->processing_error_rows,
                    'total_error_rows' => $totalErrors,
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
