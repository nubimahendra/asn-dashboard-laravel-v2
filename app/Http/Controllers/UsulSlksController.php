<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StgPegawaiImport;
use App\Models\Pegawai;
use App\Models\UsulSlks;
use Illuminate\Support\Facades\DB;

class UsulSlksController extends Controller
{
    public function index()
    {
        return view('siput.usul-slks');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip' => 'required|string|max:20',
            'nama' => 'required|string|max:255',
            'pangkat' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'no_sk_hukdis' => 'nullable|string|max:255',
            'tmt_hukdis' => 'nullable|date',
            'no_sk_cltn' => 'nullable|string|max:255',
            'tmt_cltn' => 'nullable|date',
            'kabkota' => 'nullable|string|max:255',
            'provinsi' => 'nullable|string|max:255',
            'kd_wil' => 'nullable|string|max:255',
            'slks_ada' => 'nullable|string|max:255',
            'no_slks' => 'nullable|string|max:255',
            'tgl_slks' => 'nullable|date',
            'usul_slks' => 'nullable|string|max:255',
            'bulanp' => 'nullable|string|max:255',
            'tahunp' => 'nullable|string|max:255',
            'ms_tms' => 'nullable|string|max:255',
            'ket_tms' => 'nullable|string',
        ]);
        
        DB::beginTransaction();
        try {
            $data = array_merge($validated, [
                'status' => 'draft_usulan',
                'created_by' => auth()->id()
            ]);
            
            UsulSlks::create($data);
            
            DB::commit();
            return redirect()->back()->with('success', 'Usulan SLKS berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }
    
    public function searchPegawai(Request $request)
    {
        $nip = $request->query('nip');
        $pegawai = StgPegawaiImport::where('nip_baru', $nip)
            ->whereIn('kedudukan_hukum_id', Pegawai::ACTIVE_KEDUDUKAN_HUKUM)
            ->first();

        if (!$pegawai) {
            return response()->json(['found' => false]);
        }

        // Cari riwayat SLKS yang sudah pernah diusulkan untuk NIP ini
        $riwayat = UsulSlks::where('nip', $nip)
            ->orderBy('created_at', 'desc')
            ->get(['usul_slks', 'no_slks', 'tgl_slks', 'status']);

        return response()->json([
            'found' => true,
            'nama'  => $pegawai->nama,
            'pangkat' => $pegawai->gol_akhir,
            'jabatan' => $pegawai->jabatan,
            'mk_tahun' => $pegawai->mk_tahun,
            'mk_bulan' => $pegawai->mk_bulan,
            'riwayat' => $riwayat,
        ]);
    }

    public function manage()
    {
        $data = UsulSlks::where('status', '!=', 'riwayat')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('siput.manage-usul-slks', compact('data'));
    }

    public function edit($id)
    {
        $usulSlks = UsulSlks::findOrFail($id);
        return view('siput.usul-slks', compact('usulSlks')); // form input dengan data pre-filled
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nip' => 'required|string|max:20',
            'nama' => 'required|string|max:255',
            'pangkat' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'no_sk_hukdis' => 'nullable|string|max:255',
            'tmt_hukdis' => 'nullable|date',
            'no_sk_cltn' => 'nullable|string|max:255',
            'tmt_cltn' => 'nullable|date',
            'kabkota' => 'nullable|string|max:255',
            'provinsi' => 'nullable|string|max:255',
            'kd_wil' => 'nullable|string|max:255',
            'slks_ada' => 'nullable|string|max:255',
            'no_slks' => 'nullable|string|max:255',
            'tgl_slks' => 'nullable|date',
            'usul_slks' => 'nullable|string|max:255',
            'bulanp' => 'nullable|string|max:255',
            'tahunp' => 'nullable|string|max:255',
            'ms_tms' => 'nullable|string|max:255',
            'ket_tms' => 'nullable|string',
        ]);
        
        $usulSlks = UsulSlks::findOrFail($id);
        
        try {
            $usulSlks->update(array_merge($validated, ['updated_by' => auth()->id()]));
            return redirect()->route('siput.usul-slks.manage')->with('success', 'Data usulan berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $usulSlks = UsulSlks::findOrFail($id);
            $usulSlks->delete();
            return redirect()->route('siput.usul-slks.manage')->with('success', 'Data usulan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function print(Request $request)
    {
        $data = UsulSlks::where('status', '!=', 'riwayat')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('siput.laporan-usul-slks', compact('data'));
    }
}
