<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pegawai;
use App\Models\IuranKorpriTransaksi;

class MariDashboardController extends Controller
{
    public function index()
    {
        $bulanSekarang = date('n');
        $tahunSekarang = date('Y');

        // Total Pegawai Aktif
        $totalPegawaiAktif = Pegawai::aktif()->count();

        // Total Pegawai Aktif Ber-Golongan (yang bukan PPPK PW dsb)
        $totalPegawaiGolongan = Pegawai::aktif()
            ->whereNotNull('golongan_id')
            ->where('golongan_id', '!=', '')
            ->count();

        // Total Iuran Bulan Ini
        $totalIuranBulanIni = IuranKorpriTransaksi::where('bulan', $bulanSekarang)
            ->where('tahun', $tahunSekarang)
            ->sum('nominal');

        // Jumlah Unit Kerja/OPD yang sudah ada transaksi bulan ini
        // Menggunakan distinct pegawai.unor_id secara tidak langsung atau dari relasi jika ada
        // Untuk saat ini kita pakai count distinct dari pegawai_id yang punya unor
        $jumlahOpdBulanIni = IuranKorpriTransaksi::where('bulan', $bulanSekarang)
            ->where('tahun', $tahunSekarang)
            ->whereHas('pegawai', function ($q) {
                $q->whereNotNull('unor_id');
            })
            ->with('pegawai')
            ->get()
            ->pluck('pegawai.unor_id')
            ->unique()
            ->count();

        // Hitung Data Chart Top 10 OPD (Berdasarkan tarif saat ini)
        $allIuranRates = \App\Models\IuranKorpri::all()->keyBy('golongan_key');
        $allEselonRates = \App\Models\RefIuranEselon::all()->keyBy('eselon_key');
        $eselonMappings = \App\Models\RefEselonMapping::pluck('eselon_key', 'jabatan_id');

        $pegawais = Pegawai::aktif()->with(['unor', 'golongan', 'kedudukanHukum', 'iuranOverride'])->get();
        $opdTotals = [];
        
        foreach ($pegawais as $pegawai) {
            // Exclude PPPK PW
            if ($pegawai->kedudukan_hukum_id == '101') continue;

            $opdName = $pegawai->unor->nama ?? 'Tanpa OPD';
            if (!isset($opdTotals[$opdName])) {
                $opdTotals[$opdName] = 0;
            }

            $override = $pegawai->iuranOverride;

            if ($pegawai->jenis_jabatan_id == 1) {
                $eselAsli = $eselonMappings[$pegawai->jabatan_id] ?? 'IV/b';
                $eselonKey = $override && $override->override_eselon_key ? $override->override_eselon_key : $eselAsli;
                $besaran = isset($allEselonRates[$eselonKey]) ? $allEselonRates[$eselonKey]->besaran : 0;
                $opdTotals[$opdName] += $besaran;
            } else {
                $golonganNama = trim($pegawai->golongan_pppk ?? '');
                $golonganKey = $override && $override->override_golongan_key ? $override->override_golongan_key : $golonganNama;
                
                if ($golonganKey && isset($allIuranRates[$golonganKey])) {
                    $opdTotals[$opdName] += $allIuranRates[$golonganKey]->besaran;
                }
            }
        }

        arsort($opdTotals);
        $chartTopOpdIuran = array_slice($opdTotals, 0, 10, true);

        return view('mari.dashboard', compact(
            'totalPegawaiAktif',
            'totalPegawaiGolongan',
            'totalIuranBulanIni',
            'jumlahOpdBulanIni',
            'bulanSekarang',
            'tahunSekarang',
            'chartTopOpdIuran'
        ));
    }
}
