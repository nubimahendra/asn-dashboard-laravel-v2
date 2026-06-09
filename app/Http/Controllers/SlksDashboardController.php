<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UsulSlks;
use App\Models\Pegawai;

class SlksDashboardController extends Controller
{
    public function index()
    {
        // Total usulan per status
        $totalDraft = UsulSlks::where('status', 'draft_usulan')->count();
        $totalDiajukan = UsulSlks::where('status', 'diajukan')->count();
        $totalDisetujui = UsulSlks::where('status', 'disetujui')->count();
        
        // Breakdown pegawai aktif berdasarkan jenis
        $pegawaiAktif = Pegawai::aktif();
        
        // Copy the query builder instance to avoid modifying the original
        $totalPns = (clone $pegawaiAktif)->whereIn('kedudukan_hukum_id', ['01','02','03','04','15'])->count();
        $totalPppk = (clone $pegawaiAktif)->whereIn('kedudukan_hukum_id', ['71','73'])->count();
        $totalPppkPw = (clone $pegawaiAktif)->where('kedudukan_hukum_id', '101')->count();
        
        return view('siput.dashboard', compact(
            'totalDraft', 'totalDiajukan', 'totalDisetujui',
            'totalPns', 'totalPppk', 'totalPppkPw'
        ));
    }
}
