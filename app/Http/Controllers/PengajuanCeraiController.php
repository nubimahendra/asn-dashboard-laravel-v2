<?php

namespace App\Http\Controllers;

use App\Models\PengajuanCerai;
use App\Models\SnapshotPegawai;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PengajuanCeraiExport;

class PengajuanCeraiController extends Controller
{
    public function index(Request $request)
    {
        $query = PengajuanCerai::query();

        // Filter logic if needed later

        $data = $query->latest()->paginate(10);
        return view('admin.pengajuan-cerai.index', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required',
            'nama' => 'required',
            'jabatan' => 'required',
            'tanggal_surat' => 'required|date',
            'jenis_pengajuan' => 'required|in:Penggugat,Tergugat',
            'unit_kerja' => 'required',
            'opd' => 'required',
        ]);

        PengajuanCerai::create($request->all());

        return redirect()->route('admin.pengajuan-cerai.index')
            ->with('success', 'Data pengajuan cerai berhasil disimpan.');
    }

    public function destroy($id)
    {
        $item = PengajuanCerai::findOrFail($id);
        $item->delete();

        return redirect()->route('admin.pengajuan-cerai.index')
            ->with('success', 'Data berhasil dihapus.');
    }

    public function searchPegawai(Request $request)
    {
        $term = $request->term;
        $pegawai = SnapshotPegawai::where('nip_baru', 'like', "%{$term}%")
            ->orWhere('nama_pegawai', 'like', "%{$term}%")
            ->limit(10)
            ->get(['nip_baru as nip', 'nama_pegawai as nama', 'jabatan', 'sub_pd as unit_kerja', 'pd as opd']);

        return response()->json($pegawai);
    }

    public function print(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $query = PengajuanCerai::query();

        if ($start_date && $end_date) {
            $query->whereBetween('tanggal_surat', [$start_date, $end_date]);
        }

        $data = $query->orderBy('tanggal_surat', 'asc')->get();

        return view('admin.pengajuan-cerai.print', compact('data', 'start_date', 'end_date'));
    }

    public function exportExcel(Request $request)
    {
        // Implementation for Excel export will use a separate Export class or inline download
        // For simplicity and speed, we will use a collection export here or just assume the Export class exists.
        // But since I need to create the Export class, I will defer this slightly or make a simple one.
        // Let's defer functionality if Export class is needed, but user just wants the button functionality.
        // I will implement a simple CSV/Excel download without a separate class for now to save tokens/files if possible,
        // or create the Export class in the next step. 
        // Actually, creating a new Export class is better practice. I'll add that to the plan/steps.

        return Excel::download(new \App\Exports\PengajuanCeraiExport($request->start_date, $request->end_date), 'laporan-pengajuan-cerai.xlsx');
    }
}
