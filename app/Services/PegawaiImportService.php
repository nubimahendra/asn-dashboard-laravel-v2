<?php

namespace App\Services;

use App\Models\Pegawai;
use App\Models\StgPegawaiImport;
use Illuminate\Support\Facades\Log;

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

        // Sync all references first and get the actual IDs to use
        // If sync returns NULL (because name is empty), we'll use NULL for FK
        $agamaId = $this->referenceSync->syncAgama($staging->agama_id, $staging->agama);
        $jenisKawinId = $this->referenceSync->syncJenisKawin($staging->jenis_kawin_id, $staging->jenis_kawin);
        $jenisPegawaiId = $this->referenceSync->syncJenisPegawai($staging->jenis_pegawai_id, $staging->jenis_pegawai);
        $kedudukanHukumId = $this->referenceSync->syncKedudukanHukum($staging->kedudukan_hukum_id, $staging->kedudukan_hukum);
        $golonganId = $this->referenceSync->syncGolongan($staging->gol_akhir_id, $staging->gol_akhir);
        $jabatanId = $this->referenceSync->syncJabatan($staging->jabatan_id, $staging->jabatan);
        $jenisJabatanId = $this->referenceSync->syncJenisJabatan($staging->jenis_jabatan_id, $staging->jenis_jabatan);
        $pendidikanId = $this->referenceSync->syncPendidikan($staging->pendidikan_id, $staging->pendidikan);
        $tingkatPendidikanId = $this->referenceSync->syncTingkatPendidikan($staging->tingkat_pendidikan_id, $staging->tingkat_pendidikan);
        $unorId = $this->referenceSync->syncUnor($staging->unor_id, $staging->unor);
        $instansiIndukId = $this->referenceSync->syncInstansi($staging->instansi_induk_id, $staging->instansi_induk);
        $instansiKerjaId = $this->referenceSync->syncInstansi($staging->instansi_kerja_id, $staging->instansi_kerja);
        $lokasiKerjaId = $this->referenceSync->syncLokasi($staging->lokasi_kerja_id, $staging->lokasi_kerja);
        $kpknId = $this->referenceSync->syncKpkn($staging->kpkn_id, $staging->kpkn);

        // Log if any reference was set to NULL
        $nullRefs = [];
        if ($staging->agama_id && !$agamaId)
            $nullRefs[] = 'agama';
        if ($staging->jenis_kawin_id && !$jenisKawinId)
            $nullRefs[] = 'jenis_kawin';
        if ($staging->jenis_pegawai_id && !$jenisPegawaiId)
            $nullRefs[] = 'jenis_pegawai';
        if ($staging->kedudukan_hukum_id && !$kedudukanHukumId)
            $nullRefs[] = 'kedudukan_hukum';
        if ($staging->gol_akhir_id && !$golonganId)
            $nullRefs[] = 'golongan';
        if ($staging->jabatan_id && !$jabatanId)
            $nullRefs[] = 'jabatan';
        if ($staging->jenis_jabatan_id && !$jenisJabatanId)
            $nullRefs[] = 'jenis_jabatan';
        if ($staging->pendidikan_id && !$pendidikanId)
            $nullRefs[] = 'pendidikan';
        if ($staging->tingkat_pendidikan_id && !$tingkatPendidikanId)
            $nullRefs[] = 'tingkat_pendidikan';
        if ($staging->unor_id && !$unorId)
            $nullRefs[] = 'unor';
        if ($staging->instansi_induk_id && !$instansiIndukId)
            $nullRefs[] = 'instansi_induk';
        if ($staging->instansi_kerja_id && !$instansiKerjaId)
            $nullRefs[] = 'instansi_kerja';
        if ($staging->lokasi_kerja_id && !$lokasiKerjaId)
            $nullRefs[] = 'lokasi_kerja';
        if ($staging->kpkn_id && !$kpknId)
            $nullRefs[] = 'kpkn';

        if (!empty($nullRefs)) {
            Log::warning("PNS ID {$staging->pns_id}: Following references set to NULL due to missing names: " . implode(', ', $nullRefs));
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

                // Foreign keys - use synced IDs (NULL if sync failed)
                'agama_id' => $agamaId,
                'jenis_kawin_id' => $jenisKawinId,
                'jenis_pegawai_id' => $jenisPegawaiId,
                'kedudukan_hukum_id' => $kedudukanHukumId,

                // Current status (latest)
                'golongan_id' => $golonganId,
                'jabatan_id' => $jabatanId,
                'jenis_jabatan_id' => $jenisJabatanId,
                'pendidikan_id' => $pendidikanId,
                'tingkat_pendidikan_id' => $tingkatPendidikanId,

                // Organizational
                'unor_id' => $unorId,
                'instansi_induk_id' => $instansiIndukId,
                'instansi_kerja_id' => $instansiKerjaId,
                'lokasi_kerja_id' => $lokasiKerjaId,
                'kpkn_id' => $kpknId,

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
            Log::info("Processing staging record ID: {$staging->id}, PNS ID: {$staging->pns_id}");

            \DB::beginTransaction();

            // Step 1: Sync pegawai table (which now includes reference sync)
            Log::debug("Step 1: Syncing pegawai table for PNS ID: {$staging->pns_id}");
            $pegawai = $this->syncFromStaging($staging);
            Log::info("Pegawai synced successfully - ID: {$pegawai->id}, PNS ID: {$pegawai->pns_id}, Nama: {$pegawai->nama}");

            // Step 2: Sync riwayat tables
            Log::debug("Step 2: Syncing riwayat tables for PNS ID: {$staging->pns_id}");
            $this->riwayatSync->syncAllFromStaging($pegawai, $staging);

            // Mark as processed
            $staging->update([
                'is_processed' => true,
                'processed_at' => now(),
                'processing_error' => null,
            ]);

            \DB::commit();

            Log::info("Successfully processed staging record ID: {$staging->id}, PNS ID: {$staging->pns_id}");
            return true;
        } catch (\Exception $e) {
            \DB::rollBack();

            $errorMessage = $e->getMessage();
            Log::error("Failed to process staging record ID: {$staging->id}, PNS ID: {$staging->pns_id}, Error: {$errorMessage}");

            // Log error
            $staging->update([
                'processing_error' => $errorMessage,
            ]);

            throw $e;
        }
    }
}
