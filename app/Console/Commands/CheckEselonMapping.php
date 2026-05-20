<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RefEselonMapping;
use App\Models\RefJabatan;
use Illuminate\Support\Facades\DB;

class CheckEselonMapping extends Command
{
    protected $signature = 'debug:check-eselon';
    protected $description = 'Check eselon mapping data';

    public function handle()
    {
        // Distribution of eselon_key
        $dist = RefEselonMapping::select('eselon_key', DB::raw('count(*) as cnt'))
            ->groupBy('eselon_key')
            ->orderBy('eselon_key')
            ->get();
        $this->info("Eselon Mapping Distribution:");
        foreach ($dist as $r) {
            $this->line("  {$r->eselon_key}: {$r->cnt}");
        }

        // Sample mappings with jabatan names
        $samples = RefEselonMapping::with('jabatan')->limit(30)->get();
        $this->info("\nSample Eselon Mappings (first 30):");
        foreach ($samples as $m) {
            $this->line("  [{$m->eselon_key}] {$m->jabatan->nama ?? '???'} (auto={$m->is_auto})");
        }

        // Unmapped jabatan of struktural pegawai
        $mappedIds = RefEselonMapping::pluck('jabatan_id');
        $unmapped = \App\Models\Pegawai::aktif()
            ->where('jenis_jabatan_id', '1')
            ->whereNotIn('jabatan_id', $mappedIds)
            ->with('jabatan')
            ->limit(20)
            ->get();
        $this->info("\nUnmapped struktural jabatan (pegawai with jenis_jabatan=1 but no mapping):");
        foreach ($unmapped as $p) {
            $this->line("  jabatan_id={$p->jabatan_id} -> " . ($p->jabatan->nama ?? '???'));
        }
        $this->info("Total unmapped struktural: " . \App\Models\Pegawai::aktif()->where('jenis_jabatan_id', '1')->whereNotIn('jabatan_id', $mappedIds)->count());

        // Iuran eselon rates
        $rates = \App\Models\RefIuranEselon::orderBy('id')->get();
        $this->info("\nRef Iuran Eselon (tarif):");
        foreach ($rates as $r) {
            $this->line("  {$r->eselon_key} | {$r->label} | Rp " . number_format($r->besaran));
        }

        return 0;
    }
}
