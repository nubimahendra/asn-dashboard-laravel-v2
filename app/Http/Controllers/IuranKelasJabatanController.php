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

        $bagianList = [
            'Bagian umum',
            'Bagian Tata Pemerintahan',
            'Bagian Protokol dan Kepemimpinan',
            'Bagian Perencanaan dan Keuangan',
            'Bagian Hukum',
            'Bagian Kesejahteraan Rakyat',
            'Bagian Organisasi',
            'Bagian Pengadaan Barang dan Jasa',
            'Bagian Administrasi Pembangunan'
        ];

        // Get list of unique OPDs for filter dropdown
        $dbOpd = RefUnor::whereNotNull('nama')
            ->where('nama', '!=', '')
            ->select('nama')
            ->distinct()
            ->orderBy('nama')
            ->pluck('nama')->toArray();

        // Process listOpd to group selected units into Sekretariat Daerah
        $listOpdData = [];
        $hasBagian = false;
        foreach ($dbOpd as $opd) {
            if (in_array(trim($opd), $bagianList) || in_array(trim(ucwords(strtolower($opd))), array_map('ucwords', array_map('strtolower', $bagianList)))) {
                $hasBagian = true;
            } else {
                $listOpdData[] = $opd;
            }
        }
        if ($hasBagian && !in_array('Sekretariat Daerah', $listOpdData)) {
            $listOpdData[] = 'Sekretariat Daerah';
        }
        sort($listOpdData);
        $listOpd = collect($listOpdData);

        // Build query
        $query = IuranKorpriTransaksi::with(['pegawai.unor'])
            ->where('bulan', $bulan)
            ->where('tahun', $tahun);

        if ($filterOpd) {
            if ($filterOpd === 'Sekretariat Daerah') {
                $query->whereHas('pegawai.unor', function ($q) use ($bagianList) {
                    $q->whereIn('nama', $bagianList)
                        ->orWhere('nama', 'Sekretariat Daerah'); // just in case
                });
            } else {
                $query->whereHas('pegawai.unor', function ($q) use ($filterOpd) {
                    $q->where('nama', $filterOpd);
                });
            }
        }

        $transaksiData = $query->get();

        // Calculate iuran per OPD (similar to existing IuranKorpri report)
        $opdBreakdown = [];
        $globalTotals = [
            'total_pegawai' => 0,
            'total_iuran' => 0.0,
        ];

        foreach ($transaksiData as $transaksi) {
            $rawOpdName = $transaksi->pegawai->unor->nama ?? 'Tanpa OPD';

            // Check if OPD is one of the "Bagian" to be grouped into "Sekretariat Daerah"
            $opdNameSearch = trim(ucwords(strtolower($rawOpdName)));
            $bagianListSearch = array_map('ucwords', array_map('strtolower', $bagianList));

            if (in_array($rawOpdName, $bagianList) || in_array($opdNameSearch, $bagianListSearch)) {
                $opdName = 'Sekretariat Daerah';
            } else {
                $opdName = $rawOpdName;
            }

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
            return redirect()->route('mari.iuran-kelas-jabatan.index', ['bulan' => $bulan, 'tahun' => $tahun])
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

    public function opdDetail(Request $request)
    {
        $opd = $request->input('opd');
        $bulan = $request->input('bulan', date('n'));
        $tahun = $request->input('tahun', date('Y'));

        if (!$opd) {
            return redirect()->route('mari.iuran-kelas-jabatan.index')->with('error', 'OPD tidak ditemukan');
        }

        $bagianList = [
            'Bagian umum',
            'Bagian Tata Pemerintahan',
            'Bagian Protokol dan Kepemimpinan',
            'Bagian Perencanaan dan Keuangan',
            'Bagian Hukum',
            'Bagian Kesejahteraan Rakyat',
            'Bagian Organisasi',
            'Bagian Pengadaan Barang dan Jasa',
            'Bagian Administrasi Pembangunan'
        ];

        // Build query for specific OPD inside month and year
        $query = IuranKorpriTransaksi::with(['pegawai.unor'])
            ->where('bulan', $bulan)
            ->where('tahun', $tahun);

        if ($opd === 'Sekretariat Daerah') {
            $query->whereHas('pegawai.unor', function ($q) use ($bagianList) {
                $q->whereIn('nama', $bagianList)
                    ->orWhere('nama', 'Sekretariat Daerah');
            });
        } else {
            $query->whereHas('pegawai.unor', function ($q) use ($opd) {
                $q->where('nama', $opd);
            });
        }

        $transaksiData = $query->get();

        // Calculate iuran breakdown per kelas jabatan
        $breakdownKelas = [];
        $totalPegawai = 0;
        $totalIuran = 0;

        foreach ($transaksiData as $transaksi) {
            $kelas = $transaksi->kelas_jabatan ?? 'Tanpa Kelas';
            $nominal = (float) $transaksi->nominal;
            
            if (!isset($breakdownKelas[$kelas])) {
                $breakdownKelas[$kelas] = [
                    'kelas_jabatan' => $kelas,
                    'jumlah_pegawai' => 0,
                    'nominal_per_orang' => $nominal, // Assumes nominal is the same per kelas
                    'subtotal' => 0,
                ];
            }
            
            // Adjust to the max nominal if there are different nominals within the same class occasionally
            if ($nominal > $breakdownKelas[$kelas]['nominal_per_orang']) {
                $breakdownKelas[$kelas]['nominal_per_orang'] = $nominal;
            }

            $breakdownKelas[$kelas]['jumlah_pegawai']++;
            $breakdownKelas[$kelas]['subtotal'] += $nominal;
            
            $totalPegawai++;
            $totalIuran += $nominal;
        }

        // Sort array naturally by key (Class number)
        uksort($breakdownKelas, 'strnatcmp');

        return view('admin.iuran-kelas-jabatan.opd-detail', compact(
            'opd',
            'bulan',
            'tahun',
            'breakdownKelas',
            'totalPegawai',
            'totalIuran'
        ));
    }
}
