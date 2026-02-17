<?php

namespace App\Services;

use App\Models\Pegawai;
use Illuminate\Support\Facades\DB;

class PegawaiDiffService
{
    /**
     * Generate MD5 hash from important employee data fields.
     * This hash is used to quickly detect if any data has changed.
     * 
     * @param object $row Data row (can be from staging or main table)
     * @return string MD5 hash
     */
    public function generateHash($row)
    {
        // Ensure consistent data types and formatting for hashing
        $dataToHash = [
            'nama' => trim($row->nama ?? ''),
            'gelar_depan' => trim($row->gelar_depan ?? ''),
            'gelar_belakang' => trim($row->gelar_belakang ?? ''),
            'jenis_kelamin' => trim($row->jenis_kelamin ?? ''),
            'tempat_lahir' => trim($row->tempat_lahir ?? ''),
            'tanggal_lahir' => $row->tanggal_lahir, // Assuming date format is consistent or object
            'nik' => trim($row->nik ?? ''),

            // Reference IDs (foreign keys)
            'agama_id' => $row->agama_id,
            'jenis_kawin_id' => $row->jenis_kawin_id,
            'jenis_pegawai_id' => $row->jenis_pegawai_id,
            'kedudukan_hukum_id' => $row->kedudukan_hukum_id,
            'golongan_id' => $row->gol_akhir_id ?? $row->golongan_id, // Map staging gol_akhir_id to main golongan_id
            'jabatan_id' => $row->jabatan_id,
            'jenis_jabatan_id' => $row->jenis_jabatan_id,
            'pendidikan_id' => $row->pendidikan_id,
            'tingkat_pendidikan_id' => $row->tingkat_pendidikan_id,
            'unor_id' => $row->unor_id,
            'instansi_induk_id' => $row->instansi_induk_id,
            'instansi_kerja_id' => $row->instansi_kerja_id,
            'lokasi_kerja_id' => $row->lokasi_kerja_id,
            'kpkn_id' => $row->kpkn_id,

            // ASN Status
            'status_cpns_pns' => trim($row->status_cpns_pns ?? ''),
            'tmt_cpns' => $row->tmt_cpns,
            'tmt_pns' => $row->tmt_pns,
        ];

        return md5(json_encode($dataToHash));
    }

    /**
     * Analyze a staging record against the existing data in Pegawai table.
     * 
     * @param object $stagingRow The record from stg_pegawai_import
     * @return array Analysis result containing status, hash, and changes
     */
    public function analyze($stagingRow)
    {
        // Find existing employee by PNS ID (NIP)
        $pegawai = Pegawai::where('pns_id', $stagingRow->pns_id)->first();

        // Calculate hash for the new incoming data
        $newHash = $this->generateHash($stagingRow);

        // Case 1: New Employee
        if (!$pegawai) {
            return [
                'status' => 'new',
                'hash' => $newHash,
                'changes' => null // No comparison possible
            ];
        }

        // Case 2: Existing Employee, check if hash matches
        // If data_hash exists on pegawai and matches, we can skip detailed check
        if ($pegawai->data_hash && $pegawai->data_hash === $newHash) {
            return [
                'status' => 'unchanged',
                'hash' => $newHash,
                'changes' => null
            ];
        }

        // Case 3: Changed Data - Detect specific field changes
        $changes = [];

        // Define mapping: Staging Column => [Pegawai Column, Label]
        // If key is same as value[0], means direct mapping
        $fieldMapping = [
            'nama' => ['nama', 'Nama'],
            'gelar_depan' => ['gelar_depan', 'Gelar Depan'],
            'gelar_belakang' => ['gelar_belakang', 'Gelar Belakang'],
            'jenis_kelamin' => ['jenis_kelamin', 'Jenis Kelamin'],
            'tempat_lahir' => ['tempat_lahir', 'Tempat Lahir'],
            'tanggal_lahir' => ['tanggal_lahir', 'Tanggal Lahir'],
            'nik' => ['nik', 'NIK'],
            'agama_id' => ['agama_id', 'Agama'],
            'jenis_kawin_id' => ['jenis_kawin_id', 'Status Perkawinan'],
            'jenis_pegawai_id' => ['jenis_pegawai_id', 'Jenis Pegawai'],
            'kedudukan_hukum_id' => ['kedudukan_hukum_id', 'Kedudukan Hukum'],
            'jabatan_id' => ['jabatan_id', 'Jabatan'],
            'jenis_jabatan_id' => ['jenis_jabatan_id', 'Jenis Jabatan'],
            'pendidikan_id' => ['pendidikan_id', 'Pendidikan'],
            'tingkat_pendidikan_id' => ['tingkat_pendidikan_id', 'Tingkat Pendidikan'],
            'unor_id' => ['unor_id', 'Unit Organisasi'],
            'instansi_induk_id' => ['instansi_induk_id', 'Instansi Induk'],
            'instansi_kerja_id' => ['instansi_kerja_id', 'Instansi Kerja'],
            'lokasi_kerja_id' => ['lokasi_kerja_id', 'Lokasi Kerja'],
            'kpkn_id' => ['kpkn_id', 'KPKN'],
            'status_cpns_pns' => ['status_cpns_pns', 'Status CPNS/PNS'],
            'tmt_cpns' => ['tmt_cpns', 'TMT CPNS'],
            'tmt_pns' => ['tmt_pns', 'TMT PNS'],
            // Special mapping
            'gol_akhir_id' => ['golongan_id', 'Golongan'],
        ];

        foreach ($fieldMapping as $stagingCol => $map) {
            $pegawaiCol = $map[0];
            $label = $map[1];

            $oldValue = $pegawai->$pegawaiCol;
            $newValue = $stagingRow->$stagingCol;

            // Normalization for comparison (dates, empty strings vs null)
            if ($this->normalizeValue($oldValue) != $this->normalizeValue($newValue)) {
                $changes[$pegawaiCol] = [ // Use pegawai column name as key for consistency
                    'label' => $label,
                    'old' => $oldValue,
                    'new' => $newValue
                ];
            }
        }

        // Only report as changed if we actually found differences
        // (Hash mismatch could be due to fields we don't track in detail or artifacts)
        if (empty($changes)) {
            return [
                'status' => 'unchanged',
                'hash' => $newHash,
                'changes' => null
            ];
        }

        return [
            'status' => 'changed',
            'hash' => $newHash,
            'changes' => $changes
        ];
    }

    /**
     * Normalize values for comparison to avoid false positives.
     */
    private function normalizeValue($value)
    {
        if ($value instanceof \DateTime) {
            return $value->format('Y-m-d');
        }

        // Treat null, empty string, and "0" string carefully based on context
        // For general strings, trim and empty string equals null
        if (is_string($value)) {
            $trimmed = trim($value);
            return $trimmed === '' ? null : $trimmed;
        }

        return $value;
    }
}
