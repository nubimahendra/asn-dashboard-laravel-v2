<?php

namespace App\Http\Controllers;

use App\Models\RefKelasPerbup;
use App\Models\RefJabatanMapping;
use App\Models\RefOpdMapping;
use App\Imports\KelasJabatanPerbupImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class KelasJabatanPerbupController extends Controller
{
    public function index(Request $request)
    {
        $query = RefKelasPerbup::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('nama_opd_perbup', 'like', "%{$search}%")
                  ->orWhere('nama_jabatan_perbup', 'like', "%{$search}%");
        }

        $data = $query->paginate(15)->appends(['search' => $request->search]);

        return view('admin.kelas-jabatan-perbup.index', compact('data'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls|max:10240'
        ]);

        try {
            $import = new KelasJabatanPerbupImport();
            Excel::import($import, $request->file('file'));

            $success = $import->getSuccessCount();
            $error = $import->getErrorCount();
            $errors = $import->getErrors();

            $msg = "Berhasil mengimport {$success} data kelas jabatan perbup.";
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
            $item = RefKelasPerbup::findOrFail($id);
            $item->delete();
            return redirect()->back()->with('success', 'Data kelas jabatan perbup berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data. ' . $e->getMessage());
        }
    }
}
