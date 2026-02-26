<?php

namespace App\Services;

use App\Models\Pegawai;
use App\Models\RefIuranKorpri;
use App\Models\RefJabatanKelas;
use App\Models\IuranKorpriTransaksi;
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
        $mapping = RefJabatanKelas::where('jabatan_id', $jabatanId)
            ->where('unor_id', $unorId)
            ->first();

        if ($mapping) {
            return $mapping->kelas_jabatan;
        }

        $global = RefJabatanKelas::where('jabatan_id', $jabatanId)
            ->whereNull('unor_id')
            ->first();

        return $global?->kelas_jabatan;
    }
}
