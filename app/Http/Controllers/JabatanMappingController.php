<?php

namespace App\Http\Controllers;

use App\Models\RefJabatanMapping;
use App\Models\RefJabatan;
use App\Models\RefKelasPerbup;
use Illuminate\Http\Request;

class JabatanMappingController extends Controller
{
    public function index(Request $request)
    {
        $query = RefJabatanMapping::with(['jabatanSiasn', 'kelasPerbup']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('jabatanSiasn', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            })->orWhereHas('kelasPerbup', function ($q) use ($search) {
                $q->where('nama_jabatan_perbup', 'like', "%{$search}%")
                  ->orWhere('nama_opd_perbup', 'like', "%{$search}%");
            });
        }

        $data = $query->paginate(15)->appends(['search' => $request->search]);

        // For the manual mapping form
        // Hanya ambil jabatan yang belum ada di ref_jabatan_mapping
        $mappedJabatanIds = RefJabatanMapping::pluck('jabatan_siasn_id');
        $jabatanList = RefJabatan::whereNotIn('id', $mappedJabatanIds)->orderBy('nama')->get();
        // Option to lazy load RefKelasPerbup in frontend using select2/ajax if too large, but for now we list
        $perbupList = RefKelasPerbup::orderBy('nama_opd_perbup')->orderBy('nama_jabatan_perbup')->get();

        return view('admin.jabatan-mapping.index', compact('data', 'jabatanList', 'perbupList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jabatan_siasn_id' => 'required|exists:ref_jabatan,id',
            'kelas_perbup_id' => 'nullable|exists:ref_kelas_perbup,id',
            'status_validasi' => 'required|in:unvalidated,valid,invalid',
            'catatan' => 'nullable|string'
        ]);

        try {
            RefJabatanMapping::updateOrCreate(
                ['jabatan_siasn_id' => $request->jabatan_siasn_id],
                [
                    'kelas_perbup_id' => $request->kelas_perbup_id,
                    'status_validasi' => $request->status_validasi,
                    'catatan' => $request->catatan,
                ]
            );

            return redirect()->back()->with('success', 'Mapping jabatan berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan mapping jabatan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $item = RefJabatanMapping::findOrFail($id);
            $item->delete();
            return redirect()->back()->with('success', 'Mapping jabatan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus mapping jabatan.');
        }
    }
}
