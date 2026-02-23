<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\SnapshotPegawai;
use Carbon\Carbon;
use Exception;

class PegawaiSyncService
{
    public function countSource()
    {
        return DB::connection('sidawai')->table('export_pegawai')->count();
    }

    public function syncBatch($limit, $offset)
    {
        try {
            $data = DB::connection('sidawai')->table('export_pegawai')
                ->select('nip_baru', 'nama_pegawai', 'tgl_lahir', 'eselon', 'jabatan', 'pd', 'sub_pd', 'jenikel', 'sts_peg', 'tk_pend', 'golru', 'unor_id')
                ->orderBy('nip_baru')
                ->offset($offset)
                ->limit($limit)
                ->get();

            if ($data->isEmpty()) {
                return 0; // No more data
            }

            $timestamp = Carbon::now();

            foreach ($data as $row) {
                SnapshotPegawai::updateOrCreate(
                    ['nip_baru' => $row->nip_baru],
                    [
                        'nama_pegawai' => $row->nama_pegawai,
                        'tgl_lahir' => $row->tgl_lahir,
                        'eselon' => $row->eselon,
                        'jabatan' => $row->jabatan,
                        'pd' => $row->pd,
                        'sub_pd' => $row->sub_pd,
                        'jenikel' => $row->jenikel,
                        'sts_peg' => $row->sts_peg,
                        'tk_pend' => $row->tk_pend,
                        'golongan' => $row->golru,
                        'unor_id' => $row->unor_id ?: null,
                        'last_sync_at' => $timestamp,
                    ]
                );
            }

            return $data->count();

        } catch (Exception $e) {
            throw $e;
        }
    }

    public function cleanup()
    {
        try {
            // Fetch ALL valid NIPs from source (lightweight query, just strings)
            // Even 20k NIPs is small enough for memory (~2-3MB max)
            $validNips = DB::connection('sidawai')->table('export_pegawai')
                ->pluck('nip_baru')
                ->toArray();

            if (empty($validNips)) {
                return 0;
            }

            // Delete rows where nip_baru is NOT in validNips
            return SnapshotPegawai::whereNotIn('nip_baru', $validNips)->delete();

        } catch (Exception $e) {
            throw $e;
        }
    }
}
