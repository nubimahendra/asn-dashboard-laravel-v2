<?php

namespace App\Http\Controllers;

use App\Models\SnapshotPegawai;
use App\Models\HistoryPegawai;
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

            // Search
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_pegawai', 'like', "%{$search}%")
                        ->orWhere('nip_baru', 'like', "%{$search}%");
                });
            }

            $pegawai = $query->orderBy('nama_pegawai')->paginate(10)->withQueryString();
            $isHistory = true;
        } else {
            // Viewing Live Data
            $query = SnapshotPegawai::query();

            // Search
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_pegawai', 'like', "%{$search}%")
                        ->orWhere('nip_baru', 'like', "%{$search}%");
                });
            }

            $pegawai = $query->orderBy('nama_pegawai')->paginate(10)->withQueryString();
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

            // Get all current data
            // We explicit select fields to ensure we don't get 'id' collision if we were just toArray()-ing
            // Although we're mapping manually so it's fine.
            $sourceData = SnapshotPegawai::get();

            if ($sourceData->isEmpty()) {
                return back()->with('error', 'Data kosong, tidak ada yang bisa disimpan.');
            }

            $insertData = [];
            foreach ($sourceData as $item) {
                $insertData[] = [
                    'nip_baru' => $item->nip_baru,
                    'nama_pegawai' => $item->nama_pegawai,
                    'tgl_lahir' => $item->tgl_lahir,
                    'eselon' => $item->eselon,
                    'jabatan' => $item->jabatan,
                    'pd' => $item->pd,
                    'sub_pd' => $item->sub_pd,
                    'jenikel' => $item->jenikel,
                    'sts_peg' => $item->sts_peg,
                    'tk_pend' => $item->tk_pend,
                    'golongan' => $item->golongan,
                    'no_hp' => $item->no_hp,
                    'last_sync_at' => $item->last_sync_at,
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
