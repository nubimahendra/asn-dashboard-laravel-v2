<?php

namespace App\Services;

use App\Models\{
    Pegawai,
    RiwayatStatusPegawai,
    RiwayatGolongan,
    RiwayatJabatan,
    RiwayatPendidikan
};

class RiwayatSyncService
{
    /**
     * Sync riwayat status pegawai
     * Only add new record if data is different from latest
     */
    public function syncRiwayatStatus($pegawaiId, $status, $tmt, $keterangan = null)
    {
        if (!$status)
            return;

        // Get latest riwayat
        $latest = RiwayatStatusPegawai::where('pegawai_id', $pegawaiId)
            ->latest('tmt')
            ->first();

        // If no history or data is different, add new record
        if (!$latest || $latest->status != $status || $latest->tmt != $tmt) {
            RiwayatStatusPegawai::create([
                'pegawai_id' => $pegawaiId,
                'status' => $status,
                'tmt' => $tmt,
                'keterangan' => $keterangan,
            ]);
        }
    }

    /**
     * Sync riwayat golongan
     */
    public function syncRiwayatGolongan($pegawaiId, $golonganId, $tmt, $mkTahun = null, $mkBulan = null, $keterangan = null)
    {
        if (!$golonganId)
            return;

        // Get latest riwayat
        $latest = RiwayatGolongan::where('pegawai_id', $pegawaiId)
            ->latest('tmt')
            ->first();

        // If no history or data is different, add new record
        if (!$latest || $latest->golongan_id != $golonganId || $latest->tmt != $tmt) {
            RiwayatGolongan::create([
                'pegawai_id' => $pegawaiId,
                'golongan_id' => $golonganId,
                'tmt' => $tmt,
                'mk_tahun' => $mkTahun,
                'mk_bulan' => $mkBulan,
                'keterangan' => $keterangan,
            ]);
        }
    }

    /**
     * Sync riwayat jabatan
     */
    public function syncRiwayatJabatan($pegawaiId, $jabatanId, $jenisJabatanId, $unorId, $tmt, $keterangan = null)
    {
        if (!$jabatanId)
            return;

        // Get latest riwayat
        $latest = RiwayatJabatan::where('pegawai_id', $pegawaiId)
            ->latest('tmt')
            ->first();

        // If no history or data is different, add new record
        if (!$latest || $latest->jabatan_id != $jabatanId || $latest->unor_id != $unorId || $latest->tmt != $tmt) {
            RiwayatJabatan::create([
                'pegawai_id' => $pegawaiId,
                'jabatan_id' => $jabatanId,
                'jenis_jabatan_id' => $jenisJabatanId,
                'unor_id' => $unorId,
                'tmt' => $tmt,
                'keterangan' => $keterangan,
            ]);
        }
    }

    /**
     * Sync riwayat pendidikan
     */
    public function syncRiwayatPendidikan($pegawaiId, $pendidikanId, $tingkatPendidikanId, $tahunLulus, $institusi = null, $keterangan = null)
    {
        if (!$pendidikanId)
            return;

        // Get latest riwayat
        $latest = RiwayatPendidikan::where('pegawai_id', $pegawaiId)
            ->latest('tahun_lulus')
            ->first();

        // If no history or data is different, add new record
        if (!$latest || $latest->pendidikan_id != $pendidikanId || $latest->tahun_lulus != $tahunLulus) {
            RiwayatPendidikan::create([
                'pegawai_id' => $pegawaiId,
                'pendidikan_id' => $pendidikanId,
                'tingkat_pendidikan_id' => $tingkatPendidikanId,
                'tahun_lulus' => $tahunLulus,
                'institusi' => $institusi,
                'keterangan' => $keterangan,
            ]);
        }
    }

    /**
     * Sync all riwayat from staging record
     */
    public function syncAllFromStaging($pegawai, $staging)
    {
        // Sync status (CPNS/PNS)
        if ($staging->status_cpns_pns) {
            $tmt = $staging->status_cpns_pns == 'CPNS' ? $staging->tmt_cpns : $staging->tmt_pns;
            $this->syncRiwayatStatus($pegawai->id, $staging->status_cpns_pns, $tmt);
        }

        // Sync golongan (use gol_akhir as current)
        if ($staging->gol_akhir_id) {
            $this->syncRiwayatGolongan(
                $pegawai->id,
                $staging->gol_akhir_id,
                $staging->tmt_gol_akhir,
                $staging->mk_tahun,
                $staging->mk_bulan
            );
        }

        // Sync jabatan
        if ($staging->jabatan_id) {
            $this->syncRiwayatJabatan(
                $pegawai->id,
                $staging->jabatan_id,
                $staging->jenis_jabatan_id,
                $staging->unor_id,
                $staging->tmt_jabatan
            );
        }

        // Sync pendidikan
        if ($staging->pendidikan_id) {
            $this->syncRiwayatPendidikan(
                $pegawai->id,
                $staging->pendidikan_id,
                $staging->tingkat_pendidikan_id,
                $staging->tahun_lulus
            );
        }
    }
}
