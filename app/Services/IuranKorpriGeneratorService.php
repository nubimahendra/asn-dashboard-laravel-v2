<?php

namespace App\Services;

use App\Models\Pegawai;
use App\Models\RefIuranKorpri;
use App\Models\RefJabatanKelas;
use App\Models\IuranKorpriTransaksi;
use App\Models\RefJabatanMapping;
use App\Models\RefJabatanDefault;
use Illuminate\Support\Facades\DB;

class IuranKorpriGeneratorService
{
    public function generate(int $bulan, int $tahun)
    {
        DB::beginTransaction();

        try {

            $pegawaiList = Pegawai::with('riwayatJabatanAktif')
                ->whereHas('kedudukanHukum', function ($q) {
                    $q->where('wajib_iuran', true);
                })
                ->get();

            foreach ($pegawaiList as $pegawai) {

                $riwayat = $pegawai->riwayatJabatanAktif;

                if (!$riwayat) {
                    continue;
                }

                $kelas = $this->getKelasJabatan(
                    $riwayat->jabatan_id,
                    $riwayat->unor_id
                );

                if (!$kelas) {
                    continue;
                }

                $tarif = RefIuranKorpri::where('kelas_jabatan', $kelas)
                    ->where('tahun_berlaku', $tahun)
                    ->first();

                if (!$tarif) {
                    continue;
                }

                IuranKorpriTransaksi::updateOrCreate(
                    [
                        'pegawai_id' => $pegawai->id,
                        'bulan' => (int) $bulan,
                        'tahun' => (int) $tahun
                    ],
                    [
                        'kelas_jabatan' => (string) $kelas,
                        'nominal' => (float) $tarif->nominal,
                        'status' => 'generated'
                    ]
                );
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function getKelasJabatan($jabatanId, $unorId)
    {
        // 1. Cek di tabel mapping baru (ref_jabatan_mapping -> ref_kelas_perbup)
        $mappingPerbup = RefJabatanMapping::with('kelasPerbup')
            ->where('jabatan_siasn_id', $jabatanId)
            ->where('status_validasi', 'valid') // Opsional: Pastikan mappingnya valid
            ->first();

        if ($mappingPerbup && $mappingPerbup->kelasPerbup) {
            return $mappingPerbup->kelasPerbup->kelas_jabatan;
        }

        // 2. Fallback 1: Cek di tabel default jabatan baru (ref_jabatan_default)
        $defaultKelas = RefJabatanDefault::where('jabatan_id', $jabatanId)->first();
        if ($defaultKelas) {
            return $defaultKelas->kelas_jabatan;
        }

        // 3. Fallback 2: Cek di tabel mapping lama (ref_jabatan_kelas) -> prioritas unor_id
        $mappingLama = RefJabatanKelas::where('jabatan_id', $jabatanId)
            ->where('unor_id', $unorId)
            ->first();

        if ($mappingLama) {
            return $mappingLama->kelas_jabatan;
        }

        // 4. Fallback 3: Cek di tabel mapping lama (ref_jabatan_kelas) -> global tanpa unor
        $globalLama = RefJabatanKelas::where('jabatan_id', $jabatanId)
            ->whereNull('unor_id')
            ->first();

        if ($globalLama) {
            return $globalLama->kelas_jabatan;
        }

        // Log::warning("Kelas jabatan tidak ditemukan untuk pegawai dengan jabatan_id: {$jabatanId}, unor_id: {$unorId}");
        return null;
    }
}
