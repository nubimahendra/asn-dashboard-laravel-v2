<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pegawai;
use App\Models\RefEselonMapping;
use App\Models\RefUnor;

class CheckTanpaOpd extends Command
{
    protected $signature = 'debug:check-tanpa-opd';
    protected $description = 'Check Tanpa OPD root cause';

    public function handle()
    {
        $total = Pegawai::aktif()->count();
        $this->info("Total Pegawai Aktif: $total");

        $tanpaUnorId = Pegawai::aktif()->whereNull('unor_id')->count();
        $this->info("Tanpa unor_id (NULL): $tanpaUnorId");

        $unorDeleted = Pegawai::aktif()->whereNotNull('unor_id')->whereDoesntHave('unor')->count();
        $this->info("unor_id ada tapi ref_unor deleted/missing: $unorDeleted");

        $unorNamaEmpty = Pegawai::aktif()->whereHas('unor', function($q) {
            $q->where('nama', '')->orWhereNull('nama');
        })->count();
        $this->info("unor exists tapi nama NULL/empty: $unorNamaEmpty");

        $this->info("Expected Tanpa OPD total: " . ($tanpaUnorId + $unorDeleted + $unorNamaEmpty));

        $struktural = Pegawai::aktif()->where('jenis_jabatan_id', '1')->count();
        $this->info("Pegawai Struktural (jenis_jabatan_id=1): $struktural");

        $eselonMappingCount = RefEselonMapping::count();
        $this->info("Eselon Mapping Count: $eselonMappingCount");

        $kh101 = Pegawai::where('kedudukan_hukum_id', '101')->count();
        $this->info("Kedudukan Hukum 101 count: $kh101");

        $withNama = Pegawai::aktif()->whereHas('unor', function($q) {
            $q->where('nama', '!=', '')->whereNotNull('nama');
        })->count();
        $this->info("Pegawai aktif with valid OPD: $withNama");

        // Samples
        $samples = Pegawai::aktif()->whereNull('unor_id')->limit(5)->get(['id', 'nama', 'nip_baru', 'unor_id', 'jabatan_id', 'jenis_jabatan_id', 'kedudukan_hukum_id']);
        $this->info("\nSample pegawai tanpa unor_id:");
        foreach ($samples as $s) {
            $this->line("  ID={$s->id} NIP={$s->nip_baru} Nama={$s->nama} KH={$s->kedudukan_hukum_id}");
        }

        $samples2 = Pegawai::aktif()->whereNotNull('unor_id')->whereDoesntHave('unor')->limit(5)->get(['id', 'nama', 'nip_baru', 'unor_id']);
        $this->info("\nSample pegawai with unor_id but unor missing:");
        foreach ($samples2 as $s) {
            $this->line("  ID={$s->id} NIP={$s->nip_baru} Nama={$s->nama} unor_id={$s->unor_id}");
        }

        $jenisJabatan = Pegawai::aktif()->select('jenis_jabatan_id')->distinct()->pluck('jenis_jabatan_id');
        $this->info("\nDistinct jenis_jabatan_id: " . $jenisJabatan->implode(', '));

        $sampleJabatan = Pegawai::aktif()->where('jenis_jabatan_id', '1')->with('jabatan')->limit(20)->get();
        $this->info("\nSample jabatan struktural:");
        foreach ($sampleJabatan as $p) {
            $this->line("  Jabatan: " . ($p->jabatan->nama ?? 'null') . " | jabatan_id: {$p->jabatan_id}");
        }

        // Check how Tanpa OPD appears in the actual calculation
        $this->info("\n--- Breakdown by kedudukan_hukum_id for Tanpa OPD pegawai ---");
        $byKH = Pegawai::aktif()
            ->where(function($q) {
                $q->whereNull('unor_id')
                  ->orWhereDoesntHave('unor');
            })
            ->selectRaw('kedudukan_hukum_id, count(*) as cnt')
            ->groupBy('kedudukan_hukum_id')
            ->get();
        foreach ($byKH as $row) {
            $this->line("  KH={$row->kedudukan_hukum_id}: {$row->cnt}");
        }

        // Check what the "both PNS+PPPK" filter returns for Tanpa OPD
        $bothFilter = Pegawai::aktif()
            ->where(function($q) {
                $q->where('kedudukan_hukum_id', '!=', '101')->orWhereNull('kedudukan_hukum_id');
            })
            ->where(function($q) {
                $q->whereNull('unor_id')->orWhereDoesntHave('unor');
            })
            ->count();
        $this->info("Tanpa OPD with PNS+PPPK filter (excl KH=101): $bothFilter");

        return 0;
    }
}
