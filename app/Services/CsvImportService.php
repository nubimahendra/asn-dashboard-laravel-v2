<?php

namespace App\Services;

use App\Models\StgPegawaiImport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CsvImportService
{
    /**
     * Exact number of expected columns based on SIDAWAI export format.
     */
    protected int $expectedColumns = 67;

    // INDEX MAP 0–66 (WAJIB SESUAI HEADER)
    protected const IDX = [
        'pns_id' => 0,
        'nip_baru' => 1,
        'nip_lama' => 2,
        'nama' => 3,
        'gelar_depan' => 4,
        'gelar_belakang' => 5,
        'tempat_lahir_id' => 6,
        'tempat_lahir_nama' => 7,
        'tanggal_lahir' => 8,
        'jenis_kelamin' => 9,
        'agama_id' => 10,
        'agama_nama' => 11,
        'jenis_kawin_id' => 12,
        'jenis_kawin_nama' => 13,
        'nik' => 14,
        'nomor_hp' => 15,
        'email' => 16,
        'email_gov' => 17,
        'alamat' => 18,
        'npwp_nomor' => 19,
        'bpjs' => 20,
        'jenis_pegawai_id' => 21,
        'jenis_pegawai_nama' => 22,
        'kedudukan_hukum_id' => 23,
        'kedudukan_hukum_nama' => 24,
        'status_cpns_pns' => 25,
        'kartu_asn_virtual' => 26,
        'nomor_sk_cpns' => 27,
        'tanggal_sk_cpns' => 28,
        'tmt_cpns' => 29,
        'nomor_sk_pns' => 30,
        'tanggal_sk_pns' => 31,
        'tmt_pns' => 32,
        'gol_awal_id' => 33,
        'gol_awal_nama' => 34,
        'gol_akhir_id' => 35,
        'gol_akhir_nama' => 36,
        'tmt_golongan' => 37,
        'mk_tahun' => 38,
        'mk_bulan' => 39,
        'jenis_jabatan_id' => 40,
        'jenis_jabatan_nama' => 41,
        'jabatan_id' => 42,
        'jabatan_nama' => 43,
        'tmt_jabatan' => 44,
        'tingkat_pendidikan_id' => 45,
        'tingkat_pendidikan_nama' => 46,
        'pendidikan_id' => 47,
        'pendidikan_nama' => 48,
        'tahun_lulus' => 49,
        'kpkn_id' => 50,
        'kpkn_nama' => 51,
        'lokasi_kerja_id' => 52,
        'lokasi_kerja_nama' => 53,
        'unor_id' => 54,
        'unor_nama' => 55,
        'instansi_induk_id' => 56,
        'instansi_induk_nama' => 57,
        'instansi_kerja_id' => 58,
        'instansi_kerja_nama' => 59,
        'satuan_kerja_induk_id' => 60,
        'satuan_kerja_induk_nama' => 61,
        'satuan_kerja_kerja_id' => 62,
        'satuan_kerja_kerja_nama' => 63,
        'is_valid_nik' => 64,
        'nama_sekolah' => 65,
        'flag_ikd' => 66,
    ];

    /**
     * Helper to safely get and trim value by index key.
     */
    protected function v(array $row, string $key): ?string
    {
        $idx = self::IDX[$key] ?? null;
        if ($idx === null) return null;
        return isset($row[$idx]) ? trim($row[$idx]) : null;
    }

    /**
     * Parse the CSV file and load it into the staging table.
     * 
     * @param string $path Absolute path to the CSV file
     * @param string $filename Original filename to record the source
     * @return array Array containing counts of 'inserted', 'skipped', and 'errors'
     */
    public function import(string $path, string $filename): array
    {
        $handle = fopen($path, 'r');

        if (!$handle) {
            throw new \Exception("Cannot open CSV file: {$path}");
        }

        $rowNumber = 0;
        $inserted = 0;
        $skipped = 0;
        $batchData = [];
        $batchSize = 250; // Insert 250 rows at a time
        $now = now();

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 0, '|', '"', "\\")) !== false) {
                $rowNumber++;

                // 1. Header Validation (Row 1)
                if ($rowNumber === 1) {
                    if (count($row) !== $this->expectedColumns) {
                        fclose($handle);
                        DB::rollBack();
                        throw new \Exception("Header column mismatch. Expected {$this->expectedColumns} columns, got " . count($row) . ". Ensure the file is pipe-delimited (|).");
                    }
                    continue; // Skip the header row
                }

                // 2. Column Count Validation (Data Rows)
                if (count($row) !== $this->expectedColumns) {
                    Log::warning("Row {$rowNumber} invalid column count: " . count($row), [
                        'file' => $filename,
                        'excerpt' => array_slice($row, 0, 3)
                    ]);
                    $skipped++;
                    continue; // Skip corrupt/incomplete rows
                }

                // 3. Data Cleaning and Mapping
                // Strip leading single quotes commonly used in Excel/CSV exports for text fields
                $nipBaru = ltrim($this->v($row, 'nip_baru') ?? '', "'");
                $nipLama = ltrim($this->v($row, 'nip_lama') ?? '', "'");
                $nik = ltrim($this->v($row, 'nik') ?? '', "'");
                
                // Construct the insert array using valid database columns 
                // Batch insert requires explicit existing columns mapping (unrelated extra CSV columns are ignored)
                $batchData[] = [
                    'pns_id' => $this->v($row, 'pns_id'),
                    'nik' => $nik !== '' ? $nik : null,
                    'nip_baru' => $nipBaru !== '' ? $nipBaru : null,
                    'nip_lama' => $nipLama !== '' ? $nipLama : null,
                    'nama' => $this->v($row, 'nama'),
                    'gelar_depan' => $this->v($row, 'gelar_depan'),
                    'gelar_belakang' => $this->v($row, 'gelar_belakang'),
                    
                    'agama_id' => $this->v($row, 'agama_id') !== '' ? $this->v($row, 'agama_id') : null,
                    'agama' => $this->v($row, 'agama_nama'),
                    
                    'jenis_kawin_id' => $this->v($row, 'jenis_kawin_id') !== '' ? $this->v($row, 'jenis_kawin_id') : null,
                    'jenis_kawin' => $this->v($row, 'jenis_kawin_nama'),
                    
                    'jenis_pegawai_id' => $this->v($row, 'jenis_pegawai_id') !== '' ? $this->v($row, 'jenis_pegawai_id') : null,
                    'jenis_pegawai' => $this->v($row, 'jenis_pegawai_nama'),
                    
                    'kedudukan_hukum_id' => $this->v($row, 'kedudukan_hukum_id') !== '' ? $this->v($row, 'kedudukan_hukum_id') : null,
                    'kedudukan_hukum' => $this->v($row, 'kedudukan_hukum_nama'),
                    
                    'gol_awal_id' => $this->v($row, 'gol_awal_id') !== '' ? $this->v($row, 'gol_awal_id') : null,
                    'gol_awal' => $this->v($row, 'gol_awal_nama'),
                    
                    'gol_akhir_id' => $this->v($row, 'gol_akhir_id') !== '' ? $this->v($row, 'gol_akhir_id') : null,
                    'gol_akhir' => $this->v($row, 'gol_akhir_nama'),
                    'tmt_gol_akhir' => $this->formatDate($this->v($row, 'tmt_golongan')),
                    
                    'mk_tahun' => $this->v($row, 'mk_tahun') !== '' ? (int)$this->v($row, 'mk_tahun') : null,
                    'mk_bulan' => $this->v($row, 'mk_bulan') !== '' ? (int)$this->v($row, 'mk_bulan') : null,
                    
                    'jenis_jabatan_id' => $this->v($row, 'jenis_jabatan_id') !== '' ? $this->v($row, 'jenis_jabatan_id') : null,
                    'jenis_jabatan' => $this->v($row, 'jenis_jabatan_nama'),
                    
                    'jabatan_id' => $this->v($row, 'jabatan_id') !== '' ? $this->v($row, 'jabatan_id') : null,
                    'jabatan' => $this->v($row, 'jabatan_nama'),
                    'tmt_jabatan' => $this->formatDate($this->v($row, 'tmt_jabatan')),
                    
                    'tingkat_pendidikan_id' => $this->v($row, 'tingkat_pendidikan_id') !== '' ? $this->v($row, 'tingkat_pendidikan_id') : null,
                    'tingkat_pendidikan' => $this->v($row, 'tingkat_pendidikan_nama'),
                    
                    'pendidikan_id' => $this->v($row, 'pendidikan_id') !== '' ? $this->v($row, 'pendidikan_id') : null,
                    'pendidikan' => $this->v($row, 'pendidikan_nama'),
                    'tahun_lulus' => $this->v($row, 'tahun_lulus') !== '' ? (int)$this->v($row, 'tahun_lulus') : null,
                    
                    'unor_id' => $this->v($row, 'unor_id') !== '' ? $this->v($row, 'unor_id') : null,
                    'unor' => $this->v($row, 'unor_nama'),
                    
                    'instansi_induk_id' => $this->v($row, 'instansi_induk_id') !== '' ? $this->v($row, 'instansi_induk_id') : null,
                    'instansi_induk' => $this->v($row, 'instansi_induk_nama'),
                    
                    'instansi_kerja_id' => $this->v($row, 'instansi_kerja_id') !== '' ? $this->v($row, 'instansi_kerja_id') : null,
                    'instansi_kerja' => $this->v($row, 'instansi_kerja_nama'),
                    
                    'lokasi_kerja_id' => $this->v($row, 'lokasi_kerja_id') !== '' ? $this->v($row, 'lokasi_kerja_id') : null,
                    'lokasi_kerja' => $this->v($row, 'lokasi_kerja_nama'),
                    
                    'kpkn_id' => $this->v($row, 'kpkn_id') !== '' ? $this->v($row, 'kpkn_id') : null,
                    'kpkn' => $this->v($row, 'kpkn_nama'),
                    
                    'status_cpns_pns' => $this->v($row, 'status_cpns_pns'),
                    'tmt_cpns' => $this->formatDate($this->v($row, 'tmt_cpns')),
                    'tmt_pns' => $this->formatDate($this->v($row, 'tmt_pns')),
                    
                    'jenis_kelamin' => $this->v($row, 'jenis_kelamin'),
                    'tanggal_lahir' => $this->formatDate($this->v($row, 'tanggal_lahir')),
                    'tempat_lahir' => $this->v($row, 'tempat_lahir_nama'),
                    
                    'alamat' => $this->v($row, 'alamat'),
                    'no_hp' => ltrim($this->v($row, 'nomor_hp') ?? '', "'") !== '' ? ltrim($this->v($row, 'nomor_hp') ?? '', "'") : null,
                    'email' => $this->v($row, 'email'),
                    'flag_ikd' => $this->v($row, 'flag_ikd') !== '' ? (int)$this->v($row, 'flag_ikd') : null,
                    
                    'source_file' => $filename,
                    'imported_at' => $now,
                    'is_processed' => 0,
                    
                    // Fields populated later by DiffEngine
                    'data_hash' => null,
                    'sync_status' => null,
                    'change_summary' => null,
                    
                    // Important for strict DB timestamp fields
                    'created_at' => $now,
                    'updated_at' => $now
                ];

                // 4. Batch Insert processing
                if (count($batchData) >= $batchSize) {
                    StgPegawaiImport::insert($batchData);
                    $inserted += count($batchData);
                    $batchData = []; // Reset batch
                }
            }

            // Insert remaining rows
            if (count($batchData) > 0) {
                StgPegawaiImport::insert($batchData);
                $inserted += count($batchData);
            }

            fclose($handle);
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            if (is_resource($handle)) {
                fclose($handle);
            }
            throw $e;
        }

        return [
            'inserted' => $inserted,
            'skipped'  => $skipped,
        ];
    }

    /**
     * Parse date from YYYY-MM-DD or DD-MM-YYYY formats safely.
     */
    private function formatDate(?string $dateStr): ?string
    {
        $dateStr = trim($dateStr ?? '');
        if ($dateStr === '' || $dateStr === '-' || $dateStr === '0000-00-00') {
            return null;
        }

        // Fast path: if it already looks like YYYY-MM-DD
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateStr)) {
            return $dateStr;
        }

        // If it looks like DD-MM-YYYY or DD/MM/YYYY
        if (preg_match('/^(\d{2})[\/\-](\d{2})[\/\-](\d{4})$/', $dateStr, $matches)) {
            return "{$matches[3]}-{$matches[2]}-{$matches[1]}";
        }

        // Try generic parse (fallback)
        $time = strtotime($dateStr);
        if ($time !== false) {
            return date('Y-m-d', $time);
        }

        return null;
    }
}
