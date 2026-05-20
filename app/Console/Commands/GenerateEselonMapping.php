<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RefJabatan;
use App\Models\RefEselonMapping;
use App\Models\Pegawai;

class GenerateEselonMapping extends Command
{
    protected $signature = 'eselon:generate-mapping {--dry-run : Preview tanpa insert}';
    protected $description = 'Generate eselon mapping based on jabatan name';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $strukturalJabatanIds = Pegawai::aktif()
            ->where('jenis_jabatan_id', '1') // 1 = struktural
            ->pluck('jabatan_id')
            ->filter()
            ->unique();
            
        $jabatans = RefJabatan::whereIn('id', $strukturalJabatanIds)->get();
        $mappedCount = 0;
        
        foreach ($jabatans as $jabatan) {
            $name = strtolower($jabatan->nama);
            $eselon = 'IV/b'; // Default

            // Pattern Matching
            if (str_starts_with($name, 'sekretaris daerah')) {
                $eselon = 'II/a';
            } elseif (str_starts_with($name, 'kepala dinas') || str_starts_with($name, 'kepala badan') || str_starts_with($name, 'inspektur') || str_starts_with($name, 'staf ahli')) {
                $eselon = 'II/b';
            } elseif (str_starts_with($name, 'kepala bagian') || str_starts_with($name, 'camat') || str_starts_with($name, 'sekretaris dinas') || str_starts_with($name, 'sekretaris badan') || str_starts_with($name, 'wakil direktur') || str_starts_with($name, 'inspektorat pembantu') || str_starts_with($name, 'inspektur pembantu') || $name === 'sekretaris') {
                $eselon = 'III/a';
            } elseif (str_starts_with($name, 'sekretaris camat') || str_starts_with($name, 'sekretaris kecamatan') || str_starts_with($name, 'kepala bidang')) {
                $eselon = 'III/b';
            } elseif (str_starts_with($name, 'kepala sub bagian penyusun program dan keuangan') || (str_starts_with($name, 'kepala seksi') && str_contains($name, 'kelurahan')) || (str_starts_with($name, 'sekretaris') && str_contains($name, 'kelurahan'))) {
                $eselon = 'IV/b';
            } elseif (str_starts_with($name, 'kepala upt') || str_starts_with($name, 'kepala seksi') || str_starts_with($name, 'lurah') || str_starts_with($name, 'kepala kelurahan') || str_starts_with($name, 'kepala sub bagian') || str_starts_with($name, 'kepala subbidang') || str_starts_with($name, 'kepala sub bidang')) {
                $eselon = 'IV/a';
            } elseif (str_starts_with($name, 'bupati') || str_starts_with($name, 'wakil bupati')) {
                continue; // Skip mapping for bupati
            } else {
                $eselon = 'IV/a'; // Default
            }

            if (!$dryRun) {
                // Only insert/update if is_auto is true or record doesn't exist
                $existing = RefEselonMapping::where('jabatan_id', $jabatan->id)->first();
                if (!$existing || $existing->is_auto) {
                    RefEselonMapping::updateOrCreate(
                        ['jabatan_id' => $jabatan->id],
                        ['eselon_key' => $eselon, 'is_auto' => true]
                    );
                    $mappedCount++;
                }
            } else {
                $this->info("[$eselon] {$jabatan->nama}");
            }
        }

        if (!$dryRun) {
            $this->info("Successfully mapped {$mappedCount} jabatans.");
        }
    }
}
