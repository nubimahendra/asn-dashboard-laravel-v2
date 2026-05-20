<?php

namespace App\Http\Controllers;

use App\Models\RefEselonMapping;
use App\Models\RefJabatan;
use App\Models\RefIuranEselon;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EselonMappingController extends Controller
{
    public function index(Request $request)
    {
        $query = RefEselonMapping::with('jabatan');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('jabatan', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            });
        }

        if ($request->has('eselon_key') && $request->eselon_key != '') {
            $query->where('eselon_key', $request->eselon_key);
        }

        if ($request->has('tipe') && $request->tipe != '') {
            if ($request->tipe == 'auto') {
                $query->where('is_auto', true);
            } elseif ($request->tipe == 'manual') {
                $query->where('is_auto', false);
            }
        }

        $data = $query->paginate(15)->appends($request->all());

        // For the manual mapping form
        $mappedJabatanIds = RefEselonMapping::pluck('jabatan_id');
        
        // Hanya ambil jabatan struktural yang belum termapping
        $strukturalJabatanIds = Pegawai::aktif()
            ->where('jenis_jabatan_id', '1') // 1 = struktural
            ->whereNotIn('jabatan_id', $mappedJabatanIds)
            ->pluck('jabatan_id')
            ->filter()
            ->unique();
            
        $jabatanList = RefJabatan::whereIn('id', $strukturalJabatanIds)->orderBy('nama')->get();
        $eselonList = RefIuranEselon::orderBy('id')->get();

        // Stats
        $totalMapped = RefEselonMapping::count();
        $totalStrukturalJabatan = Pegawai::aktif()
            ->where('jenis_jabatan_id', '1')
            ->pluck('jabatan_id')
            ->filter()
            ->unique()
            ->count();
        $totalAuto = RefEselonMapping::where('is_auto', true)->count();
        $totalManual = RefEselonMapping::where('is_auto', false)->count();

        return view('admin.eselon-mapping.index', compact('data', 'jabatanList', 'eselonList', 'totalMapped', 'totalStrukturalJabatan', 'totalAuto', 'totalManual'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jabatan_id' => 'required|exists:ref_jabatan,id',
            'eselon_key' => 'required|exists:ref_iuran_eselon,eselon_key',
        ]);

        try {
            RefEselonMapping::updateOrCreate(
                ['jabatan_id' => $request->jabatan_id],
                [
                    'eselon_key' => $request->eselon_key,
                    'is_auto' => false, // manual override
                ]
            );

            return redirect()->back()->with('success', 'Mapping eselon manual berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan mapping eselon: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'eselon_key' => 'required|exists:ref_iuran_eselon,eselon_key',
        ]);

        try {
            $item = RefEselonMapping::findOrFail($id);
            $item->update([
                'eselon_key' => $request->eselon_key,
                'is_auto' => false, // Jadi manual jika diedit
            ]);

            return redirect()->back()->with('success', 'Mapping eselon berhasil diubah (Manual Override).');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengubah mapping eselon: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $item = RefEselonMapping::findOrFail($id);
            $item->delete();
            return redirect()->back()->with('success', 'Mapping eselon berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus mapping eselon.');
        }
    }

    public function generate(Request $request)
    {
        try {
            $strukturalJabatanIds = Pegawai::aktif()
                ->where('jenis_jabatan_id', '1') // 1 = struktural
                ->pluck('jabatan_id')
                ->filter()
                ->unique();
                
            $jabatans = RefJabatan::whereIn('id', $strukturalJabatanIds)->get();
            $mappedCount = 0;
            
            foreach ($jabatans as $jabatan) {
                $name = strtolower($jabatan->nama);
                $eselon = 'IV/b'; // Default

                // Pattern Matching
                if (str_starts_with($name, 'sekretaris daerah')) {
                    $eselon = 'II/a';
                } elseif (str_starts_with($name, 'kepala dinas') || str_starts_with($name, 'kepala badan') || str_starts_with($name, 'inspektur') || str_starts_with($name, 'staf ahli')) {
                    $eselon = 'II/b';
                } elseif (str_starts_with($name, 'kepala bagian') || str_starts_with($name, 'camat') || str_starts_with($name, 'sekretaris dinas') || str_starts_with($name, 'sekretaris badan') || str_starts_with($name, 'wakil direktur') || str_starts_with($name, 'inspektorat pembantu') || str_starts_with($name, 'inspektur pembantu') || $name === 'sekretaris') {
                    $eselon = 'III/a';
                } elseif (str_starts_with($name, 'sekretaris camat') || str_starts_with($name, 'sekretaris kecamatan') || str_starts_with($name, 'kepala bidang')) {
                    $eselon = 'III/b';
                } elseif (str_starts_with($name, 'kepala sub bagian penyusun program dan keuangan') || (str_starts_with($name, 'kepala seksi') && str_contains($name, 'kelurahan')) || (str_starts_with($name, 'sekretaris') && str_contains($name, 'kelurahan'))) {
                    $eselon = 'IV/b';
                } elseif (str_starts_with($name, 'kepala upt') || str_starts_with($name, 'kepala seksi') || str_starts_with($name, 'lurah') || str_starts_with($name, 'kepala kelurahan') || str_starts_with($name, 'kepala sub bagian') || str_starts_with($name, 'kepala subbidang') || str_starts_with($name, 'kepala sub bidang')) {
                    $eselon = 'IV/a';
                } elseif (str_starts_with($name, 'bupati') || str_starts_with($name, 'wakil bupati')) {
                    continue; // Skip mapping for bupati
                } else {
                    $eselon = 'IV/a'; // Default
                }

                // Only insert/update if is_auto is true or record doesn't exist
                $existing = RefEselonMapping::where('jabatan_id', $jabatan->id)->first();
                if (!$existing || $existing->is_auto) {
                    RefEselonMapping::updateOrCreate(
                        ['jabatan_id' => $jabatan->id],
                        ['eselon_key' => $eselon, 'is_auto' => true]
                    );
                    $mappedCount++;
                }
            }

            return redirect()->back()->with('success', "Berhasil melakukan auto-generate mapping untuk {$mappedCount} jabatan.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal melakukan auto-generate: ' . $e->getMessage());
        }
    }
}
