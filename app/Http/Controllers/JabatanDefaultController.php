<?php

namespace App\Http\Controllers;

use App\Models\RefJabatanDefault;
use App\Models\RefJabatan;
use Illuminate\Http\Request;

class JabatanDefaultController extends Controller
{
    public function index(Request $request)
    {
        $query = RefJabatanDefault::with('jabatan');

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('jabatan', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            });
        }

        $data = $query->paginate(15)->appends(['search' => $request->search]);
        $jabatanList = RefJabatan::orderBy('nama')->get();

        return view('admin.jabatan-default.index', compact('data', 'jabatanList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jabatan_id' => 'required|exists:ref_jabatan,id',
            'kelas_jabatan' => 'required|integer|min:1|max:17',
        ]);

        try {
            RefJabatanDefault::updateOrCreate(
                ['jabatan_id' => $request->jabatan_id],
                ['kelas_jabatan' => $request->kelas_jabatan]
            );

            return redirect()->back()->with('success', 'Kelas Jabatan Default berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan Kelas Jabatan Default: ' . $e->getMessage());
        }
    }

    public function destroy($jabatan_id)
    {
        try {
            $item = RefJabatanDefault::where('jabatan_id', $jabatan_id)->firstOrFail();
            $item->delete();
            return redirect()->back()->with('success', 'Kelas Jabatan Default berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus Kelas Jabatan Default.');
        }
    }
}
