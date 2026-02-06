<?php

namespace App\Imports;

use App\Models\StgPegawaiImport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\ImportFailed;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Facades\Log;

class PegawaiImport implements
    ToModel,
    WithHeadingRow,
    WithBatchInserts,
    WithChunkReading,
    SkipsOnError,
    SkipsOnFailure,
    WithEvents
{
    protected $sourceFile;
    protected $errors = [];
    protected $failures = [];

    public function __construct($sourceFile)
    {
        $this->sourceFile = $sourceFile;
    }

    /**
     * Get all errors that occurred during import
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get all validation failures that occurred during import
     */
    public function getFailures()
    {
        return $this->failures;
    }

    /**
     * Map each row to StgPegawaiImport model
     */
    public function model(array $row)
    {
        // Map CSV columns to database columns
        // Header dari CSV menggunakan pipe delimiter
        return new StgPegawaiImport([
            'pns_id' => $row['pns_id'] ?? null,
            'nip_baru' => $row['nip_baru'] ?? null,
            'nip_lama' => $row['nip_lama'] ?? null,
            'nama' => $row['nama'] ?? null,
            'gelar_depan' => $row['gelar_depan'] ?? null,
            'gelar_belakang' => $row['gelar_belakang'] ?? null,
            'agama_id' => $row['agama_id'] ?? null,
            'agama' => $row['agama'] ?? null,
            'jenis_kawin_id' => $row['jenis_kawin_id'] ?? null,
            'jenis_kawin' => $row['jenis_kawin'] ?? null,
            'jenis_pegawai_id' => $row['jenis_pegawai_id'] ?? null,
            'jenis_pegawai' => $row['jenis_pegawai'] ?? null,
            'kedudukan_hukum_id' => $row['kedudukan_hukum_id'] ?? null,
            'kedudukan_hukum' => $row['kedudukan_hukum'] ?? null,
            'gol_awal_id' => $row['gol_awal_id'] ?? null,
            'gol_awal' => $row['gol_awal'] ?? null,
            'gol_akhir_id' => $row['gol_akhir_id'] ?? null,
            'gol_akhir' => $row['gol_akhir'] ?? null,
            'tmt_gol_akhir' => $row['tmt_gol_akhir'] ?? null,
            'mk_tahun' => $row['mk_tahun'] ?? null,
            'mk_bulan' => $row['mk_bulan'] ?? null,
            'jenis_jabatan_id' => $row['jenis_jabatan_id'] ?? null,
            'jenis_jabatan' => $row['jenis_jabatan'] ?? null,
            'jabatan_id' => $row['jabatan_id'] ?? null,
            'jabatan' => $row['jabatan'] ?? null,
            'tmt_jabatan' => $row['tmt_jabatan'] ?? null,
            'tingkat_pendidikan_id' => $row['tingkat_pendidikan_id'] ?? null,
            'tingkat_pendidikan' => $row['tingkat_pendidikan'] ?? null,
            'pendidikan_id' => $row['pendidikan_id'] ?? null,
            'pendidikan' => $row['pendidikan'] ?? null,
            'tahun_lulus' => $row['tahun_lulus'] ?? null,
            'unor_id' => $row['unor_id'] ?? null,
            'unor' => $row['unor'] ?? null,
            'instansi_induk_id' => $row['instansi_induk_id'] ?? null,
            'instansi_induk' => $row['instansi_induk'] ?? null,
            'instansi_kerja_id' => $row['instansi_kerja_id'] ?? null,
            'instansi_kerja' => $row['instansi_kerja'] ?? null,
            'lokasi_kerja_id' => $row['lokasi_kerja_id'] ?? null,
            'lokasi_kerja' => $row['lokasi_kerja'] ?? null,
            'kpkn_id' => $row['kpkn_id'] ?? null,
            'kpkn' => $row['kpkn'] ?? null,
            'status_cpns_pns' => $row['status_cpns_pns'] ?? null,
            'tmt_cpns' => $row['tmt_cpns'] ?? null,
            'tmt_pns' => $row['tmt_pns'] ?? null,
            'jenis_kelamin' => $row['jenis_kelamin'] ?? null,
            'tanggal_lahir' => $row['tanggal_lahir'] ?? null,
            'tempat_lahir' => $row['tempat_lahir'] ?? null,
            'alamat' => $row['alamat'] ?? null,
            'no_hp' => $row['no_hp'] ?? null,
            'email' => $row['email'] ?? null,
            'flag_ikd' => $row['flag_ikd'] ?? null,
            'source_file' => $this->sourceFile,
            'imported_at' => now(),
            'is_processed' => false,
        ]);
    }

    /**
     * Batch insert for performance
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * Chunk reading for memory efficiency
     */
    public function chunkSize(): int
    {
        return 100;
    }

    /**
     * Handle errors that occur during import
     */
    public function onError(\Throwable $e)
    {
        $errorMessage = $e->getMessage();
        $this->errors[] = [
            'message' => $errorMessage,
            'exception' => get_class($e),
            'trace' => $e->getTraceAsString()
        ];

        Log::error("Import error in file {$this->sourceFile}: {$errorMessage}", [
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
    }

    /**
     * Handle validation failures
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->failures[] = [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values()
            ];

            Log::warning("Validation failure in file {$this->sourceFile} at row {$failure->row()}", [
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values()
            ]);
        }
    }

    /**
     * Register events for the import
     */
    public function registerEvents(): array
    {
        return [
            ImportFailed::class => function (ImportFailed $event) {
                Log::error("Import failed for file {$this->sourceFile}", [
                    'exception' => $event->getException()->getMessage()
                ]);
            },
        ];
    }
}
