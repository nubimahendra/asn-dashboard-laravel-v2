<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\RefUnor;
use App\Models\IuranKorpri;
use App\Models\RefIuranEselon;
use App\Models\RefEselonMapping;
use Illuminate\Http\Request;

class RincianIuranController extends Controller
{
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
        $pns = $request->input('pns', 1);
        $pppk = $request->input('pppk', 1);

        $listOpd = RefUnor::whereNotNull('nama')
            ->where('nama', '!=', '')
            ->select('nama')
            ->distinct()
            ->orderBy('nama')
            ->pluck('nama');

        $allIuranRates = IuranKorpri::all()->sortBy(function ($rate) {
            $romanOrder = ['I' => 1, 'II' => 2, 'III' => 3, 'IV' => 4, 'V' => 5, 'VI' => 6, 'VII' => 7, 'VIII' => 8, 'IX' => 9, 'X' => 10, 'XI' => 11, 'XII' => 12];
            $parts = explode('/', $rate->golongan_key);
            $base = trim($parts[0]);
            $baseValue = $romanOrder[$base] ?? 99;
            $subValue = isset($parts[1]) ? ord(strtolower($parts[1])) / 1000 : 0;
            return $baseValue + $subValue;
        })->keyBy('golongan_key');

        $allEselonRates = RefIuranEselon::all()->sortBy(function ($rate) {
            $romanOrder = ['I' => 1, 'II' => 2, 'III' => 3, 'IV' => 4];
            $parts = explode('/', $rate->eselon_key);
            $base = trim($parts[0]);
            $baseValue = $romanOrder[$base] ?? 99;
            $subValue = isset($parts[1]) ? ord(strtolower($parts[1])) / 1000 : 0;
            return $baseValue + $subValue;
        })->keyBy('eselon_key');

        $eselonMappings = RefEselonMapping::pluck('eselon_key', 'jabatan_id');

        $query = Pegawai::aktif()->with(['golongan', 'unor']);

        if ($pns && !$pppk) {
            $query->whereIn('kedudukan_hukum_id', ['01','02','03','04','15'])
                  ->whereIn('status_cpns_pns', ['P','C']);
        } elseif (!$pns && $pppk) {
            $query->whereIn('kedudukan_hukum_id', ['71','73']);
        } elseif (!$pns && !$pppk) {
            $query->where('id', '<', 0);
        } else {
            $query->where(function($q) {
                $q->where('kedudukan_hukum_id', '!=', '101')
                  ->orWhereNull('kedudukan_hukum_id');
            });
        }

        if ($filterOpd) {
            $query->whereHas('unor', function ($q) use ($filterOpd) {
                $q->where('nama', $filterOpd);
            });
        }

        $pegawaiData = $query->get();

        $eselonBreakdown = [];
        foreach ($allEselonRates as $key => $rate) {
            $eselonBreakdown[$key] = ['label' => $rate->label, 'count' => 0, 'tarif' => $rate->besaran, 'subtotal' => 0];
        }

        $golonganBreakdown = [];
        foreach ($allIuranRates as $key => $rate) {
            $golonganBreakdown[$key] = ['label' => $rate->label, 'count' => 0, 'tarif' => $rate->besaran, 'subtotal' => 0];
        }

        $grandTotal = ['pegawai' => 0, 'iuran' => 0];

        foreach ($pegawaiData as $pegawai) {
            $isStruktural = $pegawai->jenis_jabatan_id == 1;

            if ($isStruktural) {
                $eselonKey = $eselonMappings[$pegawai->jabatan_id] ?? 'IV/b';
                if (isset($eselonBreakdown[$eselonKey])) {
                    $eselonBreakdown[$eselonKey]['count']++;
                    $eselonBreakdown[$eselonKey]['subtotal'] += $eselonBreakdown[$eselonKey]['tarif'];
                    $grandTotal['pegawai']++;
                    $grandTotal['iuran'] += $eselonBreakdown[$eselonKey]['tarif'];
                }
            } else {
                $golonganNama = $pegawai->golongan_pppk;
                $golonganKey = $this->extractGolonganKey($golonganNama);
                
                if ($golonganKey && isset($golonganBreakdown[$golonganKey])) {
                    $golonganBreakdown[$golonganKey]['count']++;
                    $golonganBreakdown[$golonganKey]['subtotal'] += $golonganBreakdown[$golonganKey]['tarif'];
                    $grandTotal['pegawai']++;
                    $grandTotal['iuran'] += $golonganBreakdown[$golonganKey]['tarif'];
                }
            }
        }

        return view('admin.rincian-iuran.index', compact(
            'listOpd', 'filterOpd', 'pns', 'pppk',
            'eselonBreakdown', 'golonganBreakdown', 'grandTotal'
        ));
    }
}
