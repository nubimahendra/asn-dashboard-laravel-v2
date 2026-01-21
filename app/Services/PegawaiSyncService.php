<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\SnapshotPegawai;
use Carbon\Carbon;
use Exception;

class PegawaiSyncService
{
    public function sync()
    {
        try {
            $data = DB::connection('sidawai')->table('export_pegawai')
                ->select('nip_baru', 'nama_pegawai', 'tgl_lahir', 'eselon', 'jabatan', 'pd', 'sub_pd', 'jenikel', 'sts_peg', 'tk_pend')
                ->get();

            if ($data->isEmpty()) {
                return [
                    'status' => 'warning',
                    'message' => 'No data found in source database.'
                ];
            }

            DB::transaction(function () use ($data) {
                // Convert collection to array for chunking
                $chunks = $data->chunk(100);
                $timestamp = Carbon::now();

                foreach ($chunks as $chunk) {
                    foreach ($chunk as $row) {
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
                                'last_sync_at' => $timestamp,
                            ]
                        );
                    }
                }

                // Delete records that are in local DB but not in the source data
                // Collect all valid NIPs from the source data
                $validNips = $data->pluck('nip_baru')->toArray();

                // Delete rows where nip_baru is NOT in validNips
                SnapshotPegawai::whereNotIn('nip_baru', $validNips)->delete();
            });

            return [
                'status' => 'success',
                'count' => $data->count(),
                'message' => 'Data successfully synced.'
            ];

        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Sync failed: ' . $e->getMessage()
            ];
        }
    }
}
