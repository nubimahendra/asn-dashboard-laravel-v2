<?php

namespace App\Imports;

use App\Models\StgPegawaiImport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class PegawaiImport implements ToModel, WithHeadingRow, WithCustomCsvSettings, WithBatchInserts, WithChunkReading
{
    protected $sourceFile;

    public function __construct($sourceFile)
    {
        $this->sourceFile = $sourceFile;
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
     * Configure CSV settings for pipe delimiter
     */
    public function getCsvSettings(): array
    {
        return [
            'delimiter' => '|',
            'enclosure' => '"',
            'escape_character' => '\\',
            'contiguous' => false,
            'input_encoding' => 'UTF-8'
        ];
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
}
