<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\RefUnor;
use App\Models\RefJabatan;
use App\Models\RefGolongan;
use App\Models\IuranKorpri;
use App\Models\RefIuranEselon;
use App\Models\IuranOverride;
use App\Models\IuranOverrideLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RekonIuranController extends Controller
{
    private function extractGolonganKey(?string $namaGolongan): ?string
    {
        if (empty($namaGolongan)) {
            return null;
        }
        return trim($namaGolongan);
    }

    public function index(Request $request)
    {
        $filterOpd = $request->input('opd');
        $filterUnor = $request->input('unor');
        $filterGolongan = $request->input('golongan');
        $filterJabatan = $request->input('jabatan');
        $search = $request->input('search');
        $pns = $request->has('pns') ? $request->input('pns') : 1;
        $pppk = $request->has('pppk') ? $request->input('pppk') : 1;

        $listOpd = RefUnor::whereNotNull('nama')
            ->where('nama', '!=', '')
            ->select('nama')
            ->distinct()
            ->orderBy('nama')
            ->pluck('nama');

        $listUnor = RefUnor::whereNotNull('nama_opd')
            ->where('nama_opd', '!=', '')
            ->orderBy('nama_lengkap')
            ->get(['nama_opd', 'nama_lengkap']);

        $listGolongan = RefGolongan::orderBy('nama')->pluck('nama');
        $listJabatan = RefJabatan::orderBy('nama')->pluck('nama');

        $golonganKeys = IuranKorpri::orderBy('id')->pluck('label', 'golongan_key');
        $eselonKeys = RefIuranEselon::orderBy('id')->pluck('label', 'eselon_key');

        $query = Pegawai::aktif()->with(['golongan', 'unor', 'jabatan', 'jenisJabatan', 'iuranOverride']);

        if ($pns && !$pppk) {
            $query->whereIn('kedudukan_hukum_id', ['01','02','03','04','15'])
                  ->whereIn('status_cpns_pns', ['P','C']);
        } elseif (!$pns && $pppk) {
            $query->whereIn('kedudukan_hukum_id', ['71','73']);
        } elseif (!$pns && !$pppk) {
            $query->where('id', '<', 0);
        } else {
            $query->where(function($q) {
                $q->where('kedudukan_hukum_id', '!=', '101')
                  ->orWhereNull('kedudukan_hukum_id');
            });
        }

        if ($filterOpd) {
            $query->whereHas('unor', function ($q) use ($filterOpd) {
                $q->where('nama', $filterOpd);
            });
        }

        if ($filterUnor) {
            $query->whereHas('unor', function ($q) use ($filterUnor) {
                $q->where('nama_opd', $filterUnor);
            });
        }

        if ($filterGolongan) {
            $query->whereHas('golongan', function ($q) use ($filterGolongan) {
                $q->where('nama', $filterGolongan);
            });
        }

        if ($filterJabatan) {
            $query->whereHas('jabatan', function ($q) use ($filterJabatan) {
                $q->where('nama', 'LIKE', '%' . $filterJabatan . '%');
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'LIKE', '%' . $search . '%')
                  ->orWhere('nip_baru', 'LIKE', '%' . $search . '%');
            });
        }

        $pegawaiList = $query->orderBy('nama')->paginate(25)->withQueryString();

        $eselonMappings = \App\Models\RefEselonMapping::pluck('eselon_key', 'jabatan_id');

        return view('admin.rekon-iuran.index', compact(
            'listOpd', 'listUnor', 'listGolongan', 'listJabatan', 
            'filterOpd', 'filterUnor', 'filterGolongan', 'filterJabatan', 'search',
            'pns', 'pppk', 'pegawaiList', 'eselonMappings',
            'golonganKeys', 'eselonKeys'
        ));
    }

    public function bulkOverride(Request $request)
    {
        $request->validate([
            'pegawai_ids' => 'required|array',
            'pegawai_ids.*' => 'exists:pegawai,id',
            'override_golongan_key' => 'nullable|string',
            'override_eselon_key' => 'nullable|string',
            'alasan' => 'required|string|max:255',
        ]);

        if (empty($request->override_golongan_key) && empty($request->override_eselon_key)) {
            return response()->json(['success' => false, 'message' => 'Pilih minimal salah satu (Golongan/Eselon) untuk diubah.']);
        }

        DB::beginTransaction();
        try {
            foreach ($request->pegawai_ids as $pegawai_id) {
                $pegawai = Pegawai::with('iuranOverride')->find($pegawai_id);
                $override = $pegawai->iuranOverride;
                
                $oldGolongan = $override ? $override->override_golongan_key : null;
                $oldEselon = $override ? $override->override_eselon_key : null;
                
                $action = $override ? 'update' : 'create';

                $newGolongan = $request->override_golongan_key ?: $oldGolongan;
                $newEselon = $request->override_eselon_key ?: $oldEselon;

                IuranOverride::updateOrCreate(
                    ['pegawai_id' => $pegawai_id],
                    [
                        'override_golongan_key' => $newGolongan,
                        'override_eselon_key' => $newEselon,
                        'alasan' => $request->alasan,
                        'updated_by' => 'Admin' // Assuming auth()->user()->name in a real app
                    ]
                );

                IuranOverrideLog::create([
                    'pegawai_id' => $pegawai_id,
                    'action' => $action,
                    'old_golongan_key' => $oldGolongan,
                    'new_golongan_key' => $newGolongan,
                    'old_eselon_key' => $oldEselon,
                    'new_eselon_key' => $newEselon,
                    'alasan' => $request->alasan,
                    'performed_by' => 'Admin'
                ]);
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Override berhasil disimpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan: ' . $e->getMessage()], 500);
        }
    }

    public function singleOverride(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'override_golongan_key' => 'nullable|string',
            'override_eselon_key' => 'nullable|string',
            'alasan' => 'required|string|max:255',
        ]);

        return $this->bulkOverride(new Request(array_merge($request->all(), [
            'pegawai_ids' => [$request->pegawai_id]
        ])));
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $override = IuranOverride::where('pegawai_id', $id)->firstOrFail();
            
            IuranOverrideLog::create([
                'pegawai_id' => $override->pegawai_id,
                'action' => 'delete',
                'old_golongan_key' => $override->override_golongan_key,
                'new_golongan_key' => null,
                'old_eselon_key' => $override->override_eselon_key,
                'new_eselon_key' => null,
                'alasan' => 'Reset ke data BKN',
                'performed_by' => 'Admin'
            ]);

            $override->delete();
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Override berhasil dihapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menghapus: ' . $e->getMessage()], 500);
        }
    }

    public function syncReset(Request $request)
    {
        $request->validate([
            'pegawai_ids' => 'required|array',
            'pegawai_ids.*' => 'exists:pegawai,id',
        ]);

        DB::beginTransaction();
        try {
            $overrides = IuranOverride::whereIn('pegawai_id', $request->pegawai_ids)->get();
            
            foreach ($overrides as $override) {
                IuranOverrideLog::create([
                    'pegawai_id' => $override->pegawai_id,
                    'action' => 'sync_reset',
                    'old_golongan_key' => $override->override_golongan_key,
                    'new_golongan_key' => null,
                    'old_eselon_key' => $override->override_eselon_key,
                    'new_eselon_key' => null,
                    'alasan' => 'Bulk Sync Reset dari BKN',
                    'performed_by' => 'Admin'
                ]);
            }

            IuranOverride::whereIn('pegawai_id', $request->pegawai_ids)->delete();
            DB::commit();

            return response()->json(['success' => true, 'message' => count($overrides) . ' override berhasil direset']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal mereset: ' . $e->getMessage()], 500);
        }
    }
}
