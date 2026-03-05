<?php

namespace App\Http\Controllers;

use App\Models\SnapshotPegawai;
use App\Models\HistoryPegawai;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SnapshotController extends Controller
{
    public function index(Request $request)
    {
        // Filter Month (Format: YYYY-MM)
        // If 'snapshot_month' is present, we look into HistoryPegawai
        // If not, we look into SnapshotPegawai (Live)
        $filterMonth = $request->input('snapshot_month');
        $search = $request->input('search');

        if ($filterMonth) {
            // Viewing History
            $query = HistoryPegawai::query();

            // Filter by month (created_at) - MySQL Compatible
            $query->where(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"), $filterMonth);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_pegawai', 'like', "%{$search}%")
                        ->orWhere('nip_baru', 'like', "%{$search}%")
                        ->orWhere('jabatan', 'like', "%{$search}%")
                        ->orWhere('unor_nama', 'like', "%{$search}%")
                        ->orWhere('golongan', 'like', "%{$search}%");
                });
            }

            $pegawai = $query->orderBy('nama_pegawai')->paginate(10)->withQueryString();
            $isHistory = true;
        } else {
            // Viewing Live Data
            $query = Pegawai::with([
                'agama',
                'jenisKawin',
                'jenisPegawai',
                'kedudukanHukum',
                'golongan',
                'jabatan',
                'jenisJabatan', // Added jenisJabatan
                'tingkatPendidikan',
                'pendidikan',
                'unor'
            ])->whereNull('deleted_at');

            // Search
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('nip_baru', 'like', "%{$search}%")
                        ->orWhereHas('jabatan', function ($j) use ($search) {
                            $j->where('nama', 'like', "%{$search}%");
                        })
                        ->orWhereHas('unor', function ($u) use ($search) {
                            $u->where('nama', 'like', "%{$search}%");
                        })
                        ->orWhereHas('golongan', function ($g) use ($search) {
                            $g->where('nama', 'like', "%{$search}%");
                        });
                });
            }

            $paginator = $query->orderBy('nama')->paginate(10)->withQueryString();

            // Transform live data to match history data structure
            $paginator->getCollection()->transform(function ($item) {
                // Determine Status ASN based on Golongan formatting rules
                $golonganPppk = $item->golongan_pppk;
                $statusAsn = 'PPPK PW'; // Default jika tidak ada golongan

                if (!empty($golonganPppk)) {
                    if (str_contains($golonganPppk, '/') && preg_match('/^[I|V|X]+\/[a-z]$/i', $golonganPppk)) {
                        // PNS / CPNS (misal: "I/a", "IV/e", dll) - fallback memakai data original P/C dari dB untuk bedakan CPNS vs PNS jika ada
                        $statusAsn = ($item->status_cpns_pns === 'C' || $item->status_cpns_pns === 'CPNS') ? 'CPNS' : 'PNS';
                    } elseif (preg_match('/^[I|V|X]+$/i', $golonganPppk)) {
                        // PPPK (misal: "I", "V", "VII", "IX", "X", "XI")
                        $statusAsn = 'PPPK';
                    }
                }

                return (object) [
                    'nip_baru' => $item->nip_baru,
                    'nama_pegawai' => $item->nama_lengkap ?? $item->nama,
                    'tgl_lahir' => $item->tanggal_lahir,
                    'tempat_lahir' => $item->tempat_lahir,
                    'jenis_kelamin' => $item->jenis_kelamin,
                    'agama' => $item->agama ? $item->agama->nama : null,
                    'jenis_kawin' => $item->jenisKawin ? $item->jenisKawin->nama : null,
                    'jenis_pegawai' => $item->jenisPegawai ? $item->jenisPegawai->nama : null,
                    'eselon' => null,
                    'jabatan' => $item->jabatan ? $item->jabatan->nama : null,
                    'jenis_jabatan' => $item->jenisJabatan ? $item->jenisJabatan->nama : null,
                    'pd' => null,
                    'sub_pd' => null,
                    'jenikel' => $item->jenis_kelamin == 'L' ? 'Laki-Laki' : ($item->jenis_kelamin == 'P' ? 'Perempuan' : null),
                    'sts_peg' => $statusAsn,
                    'tk_pend' => $item->tingkatPendidikan ? $item->tingkatPendidikan->nama : null,
                    'golongan' => $golonganPppk,
                    'unor_nama' => $item->unor ? $item->unor->nama : null,
                    'unor_opd' => $item->unor ? $item->unor->nama_opd : null,
                    'pendidikan' => $item->pendidikan ? $item->pendidikan->nama : null,
                    'tingkat_pendidikan' => $item->tingkatPendidikan ? $item->tingkatPendidikan->nama : null,
                    'status_cpns_pns' => $item->status_cpns_pns, // Pertahankan data legacy CPNS/PNS jika diperlukan
                    'tmt_cpns' => $item->tmt_cpns,
                    'tmt_pns' => $item->tmt_pns,
                    'kedudukan_hukum' => $item->kedudukanHukum ? $item->kedudukanHukum->nama : null,
                ];
            });

            $pegawai = $paginator;
            $isHistory = false;
        }

        // Get available months for validation/filtering dropdown - MySQL Compatible
        $historyMonths = HistoryPegawai::select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month_val"))
            ->distinct()
            ->orderBy('month_val', 'desc')
            ->pluck('month_val');

        // Check availability: Once per month for saving *new* snapshot
        $lastSnapshot = HistoryPegawai::select('created_at')->latest('created_at')->first();

        $canSnapshot = true;
        if ($lastSnapshot && Carbon::parse($lastSnapshot->created_at)->format('Y-m') === Carbon::now()->format('Y-m')) {
            $canSnapshot = false;
        }

        return view('admin.snapshot.index', compact('pegawai', 'canSnapshot', 'isHistory', 'filterMonth', 'search', 'historyMonths'));
    }

    public function downloadPdf(Request $request)
    {
        // Placeholder for PDF generation
        // In a real app, uses DomPDF or snappy
        return back()->with('error', 'Fitur export PDF belum dikonfigurasi.');
    }

    public function downloadExcel(Request $request)
    {
        // Placeholder for Excel export
        // In a real app, uses Laravel Excel
        return back()->with('error', 'Fitur export Excel belum dikonfigurasi.');
    }

    public function store()
    {
        // 1. Validation Logic
        $lastSnapshot = HistoryPegawai::select('created_at')->latest('created_at')->first();
        if ($lastSnapshot && Carbon::parse($lastSnapshot->created_at)->format('Y-m') === Carbon::now()->format('Y-m')) {
            return back()->with('error', 'Snapshot hanya bisa dilakukan 1 bulan sekali. Terakhir dilakukan pada ' . Carbon::parse($lastSnapshot->created_at)->format('d F Y'));
        }

        // 2. Heavy Lifting
        // We use cursor to minimize memory usage for large datasets
        DB::beginTransaction();
        try {
            $now = now();

            // Get all current data from main model Pegawai (not SnapshotPegawai)
            // Eager load relationships to get names instead of IDs
            $sourceData = Pegawai::with([
                'agama',
                'jenisKawin',
                'jenisPegawai',
                'kedudukanHukum',
                'golongan',
                'jabatan',
                'jenisJabatan', // Added jenisJabatan
                'tingkatPendidikan',
                'pendidikan',
                'unor'
            ])->whereNull('deleted_at')->get();

            if ($sourceData->isEmpty()) {
                return back()->with('error', 'Data kosong, tidak ada yang bisa disimpan.');
            }

            $insertData = [];
            foreach ($sourceData as $item) {
                // Determine Status ASN based on Golongan formatting rules
                $golonganPppk = $item->golongan_pppk;
                $statusAsn = 'PPPK PW'; // Default jika tidak ada golongan

                if (!empty($golonganPppk)) {
                    if (str_contains($golonganPppk, '/') && preg_match('/^[I|V|X]+\/[a-z]$/i', $golonganPppk)) {
                        // PNS / CPNS (misal: "I/a", "IV/e", dll) - fallback memakai data original P/C dari dB untuk bedakan CPNS vs PNS jika ada
                        $statusAsn = ($item->status_cpns_pns === 'C' || $item->status_cpns_pns === 'CPNS') ? 'CPNS' : 'PNS';
                    } elseif (preg_match('/^[I|V|X]+$/i', $golonganPppk)) {
                        // PPPK (misal: "I", "V", "VII", "IX", "X", "XI")
                        $statusAsn = 'PPPK';
                    }
                }

                $insertData[] = [
                    'nip_baru' => $item->nip_baru,
                    'nama_pegawai' => $item->nama_lengkap ?? $item->nama,
                    'tgl_lahir' => $item->tanggal_lahir,
                    'tempat_lahir' => $item->tempat_lahir,
                    'jenis_kelamin' => $item->jenis_kelamin,
                    'agama' => $item->agama ? $item->agama->nama : null,
                    'jenis_kawin' => $item->jenisKawin ? $item->jenisKawin->nama : null,
                    'jenis_pegawai' => $item->jenisPegawai ? $item->jenisPegawai->nama : null,
                    'eselon' => null, // Eselon can be derived if needed, mostly redundant with jabatan
                    'jabatan' => $item->jabatan ? $item->jabatan->nama : null,
                    'jenis_jabatan' => $item->jenisJabatan ? $item->jenisJabatan->nama : null, // Assigned jenis_jabatan
                    'pd' => null, // Legacy, use unor_opd instead later or map appropriately
                    'sub_pd' => null, // Legacy
                    'jenikel' => $item->jenis_kelamin == 'L' ? 'Laki-Laki' : ($item->jenis_kelamin == 'P' ? 'Perempuan' : null),
                    'sts_peg' => $statusAsn,
                    'tk_pend' => $item->tingkatPendidikan ? $item->tingkatPendidikan->nama : null,
                    'golongan' => $golonganPppk,
                    'unor_nama' => $item->unor ? $item->unor->nama : null,
                    'unor_opd' => $item->unor ? $item->unor->nama_opd : null,
                    'pendidikan' => $item->pendidikan ? $item->pendidikan->nama : null,
                    'tingkat_pendidikan' => $item->tingkatPendidikan ? $item->tingkatPendidikan->nama : null,
                    'status_cpns_pns' => $item->status_cpns_pns, // Pertahankan legacy original flag
                    'tmt_cpns' => $item->tmt_cpns,
                    'tmt_pns' => $item->tmt_pns,
                    'kedudukan_hukum' => $item->kedudukanHukum ? $item->kedudukanHukum->nama : null,
                    'no_hp' => $item->no_hp,
                    'last_sync_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // Insert in chunks to avoid query size limits
            foreach (array_chunk($insertData, 500) as $chunk) {
                HistoryPegawai::insert($chunk);
            }

            DB::commit();

            return back()->with('success', 'Berhasil menyimpan Snapshot Data Pegawai bulan ini.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan snapshot: ' . $e->getMessage());
        }
    }
}
