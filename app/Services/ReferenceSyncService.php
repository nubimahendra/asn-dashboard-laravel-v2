<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReferenceSyncService
{
    /**
     * Generic method to sync reference data
     * Uses updateOrInsert to ensure data is always up-to-date
     * 
     * @param string $table Table name (e.g., 'ref_agama')
     * @param mixed $id Reference ID
     * @param string|null $nama Reference name
     * @return mixed The ID if successful, null otherwise
     */
    private function syncRef($table, $id, $nama)
    {
        // Skip if ID is empty
        if (empty($id)) {
            return null;
        }

        // Skip if nama is empty (we need both ID and nama)
        if (empty($nama)) {
            Log::warning("Reference sync skipped: {$table} - ID {$id} has no name");
            return null;
        }

        try {
            DB::table($table)->updateOrInsert(
                ['id' => $id],
                [
                    'nama' => $nama,
                    'updated_at' => now(),
                    'created_at' => DB::raw('COALESCE(created_at, NOW())'),
                ]
            );

            Log::debug("Reference synced: {$table} - ID: {$id}, Nama: {$nama}");
            return $id;
        } catch (\Exception $e) {
            Log::error("Failed to sync reference {$table} - ID: {$id}, Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Sync individual reference tables
     */
    public function syncAgama($id, $nama)
    {
        return $this->syncRef('ref_agama', $id, $nama);
    }

    public function syncJenisKawin($id, $nama)
    {
        return $this->syncRef('ref_jenis_kawin', $id, $nama);
    }

    public function syncJenisPegawai($id, $nama)
    {
        return $this->syncRef('ref_jenis_pegawai', $id, $nama);
    }

    public function syncKedudukanHukum($id, $nama)
    {
        return $this->syncRef('ref_kedudukan_hukum', $id, $nama);
    }

    public function syncGolongan($id, $nama)
    {
        return $this->syncRef('ref_golongan', $id, $nama);
    }

    public function syncJenisJabatan($id, $nama)
    {
        return $this->syncRef('ref_jenis_jabatan', $id, $nama);
    }

    public function syncJabatan($id, $nama)
    {
        return $this->syncRef('ref_jabatan', $id, $nama);
    }

    public function syncTingkatPendidikan($id, $nama)
    {
        return $this->syncRef('ref_tingkat_pendidikan', $id, $nama);
    }

    public function syncPendidikan($id, $nama)
    {
        return $this->syncRef('ref_pendidikan', $id, $nama);
    }

    public function syncUnor($id, $nama)
    {
        // Custom logic for RefUnor because of new columns
        if (empty($id)) {
            return null;
        }

        if (empty($nama)) {
            Log::warning("Reference sync skipped: ref_unor - ID {$id} has no name");
            return null;
        }

        // Logic split nama
        // Assumed format: "OPD - Unit - Sub Unit" or just "OPD"
        $parts = explode(' - ', $nama);
        $count = count($parts);

        $namaOpd = null;
        $namaUnit = null;
        $namaUnor = $nama; // Default to full string if not split, or last part?

        $namaLengkap = $nama;

        if ($count >= 3) {
            $namaOpd = $parts[0];
            $namaUnor = end($parts); // The specific unit name (last part)
            // Join everything in between as nama_unit
            $namaUnit = implode(' - ', array_slice($parts, 1, $count - 2));
        } elseif ($count == 2) {
            $namaOpd = $parts[0];
            $namaUnor = $parts[1];
            $namaUnit = null; // No middle unit
        } else {
            // Only 1 part
            $namaOpd = $parts[0];
            $namaUnor = $parts[0];
            $namaUnit = null;
        }

        try {
            DB::table('ref_unor')->updateOrInsert(
                ['id' => $id],
                [
                    'nama' => $namaUnor,
                    'nama_lengkap' => $namaLengkap,
                    'nama_unit' => $namaUnit,
                    'nama_opd' => $namaOpd,
                    'updated_at' => now(),
                    'created_at' => DB::raw('COALESCE(created_at, NOW())'),
                ]
            );

            Log::debug("Reference synced: ref_unor - ID: {$id}, Nama: {$namaUnor}, OPD: {$namaOpd}");
            return $id;
        } catch (\Exception $e) {
            Log::error("Failed to sync reference ref_unor - ID: {$id}, Error: " . $e->getMessage());
            throw $e;
        }
    }

    public function syncInstansi($id, $nama)
    {
        return $this->syncRef('ref_instansi', $id, $nama);
    }

    public function syncLokasi($id, $nama)
    {
        return $this->syncRef('ref_lokasi', $id, $nama);
    }

    public function syncKpkn($id, $nama)
    {
        return $this->syncRef('ref_kpkn', $id, $nama);
    }

    public function syncJenisAsn($id, $nama)
    {
        return $this->syncRef('ref_jenis_asn', $id, $nama);
    }

    /**
     * Sync all reference data from staging record
     * This method is called BEFORE inserting pegawai data
     * to ensure all foreign keys exist
     */
    public function syncAllFromStaging($staging)
    {
        Log::info("Syncing all references for PNS ID: {$staging->pns_id}");

        $syncCount = 0;

        // Sync all reference tables
        if ($this->syncAgama($staging->agama_id, $staging->agama))
            $syncCount++;
        if ($this->syncJenisKawin($staging->jenis_kawin_id, $staging->jenis_kawin))
            $syncCount++;
        if ($this->syncJenisPegawai($staging->jenis_pegawai_id, $staging->jenis_pegawai))
            $syncCount++;
        if ($this->syncKedudukanHukum($staging->kedudukan_hukum_id, $staging->kedudukan_hukum))
            $syncCount++;

        // Sync golongan (both awal and akhir)
        if ($this->syncGolongan($staging->gol_awal_id, $staging->gol_awal))
            $syncCount++;
        if ($this->syncGolongan($staging->gol_akhir_id, $staging->gol_akhir))
            $syncCount++;

        // Sync jabatan related
        if ($this->syncJenisJabatan($staging->jenis_jabatan_id, $staging->jenis_jabatan))
            $syncCount++;
        if ($this->syncJabatan($staging->jabatan_id, $staging->jabatan))
            $syncCount++;

        // Sync pendidikan related
        if ($this->syncTingkatPendidikan($staging->tingkat_pendidikan_id, $staging->tingkat_pendidikan))
            $syncCount++;
        if ($this->syncPendidikan($staging->pendidikan_id, $staging->pendidikan))
            $syncCount++;

        // Sync organizational
        if ($this->syncUnor($staging->unor_id, $staging->unor))
            $syncCount++;
        if ($this->syncInstansi($staging->instansi_induk_id, $staging->instansi_induk))
            $syncCount++;
        if ($this->syncInstansi($staging->instansi_kerja_id, $staging->instansi_kerja))
            $syncCount++;
        if ($this->syncLokasi($staging->lokasi_kerja_id, $staging->lokasi_kerja))
            $syncCount++;
        if ($this->syncKpkn($staging->kpkn_id, $staging->kpkn))
            $syncCount++;

        Log::info("Reference sync completed: {$syncCount} references synced for PNS ID: {$staging->pns_id}");
    }
}
