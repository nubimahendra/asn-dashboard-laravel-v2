<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 0. Filter Setup (pd = Perangkat Daerah / OPD)
        // 0. Filter Setup (pd = Perangkat Daerah / OPD)
        $filterOpd = $request->input('opd');

        // Fetch distinct nama from RefUnor directly for better performance (if needed for filtering)
        $listOpd = \App\Models\RefUnor::whereNotNull('nama')
            ->where('nama', '!=', '')
            ->distinct()
            ->orderBy('nama')
            ->pluck('nama');

        $query = Pegawai::with([
            'golongan',
            'jabatan',
            'tingkatPendidikan',
            'unor',
            'jenisPegawai',
            'instansiKerja'
        ]);

        if ($filterOpd) {
            $query->whereHas('unor', function ($q) use ($filterOpd) {
                // Filter by nama per user request
                $q->where('nama', $filterOpd);
            });
        }

        // 1. Top Cards Metrics
        // 1. Top Cards Metrics
        // Note: Calculations need to check for specific string matches or just count
        $totalPegawai = (clone $query)->count();

        // jenis_kelamin from pegawai table: 'M' (Male) = Laki-laki, 'F' (Female) = Perempuan
        $totalLaki = (clone $query)->where('jenis_kelamin', 'M')->count();
        $totalPerempuan = (clone $query)->where('jenis_kelamin', 'F')->count();

        // Status pegawai kombinasi kedudukan_hukum_id dan status_cpns_pns
        // PNS: kedudukan_hukum_id IN (1,2,3,4,15) AND status_cpns_pns = 'P'
        $totalPns = (clone $query)
            ->whereIn('kedudukan_hukum_id', [1, 2, 3, 4, 15])
            ->where('status_cpns_pns', 'P')
            ->count();

        // CPNS: kedudukan_hukum_id IN (1,2,3,4,15) AND status_cpns_pns = 'C'
        $totalCpns = (clone $query)
            ->whereIn('kedudukan_hukum_id', [1, 2, 3, 4, 15])
            ->where('status_cpns_pns', 'C')
            ->count();

        // PPPK: kedudukan_hukum_id IN (71, 73)
        $totalPppk = (clone $query)->whereIn('kedudukan_hukum_id', [71, 73])->count();

        // PPPK PW: kedudukan_hukum_id = 101
        $totalPppkPw = (clone $query)->where('kedudukan_hukum_id', 101)->count();

        // 2. Charts Data

        // Chart 1: Jenis Kelamin (Pie)
        $statsJenikel = [
            'Laki-laki' => $totalLaki,
            'Perempuan' => $totalPerempuan
        ];

        $chartJenikel = [
            'labels' => array_keys($statsJenikel),
            'series' => array_values($statsJenikel),
        ];

        // Chart 2: Status Pegawai (Pie) - CPNS, PNS, PPPK, PPPK PW
        $statsStsPeg = [
            'PNS' => $totalPns,
            'CPNS' => $totalCpns,
            'PPPK' => $totalPppk,
            'PPPK PW' => $totalPppkPw,
        ];

        $chartStsPeg = [
            'labels' => array_keys($statsStsPeg),
            'series' => array_values($statsStsPeg),
        ];

        // Chart 3: Pendidikan (tingkat_pendidikan) (Bar)
        $dataPendidikan = (clone $query)
            ->whereHas('tingkatPendidikan')
            ->with('tingkatPendidikan')
            ->get()
            ->groupBy(function ($item) {
                return $item->tingkatPendidikan->nama ?? 'Tidak Diketahui';
            })
            ->map(function ($group) {
                return $group->count();
            })
            ->sortDesc();

        $chartPendidikan = [
            'categories' => $dataPendidikan->keys()->toArray(),
            'series' => $dataPendidikan->values()->toArray(),
        ];

        // Chart 4: Eselon (Bar) - Based on jenis_jabatan
        $dataEselon = (clone $query)
            ->whereHas('jenisJabatan')
            ->with('jenisJabatan')
            ->get()
            ->groupBy(function ($item) {
                return $item->jenisJabatan->nama ?? 'Tidak Diketahui';
            })
            ->map(function ($group) {
                return $group->count();
            })
            ->sortKeys();

        $chartEselon = [
            'categories' => $dataEselon->keys()->toArray(),
            'series' => $dataEselon->values()->toArray(),
        ];

        // Chart 5: Unit Kerja (Horizontal Bar - Top 10)
        $dataOpd = (clone $query)
            ->whereHas('unor')
            ->with('unor')
            ->get()
            ->groupBy(function ($item) {
                // Use nama per user request
                return $item->unor->nama ?? 'Tidak Diketahui';
            })
            ->map(function ($group) {
                return $group->count();
            })
            ->sortDesc()
            ->take(10);

        $chartOpd = [
            'categories' => $dataOpd->keys()->toArray(),
            'series' => $dataOpd->values()->toArray(),
        ];

        // Chart 6: Golongan (Bar)
        // Group by pure golongan name to merge duplicate groups like 'III/a' 
        // Then sort them properly by parsing their values, or just let string sort handle it (or use custom sort if string sort isn't enough, but usually string sort keys is fine, or we can just sortDesc to show the highest populated).
        $dataGolongan = (clone $query)
            ->whereHas('golongan')
            ->with('golongan')
            ->get()
            ->groupBy(function ($item) {
                // Hardcode specific grouping request for III/a
                if (in_array($item->golongan_id, ['19.8', '21.9'])) {
                    return 'III/a';
                }

                $namaGolongan = trim($item->golongan->nama ?? 'Tidak Diketahui');
                // Standardize common whitespace/case issues just in case
                return empty($namaGolongan) ? 'Tidak Diketahui' : $namaGolongan;
            })
            ->map(function ($group) {
                return $group->count();
            });

        // Custom sort for Golongan (I, II, III, IV)
        $dataGolongan = $dataGolongan->sortBy(function ($count, $key) {
           return $key; // simple sort by key name 'I', 'II', 'III', 'IV' etc.
        });

        $chartGolongan = [
            'categories' => $dataGolongan->keys()->toArray(),
            'series' => $dataGolongan->values()->toArray(),
        ];

        // Chart 7: Generasi (Pie) - Based on tanggal_lahir
        // Gen Z: 1997 - 2012
        // Gen Y: 1981 - 1996
        // Gen X: 1965 - 1980
        // Baby Boomer: 1946 - 1964 (Using < 1965 as Others/Boomers)
        $rawTglLahir = (clone $query)->select('tanggal_lahir')->whereNotNull('tanggal_lahir')->get();

        $statsGenerasi = [
            'Gen Z (1997-2012)' => 0,
            'Gen Y (1981-1996)' => 0,
            'Gen X (1965-1980)' => 0,
            'Lainnya' => 0
        ];

        foreach ($rawTglLahir as $item) {
            if (!$item->tanggal_lahir)
                continue;

            try {
                $year = Carbon::parse($item->tanggal_lahir)->year;

                if ($year >= 1997 && $year <= 2012) {
                    $statsGenerasi['Gen Z (1997-2012)']++;
                } elseif ($year >= 1981 && $year <= 1996) {
                    $statsGenerasi['Gen Y (1981-1996)']++;
                } elseif ($year >= 1965 && $year <= 1980) {
                    $statsGenerasi['Gen X (1965-1980)']++;
                } else {
                    $statsGenerasi['Lainnya']++;
                }
            } catch (\Exception $e) {
                // Ignore invalid dates
                continue;
            }
        }

        $chartGenerasi = [
            'labels' => array_keys($statsGenerasi),
            'series' => array_values($statsGenerasi),
        ];

        // 6. Paginated Table Data
        $pegawaiQuery = (clone $query)->select('pegawai.*');

        if ($request->has('search') && !empty($request->search)) {
            $pegawaiQuery->where('nama', 'like', '%' . $request->search . '%');
        }

        $pegawai = $pegawaiQuery->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        // Last Sync Info
        $lastSyncRaw = Pegawai::max('updated_at');
        $lastSync = $lastSyncRaw ? Carbon::parse($lastSyncRaw)->format('d M Y H:i') : '-';

        if ($request->ajax()) {
            return view('partials.employee-table', compact('pegawai'));
        }

        return view('dashboard', compact(
            'listOpd',
            'filterOpd',
            'totalPegawai',
            'totalLaki',
            'totalPerempuan',
            'totalPns',
            'totalCpns',
            'totalPppk',
            'totalPppkPw',
            'chartJenikel',
            'chartStsPeg',
            'chartPendidikan',
            'chartEselon',
            'chartEselon',
            'chartOpd',
            'chartGolongan',
            'chartGenerasi',
            'pegawai',
            'lastSync'
        ));
    }
}
