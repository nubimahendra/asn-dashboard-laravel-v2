<?php

namespace App\Http\Controllers;

use App\Models\SuratMasuk;
use App\Models\SnapshotPegawai;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SuratMasukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SuratMasuk::query();

        // Month and Year Filters
        if ($request->filled('month')) {
            $query->whereMonth('tanggal_terima', $request->month);
        }
        if ($request->filled('year')) {
            $query->whereYear('tanggal_terima', $request->year);
        } else {
            // Default to current year if only month is selected? 
            // Or let it be optional. Let's default year to current if month is present
            if ($request->filled('month')) {
                $query->whereYear('tanggal_terima', Carbon::now()->year);
            }
        }

        $suratMasuks = $query->latest('tanggal_terima')->paginate(10)->withQueryString();

        return view('admin.surat-masuk.index', compact('suratMasuks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get list of PD for autocomplete
        $listOpd = SnapshotPegawai::select('pd')
            ->whereNotNull('pd')
            ->distinct()
            ->orderBy('pd')
            ->pluck('pd');

        return view('admin.surat-masuk.create', compact('listOpd'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nomor_agenda' => 'required|string|max:255',
            'nomor_surat' => 'required|string|max:255',
            'pengirim' => 'required|string|max:255',
            'perihal' => 'required|string|max:255',
            'tanggal_terima' => 'required|date',
            'disposisi' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        SuratMasuk::create($request->all());

        return redirect()->route('surat-masuk.index')
            ->with('success', 'Surat masuk berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SuratMasuk $suratMasuk)
    {
        // Get list of PD for autocomplete
        $listOpd = SnapshotPegawai::select('pd')
            ->whereNotNull('pd')
            ->distinct()
            ->orderBy('pd')
            ->pluck('pd');

        return view('admin.surat-masuk.edit', compact('suratMasuk', 'listOpd'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SuratMasuk $suratMasuk)
    {
        $request->validate([
            'nomor_agenda' => 'required|string|max:255',
            'nomor_surat' => 'required|string|max:255',
            'pengirim' => 'required|string|max:255',
            'perihal' => 'required|string|max:255',
            'tanggal_terima' => 'required|date',
            'disposisi' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        $suratMasuk->update($request->all());

        return redirect()->route('surat-masuk.index')
            ->with('success', 'Surat masuk berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SuratMasuk $suratMasuk)
    {
        $suratMasuk->delete();

        return redirect()->route('surat-masuk.index')
            ->with('success', 'Surat masuk berhasil dihapus.');
    }

    /**
     * Print report based on filters.
     */
    public function print(Request $request)
    {
        $query = SuratMasuk::query();

        $month = $request->input('month');
        $year = $request->input('year', Carbon::now()->year);

        if ($month) {
            $query->whereMonth('tanggal_terima', $month);
            $query->whereYear('tanggal_terima', $year);
        } else {
            // If no filter, maybe default to current month? 
            // Or allow printing all? Let's assume printing requires at least a month or defaults to current month for safety/usability
            // User request says "Filter bulan", implies specific month.
            // If not provided, let's just default to current month to be safe or show all if specifically requested.
            // Let's check logic: "tampilkan tabel degan fungsi CRUD inbox tersebut" + "Tombol cetak dengan filter bulan".
            // I'll make it so it prints what's filtered.
        }

        // If filters are applied in index, we probably want to print that view.
        // Let's enforce month filter for print to make it a "Laporan Bulanan"
        if (!$month) {
            $month = Carbon::now()->month;
            $query->whereMonth('tanggal_terima', $month);
            $query->whereYear('tanggal_terima', $year);
        }

        $suratMasuks = $query->orderBy('tanggal_terima')->get();
        $monthName = Carbon::createFromDate(null, $month, null)->translatedFormat('F');

        return view('admin.surat-masuk.print', compact('suratMasuks', 'month', 'year', 'monthName'));
    }
}
