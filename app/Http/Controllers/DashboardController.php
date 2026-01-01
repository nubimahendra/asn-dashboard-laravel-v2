<?php

namespace App\Http\Controllers;

use App\Models\PegawaiAktif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 0. Filter Setup
        $filterOpd = $request->input('opd');
        $query = PegawaiAktif::query();

        if ($filterOpd) {
            $query->where('opd', $filterOpd);
        }

        // Fetch List OPD for Sidebar
        $listOpd = PegawaiAktif::select('opd')
            ->distinct()
            ->orderBy('opd')
            ->pluck('opd');

        // 1. Top Cards Metrics
        // Clone query for scalar values so we don't mutate the base query if we were re-using it (though we are building fresh queries for most)
        // Actually, let's just apply the filter filter to every specific sub-query to be safe and explicit, or use a base builder.
        // Using a base builder is cleaner but model static calls are used below. Let's just create a helper closure or apply individually.

        $applyFilter = function ($q) use ($filterOpd) {
            if ($filterOpd) {
                $q->where('opd', $filterOpd);
            }
        };

        $totalPegawai = PegawaiAktif::when($filterOpd, function ($q) use ($filterOpd) {
            $q->where('opd', $filterOpd); })->count();

        // Fetch jenikel stats
        $statsJenikel = PegawaiAktif::select('jenikel', DB::raw('count(*) as total'))
            ->when($filterOpd, function ($q) use ($filterOpd) {
                $q->where('opd', $filterOpd); })
            ->groupBy('jenikel')
            ->pluck('total', 'jenikel');

        // Map keys loosely to 'L'/'Laki-laki' and 'P'/'Perempuan' just in case
        $totalLaki = 0;
        $totalPerempuan = 0;
        foreach ($statsJenikel as $key => $val) {
            if (stripos($key, 'L') === 0 || stripos($key, 'Pria') !== false)
                $totalLaki += $val;
            if (stripos($key, 'P') === 0 && stripos($key, 'Pria') === false)
                $totalPerempuan += $val; // 'Perempuan' or 'P'
        }

        $avgUsia = PegawaiAktif::when($filterOpd, function ($q) use ($filterOpd) {
            $q->where('opd', $filterOpd); })->avg('usia');

        // 2. Charts Data

        // Chart 1: Jenis Kelamin (Pie)
        // Format for ApexCharts: labels: [], series: []
        $chartJenikel = [
            'labels' => $statsJenikel->keys()->toArray(),
            'series' => $statsJenikel->values()->toArray(),
        ];

        // Chart New: Jenis Pegawai (Pie)
        $statsStsPeg = PegawaiAktif::select('sts_peg', DB::raw('count(*) as total'))
            ->when($filterOpd, function ($q) use ($filterOpd) {
                $q->where('opd', $filterOpd); })
            ->groupBy('sts_peg')
            ->pluck('total', 'sts_peg');

        $chartStsPeg = [
            'labels' => $statsStsPeg->keys()->toArray(),
            'series' => $statsStsPeg->values()->toArray(),
        ];

        // Chart 2: Golongan (Bar)
        $dataGol = PegawaiAktif::select('gol', DB::raw('count(*) as total'))
            ->when($filterOpd, function ($q) use ($filterOpd) {
                $q->where('opd', $filterOpd); })
            ->groupBy('gol')
            ->orderBy('gol')
            ->pluck('total', 'gol');
        $chartGolongan = [
            'categories' => $dataGol->keys()->toArray(),
            'series' => $dataGol->values()->toArray(),
        ];

        // Chart 3: Pendidikan (Bar)
        $dataPendidikan = PegawaiAktif::select('pendidikan', DB::raw('count(*) as total'))
            ->when($filterOpd, function ($q) use ($filterOpd) {
                $q->where('opd', $filterOpd); })
            ->groupBy('pendidikan')
            ->orderBy('total', 'desc')
            ->pluck('total', 'pendidikan');
        $chartPendidikan = [
            'categories' => $dataPendidikan->keys()->toArray(),
            'series' => $dataPendidikan->values()->toArray(),
        ];

        // Chart 4: Distribusi Usia (Area/Line)
        $dataUsia = PegawaiAktif::select('usia', DB::raw('count(*) as total'))
            ->when($filterOpd, function ($q) use ($filterOpd) {
                $q->where('opd', $filterOpd); })
            ->groupBy('usia')
            ->orderBy('usia')
            ->pluck('total', 'usia');
        $chartUsia = [
            'categories' => $dataUsia->keys()->toArray(),
            'series' => $dataUsia->values()->toArray(),
        ];

        // Chart 5: Jenis Jabatan (Bar)
        $dataJbt = PegawaiAktif::select('jenis_jbt', DB::raw('count(*) as total'))
            ->when($filterOpd, function ($q) use ($filterOpd) {
                $q->where('opd', $filterOpd); })
            ->groupBy('jenis_jbt')
            ->orderBy('total', 'desc')
            ->pluck('total', 'jenis_jbt');
        $chartJenisJbt = [
            'categories' => $dataJbt->keys()->toArray(),
            'series' => $dataJbt->values()->toArray(),
        ];

        // Chart 6: Unit Kerja (Horizontal Bar - Top 10)
        // Even if filtered by OPD, we might still want to see maybe sub-units if they existed, but here we only have OPD.
        // If filtered, it will just show 1 bar, which is correct behavior (verifies filter).
        $dataOpd = PegawaiAktif::select('opd', DB::raw('count(*) as total'))
            ->when($filterOpd, function ($q) use ($filterOpd) {
                $q->where('opd', $filterOpd); })
            ->groupBy('opd')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->pluck('total', 'opd');
        $chartOpd = [
            'categories' => $dataOpd->keys()->toArray(),
            'series' => $dataOpd->values()->toArray(),
        ];

        return view('dashboard', compact(
            'listOpd',
            'filterOpd',
            'totalPegawai',
            'totalLaki',
            'totalPerempuan',
            'avgUsia',
            'chartJenikel',
            'chartGolongan',
            'chartPendidikan',
            'chartUsia',
            'chartJenisJbt',
            'chartOpd',
            'chartStsPeg'
        ));
    }
}
