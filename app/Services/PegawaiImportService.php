<?php

namespace App\Services;

use App\Models\Pegawai;
use App\Models\StgPegawaiImport;

class PegawaiImportService
{
    protected $referenceSync;
    protected $riwayatSync;

    public function __construct(
        ReferenceSyncService $referenceSync,
        RiwayatSyncService $riwayatSync
    ) {
        $this->referenceSync = $referenceSync;
        $this->riwayatSync = $riwayatSync;
    }

    /**
     * Sync pegawai data from staging to main table
     * Update if exists (by pns_id), insert if new
     */
    public function syncFromStaging(StgPegawaiImport $staging)
    {
        if (!$staging->pns_id) {
            throw new \Exception('PNS ID is required for syncing pegawai');
        }

        $pegawai = Pegawai::updateOrCreate(
            ['pns_id' => $staging->pns_id],
            [
                'nip_baru' => $staging->nip_baru,
                'nip_lama' => $staging->nip_lama,
                'nama' => $staging->nama,
                'gelar_depan' => $staging->gelar_depan,
                'gelar_belakang' => $staging->gelar_belakang,
                'jenis_kelamin' => $staging->jenis_kelamin,
                'tanggal_lahir' => $staging->tanggal_lahir,
                'tempat_lahir' => $staging->tempat_lahir,
                'alamat' => $staging->alamat,
                'no_hp' => $staging->no_hp,
                'email' => $staging->email,

                // Foreign keys to reference tables
                'agama_id' => $staging->agama_id,
                'jenis_kawin_id' => $staging->jenis_kawin_id,
                'jenis_pegawai_id' => $staging->jenis_pegawai_id,
                'kedudukan_hukum_id' => $staging->kedudukan_hukum_id,

                // Current status (latest)
                'golongan_id' => $staging->gol_akhir_id,
                'jabatan_id' => $staging->jabatan_id,
                'jenis_jabatan_id' => $staging->jenis_jabatan_id,
                'pendidikan_id' => $staging->pendidikan_id,
                'tingkat_pendidikan_id' => $staging->tingkat_pendidikan_id,

                // Organizational
                'unor_id' => $staging->unor_id,
                'instansi_induk_id' => $staging->instansi_induk_id,
                'instansi_kerja_id' => $staging->instansi_kerja_id,
                'lokasi_kerja_id' => $staging->lokasi_kerja_id,
                'kpkn_id' => $staging->kpkn_id,

                // Status ASN
                'status_cpns_pns' => $staging->status_cpns_pns,
                'tmt_cpns' => $staging->tmt_cpns,
                'tmt_pns' => $staging->tmt_pns,

                'flag_ikd' => $staging->flag_ikd,
            ]
        );

        return $pegawai;
    }

    /**
     * Process a single staging record
     * 1. Sync reference tables
     * 2. Sync pegawai table
     * 3. Sync riwayat tables
     */
    public function processStagingRecord(StgPegawaiImport $staging)
    {
        try {
            \DB::beginTransaction();

            // Step 1: Sync all reference tables
            $this->referenceSync->syncAllFromStaging($staging);

            // Step 2: Sync pegawai table
            $pegawai = $this->syncFromStaging($staging);

            // Step 3: Sync riwayat tables
            $this->riwayatSync->syncAllFromStaging($pegawai, $staging);

            // Mark as processed
            $staging->update([
                'is_processed' => true,
                'processed_at' => now(),
                'processing_error' => null,
            ]);

            \DB::commit();

            return true;
        } catch (\Exception $e) {
            \DB::rollBack();

            // Log error
            $staging->update([
                'processing_error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
