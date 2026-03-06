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

    public function generateBulkSuggestions(Request $request)
    {
        $mappedJabatanIds = RefJabatanMapping::pluck('jabatan_siasn_id');
        $siasnList = RefJabatan::whereNotIn('id', $mappedJabatanIds)->get();
        $perbups = RefKelasPerbup::all();

        $suggestions = [];

        foreach ($siasnList as $siasn) {
            $searchName = strtolower(trim($siasn->nama));

            // Jika jabatan adalah Sekretaris, tambahkan nama unor untuk meningkatkan akurasi mapping
            if (str_starts_with($searchName, 'sekretaris')) {
                $pegawai = \App\Models\Pegawai::with('unor')
                    ->where('jabatan_id', $siasn->id)
                    ->first();
                
                if ($pegawai && $pegawai->unor) {
                    $unorName = trim($pegawai->unor->nama);
                    $firstWord = explode(' ', $unorName)[0];
                    if (strtolower($firstWord) === 'sekretariat' && stripos($unorName, 'dprd') !== false) {
                        $firstWord = 'Dewan';
                    }
                    $searchName .= ' ' . strtolower($firstWord);
                }
            }

            $bestMatch = null;
            $highestSimilarity = -1;

            foreach ($perbups as $perbup) {
                $perbupName = strtolower(trim($perbup->nama_jabatan_perbup));
                similar_text($searchName, $perbupName, $percent);

                if ($percent > $highestSimilarity) {
                    $highestSimilarity = $percent;
                    $bestMatch = $perbup;
                }
            }

            if ($bestMatch && $highestSimilarity >= 20) { // suggest if similarity >= 20%
                $displayNama = $siasn->nama;
                if (str_starts_with(strtolower(trim($siasn->nama)), 'sekretaris')) {
                    $displayNama = ucwords($searchName);
                }

                $suggestions[] = [
                    'siasn_id' => $siasn->id,
                    'siasn_nama' => $displayNama,
                    'perbup_id' => $bestMatch->id,
                    'perbup_nama' => $bestMatch->nama_opd_perbup . ' - ' . $bestMatch->nama_jabatan_perbup . ' (Kelas ' . $bestMatch->kelas_jabatan . ')',
                    'similarity' => round($highestSimilarity, 2),
                ];
            }
        }

        // Sort by similarity descending
        usort($suggestions, function ($a, $b) {
            return $b['similarity'] <=> $a['similarity'];
        });

        return response()->json([
            'success' => true,
            'data' => $suggestions
        ]);
    }

    public function storeBulk(Request $request)
    {
        $mappings = $request->input('mappings', []);
        if (empty($mappings)) {
            return redirect()->back()->with('error', 'Tidak ada data saran mapping yang diproses.');
        }

        $count = 0;
        foreach ($mappings as $map) {
            // Only process if the checkbox was checked
            if (!empty($map['process']) && !empty($map['siasn_id']) && !empty($map['perbup_id'])) {
                $similarity = floatval($map['similarity'] ?? 0);
                $status = $similarity >= 60 ? 'valid' : 'unvalidated';

                RefJabatanMapping::updateOrCreate(
                    ['jabatan_siasn_id' => $map['siasn_id']],
                    [
                        'kelas_perbup_id' => $map['perbup_id'],
                        'status_validasi' => $status,
                        'catatan' => 'Auto-mapped (' . $similarity . '%)'
                    ]
                );
                $count++;
            }
        }

        return redirect()->back()->with('success', "$count mapping berhasil diproses dan disimpan.");
    }

    public function findSimilarPerbup(Request $request)
    {
        $jabatanSiasnId = $request->get('jabatan_siasn_id');
        if (!$jabatanSiasnId) {
            return response()->json(['success' => false, 'message' => 'ID Jabatan SIASN tidak diberikan']);
        }

        $jabatanSiasn = RefJabatan::find($jabatanSiasnId);
        if (!$jabatanSiasn) {
            return response()->json(['success' => false, 'message' => 'Jabatan SIASN tidak ditemukan']);
        }

        $searchName = strtolower(trim($jabatanSiasn->nama));

        // Jika jabatan adalah Sekretaris, tambahkan nama unor
        if (str_starts_with($searchName, 'sekretaris')) {
            $pegawai = \App\Models\Pegawai::with('unor')
                ->where('jabatan_id', $jabatanSiasn->id)
                ->first();
            
            if ($pegawai && $pegawai->unor) {
                $unorName = trim($pegawai->unor->nama);
                $firstWord = explode(' ', $unorName)[0];
                if (strtolower($firstWord) === 'sekretariat' && stripos($unorName, 'dprd') !== false) {
                    $firstWord = 'Dewan';
                }
                $searchName .= ' ' . strtolower($firstWord);
            }
        }

        $perbups = RefKelasPerbup::all();
        $bestMatch = null;
        $highestSimilarity = -1;

        foreach ($perbups as $perbup) {
            $perbupName = strtolower(trim($perbup->nama_jabatan_perbup));

            similar_text($searchName, $perbupName, $percent);

            if ($percent > $highestSimilarity) {
                $highestSimilarity = $percent;
                $bestMatch = $perbup;
            }
        }

        if ($bestMatch && $highestSimilarity >= 30) { // Set a reasonable threshold
            return response()->json([
                'success' => true,
                'perbup_id' => $bestMatch->id,
                'similarity' => round($highestSimilarity, 2)
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Kemiripan terlalu rendah']);
    }
}
