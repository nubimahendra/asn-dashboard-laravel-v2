<?php

namespace App\Http\Controllers;

use App\Models\RefJabatanKelas;
use App\Imports\KelasJabatanImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class KelasJabatanController extends Controller
{
    public function index(Request $request)
    {
        $query = RefJabatanKelas::with(['jabatan', 'unor']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('jabatan', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            })->orWhereHas('unor', function ($q) use ($search) {
                $q->where('nama_opd', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        $data = $query->paginate(10);
        $data->appends(['search' => $request->search]);

        // Fetch or create Tarif Iuran Korpri for the current year
        $tahun = date('Y');
        if (\App\Models\RefIuranKorpri::where('tahun_berlaku', $tahun)->count() === 0) {
            for ($i = 1; $i <= 17; $i++) {
                \App\Models\RefIuranKorpri::create([
                    'kelas_jabatan' => $i,
                    'nominal' => 0,
                    'tahun_berlaku' => $tahun
                ]);
            }
        }
        $tarifIuran = \App\Models\RefIuranKorpri::where('tahun_berlaku', $tahun)
            ->orderBy('kelas_jabatan', 'asc')
            ->get();

        return view('admin.kelas-jabatan.index', compact('data', 'tarifIuran'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls|max:10240'
        ]);

        try {
            $import = new KelasJabatanImport();
            Excel::import($import, $request->file('file'));

            $success = $import->getSuccessCount();
            $error = $import->getErrorCount();
            $errors = $import->getErrors();

            $msg = "Berhasil mengimport {$success} data.";
            if ($error > 0) {
                $msg .= " Terdapat {$error} baris gagal diimport.";
                return redirect()->back()->with('success', $msg)->with('import_errors', $errors);
            }

            return redirect()->back()->with('success', $msg);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
            }
            return redirect()->back()->with('error', 'Gagal mengimport file. Terdapat kesalahan format data.')->with('import_errors', $errors);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $item = RefJabatanKelas::findOrFail($id);
            $item->delete();
            return redirect()->back()->with('success', 'Data kelas jabatan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data.');
        }
    }

    public function updateTarif(Request $request)
    {
        $request->validate([
            'tarif' => 'required|array',
            'tarif.*' => 'required|numeric|min:0'
        ]);

        try {
            foreach ($request->tarif as $id => $nominal) {
                \App\Models\RefIuranKorpri::where('id', $id)->update(['nominal' => $nominal]);
            }
            return redirect()->back()->with('success', 'Tarif Iuran Korpri berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui tarif: ' . $e->getMessage());
        }
    }
}
