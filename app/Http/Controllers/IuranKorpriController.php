<?php

namespace App\Http\Controllers;

use App\Models\IuranKorpri;
use App\Models\Pegawai;
use App\Models\RefUnor;
use App\Helpers\UptFilterHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IuranKorpriController extends Controller
{
    private function extractGolonganKey(?string $namaGolongan): ?string
    {
        if (empty($namaGolongan)) {
            return null;
        }
        
        $parts = explode('-', $namaGolongan);
        return trim($parts[0]);
    }

    public function index(Request $request)
    {
        $filterOpd = $request->input('opd');
        $filterUpt = $request->input('upt');
        $hasUptFilter = UptFilterHelper::hasUptFilter($filterOpd);
        $uptGroups = $hasUptFilter ? UptFilterHelper::getUptListGrouped($filterOpd) : [];
        
        $pns = $request->has('pns') ? $request->input('pns') : 1;
        $pppk = $request->has('pppk') ? $request->input('pppk') : 0;
        
        $bulan = $request->input('bulan', date('n'));
        $tahun = $request->input('tahun', date('Y'));
        $hitungUlang = $request->has('hitung_ulang');

        $ratesSorted = IuranKorpri::all()->sortBy(function ($rate) {
            return \App\Helpers\GolonganHelper::parseRoman($rate->golongan_key);
        });

        $allIuranRates = $ratesSorted->keyBy('golongan_key');
        
        $pnsGolKeys = ['I/a','I/b','I/c','I/d','II/a','II/b','II/c','II/d','II/e','III/a','III/b','III/c','III/d','III/e','IV/a','IV/b','IV/c','IV/d','IV/e'];
        $pppkGolKeys = ['I','V','VII','IX','X','XI'];
        
        if ($pns && !$pppk) {
            $filteredRates = $allIuranRates->whereIn('golongan_key', $pnsGolKeys);
        } elseif (!$pns && $pppk) {
            $filteredRates = $allIuranRates->whereIn('golongan_key', $pppkGolKeys);
        } elseif ($pns && $pppk) {
            $filteredRates = $allIuranRates;
        } else {
            $filteredRates = collect();
        }
        $allIuranRates = $filteredRates;

        $listOpd = RefUnor::whereNotNull('nama')
            ->where('nama', '!=', '')
            ->when(auth()->user()->hasPdScope(), function ($q) {
                $q->where('nama', auth()->user()->pd_scope);
            })
            ->select('nama')
            ->distinct()
            ->orderBy('nama')
            ->pluck('nama');

        $isArsip = false;
        $arsipDate = null;
        $arsipCreator = null;

        $arsipQuery = \App\Models\RekapIuranBulanan::where('bulan', $bulan)->where('tahun', $tahun);
        if (auth()->user()->hasPdScope()) {
            $arsipQuery->where('nama_opd', auth()->user()->pd_scope);
        }
        $arsipList = $arsipQuery->get();

        if (!$hitungUlang && $arsipList->count() > 0) {
            $isArsip = true;
            $arsipDate = $arsipList->first()->created_at;
            $arsipCreator = $arsipList->first()->created_by;
            
            $calcData = $this->formatArsipData($arsipList, $allIuranRates, $filterOpd);
            $opdBreakdown = $calcData['opdBreakdown'];
            $globalTotals = $calcData['globalTotals'];
            $subTotals = $calcData['subTotals'] ?? null;
        } else {
            $calcData = $this->calculateRealtime($allIuranRates, $pns, $pppk, $filterOpd, $filterUpt);
            $opdBreakdown = $calcData['opdBreakdown'];
            $globalTotals = $calcData['globalTotals'];
            $subTotals = $calcData['subTotals'] ?? null;
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
            'hasUptFilter', 'uptGroups', 'filterUpt',
            'globalTotals', 'subTotals', 'opdBreakdown', 'pns', 'pppk', 'bulan', 'tahun', 'isArsip', 'arsipDate', 'arsipCreator'
        ));
    }

    public function pengaturanTarifGolongan(Request $request)
    {
        $ratesSorted = IuranKorpri::all()->sortBy(function ($rate) {
            return \App\Helpers\GolonganHelper::parseRoman($rate->golongan_key);
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

    public function pengaturanInvoice()
    {
        $invoiceSettings = [
            'logo' => \App\Models\AppSetting::getValue('invoice_logo'),
            'bank_nama' => \App\Models\AppSetting::getValue('invoice_bank_nama'),
            'bank_rekening' => \App\Models\AppSetting::getValue('invoice_bank_rekening'),
            'bank_atas_nama' => \App\Models\AppSetting::getValue('invoice_bank_atas_nama'),
            'batas_setor' => \App\Models\AppSetting::getValue('invoice_batas_setor', '10'),
        ];

        return view('admin.pengaturan-tarif.invoice', compact('invoiceSettings'));
    }

    public function updatePengaturanInvoice(Request $request)
    {
        $request->validate([
            'invoice_logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'invoice_bank_nama' => 'nullable|string|max:100',
            'invoice_bank_rekening' => 'nullable|string|max:100',
            'invoice_bank_atas_nama' => 'nullable|string|max:100',
            'invoice_batas_setor' => 'nullable|integer|min:1|max:31',
        ]);

        if ($request->hasFile('invoice_logo')) {
            $logoPath = $request->file('invoice_logo')->store('logos', 'public');
            // Hapus logo lama jika ada
            $oldLogo = \App\Models\AppSetting::getValue('invoice_logo');
            if ($oldLogo && \Illuminate\Support\Facades\Storage::disk('public')->exists($oldLogo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($oldLogo);
            }
            \App\Models\AppSetting::setValue('invoice_logo', $logoPath);
        } elseif ($request->has('remove_logo') && $request->remove_logo == '1') {
            $oldLogo = \App\Models\AppSetting::getValue('invoice_logo');
            if ($oldLogo && \Illuminate\Support\Facades\Storage::disk('public')->exists($oldLogo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($oldLogo);
            }
            \App\Models\AppSetting::setValue('invoice_logo', null);
        }

        \App\Models\AppSetting::setValue('invoice_bank_nama', $request->input('invoice_bank_nama', ''));
        \App\Models\AppSetting::setValue('invoice_bank_rekening', $request->input('invoice_bank_rekening', ''));
        \App\Models\AppSetting::setValue('invoice_bank_atas_nama', $request->input('invoice_bank_atas_nama', ''));
        \App\Models\AppSetting::setValue('invoice_batas_setor', $request->input('invoice_batas_setor', '10'));

        return redirect()->back()->with('success', 'Pengaturan invoice berhasil diperbarui.');
    }

    private function calculateRealtime($allIuranRates, $pns, $pppk, $filterOpd = null, $filterUpt = null)
    {
        $allEselonRates = \App\Models\RefIuranEselon::all()->keyBy('eselon_key');
        $eselonMappings = \App\Models\RefEselonMapping::pluck('eselon_key', 'jabatan_id');

        $query = Pegawai::aktif()->authorizedPd(auth()->user())->with(['golongan', 'unor', 'iuranOverride']);

        if ($pns && !$pppk) {
            $query->whereIn('kedudukan_hukum_id', ['01','02','03','04','15'])->whereIn('status_cpns_pns', ['P','C']);
        } elseif (!$pns && $pppk) {
            $query->whereIn('kedudukan_hukum_id', ['71','73']);
        } elseif (!$pns && !$pppk) {
            $query->where('id', '<', 0);
        } else {
            $query->where(function($q) {
                $q->where(function($qPns) {
                    $qPns->whereIn('kedudukan_hukum_id', ['01','02','03','04','15'])
                         ->whereIn('status_cpns_pns', ['P','C']);
                })->orWhereIn('kedudukan_hukum_id', ['71','73']);
            });
        }

        if ($filterOpd) {
            $query->where(function($q) use ($filterOpd) {
                $q->whereHas('unor', function ($q2) use ($filterOpd) {
                    $q2->where('nama', $filterOpd);
                })->orWhereHas('iuranOverride', function ($q2) use ($filterOpd) {
                    $q2->where('override_opd_nama', $filterOpd);
                });
            });
        }

        if ($filterUpt && UptFilterHelper::hasUptFilter($filterOpd)) {
            $resolved = UptFilterHelper::resolveUptFilter($filterUpt);
            $query->whereHas('unor', function ($q) use ($resolved) {
                $q->where($resolved['column'], $resolved['operator'], $resolved['value']);
            });
        }

        $pegawaiData = $query->get();

        $opdBreakdown = [];
        $globalTotals = [
            'total_pegawai' => 0, 'total_ber_golongan' => 0, 'total_non_golongan' => 0,
            'total_struktural' => 0, 'total_iuran' => 0, 'per_golongan' => [],
        ];

        $subTotals = null;
        $hasUptFilterActive = $filterUpt && UptFilterHelper::hasUptFilter($filterOpd);
        if ($filterOpd && !$hasUptFilterActive && in_array($filterOpd, ['Dinas Pendidikan', 'Dinas Kesehatan'])) {
            $subTotals = [
                'Induk' => ['label' => $filterOpd . ' (Induk)', 'total_pegawai' => 0, 'total_iuran' => 0],
            ];
            
            if ($filterOpd === 'Dinas Pendidikan') {
                $subTotals['UPT SD'] = ['label' => 'Semua UPT SD', 'total_pegawai' => 0, 'total_iuran' => 0];
                $subTotals['UPT SMP'] = ['label' => 'Semua UPT SMP', 'total_pegawai' => 0, 'total_iuran' => 0];
            } elseif ($filterOpd === 'Dinas Kesehatan') {
                $subTotals['UPT Puskesmas'] = ['label' => 'Semua UPT Puskesmas', 'total_pegawai' => 0, 'total_iuran' => 0];
            }
        }

        foreach ($allIuranRates as $key => $rate) {
            $globalTotals['per_golongan'][$key] = [
                'label' => $rate->label, 'count' => 0, 'besaran' => $rate->besaran, 'subtotal' => 0,
            ];
        }

        foreach ($pegawaiData as $pegawai) {
            $override = $pegawai->iuranOverride;
            
            if ($override && !$override->is_active) {
                continue;
            }

            $opdName = ($override && $override->override_opd_nama) ? $override->override_opd_nama : ($pegawai->unor->nama ?? 'Tanpa OPD');

            if (!$filterOpd && in_array($opdName, ['Dinas Pendidikan', 'Dinas Kesehatan'])) {
                $checkName = $opdName;
                if (!$override && isset($pegawai->unor->nama_opd)) {
                    $checkName = $pegawai->unor->nama_opd;
                }
                
                if (str_starts_with($checkName, 'UPT SD')) {
                    $opdName = 'Grup UPT SD';
                } elseif (str_starts_with($checkName, 'UPT SMP')) {
                    $opdName = 'Grup UPT SMP';
                } elseif (str_starts_with($checkName, 'UPT Puskesmas')) {
                    $opdName = 'Grup UPT Puskesmas';
                }
            }

            if ($filterOpd) {
                if ($hasUptFilterActive) {
                    $opdName = $pegawai->unor->nama_opd ?? 'Tanpa UPT';
                } else if ($opdName !== $filterOpd) {
                    continue;
                }
            }

            $isStruktural = $pegawai->jenis_jabatan_id == 1;

            if (!isset($opdBreakdown[$opdName])) {
                $opdBreakdown[$opdName] = [
                    'nama_opd' => $opdName, 'total_pegawai' => 0, 'total_ber_golongan' => 0,
                    'total_non_golongan' => 0, 'total_struktural' => 0, 'total_iuran' => 0, 'per_golongan' => [],
                ];
                foreach ($allIuranRates as $key => $rate) {
                    $opdBreakdown[$opdName]['per_golongan'][$key] = ['count' => 0, 'subtotal' => 0];
                }
            }

            $globalTotals['total_pegawai']++;
            $opdBreakdown[$opdName]['total_pegawai']++;

            $hasEselonOverride = $override && $override->override_eselon_key;

            if ($hasEselonOverride || ($isStruktural && $pns)) {
                $eselAsli = $eselonMappings[$pegawai->jabatan_id] ?? 'IV/b';
                if (!isset($eselonMappings[$pegawai->jabatan_id]) && $isStruktural) {
                    \Illuminate\Support\Facades\Log::warning("Jabatan ID {$pegawai->jabatan_id} unmapped to eselon, defaulting to IV/b for Iuran Korpri");
                }
                $eselonKey = $hasEselonOverride ? $override->override_eselon_key : $eselAsli;
                $besaran = isset($allEselonRates[$eselonKey]) ? $allEselonRates[$eselonKey]->besaran : 0;
                
                $globalTotals['total_struktural']++;
                $globalTotals['total_iuran'] += $besaran;
                
                $opdBreakdown[$opdName]['total_struktural']++;
                $opdBreakdown[$opdName]['total_iuran'] += $besaran;
            } else {
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
                    $opdBreakdown[$opdName]['per_golongan'][$golonganKey]['count']++;
                    $opdBreakdown[$opdName]['per_golongan'][$golonganKey]['subtotal'] += $besaran;
                    $opdBreakdown[$opdName]['total_iuran'] += $besaran;
                } else {
                    $globalTotals['total_non_golongan']++;
                    $opdBreakdown[$opdName]['total_non_golongan']++;
                }
            }

            if ($subTotals !== null && $besaran > 0) {
                $checkName = ($override && $override->override_opd_nama) ? $override->override_opd_nama : ($pegawai->unor->nama_opd ?? $opdName);
                $subKey = 'Induk';
                
                if (str_starts_with($checkName, 'UPT SD')) {
                    $subKey = 'UPT SD';
                } elseif (str_starts_with($checkName, 'UPT SMP')) {
                    $subKey = 'UPT SMP';
                } elseif (str_starts_with($checkName, 'UPT Puskesmas')) {
                    $subKey = 'UPT Puskesmas';
                }
                
                if (isset($subTotals[$subKey])) {
                    $subTotals[$subKey]['total_pegawai']++;
                    $subTotals[$subKey]['total_iuran'] += $besaran;
                }
            }
        }

        ksort($opdBreakdown);
        return [
            'opdBreakdown' => array_values($opdBreakdown),
            'globalTotals' => $globalTotals,
            'subTotals' => $subTotals ? array_values($subTotals) : null
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

        $subTotals = null;
        if ($filterOpd && in_array($filterOpd, ['Dinas Pendidikan', 'Dinas Kesehatan'])) {
            $subTotals = [
                'Induk' => ['label' => $filterOpd . ' (Induk)', 'total_pegawai' => 0, 'total_iuran' => 0],
            ];
            if ($filterOpd === 'Dinas Pendidikan') {
                $subTotals['UPT SD'] = ['label' => 'Semua UPT SD', 'total_pegawai' => 0, 'total_iuran' => 0];
                $subTotals['UPT SMP'] = ['label' => 'Semua UPT SMP', 'total_pegawai' => 0, 'total_iuran' => 0];
            } elseif ($filterOpd === 'Dinas Kesehatan') {
                $subTotals['UPT Puskesmas'] = ['label' => 'Semua UPT Puskesmas', 'total_pegawai' => 0, 'total_iuran' => 0];
            }
        }

        foreach ($arsipList as $arsip) {
            if ($filterOpd) {
                if (in_array($filterOpd, ['Dinas Pendidikan', 'Dinas Kesehatan'])) {
                    $isUptOfFilter = false;
                    if ($filterOpd === 'Dinas Pendidikan') {
                        $isUptOfFilter = in_array($arsip->nama_opd, ['Grup UPT SD', 'Grup UPT SMP']);
                    } elseif ($filterOpd === 'Dinas Kesehatan') {
                        $isUptOfFilter = $arsip->nama_opd === 'Grup UPT Puskesmas';
                    }
                    
                    if ($arsip->nama_opd !== $filterOpd && !$isUptOfFilter) {
                        continue;
                    }
                } else {
                    if ($arsip->nama_opd !== $filterOpd) {
                        continue;
                    }
                }
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
                $opdArr['per_golongan'][$key] = ['count' => $count, 'subtotal' => $subtotal];

                $globalTotals['per_golongan'][$key]['count'] += $count;
                $globalTotals['per_golongan'][$key]['subtotal'] += $subtotal;
            }

            $opdBreakdown[$arsip->nama_opd] = $opdArr;

            if ($subTotals !== null) {
                $subKey = 'Induk';
                if ($arsip->nama_opd === 'Grup UPT SD') {
                    $subKey = 'UPT SD';
                } elseif ($arsip->nama_opd === 'Grup UPT SMP') {
                    $subKey = 'UPT SMP';
                } elseif ($arsip->nama_opd === 'Grup UPT Puskesmas') {
                    $subKey = 'UPT Puskesmas';
                }
                
                if (isset($subTotals[$subKey])) {
                    $subTotals[$subKey]['total_pegawai'] += $arsip->total_pegawai;
                    $subTotals[$subKey]['total_iuran'] += $arsip->total_iuran;
                }
            }
        }

        ksort($opdBreakdown);
        return [
            'opdBreakdown' => array_values($opdBreakdown),
            'globalTotals' => $globalTotals,
            'subTotals' => $subTotals ? array_values($subTotals) : null
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
                    $sub = $opd['per_golongan'][$key]['subtotal'] ?? 0;
                    $count = $opd['per_golongan'][$key]['count'] ?? 0;
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

    public function invoice(Request $request)
    {
        $bulan = $request->input('bulan', date('n'));
        $tahun = $request->input('tahun', date('Y'));
        $filterOpd = $request->input('opd');
        $pns = $request->has('pns') ? $request->input('pns') : 1;
        $pppk = $request->has('pppk') ? $request->input('pppk') : 0;

        $allEselonRates = \App\Models\RefIuranEselon::all()->keyBy('eselon_key');
        $eselonMappings = \App\Models\RefEselonMapping::pluck('eselon_key', 'jabatan_id');
        $allIuranRates = IuranKorpri::all()->keyBy('golongan_key');

        $query = Pegawai::aktif()->authorizedPd(auth()->user())->with(['golongan', 'unor', 'jabatan', 'jenisJabatan', 'iuranOverride']);

        if ($pns && !$pppk) {
            $query->whereIn('kedudukan_hukum_id', ['01','02','03','04','15'])->whereIn('status_cpns_pns', ['P','C']);
        } elseif (!$pns && $pppk) {
            $query->whereIn('kedudukan_hukum_id', ['71','73']);
        } elseif (!$pns && !$pppk) {
            $query->where('id', '<', 0);
        } else {
            $query->where(function($q) {
                $q->where(function($qPns) {
                    $qPns->whereIn('kedudukan_hukum_id', ['01','02','03','04','15'])
                         ->whereIn('status_cpns_pns', ['P','C']);
                })->orWhereIn('kedudukan_hukum_id', ['71','73']);
            });
        }

        if ($filterOpd) {
            $hasUptFilter = UptFilterHelper::hasUptFilter($filterOpd);
            $filterUpt = $request->input('upt');

            if (in_array($filterOpd, ['Grup UPT SD', 'Grup UPT SMP', 'Grup UPT Puskesmas'])) {
                $prefix = str_replace('Grup ', '', $filterOpd);
                $query->where(function($q) use ($prefix) {
                    $q->whereHas('unor', function ($q2) use ($prefix) {
                        $q2->where('nama_opd', 'LIKE', $prefix . '%');
                    })->orWhereHas('iuranOverride', function ($q2) use ($prefix) {
                        $q2->where('override_opd_nama', 'LIKE', $prefix . '%');
                    });
                });
            } elseif ($hasUptFilter && $filterUpt) {
                // If UPT filter is active, use the exact resolved UPT condition
                $resolved = UptFilterHelper::resolveUptFilter($filterUpt);
                $query->whereHas('unor', function ($q) use ($resolved) {
                    $q->where($resolved['column'], $resolved['operator'], $resolved['value']);
                });
            } else {
                // Original OPD filter logic
                $query->where(function($q) use ($filterOpd) {
                    $q->whereHas('unor', function ($q2) use ($filterOpd) {
                        $q2->where('nama', $filterOpd);
                    })->orWhereHas('iuranOverride', function ($q2) use ($filterOpd) {
                        $q2->where('override_opd_nama', $filterOpd);
                    });
                });
            }
        }

        $pegawaiData = $query->get();

        $invoiceData = [];
        $totalIuran = 0;
        $totalPegawai = 0;

        foreach ($pegawaiData as $pegawai) {
            $override = $pegawai->iuranOverride;

            if ($override && !$override->is_active) {
                continue;
            }

            $opdName = ($override && $override->override_opd_nama) ? $override->override_opd_nama : ($pegawai->unor->nama ?? 'Tanpa OPD');
            
            $hasUptFilter = $filterOpd ? UptFilterHelper::hasUptFilter($filterOpd) : false;
            $filterUpt = $request->input('upt');

            if (in_array($opdName, ['Dinas Pendidikan', 'Dinas Kesehatan'])) {
                $checkName = $opdName;
                if (!$override && isset($pegawai->unor->nama_opd)) {
                    $checkName = $pegawai->unor->nama_opd;
                }
                
                $isSubCat = false;
                if (str_starts_with($checkName, 'UPT SD')) {
                    $opdName = 'Semua UPT SD';
                    $isSubCat = true;
                } elseif (str_starts_with($checkName, 'UPT SMP')) {
                    $opdName = 'Semua UPT SMP';
                    $isSubCat = true;
                } elseif (str_starts_with($checkName, 'UPT Puskesmas')) {
                    $opdName = 'Semua UPT Puskesmas';
                    $isSubCat = true;
                }
                
                if (!$isSubCat) {
                    $opdName = $opdName . ' (Induk)';
                }
            }

            if ($filterOpd) {
                if ($hasUptFilter && $filterUpt) {
                    $opdName = $pegawai->unor->nama_opd ?? 'Tanpa UPT';
                } else if ($hasUptFilter && empty($filterUpt)) {
                    $validGroups = [$filterOpd . ' (Induk)', 'Semua UPT SD', 'Semua UPT SMP', 'Semua UPT Puskesmas'];
                    if (!in_array($opdName, $validGroups)) {
                        continue;
                    }
                } else if ($opdName !== $filterOpd) {
                    continue;
                }
            }

            $isStruktural = $pegawai->jenis_jabatan_id == 1;
            $besaran = 0;
            $dasarIuran = '-';
            $keyIuran = '-';

            $hasEselonOverride = $override && $override->override_eselon_key;

            if ($hasEselonOverride || ($isStruktural && $pns)) {
                $eselAsli = $eselonMappings[$pegawai->jabatan_id] ?? 'IV/b';
                if (!isset($eselonMappings[$pegawai->jabatan_id]) && $isStruktural) {
                    \Illuminate\Support\Facades\Log::warning("Jabatan ID {$pegawai->jabatan_id} unmapped to eselon, defaulting to IV/b for Iuran Korpri Invoice");
                }
                $eselonKey = $hasEselonOverride ? $override->override_eselon_key : $eselAsli;
                $besaran = isset($allEselonRates[$eselonKey]) ? $allEselonRates[$eselonKey]->besaran : 0;
                $dasarIuran = 'Eselon';
                $keyIuran = $eselonKey;
            } else {
                $golonganNama = $pegawai->golongan_pppk;
                $golAsliKey = $this->extractGolonganKey($golonganNama);
                $golonganKey = $override && $override->override_golongan_key ? $override->override_golongan_key : $golAsliKey;
                
                if ($golonganKey && isset($allIuranRates[$golonganKey])) {
                    $besaran = $allIuranRates[$golonganKey]->besaran;
                    $dasarIuran = 'Golongan';
                    $keyIuran = $golonganKey;
                }
            }

            if ($besaran > 0) {
                if (!isset($invoiceData[$opdName])) {
                    $invoiceData[$opdName] = [];
                }

                $invoiceData[$opdName][] = [
                    'nama' => $pegawai->nama,
                    'nip' => $pegawai->nip_baru,
                    'jabatan' => $pegawai->jabatan->nama ?? '-',
                    'dasar' => $dasarIuran,
                    'key' => $keyIuran,
                    'besaran' => $besaran,
                    'has_override' => $override ? true : false
                ];

                $totalIuran += $besaran;
                $totalPegawai++;
            }
        }

        // Sort outer and inner arrays
        ksort($invoiceData);
        foreach ($invoiceData as $opd => &$pegawais) {
            usort($pegawais, function($a, $b) {
                if ($a['besaran'] !== $b['besaran']) {
                    return $b['besaran'] <=> $a['besaran'];
                }
                return $a['nama'] <=> $b['nama'];
            });
        }

        $filterUpt = $request->input('upt');
        $invoiceTitle = $filterOpd ?: 'Seluruh PD';
        if ($filterOpd && UptFilterHelper::hasUptFilter($filterOpd) && $filterUpt) {
            $invoiceTitle = str_starts_with($filterUpt, '__cat:') ? substr($filterUpt, 6) : $filterUpt;
        }

        $invoiceSettings = [
            'logo' => \App\Models\AppSetting::getValue('invoice_logo'),
            'bank_nama' => \App\Models\AppSetting::getValue('invoice_bank_nama'),
            'bank_rekening' => \App\Models\AppSetting::getValue('invoice_bank_rekening'),
            'bank_atas_nama' => \App\Models\AppSetting::getValue('invoice_bank_atas_nama'),
            'batas_setor' => \App\Models\AppSetting::getValue('invoice_batas_setor', '10'),
        ];

        return view('admin.iuran-korpri.invoice', compact(
            'invoiceData', 'invoiceTitle', 'bulan', 'tahun', 'totalIuran', 'totalPegawai', 'invoiceSettings'
        ));
    }

    public function invoiceGolongan(Request $request)
    {
        $bulan = $request->input('bulan', date('n'));
        $tahun = $request->input('tahun', date('Y'));
        $filterOpd = $request->input('opd');
        $pns = $request->has('pns') ? $request->input('pns') : 1;
        $pppk = $request->has('pppk') ? $request->input('pppk') : 0;

        $allEselonRates = \App\Models\RefIuranEselon::all()->keyBy('eselon_key');
        $eselonMappings = \App\Models\RefEselonMapping::pluck('eselon_key', 'jabatan_id');
        $allIuranRates = IuranKorpri::all()->keyBy('golongan_key');

        $query = Pegawai::aktif()->authorizedPd(auth()->user())->with(['golongan', 'unor', 'jabatan', 'jenisJabatan', 'iuranOverride']);

        if ($pns && !$pppk) {
            $query->whereIn('kedudukan_hukum_id', ['01','02','03','04','15'])->whereIn('status_cpns_pns', ['P','C']);
        } elseif (!$pns && $pppk) {
            $query->whereIn('kedudukan_hukum_id', ['71','73']);
        } elseif (!$pns && !$pppk) {
            $query->where('id', '<', 0);
        } else {
            $query->where(function($q) {
                $q->where(function($qPns) {
                    $qPns->whereIn('kedudukan_hukum_id', ['01','02','03','04','15'])
                         ->whereIn('status_cpns_pns', ['P','C']);
                })->orWhereIn('kedudukan_hukum_id', ['71','73']);
            });
        }

        if ($filterOpd) {
            $hasUptFilter = UptFilterHelper::hasUptFilter($filterOpd);
            $filterUpt = $request->input('upt');

            if (in_array($filterOpd, ['Grup UPT SD', 'Grup UPT SMP', 'Grup UPT Puskesmas'])) {
                $prefix = str_replace('Grup ', '', $filterOpd);
                $query->where(function($q) use ($prefix) {
                    $q->whereHas('unor', function ($q2) use ($prefix) {
                        $q2->where('nama_opd', 'LIKE', $prefix . '%');
                    })->orWhereHas('iuranOverride', function ($q2) use ($prefix) {
                        $q2->where('override_opd_nama', 'LIKE', $prefix . '%');
                    });
                });
            } elseif ($hasUptFilter && $filterUpt) {
                $resolved = UptFilterHelper::resolveUptFilter($filterUpt);
                $query->whereHas('unor', function ($q) use ($resolved) {
                    $q->where($resolved['column'], $resolved['operator'], $resolved['value']);
                });
            } else {
                $query->where(function($q) use ($filterOpd) {
                    $q->whereHas('unor', function ($q2) use ($filterOpd) {
                        $q2->where('nama', $filterOpd);
                    })->orWhereHas('iuranOverride', function ($q2) use ($filterOpd) {
                        $q2->where('override_opd_nama', $filterOpd);
                    });
                });
            }
        }

        $pegawaiData = $query->get();

        $invoiceData = [];
        $totalIuran = 0;
        $totalPegawai = 0;

        foreach ($pegawaiData as $pegawai) {
            $override = $pegawai->iuranOverride;

            if ($override && !$override->is_active) {
                continue;
            }

            $opdName = ($override && $override->override_opd_nama) ? $override->override_opd_nama : ($pegawai->unor->nama ?? 'Tanpa PD');
            
            $hasUptFilter = $filterOpd ? UptFilterHelper::hasUptFilter($filterOpd) : false;
            $filterUpt = $request->input('upt');

            if (in_array($opdName, ['Dinas Pendidikan', 'Dinas Kesehatan'])) {
                $checkName = $opdName;
                if (!$override && isset($pegawai->unor->nama_opd)) {
                    $checkName = $pegawai->unor->nama_opd;
                }
                
                $isSubCat = false;
                if (str_starts_with($checkName, 'UPT SD')) {
                    $opdName = 'Semua UPT SD';
                    $isSubCat = true;
                } elseif (str_starts_with($checkName, 'UPT SMP')) {
                    $opdName = 'Semua UPT SMP';
                    $isSubCat = true;
                } elseif (str_starts_with($checkName, 'UPT Puskesmas')) {
                    $opdName = 'Semua UPT Puskesmas';
                    $isSubCat = true;
                }
                
                if (!$isSubCat) {
                    $opdName = $opdName . ' (Induk)';
                }
            }

            if ($filterOpd) {
                if ($hasUptFilter && $filterUpt) {
                    $opdName = $pegawai->unor->nama_opd ?? 'Tanpa UPT';
                } else if ($hasUptFilter && empty($filterUpt)) {
                    $validGroups = [$filterOpd . ' (Induk)', 'Semua UPT SD', 'Semua UPT SMP', 'Semua UPT Puskesmas'];
                    if (!in_array($opdName, $validGroups)) {
                        continue;
                    }
                } else if ($opdName !== $filterOpd) {
                    continue;
                }
            }

            if (!isset($invoiceData[$opdName])) {
                $invoiceData[$opdName] = [];
            }

            $isStruktural = $pegawai->jenis_jabatan_id == 1;

            $hasEselonOverride = $override && $override->override_eselon_key;

            if ($hasEselonOverride || ($isStruktural && $pns)) {
                $eselAsli = $eselonMappings[$pegawai->jabatan_id] ?? 'IV/b';
                $eselonKey = $hasEselonOverride ? $override->override_eselon_key : $eselAsli;
                $besaran = isset($allEselonRates[$eselonKey]) ? $allEselonRates[$eselonKey]->besaran : 0;
                
                if ($besaran > 0) {
                    $keyString = "eselon_{$eselonKey}";
                    if (!isset($invoiceData[$opdName][$keyString])) {
                        $invoiceData[$opdName][$keyString] = [
                            'key' => $eselonKey,
                            'dasar' => 'Eselon',
                            'jumlah_orang' => 0,
                            'besaran' => $besaran,
                            'subtotal' => 0,
                        ];
                    }
                    $invoiceData[$opdName][$keyString]['jumlah_orang']++;
                    $invoiceData[$opdName][$keyString]['subtotal'] += $besaran;
                    $totalIuran += $besaran;
                    $totalPegawai++;
                }
            } else {
                $golonganNama = $pegawai->golongan_pppk;
                $golAsliKey = $this->extractGolonganKey($golonganNama);
                $golonganKey = $override && $override->override_golongan_key ? $override->override_golongan_key : $golAsliKey;
                
                if ($golonganKey && isset($allIuranRates[$golonganKey])) {
                    $besaran = $allIuranRates[$golonganKey]->besaran;
                    if ($besaran > 0) {
                        $keyString = "golongan_{$golonganKey}";
                        if (!isset($invoiceData[$opdName][$keyString])) {
                            $invoiceData[$opdName][$keyString] = [
                                'key' => $golonganKey,
                                'dasar' => 'Golongan',
                                'jumlah_orang' => 0,
                                'besaran' => $besaran,
                                'subtotal' => 0,
                            ];
                        }
                        $invoiceData[$opdName][$keyString]['jumlah_orang']++;
                        $invoiceData[$opdName][$keyString]['subtotal'] += $besaran;
                        $totalIuran += $besaran;
                        $totalPegawai++;
                    }
                }
            }
        }

        // Sort data
        ksort($invoiceData);
        foreach ($invoiceData as $opd => &$rows) {
            usort($rows, function($a, $b) {
                if ($a['besaran'] !== $b['besaran']) {
                    return $b['besaran'] <=> $a['besaran'];
                }
                if ($a['dasar'] !== $b['dasar']) {
                    return $a['dasar'] === 'Eselon' ? -1 : 1;
                }
                return 0;
            });
        }

        $filterUpt = $request->input('upt');
        $invoiceTitle = $filterOpd ?: 'Seluruh PD';
        if ($filterOpd && UptFilterHelper::hasUptFilter($filterOpd) && $filterUpt) {
            $invoiceTitle = str_starts_with($filterUpt, '__cat:') ? substr($filterUpt, 6) : $filterUpt;
        }
        
        $invoiceSettings = [
            'logo' => \App\Models\AppSetting::getValue('invoice_logo'),
            'bank_nama' => \App\Models\AppSetting::getValue('invoice_bank_nama'),
            'bank_rekening' => \App\Models\AppSetting::getValue('invoice_bank_rekening'),
            'bank_atas_nama' => \App\Models\AppSetting::getValue('invoice_bank_atas_nama'),
            'batas_setor' => \App\Models\AppSetting::getValue('invoice_batas_setor', '10'),
        ];

        return view('admin.iuran-korpri.invoice-golongan', compact(
            'invoiceData', 'invoiceTitle', 'bulan', 'tahun', 'totalIuran', 'totalPegawai', 'invoiceSettings'
        ));
    }
}
