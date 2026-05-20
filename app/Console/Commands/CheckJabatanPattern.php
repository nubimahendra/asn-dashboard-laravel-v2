<?php
// Run via: php artisan tinker < check_jabatan.php
// (Oh wait, powershell doesn't like <, so I will make an artisan command)

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RefJabatan;
use App\Models\Pegawai;

class CheckJabatanPattern extends Command
{
    protected $signature = 'debug:check-jabatan';
    protected $description = 'Check jabatan pattern';

    public function handle()
    {
        $jabatans = RefJabatan::where('nama', 'LIKE', '%Kelurahan%')
            ->orWhere('nama', 'LIKE', '%Kecamatan%')
            ->orWhere('nama', 'LIKE', '%Inspektur Pembantu%')
            ->orWhere('nama', 'LIKE', '%Inspektorat Pembantu%')
            ->orWhere('nama', 'LIKE', '%Wakil Direktur%')
            ->orWhere('nama', 'LIKE', '%Kepala Sub Bagian Penyusun Program%')
            ->orWhere('nama', 'LIKE', 'Sekretaris')
            ->get();
            
        foreach($jabatans as $j) {
            $this->line($j->nama);
        }
    }
}
