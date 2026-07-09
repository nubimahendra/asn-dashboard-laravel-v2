<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\HistoryPegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $filterOpd = $request->input('opd');
        $filterSnapshot = $request->input('snapshot_month');

        $listOpd = \App\Models\RefUnor::whereNotNull('nama')
            ->where('nama', '!=', '')
            ->distinct()
            ->orderBy('nama')
            ->pluck('nama');

        $historyMonths = HistoryPegawai::select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month_val"))
            ->distinct()
            ->orderBy('month_val', 'desc')
            ->pluck('month_val');

        if ($filterSnapshot) {
            $query = HistoryPegawai::where(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"), $filterSnapshot);

            if ($filterOpd) {
                $query->where('unor_nama', $filterOpd);
            }

            $totalPegawai = (clone $query)->count();
            
            $totalLaki = (clone $query)->where('jenis_kelamin', 'M')->count();
            $totalPerempuan = (clone $query)->where('jenis_kelamin', 'F')->count();

            $totalPns = (clone $query)->where('sts_peg', 'PNS')->count();
            $totalCpns = (clone $query)->where('sts_peg', 'CPNS')->count();
            $totalPppk = (clone $query)->where('sts_peg', 'PPPK')->count();
            $totalPppkPw = (clone $query)->where('sts_peg', 'PPPK PW')->count();

            $statsJenikel = ['Laki-laki' => $totalLaki, 'Perempuan' => $totalPerempuan];
            $chartJenikel = ['labels' => array_keys($statsJenikel), 'series' => array_values($statsJenikel)];

            $statsStsPeg = ['PNS' => $totalPns, 'CPNS' => $totalCpns, 'PPPK' => $totalPppk, 'PPPK PW' => $totalPppkPw];
            $chartStsPeg = ['labels' => array_keys($statsStsPeg), 'series' => array_values($statsStsPeg)];

            $dataPendidikan = (clone $query)->select('tingkat_pendidikan', DB::raw('count(*) as total'))
                ->groupBy('tingkat_pendidikan')->pluck('total', 'tingkat_pendidikan')->sortDesc();
            if ($dataPendidikan->has('')) {
                $val = $dataPendidikan->pull('');
                $dataPendidikan['Tidak Diketahui'] = ($dataPendidikan['Tidak Diketahui'] ?? 0) + $val;
            }
            $chartPendidikan = ['categories' => $dataPendidikan->keys()->toArray(), 'series' => $dataPendidikan->values()->toArray()];

            $dataEselon = (clone $query)->select('jenis_jabatan', DB::raw('count(*) as total'))
                ->groupBy('jenis_jabatan')->pluck('total', 'jenis_jabatan')->sortKeys();
            if ($dataEselon->has('')) {
                $val = $dataEselon->pull('');
                $dataEselon['Tidak Diketahui'] = ($dataEselon['Tidak Diketahui'] ?? 0) + $val;
            }
            $chartEselon = ['categories' => $dataEselon->keys()->toArray(), 'series' => $dataEselon->values()->toArray()];

            $dataOpd = (clone $query)->select('unor_nama', DB::raw('count(*) as total'))
                ->groupBy('unor_nama')->pluck('total', 'unor_nama')->sortDesc()->take(10);
            if ($dataOpd->has('')) {
                $val = $dataOpd->pull('');
                $dataOpd['Tidak Diketahui'] = ($dataOpd['Tidak Diketahui'] ?? 0) + $val;
            }
            $chartOpd = ['categories' => $dataOpd->keys()->toArray(), 'series' => $dataOpd->values()->toArray()];

            $dataGolonganRaw = (clone $query)->select('golongan', DB::raw('count(*) as total'))
                ->groupBy('golongan')->pluck('total', 'golongan');
            $dataGolongan = collect();
            foreach($dataGolonganRaw as $key => $val) {
                $k = empty($key) ? 'Tidak Diketahui' : $key;
                $dataGolongan[$k] = ($dataGolongan[$k] ?? 0) + $val;
            }
            $dataGolongan = $dataGolongan->sortBy(function ($count, $key) {
                if ($key === 'Tidak Diketahui') return 999;
                return \App\Helpers\GolonganHelper::parseRoman($key);
            });
            $chartGolongan = ['categories' => $dataGolongan->keys()->toArray(), 'series' => $dataGolongan->values()->toArray()];

            $rawTglLahir = (clone $query)->select('tgl_lahir')->whereNotNull('tgl_lahir')->get();
            $statsGenerasi = ['Gen Z (1997-2012)' => 0, 'Gen Y (1981-1996)' => 0, 'Gen X (1965-1980)' => 0, 'Lainnya' => 0];
            foreach ($rawTglLahir as $item) {
                try {
                    $year = Carbon::parse($item->tgl_lahir)->year;
                    if ($year >= 1997 && $year <= 2012) $statsGenerasi['Gen Z (1997-2012)']++;
                    elseif ($year >= 1981 && $year <= 1996) $statsGenerasi['Gen Y (1981-1996)']++;
                    elseif ($year >= 1965 && $year <= 1980) $statsGenerasi['Gen X (1965-1980)']++;
                    else $statsGenerasi['Lainnya']++;
                } catch (\Exception $e) {}
            }
            $chartGenerasi = ['labels' => array_keys($statsGenerasi), 'series' => array_values($statsGenerasi)];

            $rawKdHukum = (clone $query)->whereIn('sts_peg', ['PNS', 'CPNS'])->select('kedudukan_hukum', DB::raw('count(*) as total'))->groupBy('kedudukan_hukum')->pluck('total', 'kedudukan_hukum');
            $dataKedudukanHukum = collect();
            foreach ($rawKdHukum as $k => $v) {
                $key = empty($k) ? 'Tidak Terdaftar' : $k;
                $dataKedudukanHukum[$key] = ($dataKedudukanHukum[$key] ?? 0) + $v;
            }
            $chartKedudukanHukum = ['labels' => $dataKedudukanHukum->keys()->toArray(), 'series' => $dataKedudukanHukum->values()->toArray()];

            $jjCategories = ['Struktural' => 0, 'Fungsional' => 0, 'Pelaksana' => 0];
            $rawJJ = (clone $query)->select('jenis_jabatan', DB::raw('count(*) as total'))->whereNotNull('jenis_jabatan')->groupBy('jenis_jabatan')->pluck('total', 'jenis_jabatan');
            foreach ($rawJJ as $k => $count) {
                $nama = strtolower($k);
                if (str_contains($nama, 'struktural')) $jjCategories['Struktural'] += $count;
                elseif (str_contains($nama, 'fungsional')) $jjCategories['Fungsional'] += $count;
                else $jjCategories['Pelaksana'] += $count;
            }
            $jjCategories = array_filter($jjCategories, fn($v) => $v > 0);
            $chartJenisJabatan = ['labels' => array_keys($jjCategories), 'series' => array_values($jjCategories)];

            $pegawaiQuery = (clone $query);
            if ($request->has('search') && !empty($request->search)) {
                $pegawaiQuery->where('nama_pegawai', 'like', '%' . $request->search . '%');
            }
            $paginator = $pegawaiQuery->orderBy('nama_pegawai')->paginate(10)->withQueryString();
            
            $paginator->getCollection()->transform(function($item) {
                return (object) [
                    'nip_baru' => $item->nip_baru,
                    'nama_lengkap' => $item->nama_pegawai,
                    'unor' => (object) ['nama' => $item->unor_nama],
                    'jabatan' => (object) ['nama' => $item->jabatan],
                    'gol_akhir' => $item->golongan,
                    'status_cpns_pns' => $item->status_cpns_pns,
                ];
            });
            $pegawai = $paginator;
            
            $lastSyncRaw = HistoryPegawai::where(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"), $filterSnapshot)->max('created_at');
            $lastSync = $lastSyncRaw ? Carbon::parse($lastSyncRaw)->format('d M Y H:i') . ' (Snapshot)' : '-';

        } else {
            $query = Pegawai::aktif()->with([
                'golongan', 'jabatan', 'tingkatPendidikan', 'unor', 'jenisPegawai', 'instansiKerja'
            ]);

            if ($filterOpd) {
                $query->whereHas('unor', function ($q) use ($filterOpd) {
                    $q->where('nama', $filterOpd);
                });
            }

            $totalPegawai = (clone $query)->count();
            $totalLaki = (clone $query)->where('jenis_kelamin', 'M')->count();
            $totalPerempuan = (clone $query)->where('jenis_kelamin', 'F')->count();

            $totalPns = (clone $query)->where('status_cpns_pns', 'P')
                ->where(function ($q) { $q->whereIn('kedudukan_hukum_id', ['01', '02', '03', '04', '15'])->orWhereNull('kedudukan_hukum_id'); })->count();
            $totalCpns = (clone $query)->where('status_cpns_pns', 'C')
                ->where(function ($q) { $q->whereIn('kedudukan_hukum_id', ['01', '02', '03', '04', '15'])->orWhereNull('kedudukan_hukum_id'); })->count();
            $totalPppk = (clone $query)->whereIn('kedudukan_hukum_id', ['71', '73'])->count();
            $totalPppkPw = (clone $query)->where('kedudukan_hukum_id', '101')->count();

            $statsJenikel = ['Laki-laki' => $totalLaki, 'Perempuan' => $totalPerempuan];
            $chartJenikel = ['labels' => array_keys($statsJenikel), 'series' => array_values($statsJenikel)];

            $statsStsPeg = ['PNS' => $totalPns, 'CPNS' => $totalCpns, 'PPPK' => $totalPppk, 'PPPK PW' => $totalPppkPw];
            $chartStsPeg = ['labels' => array_keys($statsStsPeg), 'series' => array_values($statsStsPeg)];

            $dataPendidikan = (clone $query)->whereHas('tingkatPendidikan')->with('tingkatPendidikan')->get()
                ->groupBy(function ($item) { return $item->tingkatPendidikan->nama ?? 'Tidak Diketahui'; })
                ->map(function ($group) { return $group->count(); })->sortDesc();
            $chartPendidikan = ['categories' => $dataPendidikan->keys()->toArray(), 'series' => $dataPendidikan->values()->toArray()];

            $dataEselon = (clone $query)->whereHas('jenisJabatan')->with('jenisJabatan')->get()
                ->groupBy(function ($item) { return $item->jenisJabatan->nama ?? 'Tidak Diketahui'; })
                ->map(function ($group) { return $group->count(); })->sortKeys();
            $chartEselon = ['categories' => $dataEselon->keys()->toArray(), 'series' => $dataEselon->values()->toArray()];

            $dataOpd = (clone $query)->whereHas('unor')->with('unor')->get()
                ->groupBy(function ($item) { return $item->unor->nama ?? 'Tidak Diketahui'; })
                ->map(function ($group) { return $group->count(); })->sortDesc()->take(10);
            $chartOpd = ['categories' => $dataOpd->keys()->toArray(), 'series' => $dataOpd->values()->toArray()];

            $dataGolongan = (clone $query)->with(['golongan', 'kedudukanHukum'])->get()
                ->groupBy(function ($item) {
                    $namaGolongan = $item->golongan_pppk;
                    if (in_array($item->golongan_id, ['19.8', '21.9'])) { $namaGolongan = 'III/a'; }
                    return empty($namaGolongan) ? 'Tidak Diketahui' : $namaGolongan;
                })->map(function ($group) { return $group->count(); });
            $dataGolongan = $dataGolongan->sortBy(function ($count, $key) {
                if ($key === 'Tidak Diketahui') return 999;
                return \App\Helpers\GolonganHelper::parseRoman($key);
            });
            $chartGolongan = ['categories' => $dataGolongan->keys()->toArray(), 'series' => $dataGolongan->values()->toArray()];

            $rawTglLahir = (clone $query)->select('tanggal_lahir')->whereNotNull('tanggal_lahir')->get();
            $statsGenerasi = ['Gen Z (1997-2012)' => 0, 'Gen Y (1981-1996)' => 0, 'Gen X (1965-1980)' => 0, 'Lainnya' => 0];
            foreach ($rawTglLahir as $item) {
                if (!$item->tanggal_lahir) continue;
                try {
                    $year = Carbon::parse($item->tanggal_lahir)->year;
                    if ($year >= 1997 && $year <= 2012) $statsGenerasi['Gen Z (1997-2012)']++;
                    elseif ($year >= 1981 && $year <= 1996) $statsGenerasi['Gen Y (1981-1996)']++;
                    elseif ($year >= 1965 && $year <= 1980) $statsGenerasi['Gen X (1965-1980)']++;
                    else $statsGenerasi['Lainnya']++;
                } catch (\Exception $e) {}
            }
            $chartGenerasi = ['labels' => array_keys($statsGenerasi), 'series' => array_values($statsGenerasi)];

            $kdLabels = ['01' => 'Aktif', '02' => 'CLTN', '03' => 'Tugas Belajar', '04' => 'Pemberhentian Sementara', '15' => 'Hukuman Disiplin'];
            $dataKedudukanHukum = collect();
            foreach ($kdLabels as $id => $label) {
                $count = (clone $query)->whereIn('status_cpns_pns', ['P', 'C'])->where('kedudukan_hukum_id', $id)->count();
                if ($count > 0) $dataKedudukanHukum->put($label, $count);
            }
            $countBlank = (clone $query)->whereIn('status_cpns_pns', ['P', 'C'])->whereNull('kedudukan_hukum_id')->count();
            if ($countBlank > 0) $dataKedudukanHukum->put('Tidak Terdaftar', $countBlank);
            $chartKedudukanHukum = ['labels' => $dataKedudukanHukum->keys()->toArray(), 'series' => $dataKedudukanHukum->values()->toArray()];

            $allJenisJabatan = \App\Models\RefJenisJabatan::all()->keyBy('id');
            $jjCategories = ['Struktural' => 0, 'Fungsional' => 0, 'Pelaksana' => 0];
            $jenisJabatanCounts = (clone $query)->whereNotNull('jenis_jabatan_id')->selectRaw('jenis_jabatan_id, COUNT(*) as total')
                ->groupBy('jenis_jabatan_id')->pluck('total', 'jenis_jabatan_id');
            foreach ($jenisJabatanCounts as $jjId => $count) {
                $nama = strtolower($allJenisJabatan[$jjId]->nama ?? '');
                if (str_contains($nama, 'struktural')) $jjCategories['Struktural'] += $count;
                elseif (str_contains($nama, 'fungsional')) $jjCategories['Fungsional'] += $count;
                else $jjCategories['Pelaksana'] += $count;
            }
            $jjCategories = array_filter($jjCategories, fn($v) => $v > 0);
            $chartJenisJabatan = ['labels' => array_keys($jjCategories), 'series' => array_values($jjCategories)];

            $pegawaiQuery = (clone $query)->select('pegawai.*');
            if ($request->has('search') && !empty($request->search)) {
                $pegawaiQuery->where('nama', 'like', '%' . $request->search . '%');
            }
            $pegawai = $pegawaiQuery->orderBy('nama')->paginate(10)->withQueryString();

            $lastSyncRaw = Pegawai::max('updated_at');
            $lastSync = $lastSyncRaw ? Carbon::parse($lastSyncRaw)->format('d M Y H:i') : '-';
        }

        if ($request->ajax()) {
            return view('partials.employee-table', compact('pegawai'));
        }

        return view('dashboard', compact(
            'listOpd',
            'filterOpd',
            'filterSnapshot',
            'historyMonths',
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
            'chartGolongan',
            'chartOpd',
            'chartGenerasi',
            'chartKedudukanHukum',
            'chartJenisJabatan',
            'pegawai',
            'lastSync'
        ));
    }
}
