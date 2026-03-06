<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPegawaiImport;
use App\Models\StgPegawaiImport;
use App\Models\Pegawai;
use App\Models\RefAgama;
use App\Models\RefJenisKawin;
use App\Models\RefJenisJabatan;
use App\Models\RefTingkatPendidikan;
use App\Models\RefLokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PegawaiImportController extends Controller
{
    /**
     * Search for employees by NIP or Name
     */
    public function searchEmployee(Request $request)
    {
        $search = $request->input('query', '');

        if (empty($search)) {
            return response()->json([]);
        }

        $employees = Pegawai::where(function ($q) use ($search) {
            $q->where('nip_baru', 'like', "%{$search}%")
                ->orWhere('nama', 'like', "%{$search}%")
                ->orWhere('nip_lama', 'like', "%{$search}%");
        })
            ->select('id', 'nip_baru', 'nama', 'gelar_depan', 'gelar_belakang')
            ->limit(20)
            ->get()
            ->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'nip_baru' => $employee->nip_baru,
                    'nama_lengkap' => trim(implode(' ', array_filter([
                        $employee->gelar_depan,
                        $employee->nama,
                        $employee->gelar_belakang
                    ])))
                ];
            });

        return response()->json($employees);
    }

    /**
     * Get complete employee profile with history
     */
    public function getEmployeeProfile($id)
    {
        $employee = Pegawai::with([
            'agama',
            'jenisKawin',
            'jenisPegawai',
            'kedudukanHukum',
            'golongan',
            'jabatan',
            'jenisJabatan',
            'pendidikan',
            'tingkatPendidikan',
            'unor',
            'instansiInduk',
            'instansiKerja',
            'lokasiKerja',
            'kpkn',
            'riwayatGolongan.golongan',
            'riwayatJabatan.jabatan',
            'riwayatJabatan.jenisJabatan',
            'riwayatJabatan.unor',
            'riwayatPendidikan.pendidikan',
            'riwayatPendidikan.tingkatPendidikan',
            'riwayatStatus'
        ])->find($id);

        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        // Format the response
        $data = [
            'profile' => [
                'id' => $employee->id,
                'nik' => $employee->nik,
                'nip_baru' => $employee->nip_baru,
                'nip_lama' => $employee->nip_lama,
                'nama_lengkap' => $employee->nama_lengkap,
                'nama' => $employee->nama,
                'gelar_depan' => $employee->gelar_depan,
                'gelar_belakang' => $employee->gelar_belakang,
                'jenis_kelamin' => $employee->jenis_kelamin,
                'tanggal_lahir' => $employee->tanggal_lahir?->format('d/m/Y'),
                'tempat_lahir' => $employee->tempat_lahir,
                'alamat' => $employee->alamat,
                'no_hp' => $employee->no_hp,
                'email' => $employee->email,
                'agama' => $employee->agama?->nama,
                'jenis_kawin' => $employee->jenisKawin?->nama,
                'jenis_pegawai' => $employee->jenisPegawai?->nama,
                'kedudukan_hukum' => $employee->kedudukanHukum?->nama,
                'golongan' => $employee->golongan_pppk,
                'jabatan' => $employee->jabatan?->nama,
                'jenis_jabatan' => $employee->jenisJabatan?->nama,
                'pendidikan' => $employee->pendidikan?->nama,
                'tingkat_pendidikan' => $employee->tingkatPendidikan?->nama,
                'unor' => $employee->unor?->nama,
                'instansi_induk' => $employee->instansiInduk?->nama,
                'instansi_kerja' => $employee->instansiKerja?->nama,
                'lokasi_kerja' => $employee->lokasiKerja?->nama,
                'kpkn' => $employee->kpkn?->nama,
                'status_cpns_pns' => $employee->status_cpns_pns,
                'tmt_cpns' => $employee->tmt_cpns?->format('d/m/Y'),
                'tmt_pns' => $employee->tmt_pns?->format('d/m/Y'),
            ],
            'riwayat' => [
                'golongan' => $employee->riwayatGolongan->map(function ($riwayat) {
                    return [
                        'id' => $riwayat->id,
                        'golongan' => $riwayat->golongan?->nama,
                        'tmt' => $riwayat->tmt?->format('d/m/Y'),
                        'mk_tahun' => $riwayat->mk_tahun,
                        'mk_bulan' => $riwayat->mk_bulan,
                        'keterangan' => $riwayat->keterangan,
                    ];
                })->sortByDesc('tmt')->values(),
                'jabatan' => $employee->riwayatJabatan->map(function ($riwayat) {
                    return [
                        'id' => $riwayat->id,
                        'jabatan' => $riwayat->jabatan?->nama,
                        'jenis_jabatan' => $riwayat->jenisJabatan?->nama,
                        'unor' => $riwayat->unor?->nama,
                        'tmt' => $riwayat->tmt?->format('d/m/Y'),
                        'keterangan' => $riwayat->keterangan,
                    ];
                })->sortByDesc('tmt')->values(),
                'pendidikan' => $employee->riwayatPendidikan->map(function ($riwayat) {
                    return [
                        'id' => $riwayat->id,
                        'pendidikan' => $riwayat->pendidikan?->nama,
                        'tingkat_pendidikan' => $riwayat->tingkatPendidikan?->nama,
                        'institusi' => $riwayat->institusi,
                        'tahun_lulus' => $riwayat->tahun_lulus,
                        'keterangan' => $riwayat->keterangan,
                    ];
                })->sortByDesc('tahun_lulus')->values(),
                'status' => $employee->riwayatStatus->map(function ($riwayat) {
                    return [
                        'id' => $riwayat->id,
                        'status' => $riwayat->status,
                        'tmt' => $riwayat->tmt?->format('d/m/Y'),
                        'keterangan' => $riwayat->keterangan,
                    ];
                })->sortByDesc('tmt')->values(),
            ]
        ];

        return response()->json($data);
    }

    /**
     * Display the import page
     */
    public function index()
    {
        return view('pegawai.import.index');
    }

    public function syncPage()
    {
        return view('admin.sync.index');
    }

    /**
     * Handle file upload and import to staging
     */
    public function upload(Request $request)
    {
        // Check if file was uploaded
        if (!$request->hasFile('files') || empty($request->file('files'))) {
            $message = 'Tidak ada file yang diupload.';
            $fileError = $_FILES['file']['error'] ?? null;

            if ($fileError !== null && $fileError !== UPLOAD_ERR_OK) {
                $maxSize = ini_get('upload_max_filesize');
                switch ($fileError) {
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        $message = "File gagal diupload: Ukuran file melebihi batas server ({$maxSize}).";
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $message = "File gagal diupload: File hanya terupload sebagian.";
                        break;
                    case UPLOAD_ERR_NO_TMP_DIR:
                        $message = "File gagal diupload: Folder temporary ('tmp') tidak ditemukan di server.";
                        break;
                    case UPLOAD_ERR_CANT_WRITE:
                        $message = "File gagal diupload: Gagal menulis file ke disk server.";
                        break;
                    case UPLOAD_ERR_EXTENSION:
                        $message = "File gagal diupload: Ekstensi PHP menghentikan upload file.";
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        $message = "Tidak ada file yang dipilih untuk diupload.";
                        break;
                    default:
                        $message = "Gagal upload file (Error code: {$fileError}).";
                        break;
                }
            } elseif (empty($_FILES) && empty($_POST) && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $postMaxSize = ini_get('post_max_size');
                $message = "Request terlalu besar melebihi batas server (post_max_size: {$postMaxSize}).";
            }

            return response()->json([
                'success' => false,
                'message' => $message,
                'debug' => [
                    'files' => $_FILES,
                    'post' => $_POST,
                    'content_length' => $_SERVER['CONTENT_LENGTH'] ?? null
                ]
            ], 422);
        }

        // Validate that each uploaded file is valid
        $files = $request->file('files');
        foreach ($files as $file) {
            if (!$file->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Salah satu file gagal diupload. Periksa kembali ukuran (max 10MB) dan format file.',
                ], 422);
            }
        }

        $request->validate([
            'files.*' => 'required|file|mimetypes:text/plain,text/csv,application/csv|max:51200', // Max 50MB per file
        ]);

        try {
            $sharedFilename = time() . '_merged_import';
            $sanitizer = new \App\Services\CsvSanitizerService();
            $csvService = new \App\Services\CsvImportService();

            $batchCreated = false;
            $batchId = null;
            $totalInserted = 0;
            $totalSkipped = 0;
            $fileSummaries = [];

            // 1. Process each file
            foreach ($files as $file) {
                $originalName = $file->getClientOriginalName();
                $filename = time() . '_' . $originalName; // unique stored name

                // Store file
                $path = $file->storeAs('imports/pegawai', $filename);
                $fullPath = \Illuminate\Support\Facades\Storage::path($path);

                // Sanitize the CSV first
                $sanitizer->sanitize($fullPath);

                // If this is the very first file being inserted, create the batch using the generic import()
                if (!$batchCreated) {
                    $result = $csvService->import($fullPath, $sharedFilename);
                    $batchId = $result['batch_id'];
                    $batchCreated = true;
                } else {
                    // Subsequent files append to the same batch and use the same $sharedFilename
                    $result = $csvService->importIntoSharedBatch($fullPath, $sharedFilename, $batchId);
                }

                $totalInserted += $result['inserted'];
                $totalSkipped += $result['skipped'];

                $fileSummaries[] = [
                    'filename' => $originalName,
                    'inserted' => $result['inserted'],
                    'skipped' => $result['skipped']
                ];
            }

            if ($totalInserted === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data yang berhasil diimport. Periksa format file, pastikan menggunakan delimiter pipa (|) dan berisi data minimal 1 baris selain header.',
                    'skipped' => $totalSkipped
                ], 422);
            }

            $recordCount = $totalInserted;
            $filename = $sharedFilename; // Diff service runs based on this shared name

            // DIFFERENCE ANALYSIS START
            $diffService = new \App\Services\PegawaiDiffService();
            $stagingRows = StgPegawaiImport::where('source_file', $filename)->get();
            $counts = ['new' => 0, 'changed' => 0, 'unchanged' => 0];
            $validationService = app(\App\Services\PegawaiValidationService::class);

            foreach ($stagingRows as $row) {
                /** @var StgPegawaiImport $row */
                try {
                    $row->processing_error = null;
                    $validationService->validate($row);
                } catch (\Exception $e) {
                    $row->processing_error = $e->getMessage();
                }

                $analysis = $diffService->analyze($row);

                $row->update([
                    'data_hash' => $analysis['hash'],
                    'sync_status' => $analysis['status'],
                    'change_summary' => $analysis['changes'] ? json_encode($analysis['changes']) : null,
                    'processing_error' => $row->processing_error,
                ]);

                if (isset($counts[$analysis['status']])) {
                    $counts[$analysis['status']]++;
                }
            }
            // DIFF ANALYSIS END

            $batchSummary = null;
            if ($batchId) {
                $total = StgPegawaiImport::where('batch_id', $batchId)->count();
                $invalid = StgPegawaiImport::where('batch_id', $batchId)
                    ->whereNotNull('processing_error')
                    ->count();
                $valid = $total - $invalid;

                $batch = \App\Models\ImportBatch::find($batchId);
                if ($batch) {
                    $status = ($invalid > 0 && $valid == 0) ? 'failed' : (($invalid > 0) ? 'partial' : 'ready');
                    $batch->update([
                        'total_rows' => $total,
                        'valid_rows' => $valid,
                        'invalid_rows' => $invalid,
                        'status' => $status
                    ]);

                    $batchSummary = [
                        'total' => $total,
                        'valid' => $valid,
                        'invalid' => $invalid,
                        'status' => $status
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'File berhasil diupload dan dianalisis. Silakan konfirmasi sinkronisasi.',
                'filename' => $filename,
                'record_count' => $recordCount,
                'diff_summary' => $counts,
                'batch_id' => $batchId,
                'batch_summary' => $batchSummary
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
            ->selectRaw('SUM(CASE WHEN is_anomali = 1 THEN 1 ELSE 0 END) as anomaly_rows')
            ->groupBy('source_file', 'imported_at')
            ->orderBy('imported_at', 'desc')
            ->paginate(5);

        $imports->getCollection()->transform(function ($import) {
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
                'anomaly_rows' => $import->anomaly_rows,
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
     * Get anomaly details for a specific file
     */
    public function anomalyDetails(Request $request, $filename)
    {
        $anomalies = tap(StgPegawaiImport::where('source_file', $filename)
            ->where('is_anomali', true)
            ->select('id', 'pns_id', 'nama', 'nip_baru', 'catatan_anomali')
            ->paginate(15))->map(function ($q) {
                return $q;
            });

        return response()->json($anomalies);
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

    /**
     * Get diff summary for a specific file
     */
    public function diffSummary($filename)
    {
        $summary = StgPegawaiImport::where('source_file', $filename)
            ->selectRaw('sync_status, COUNT(*) as total')
            ->groupBy('sync_status')
            ->pluck('total', 'sync_status');

        return response()->json([
            'new' => $summary['new'] ?? 0,
            'changed' => $summary['changed'] ?? 0,
            'unchanged' => $summary['unchanged'] ?? 0,
            'total' => $summary->sum()
        ]);
    }

    /**
     * Get diff details (list of new/changed records)
     */
    public function diffDetails(Request $request, $filename)
    {
        $type = $request->query('type', 'all'); // new, changed, all

        $query = StgPegawaiImport::where('source_file', $filename);

        if ($type !== 'all') {
            $query->where('sync_status', $type);
        } else {
            $query->whereIn('sync_status', ['new', 'changed']);
        }

        $details = $query->paginate(20);

        // Transform for frontend
        $details->getCollection()->transform(function ($item) {
            return [
                'id' => $item->id,
                'nama' => $item->nama,
                'nip_baru' => $item->nip_baru,
                'status' => $item->sync_status,
                'changes' => $item->change_summary ? json_decode($item->change_summary) : null,
            ];
        });

        return response()->json($details);
    }

    /**
     * Preview sync to show how many will be added/updated/deleted
     */
    public function syncPreview(Request $request, $filename)
    {
        $count = StgPegawaiImport::where('source_file', $filename)->count();
        if ($count == 0) {
            return response()->json(['success' => false, 'message' => 'File tidak ditemukan'], 404);
        }

        $diffService = new \App\Services\PegawaiDiffService();
        $toBeDeleted = $diffService->getToBeDeletedPegawai($filename);

        $totalDb = Pegawai::count();
        $summary = StgPegawaiImport::where('source_file', $filename)
            ->selectRaw('sync_status, COUNT(*) as total')
            ->groupBy('sync_status')
            ->pluck('total', 'sync_status');

        $toAdd = $summary['new'] ?? 0;
        $toUpdate = $summary['changed'] ?? 0;
        $unchanged = $summary['unchanged'] ?? 0;
        $totalImport = $toAdd + $toUpdate + $unchanged;

        // Map the deleted employees for preview
        $deletedPreview = $toBeDeleted->map(function ($pegawai) {
            return [
                'id' => $pegawai->id,
                'nama' => $pegawai->nama_lengkap,
                'nip_baru' => $pegawai->nip_baru,
                'jabatan' => $pegawai->jabatan ? $pegawai->jabatan->nama : '-',
                'unor' => $pegawai->unor ? $pegawai->unor->nama : '-',
            ];
        });

        return response()->json([
            'success' => true,
            'summary' => [
                'total_db' => $totalDb,
                'total_import' => $totalImport,
                'to_add' => $toAdd,
                'to_update' => $toUpdate,
                'unchanged' => $unchanged,
                'to_delete' => $toBeDeleted->count(),
            ],
            'to_be_deleted' => $deletedPreview
        ]);
    }

    /**
     * Confirm synchronization
     */
    public function confirmSync(Request $request)
    {
        $request->validate([
            'filename' => 'required|string',
            'delete_removed' => 'nullable|boolean'
        ]);

        $filename = $request->input('filename');
        $deleteRemoved = $request->boolean('delete_removed', false);

        // Check if file exists in staging
        $count = StgPegawaiImport::where('source_file', $filename)->count();
        if ($count == 0) {
            return response()->json(['success' => false, 'message' => 'File tidak ditemukan'], 404);
        }

        ProcessPegawaiImport::dispatch($filename, $deleteRemoved);

        return response()->json([
            'success' => true,
            'message' => 'Sinkronisasi sedang berjalan di background',
        ]);
    }

    /**
     * Cancel an import batch containing staged data
     */
    public function cancelImport(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'filename' => 'required|string'
        ]);

        $filename = $request->input('filename');

        $batch = \App\Models\ImportBatch::where('source_file', $filename)
            ->whereIn('status', ['uploaded', 'ready', 'partial', 'failed'])
            ->first();

        if (!$batch) {
            return response()->json([
                'success' => false,
                'message' => 'Batch tidak ditemukan atau sudah diproses.'
            ], 404);
        }

        // Hapus staging records & batch
        StgPegawaiImport::where('batch_id', $batch->id)->delete();
        $batch->delete();

        return response()->json([
            'success' => true,
            'message' => "Import \"{$filename}\" berhasil dibatalkan."
        ]);
    }

    public function downloadErrors($batchId)
    {
        $rows = StgPegawaiImport::where('batch_id', $batchId)
            ->whereNotNull('processing_error')
            ->get();

        $filename = "error_batch_{$batchId}.csv";

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        $callback = function () use ($rows) {
            $handle = fopen('php://output', 'w');

            // Optionally add headers
            fputcsv($handle, ['ID', 'NIP Baru', 'Nama', 'Error'], '|');

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->id,
                    $row->nip_baru,
                    $row->nama,
                    $row->processing_error
                ], '|');
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function retry($batchId)
    {
        $rows = StgPegawaiImport::where('batch_id', $batchId)
            ->whereNotNull('processing_error')
            ->get();

        $validationService = app(\App\Services\PegawaiValidationService::class);

        foreach ($rows as $row) {
            /** @var \App\Models\StgPegawaiImport $row */
            // reset error
            $row->update(['processing_error' => null]);

            try {
                $validationService->validate($row);
            } catch (\Exception $e) {
                $row->update(['processing_error' => $e->getMessage()]);
            }
        }

        // Recalculate properties
        $total = StgPegawaiImport::where('batch_id', $batchId)->count();
        $invalid = StgPegawaiImport::where('batch_id', $batchId)
            ->whereNotNull('processing_error')
            ->count();
        $valid = $total - $invalid;

        $batch = \App\Models\ImportBatch::find($batchId);
        if ($batch) {
            $status = ($invalid > 0 && $valid == 0) ? 'failed' : (($invalid > 0) ? 'partial' : 'ready');
            $batch->update([
                'total_rows' => $total,
                'valid_rows' => $valid,
                'invalid_rows' => $invalid,
                'status' => $status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Retry validation selesai.',
                'batch_summary' => [
                    'total' => $total,
                    'valid' => $valid,
                    'invalid' => $invalid,
                    'status' => $status
                ]
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Batch not found'], 404);
    }
}
