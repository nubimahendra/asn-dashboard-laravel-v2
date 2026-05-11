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

        return view('mari.dashboard', compact(
            'totalPegawaiAktif',
            'totalPegawaiGolongan',
            'totalIuranBulanIni',
            'jumlahOpdBulanIni',
            'bulanSekarang',
            'tahunSekarang'
        ));
    }
}
