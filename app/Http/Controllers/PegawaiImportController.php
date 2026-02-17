<?php

namespace App\Http\Controllers;

use App\Imports\PegawaiImport;
use App\Jobs\ProcessPegawaiImport;
use App\Models\StgPegawaiImport;
use App\Models\Pegawai;
use App\Models\RefAgama;
use App\Models\RefJenisKawin;
use App\Models\RefJenisJabatan;
use App\Models\RefTingkatPendidikan;
use App\Models\RefLokasi;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
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
                'golongan' => $employee->golongan?->nama,
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
                // ... validation error handling (keep as is)
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
                // ... error handling (keep as is)
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
                // ... failure handling (keep as is)
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

            // DIFF ANALYSIS START
            $diffService = new \App\Services\PegawaiDiffService();
            $stagingRows = StgPegawaiImport::where('source_file', $filename)->get();
            $counts = ['new' => 0, 'changed' => 0, 'unchanged' => 0];

            foreach ($stagingRows as $row) {
                /** @var StgPegawaiImport $row */
                $analysis = $diffService->analyze($row);

                $row->update([
                    'data_hash' => $analysis['hash'],
                    'sync_status' => $analysis['status'],
                    'change_summary' => $analysis['changes'] ? json_encode($analysis['changes']) : null,
                ]);

                if (isset($counts[$analysis['status']])) {
                    $counts[$analysis['status']]++;
                }
            }
            // DIFF ANALYSIS END

            return response()->json([
                'success' => true,
                'message' => 'File berhasil diupload dan dianalisis. Silakan konfirmasi sinkronisasi.',
                'filename' => $filename,
                'record_count' => $recordCount,
                'diff_summary' => $counts
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
     * Confirm synchronization
     */
    public function confirmSync(Request $request)
    {
        $request->validate([
            'filename' => 'required|string',
        ]);

        $filename = $request->input('filename');

        // Check if file exists in staging
        $count = StgPegawaiImport::where('source_file', $filename)->count();
        if ($count == 0) {
            return response()->json(['success' => false, 'message' => 'File tidak ditemukan'], 404);
        }

        // Dispatch job to process the confirmed file
        // We can reuse the existing job but it needs to be updated to handle logic conditionally
        // OR we can process it here if it's not too large, but better use queue.
        // For now, let's use the existing job class but update it to handle "confirmed" logic?
        // Actually, the existing job `ProcessPegawaiImport` processes where `is_processed` = false.
        // The implementation plan says: "Step 6... confirmSync... foreach $rows... $service->sync($row)"

        // Let's implement immediate sync for now as per user request example, 
        // OR use the existing job mechanism. 
        // Given "TIDAK boleh langsung update", we stopped it at upload.
        // Now at confirm, we can dispatch the job.

        ProcessPegawaiImport::dispatch($filename);

        return response()->json([
            'success' => true,
            'message' => 'Sinkronisasi sedang berjalan di background',
        ]);
    }
}
