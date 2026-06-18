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

    private function getPangkatFromGolongan($golongan)
    {
        if (!$golongan) return null;
        
        $map = [
            'I/a'  => 'Juru Muda',
            'I/b'  => 'Juru Muda Tk. I',
            'I/c'  => 'Juru',
            'I/d'  => 'Juru Tk. I',
            'II/a' => 'Pengatur Muda',
            'II/b' => 'Pengatur Muda Tk. I',
            'II/c' => 'Pengatur',
            'II/d' => 'Pengatur Tk. I',
            'III/a'=> 'Penata Muda',
            'III/b'=> 'Penata Muda Tk. I',
            'III/c'=> 'Penata',
            'III/d'=> 'Penata Tk. I',
            'IV/a' => 'Pembina',
            'IV/b' => 'Pembina Tk. I',
            'IV/c' => 'Pembina Utama Muda',
            'IV/d' => 'Pembina Utama Madya',
            'IV/e' => 'Pembina Utama',
        ];

        return $map[$golongan] ?? $golongan;
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip'               => 'required|string|max:20',
            'nama'              => 'required|string|max:255',
            'pangkat'           => 'nullable|string|max:255',
            'jabatan'           => 'nullable|string|max:255',
            'no_sk_hukdis'      => 'nullable|string|max:255',
            'tmt_hukdis'        => 'nullable|date',
            'no_sk_cltn'        => 'nullable|string|max:255',
            'tmt_cltn'          => 'nullable|date',
            'kabkota'           => 'nullable|string|max:255',
            'provinsi'          => 'nullable|string|max:255',
            'kd_wil'            => 'nullable|string|max:255',
            'slks_ada'          => 'nullable|string|max:255',
            'no_slks'           => 'nullable|string|max:255',
            'tgl_slks'          => 'nullable|date',
            'usul_slks'         => 'nullable|string|max:255',
            'bulanp'            => 'nullable|string|max:255',
            'tahunp'            => 'nullable|string|max:255',
            'ms_tms'            => 'nullable|string|max:255',
            'ket_tms'           => 'nullable|string',
            // Field dari data pegawai (auto-fill via NIP search)
            'masa_kerja_tahun'  => 'nullable|integer|min:0',
            'masa_kerja_bulan'  => 'nullable|integer|min:0|max:11',
            'kedudukan_hukum_id'=> 'nullable|string|max:10',
            'jenis_pegawai'     => 'nullable|string|max:50',
        ]);
        
        // Cek duplikat usul_slks pada tahun yang sama untuk nip tersebut
        $exists = UsulSlks::where('nip', $validated['nip'])
            ->where('usul_slks', $validated['usul_slks'])
            ->where('tahunp', $validated['tahunp'])
            ->exists();
            
        if ($exists) {
            return redirect()->back()->withInput()->withErrors(['Duplikat usulan: NIP ' . $validated['nip'] . ' sudah mengajukan ' . $validated['usul_slks'] . ' untuk tahun ' . $validated['tahunp'] . '.']);
        }
        
        // Nama selalu disimpan UPPERCASE
        $validated['nama'] = strtoupper($validated['nama']);
        
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
            ->get(['usul_slks', 'no_slks', 'tgl_slks', 'status', 'tahunp']);

        // Susun nama lengkap beserta gelar
        $fullName = $pegawai->nama;
        if (!empty($pegawai->gelar_depan)) {
            $fullName = trim($pegawai->gelar_depan) . ' ' . $fullName;
        }
        if (!empty($pegawai->gelar_belakang)) {
            $fullName = $fullName . ', ' . trim($pegawai->gelar_belakang);
        }

        return response()->json([
            'found'               => true,
            'nama'                => $fullName,
            'pangkat'             => $this->getPangkatFromGolongan($pegawai->gol_akhir),
            'jabatan'             => $pegawai->jabatan,
            'mk_tahun'            => $pegawai->mk_tahun,
            'mk_bulan'            => $pegawai->mk_bulan,
            'kedudukan_hukum_id'  => $pegawai->kedudukan_hukum_id,
            'kedudukan_hukum'     => $pegawai->kedudukan_hukum,  // nama teks untuk display
            'jenis_pegawai'       => $pegawai->jenis_pegawai,
            'riwayat'             => $riwayat,
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
            'nip'               => 'required|string|max:20',
            'nama'              => 'required|string|max:255',
            'pangkat'           => 'nullable|string|max:255',
            'jabatan'           => 'nullable|string|max:255',
            'no_sk_hukdis'      => 'nullable|string|max:255',
            'tmt_hukdis'        => 'nullable|date',
            'no_sk_cltn'        => 'nullable|string|max:255',
            'tmt_cltn'          => 'nullable|date',
            'kabkota'           => 'nullable|string|max:255',
            'provinsi'          => 'nullable|string|max:255',
            'kd_wil'            => 'nullable|string|max:255',
            'slks_ada'          => 'nullable|string|max:255',
            'no_slks'           => 'nullable|string|max:255',
            'tgl_slks'          => 'nullable|date',
            'usul_slks'         => 'nullable|string|max:255',
            'bulanp'            => 'nullable|string|max:255',
            'tahunp'            => 'nullable|string|max:255',
            'ms_tms'            => 'nullable|string|max:255',
            'ket_tms'           => 'nullable|string',
            // Field dari data pegawai (auto-fill via NIP search)
            'masa_kerja_tahun'  => 'nullable|integer|min:0',
            'masa_kerja_bulan'  => 'nullable|integer|min:0|max:11',
            'kedudukan_hukum_id'=> 'nullable|string|max:10',
            'jenis_pegawai'     => 'nullable|string|max:50',
        ]);
        
        // Cek duplikat usul_slks pada tahun yang sama untuk nip tersebut
        $exists = UsulSlks::where('nip', $validated['nip'])
            ->where('usul_slks', $validated['usul_slks'])
            ->where('tahunp', $validated['tahunp'])
            ->where('id', '!=', $id)
            ->exists();
            
        if ($exists) {
            return redirect()->back()->withInput()->withErrors(['Duplikat usulan: NIP ' . $validated['nip'] . ' sudah mengajukan ' . $validated['usul_slks'] . ' untuk tahun ' . $validated['tahunp'] . '.']);
        }
        
        $usulSlks = UsulSlks::findOrFail($id);
        
        // Nama selalu disimpan UPPERCASE
        $validated['nama'] = strtoupper($validated['nama']);
        
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
            
        // Ambil riwayat usulan sebelumnya untuk tiap NIP
        $nips = $data->pluck('nip')->unique();
        $riwayatList = UsulSlks::whereIn('nip', $nips)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('nip');
            
        foreach ($data as $item) {
            // Cari usulan yang BUKAN diri sendiri, dan yang memiliki usul_slks / no_slks
            $riwayatLama = null;
            if (isset($riwayatList[$item->nip])) {
                $riwayatLama = $riwayatList[$item->nip]->first(function($r) use ($item) {
                    return $r->id !== $item->id;
                });
            }
            
            $item->riwayat_lama = $riwayatLama;
        }
        
        return view('siput.laporan-usul-slks', compact('data'));
    }

    public function approve()
    {
        $data = UsulSlks::where('status', '!=', 'riwayat')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('siput.approve-usul-slks', compact('data'));
    }

    public function searchApprove(Request $request)
    {
        $nip = $request->query('nip');
        
        // Find proposal by NIP or Name, prioritizing NIP
        $query = UsulSlks::where('status', '!=', 'riwayat');
        if (is_numeric($nip)) {
            $query->where('nip', 'like', "%{$nip}%");
        } else {
            $query->where('nama', 'like', "%{$nip}%");
        }
        
        $usulan = $query->orderBy('created_at', 'desc')->first();

        if (!$usulan) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found' => true,
            'id' => $usulan->id,
            'nip' => $usulan->nip,
            'nama' => $usulan->nama,
            'pangkat' => $usulan->pangkat,
            'jabatan' => $usulan->jabatan,
            'usul_slks' => $usulan->usul_slks,
            'no_kepres' => $usulan->no_kepres,
            'tanggal_kepres' => $usulan->tanggal_kepres ? $usulan->tanggal_kepres->format('Y-m-d') : null,
            'masa_kerja_tahun' => $usulan->masa_kerja_tahun,
            'masa_kerja_bulan' => $usulan->masa_kerja_bulan,
            'kedudukan_hukum_id' => $usulan->kedudukan_hukum_id,
        ]);
    }

    public function updateApprove(Request $request, $id)
    {
        $validated = $request->validate([
            'no_kepres' => 'nullable|string|max:255',
            'tanggal_kepres' => 'nullable|date',
        ]);
        
        try {
            $usulSlks = UsulSlks::findOrFail($id);
            $usulSlks->update([
                'no_kepres' => $validated['no_kepres'],
                'tanggal_kepres' => $validated['tanggal_kepres'],
                'updated_by' => auth()->id()
            ]);
            
            return redirect()->route('siput.usul-slks.approve')->with('success', 'Data No Kepres dan Tanggal Kepres berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }
}
