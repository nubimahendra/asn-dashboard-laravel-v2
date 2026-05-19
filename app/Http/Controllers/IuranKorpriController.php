<?php

namespace App\Http\Controllers;

use App\Models\IuranKorpri;
use App\Models\Pegawai;
use App\Models\RefUnor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\IuranKorpriGeneratorService;

class IuranKorpriController extends Controller
{
    public function generateIuran(Request $request)
    {
        $bulan = $request->bulan ?? date('n');
        $tahun = $request->tahun ?? date('Y');

        app(IuranKorpriGeneratorService::class)->generate($bulan, $tahun);

        return back()->with('success', "Iuran bulan $bulan tahun $tahun berhasil digenerate.");
    }
    
    private function extractGolonganKey(?string $namaGolongan): ?string
    {
        if (empty($namaGolongan)) {
            return null;
        }
        return trim($namaGolongan);
    }

    public function index(Request $request)
    {
        $filterOpd = $request->input('opd');
        $pns = $request->has('pns') ? $request->input('pns') : 1;
        $pppk = $request->has('pppk') ? $request->input('pppk') : 1;
        
        $bulan = $request->input('bulan', date('n'));
        $tahun = $request->input('tahun', date('Y'));
        $hitungUlang = $request->has('hitung_ulang');

        $ratesSorted = IuranKorpri::all()->sortBy(function ($rate) {
            $romanOrder = ['I' => 1, 'II' => 2, 'III' => 3, 'IV' => 4, 'V' => 5, 'VI' => 6, 'VII' => 7, 'VIII' => 8, 'IX' => 9, 'X' => 10, 'XI' => 11, 'XII' => 12];
            $parts = explode('/', $rate->golongan_key);
            $base = trim($parts[0]);
            $baseValue = $romanOrder[$base] ?? 99;
            $subValue = isset($parts[1]) ? ord(strtolower($parts[1])) / 1000 : 0;
            return $baseValue + $subValue;
        });

        $allIuranRates = $ratesSorted->keyBy('golongan_key');
        
        $pnsGolKeys = ['I/a','I/b','I/c','I/d','II/a','II/b','II/c','II/d','II/e','III/a','III/b','III/c','III/d','III/e','IV/a','IV/b','IV/c','IV/d','IV/e'];
        $pppkGolKeys = ['I','V','VII','IX','X','XI'];
        
        if ($pns && !$pppk) {
            $filteredRates = $allIuranRates->only($pnsGolKeys);
        } elseif (!$pns && $pppk) {
            $filteredRates = $allIuranRates->only($pppkGolKeys);
        } elseif ($pns && $pppk) {
            $filteredRates = $allIuranRates;
        } else {
            $filteredRates = collect();
        }
        $allIuranRates = $filteredRates;

        $listOpd = RefUnor::whereNotNull('nama')->where('nama', '!=', '')->select('nama')->distinct()->orderBy('nama')->pluck('nama');

        $isArsip = false;
        $arsipDate = null;
        $arsipCreator = null;

        $arsipList = \App\Models\RekapIuranBulanan::where('bulan', $bulan)->where('tahun', $tahun)->get();

        if (!$hitungUlang && $arsipList->count() > 0) {
            $isArsip = true;
            $arsipDate = $arsipList->first()->created_at;
            $arsipCreator = $arsipList->first()->created_by;
            
            $calcData = $this->formatArsipData($arsipList, $allIuranRates, $filterOpd);
            $opdBreakdown = $calcData['opdBreakdown'];
            $globalTotals = $calcData['globalTotals'];
        } else {
            $calcData = $this->calculateRealtime($allIuranRates, $pns, $pppk, $filterOpd);
            $opdBreakdown = $calcData['opdBreakdown'];
            $globalTotals = $calcData['globalTotals'];
        }

        $page = $request->input('page', 1);
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $opdBreakdownCollection = collect($opdBreakdown);
        $paginatedItems = $opdBreakdownCollection->slice($offset, $perPage);

        $opdBreakdown = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedItems, $opdBreakdownCollection->count(), $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.iuran-korpri.index', compact(
            'allIuranRates', 'listOpd', 'filterOpd',
            'globalTotals', 'opdBreakdown', 'pns', 'pppk', 'bulan', 'tahun', 'isArsip', 'arsipDate', 'arsipCreator'
        ));
    }

    public function pengaturanTarifGolongan(Request $request)
    {
        $ratesSorted = IuranKorpri::all()->sortBy(function ($rate) {
            $romanOrder = ['I' => 1, 'II' => 2, 'III' => 3, 'IV' => 4, 'V' => 5, 'VI' => 6, 'VII' => 7, 'VIII' => 8, 'IX' => 9, 'X' => 10, 'XI' => 11, 'XII' => 12];
            $parts = explode('/', $rate->golongan_key);
            $base = trim($parts[0]);
            $baseValue = $romanOrder[$base] ?? 99;
            $subValue = isset($parts[1]) ? ord(strtolower($parts[1])) / 1000 : 0;
            return $baseValue + $subValue;
        });

        // We need global totals just for the count per golongan to show in the table
        $allIuranRates = collect(); // Calculate empty for speed, or we can just run the full calc
        
        $tarifPage = $request->input('page', 1);
        $tarifPerPage = 10;
        $tarifOffset = ($tarifPage - 1) * $tarifPerPage;
        $ratesPaginatedItems = $ratesSorted->slice($tarifOffset, $tarifPerPage);

        $iuranRatesPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $ratesPaginatedItems, $ratesSorted->count(), $tarifPerPage, $tarifPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Call calculateRealtime just to get the current counts for the table
        // For settings page, default to all OPDs, both PNS and PPPK
        $calcData = $this->calculateRealtime($ratesSorted->keyBy('golongan_key'), 1, 1, null);
        $globalTotals = $calcData['globalTotals'];

        return view('admin.pengaturan-tarif.iuran-golongan', compact(
            'iuranRatesPaginated', 'globalTotals'
        ));
    }

    private function calculateRealtime($allIuranRates, $pns, $pppk, $filterOpd = null)
    {
        $allEselonRates = \App\Models\RefIuranEselon::all()->keyBy('eselon_key');
        $eselonMappings = \App\Models\RefEselonMapping::pluck('eselon_key', 'jabatan_id');

        $query = Pegawai::aktif()->with(['golongan', 'unor', 'iuranOverride']);

        if ($pns && !$pppk) {
            $query->whereIn('kedudukan_hukum_id', ['01','02','03','04','15'])->whereIn('status_cpns_pns', ['P','C']);
        } elseif (!$pns && $pppk) {
            $query->whereIn('kedudukan_hukum_id', ['71','73']);
        } elseif (!$pns && !$pppk) {
            $query->where('id', '<', 0);
        } else {
            $query->where(function($q) {
                $q->where('kedudukan_hukum_id', '!=', '101')->orWhereNull('kedudukan_hukum_id');
            });
        }

        if ($filterOpd) {
            $query->whereHas('unor', function ($q) use ($filterOpd) {
                $q->where('nama', $filterOpd);
            });
        }

        $pegawaiData = $query->get();

        $opdBreakdown = [];
        $globalTotals = [
            'total_pegawai' => 0, 'total_ber_golongan' => 0, 'total_non_golongan' => 0,
            'total_struktural' => 0, 'total_iuran' => 0, 'per_golongan' => [],
        ];

        foreach ($allIuranRates as $key => $rate) {
            $globalTotals['per_golongan'][$key] = [
                'label' => $rate->label, 'count' => 0, 'besaran' => $rate->besaran, 'subtotal' => 0,
            ];
        }

        foreach ($pegawaiData as $pegawai) {
            $opdName = $pegawai->unor->nama ?? 'Tanpa OPD';
            $isStruktural = $pegawai->jenis_jabatan_id == 1;
            $override = $pegawai->iuranOverride;

            if (!isset($opdBreakdown[$opdName])) {
                $opdBreakdown[$opdName] = [
                    'nama_opd' => $opdName, 'total_pegawai' => 0, 'total_ber_golongan' => 0,
                    'total_non_golongan' => 0, 'total_struktural' => 0, 'total_iuran' => 0, 'per_golongan' => [],
                ];
                foreach ($allIuranRates as $key => $rate) {
                    $opdBreakdown[$opdName]['per_golongan'][$key] = 0;
                }
            }

            $globalTotals['total_pegawai']++;
            $opdBreakdown[$opdName]['total_pegawai']++;

            if ($isStruktural && $pns) {
                $eselAsli = $eselonMappings[$pegawai->jabatan_id] ?? 'IV/b';
                $eselonKey = $override && $override->override_eselon_key ? $override->override_eselon_key : $eselAsli;
                $besaran = isset($allEselonRates[$eselonKey]) ? $allEselonRates[$eselonKey]->besaran : 0;
                
                $globalTotals['total_struktural']++;
                $globalTotals['total_iuran'] += $besaran;
                
                $opdBreakdown[$opdName]['total_struktural']++;
                $opdBreakdown[$opdName]['total_iuran'] += $besaran;
            } elseif (!$isStruktural || ($isStruktural && !$pns)) {
                $golonganNama = $pegawai->golongan_pppk;
                $golAsliKey = $this->extractGolonganKey($golonganNama);
                $golonganKey = $override && $override->override_golongan_key ? $override->override_golongan_key : $golAsliKey;
                
                if ($golonganKey && isset($allIuranRates[$golonganKey])) {
                    $besaran = $allIuranRates[$golonganKey]->besaran;
                    $globalTotals['total_ber_golongan']++;
                    $globalTotals['per_golongan'][$golonganKey]['count']++;
                    $globalTotals['per_golongan'][$golonganKey]['subtotal'] += $besaran;
                    $globalTotals['total_iuran'] += $besaran;

                    $opdBreakdown[$opdName]['total_ber_golongan']++;
                    $opdBreakdown[$opdName]['per_golongan'][$golonganKey] += $besaran;
                    $opdBreakdown[$opdName]['total_iuran'] += $besaran;
                } else {
                    $globalTotals['total_non_golongan']++;
                    $opdBreakdown[$opdName]['total_non_golongan']++;
                }
            }
        }

        ksort($opdBreakdown);
        return [
            'opdBreakdown' => array_values($opdBreakdown),
            'globalTotals' => $globalTotals
        ];
    }

    private function formatArsipData($arsipList, $allIuranRates, $filterOpd)
    {
        $opdBreakdown = [];
        $globalTotals = [
            'total_pegawai' => 0, 'total_ber_golongan' => 0, 'total_non_golongan' => 0,
            'total_struktural' => 0, 'total_iuran' => 0, 'per_golongan' => [],
        ];

        foreach ($allIuranRates as $key => $rate) {
            $globalTotals['per_golongan'][$key] = [
                'label' => $rate->label, 'count' => 0, 'besaran' => $rate->besaran, 'subtotal' => 0,
            ];
        }

        foreach ($arsipList as $arsip) {
            if ($filterOpd && $arsip->nama_opd !== $filterOpd) {
                continue;
            }

            $breakdown = is_array($arsip->breakdown_golongan) ? $arsip->breakdown_golongan : (json_decode($arsip->breakdown_golongan, true) ?: []);

            $opdArr = [
                'nama_opd' => $arsip->nama_opd,
                'total_pegawai' => $arsip->total_pegawai,
                'total_ber_golongan' => $arsip->total_non_struktural,
                'total_non_golongan' => 0,
                'total_struktural' => $arsip->total_struktural,
                'total_iuran' => $arsip->total_iuran,
                'per_golongan' => []
            ];

            $globalTotals['total_pegawai'] += $arsip->total_pegawai;
            $globalTotals['total_struktural'] += $arsip->total_struktural;
            $globalTotals['total_ber_golongan'] += $arsip->total_non_struktural;
            $globalTotals['total_iuran'] += $arsip->total_iuran;

            foreach ($allIuranRates as $key => $rate) {
                $subtotal = $breakdown[$key]['subtotal'] ?? 0;
                $count = $breakdown[$key]['count'] ?? 0;
                $opdArr['per_golongan'][$key] = $subtotal;

                $globalTotals['per_golongan'][$key]['count'] += $count;
                $globalTotals['per_golongan'][$key]['subtotal'] += $subtotal;
            }

            $opdBreakdown[$arsip->nama_opd] = $opdArr;
        }

        ksort($opdBreakdown);
        return [
            'opdBreakdown' => array_values($opdBreakdown),
            'globalTotals' => $globalTotals
        ];
    }

    public function simpanRekap(Request $request)
    {
        $bulan = $request->input('bulan', date('n'));
        $tahun = $request->input('tahun', date('Y'));

        $ratesSorted = IuranKorpri::all()->keyBy('golongan_key');
        
        $calcData = $this->calculateRealtime($ratesSorted, 1, 1);
        $opdBreakdown = $calcData['opdBreakdown'];

        DB::beginTransaction();
        try {
            foreach ($opdBreakdown as $opd) {
                // Format breakdown
                $breakdown = [];
                foreach ($ratesSorted as $key => $rate) {
                    $sub = $opd['per_golongan'][$key] ?? 0;
                    $count = $sub > 0 && $rate->besaran > 0 ? $sub / $rate->besaran : 0;
                    $breakdown[$key] = [
                        'count' => $count,
                        'besaran' => $rate->besaran,
                        'subtotal' => $sub
                    ];
                }

                \App\Models\RekapIuranBulanan::updateOrCreate(
                    [
                        'nama_opd' => $opd['nama_opd'],
                        'bulan' => $bulan,
                        'tahun' => $tahun
                    ],
                    [
                        'total_pegawai' => $opd['total_pegawai'],
                        'total_struktural' => $opd['total_struktural'],
                        'total_non_struktural' => $opd['total_ber_golongan'],
                        'total_iuran' => $opd['total_iuran'],
                        'breakdown_golongan' => $breakdown,
                        'created_by' => auth()->user()->name ?? 'Admin'
                    ]
                );
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => "Data iuran bulan $bulan/$tahun berhasil disimpan."]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan data: ' . $e->getMessage()], 500);
        }
    }

    public function updateBesaran(Request $request)
    {
        $request->validate([
            'rates' => 'required|array',
            'rates.*.id' => 'required|exists:iuran_korpri,id',
            'rates.*.besaran' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->rates as $rate) {
                IuranKorpri::where('id', $rate['id'])->update([
                    'besaran' => $rate['besaran'],
                ]);
            }
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Besaran iuran berhasil diperbarui']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui: ' . $e->getMessage()], 500);
        }
    }
}
