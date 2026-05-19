<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackfillGolAkhirFromStaging extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:backfill-gol-akhir';
    protected $description = 'Backfill gol_akhir in pegawai table from the latest staging data';

    public function handle()
    {
        $this->info('Starting backfill of gol_akhir...');

        $latestStaging = \Illuminate\Support\Facades\DB::table('stg_pegawai_import')
            ->select('pns_id', 'gol_akhir')
            ->whereNotNull('gol_akhir')
            ->whereNotNull('pns_id')
            ->orderBy('imported_at', 'desc')
            ->get()
            ->unique('pns_id');

        $bar = $this->output->createProgressBar(count($latestStaging));
        $bar->start();

        $updated = 0;
        foreach ($latestStaging as $stg) {
            $affected = \App\Models\Pegawai::where('pns_id', $stg->pns_id)
                ->update(['gol_akhir' => $stg->gol_akhir]);
            
            if ($affected > 0) {
                $updated++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Completed! Updated {$updated} records.");
    }
}
