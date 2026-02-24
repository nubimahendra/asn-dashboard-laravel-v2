<?php

namespace App\Http\Controllers;

use App\Models\IuranKorpri;
use App\Models\Pegawai;
use App\Models\RefUnor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IuranKorpriController extends Controller
{
    /**
     * Extract golongan key (roman numeral before "/") from golongan name
     * e.g. "IV/d" => "IV", "II/b" => "II"
     */
    private function extractGolonganKey(?string $namaGolongan): ?string
    {
        if (empty($namaGolongan)) {
            return null;
        }

        return trim($namaGolongan);
    }

    /**
     * Show the iuran korpri report page
     */
    public function index(Request $request)
    {
        $filterOpd = $request->input('opd');

        // Get all iuran rates sorted by Roman Numeral logic
        $ratesSorted = IuranKorpri::all()->sortBy(function ($rate) {
            $romanOrder = [
                'I' => 1,
                'II' => 2,
                'III' => 3,
                'IV' => 4,
                'V' => 5,
                'VI' => 6,
                'VII' => 7,
                'VIII' => 8,
                'IX' => 9,
                'X' => 10,
                'XI' => 11,
                'XII' => 12
            ];
            $parts = explode('/', $rate->golongan_key);
            $base = trim($parts[0]);
            $baseValue = $romanOrder[$base] ?? 99;
            $subValue = 0;
            if (isset($parts[1])) {
                $subValue = ord(strtolower($parts[1])) / 1000;
            }
            return $baseValue + $subValue;
        });

        $allIuranRates = $ratesSorted->keyBy('golongan_key');

        // Get list of unique OPDs for filter dropdown
        $listOpd = RefUnor::whereNotNull('nama')
            ->where('nama', '!=', '')
            ->select('nama')
            ->distinct()
            ->orderBy('nama')
            ->pluck('nama');

        // Build pegawai query with relationships
        $query = Pegawai::with(['golongan', 'unor']);

        if ($filterOpd) {
            $query->whereHas('unor', function ($q) use ($filterOpd) {
                $q->where('nama', $filterOpd);
            });
        }

        $pegawaiData = $query->get();

        // Calculate iuran per OPD
        $opdBreakdown = [];
        $globalTotals = [
            'total_pegawai' => 0,
            'total_ber_golongan' => 0,
            'total_non_golongan' => 0,
            'total_iuran' => 0,
            'per_golongan' => [],
        ];

        // Initialize per_golongan counters
        foreach ($allIuranRates as $key => $rate) {
            $globalTotals['per_golongan'][$key] = [
                'label' => $rate->label,
                'count' => 0,
                'besaran' => $rate->besaran,
                'subtotal' => 0,
            ];
        }

        foreach ($pegawaiData as $pegawai) {
            $opdName = $pegawai->unor->nama ?? 'Tanpa OPD';
            $golonganNama = $pegawai->golongan->nama ?? null;

            // Konversi golongan untuk PPPK Aktif (ID 71)
            $isPppkAktif = false;
            if (isset($pegawai->kedudukan_hukum_id) && $pegawai->kedudukan_hukum_id == 71) {
                $isPppkAktif = true;
            } elseif (isset($pegawai->kedudukan_hukum->nama) && strtolower(trim($pegawai->kedudukan_hukum->nama)) == 'pppk aktif') {
                $isPppkAktif = true;
            }

            if ($isPppkAktif && !empty($golonganNama)) {
                $namaGolonganClean = trim($golonganNama);
                switch ($namaGolonganClean) {
                    case 'I':
                        $golonganNama = 'I';
                        break;
                    case 'V':
                        $golonganNama = 'V';
                        break;
                    case 'II/c':
                        $golonganNama = 'VII';
                        break;
                    case 'III/a':
                        $golonganNama = 'IX';
                        break;
                    case 'III/b':
                        $golonganNama = 'X';
                        break;
                    case 'III/c':
                        $golonganNama = 'XI';
                        break;
                }
            }

            $golonganKey = $this->extractGolonganKey($golonganNama);

            // Initialize OPD entry if not exists
            if (!isset($opdBreakdown[$opdName])) {
                $opdBreakdown[$opdName] = [
                    'nama_opd' => $opdName,
                    'total_pegawai' => 0,
                    'total_ber_golongan' => 0,
                    'total_non_golongan' => 0,
                    'total_iuran' => 0,
                    'per_golongan' => [],
                ];
                foreach ($allIuranRates as $key => $rate) {
                    $opdBreakdown[$opdName]['per_golongan'][$key] = 0;
                }
            }

            $globalTotals['total_pegawai']++;
            $opdBreakdown[$opdName]['total_pegawai']++;

            if ($golonganKey && isset($allIuranRates[$golonganKey])) {
                // Pegawai with matching golongan - count iuran
                $besaran = $allIuranRates[$golonganKey]->besaran;

                $globalTotals['total_ber_golongan']++;
                $globalTotals['per_golongan'][$golonganKey]['count']++;
                $globalTotals['per_golongan'][$golonganKey]['subtotal'] += $besaran;
                $globalTotals['total_iuran'] += $besaran;

                $opdBreakdown[$opdName]['total_ber_golongan']++;
                $opdBreakdown[$opdName]['per_golongan'][$golonganKey]++;
                $opdBreakdown[$opdName]['total_iuran'] += $besaran;
            } else {
                // Non-golongan (PPPK PW etc.) - count but no iuran
                $globalTotals['total_non_golongan']++;
                $opdBreakdown[$opdName]['total_non_golongan']++;
            }
        }

        // Sort OPD by name
        ksort($opdBreakdown);

        // Pagination Manual
        $page = $request->input('page', 1);
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $opdBreakdownCollection = collect($opdBreakdown);
        $paginatedItems = $opdBreakdownCollection->slice($offset, $perPage);

        $opdBreakdown = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedItems,
            $opdBreakdownCollection->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $tarifPage = $request->input('tarif_page', 1);
        $tarifPerPage = 5;
        $tarifOffset = ($tarifPage - 1) * $tarifPerPage;
        $ratesPaginatedItems = $ratesSorted->slice($tarifOffset, $tarifPerPage);

        $iuranRatesPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $ratesPaginatedItems,
            $ratesSorted->count(),
            $tarifPerPage,
            $tarifPage,
            ['path' => $request->url(), 'query' => $request->query(), 'pageName' => 'tarif_page']
        );

        return view('admin.iuran-korpri.index', compact(
            'allIuranRates',
            'iuranRatesPaginated',
            'listOpd',
            'filterOpd',
            'globalTotals',
            'opdBreakdown',
        ));
    }

    /**
     * Update iuran besaran via AJAX
     */
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
