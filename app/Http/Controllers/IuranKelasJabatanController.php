<?php

namespace App\Http\Controllers;

use App\Models\RefUnor;
use App\Models\IuranKorpriTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\IuranKorpriGeneratorService;

class IuranKelasJabatanController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->input('bulan', date('n'));
        $tahun = $request->input('tahun', date('Y'));

        $filterOpd = $request->input('opd');

        // Get list of unique OPDs for filter dropdown
        $listOpd = RefUnor::whereNotNull('nama')
            ->where('nama', '!=', '')
            ->select('nama')
            ->distinct()
            ->orderBy('nama')
            ->pluck('nama');

        // Build query
        $query = IuranKorpriTransaksi::with(['pegawai.unor'])
            ->where('bulan', $bulan)
            ->where('tahun', $tahun);

        if ($filterOpd) {
            $query->whereHas('pegawai.unor', function ($q) use ($filterOpd) {
                $q->where('nama', $filterOpd);
            });
        }

        $transaksiData = $query->get();

        // Calculate iuran per OPD (similar to existing IuranKorpri report)
        $opdBreakdown = [];
        $globalTotals = [
            'total_pegawai' => 0,
            'total_iuran' => 0.0,
        ];

        foreach ($transaksiData as $transaksi) {
            $opdName = $transaksi->pegawai->unor->nama ?? 'Tanpa OPD';

            if (!isset($opdBreakdown[$opdName])) {
                $opdBreakdown[$opdName] = [
                    'nama_opd' => $opdName,
                    'total_pegawai' => 0,
                    'total_iuran' => 0.0,
                ];
            }

            $globalTotals['total_pegawai']++;
            $globalTotals['total_iuran'] += (float) $transaksi->nominal;

            $opdBreakdown[$opdName]['total_pegawai']++;
            $opdBreakdown[$opdName]['total_iuran'] += (float) $transaksi->nominal;
        }

        // Sort OPD by name
        ksort($opdBreakdown);
        $opdBreakdown = array_values($opdBreakdown);

        // Pagination Manual
        $page = $request->input('page', 1);
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $opdBreakdownCollection = collect($opdBreakdown);
        $paginatedItems = $opdBreakdownCollection->slice($offset, $perPage);

        $opdBreakdownPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedItems,
            $opdBreakdownCollection->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.iuran-kelas-jabatan.index', compact(
            'listOpd',
            'filterOpd',
            'bulan',
            'tahun',
            'globalTotals',
            'opdBreakdownPaginated'
        ));
    }

    public function generate(Request $request)
    {
        $bulan = $request->bulan ?? date('n');
        $tahun = $request->tahun ?? date('Y');

        try {
            app(IuranKorpriGeneratorService::class)->generate($bulan, $tahun);
            return redirect()->route('iuran-kelas-jabatan.index', ['bulan' => $bulan, 'tahun' => $tahun])
                ->with('success', "Iuran Kelas Jabatan bulan $bulan tahun $tahun berhasil digenerate.");
        } catch (\TypeError $e) {
            \Log::error("TypeError in Generate Iuran Korpri: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->back()
                ->with('error', "Type Error generate Iuran: " . $e->getMessage());
        } catch (\Exception $e) {
            \Log::error("Error in Generate Iuran Korpri: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->back()
                ->with('error', "Gagal generate Iuran: " . $e->getMessage());
        }
    }
}
