<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pegawai;
use App\Models\RefGolongan;
use Illuminate\Support\Facades\Log;

class SyncGolonganPegawai extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pegawai:sync-golongan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync golongan_id for pegawai based on data from staging import';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting sync golongan pegawai...');

        // In this specific scenario, since we know there is no III/a from the staging import,
        // and we just seeded ref_golongan, we might want a general mechanism
        // But since we want to sync based on stg_pegawai_import for all records

        // Actually, we can fetch all pegawai, get their pns_id, find them in stg_pegawai_import,
        // and update their golongan_id. Or we can just use the latest stg_pegawai_import data.

        $this->info('Fetching data from stg_pegawai_import...');
        $pegawais = Pegawai::all();
        $updatedCount = 0;

        foreach ($pegawais as $pegawai) {
            $staging = \Illuminate\Support\Facades\DB::table('stg_pegawai_import')
                ->where('pns_id', $pegawai->pns_id)
                ->orderBy('imported_at', 'desc')
                ->first();

            if ($staging && $staging->gol_akhir_id) {
                // Determine if this ref_golongan exists
                $ref = RefGolongan::find($staging->gol_akhir_id);
                if ($ref) {
                    if ($pegawai->golongan_id !== $staging->gol_akhir_id) {
                        $pegawai->golongan_id = $staging->gol_akhir_id;
                        $pegawai->save();
                        $updatedCount++;
                        $this->line("Updated {$pegawai->nama} with golongan {$staging->gol_akhir}");
                    }
                }
            }
        }

        $this->info("Completed sync. Updated {$updatedCount} records.");
        return 0;
    }
}
