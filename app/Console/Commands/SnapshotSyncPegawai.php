<?php

namespace App\Console\Commands;

use App\Services\PegawaiSyncService;
use Illuminate\Console\Command;
use Throwable;

class SnapshotSyncPegawai extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'snapshot:sync-pegawai
                            {--chunk=500 : Jumlah baris sumber yang diproses per batch}
                            {--no-cleanup : Lewati pembersihan snapshot yang NIP-nya sudah tidak ada di sumber}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tarik snapshot ringkas pegawai dari DB Sidawai (tabel export_pegawai) ke tabel snapshot_pegawai';

    /**
     * Execute the console command.
     */
    public function handle(PegawaiSyncService $service)
    {
        $chunk = (int) $this->option('chunk');

        if ($chunk < 1) {
            $this->error('Opsi --chunk harus bilangan bulat minimal 1.');

            return self::FAILURE;
        }

        $this->info('Mulai sync snapshot pegawai dari sumber Sidawai...');

        try {
            $totalSource = $service->countSource();
        } catch (Throwable $e) {
            $this->error('Gagal membaca sumber Sidawai (tabel export_pegawai).');
            $this->line('Periksa koneksi "sidawai" dan env DB_REMOTE_HOST / DB_REMOTE_PORT / DB_REMOTE_DATABASE / DB_REMOTE_USERNAME / DB_REMOTE_PASSWORD.');
            $this->line('Detail: ' . $this->sanitize($e->getMessage()));

            return self::FAILURE;
        }

        $this->line("Total baris di sumber: {$totalSource}");

        $offset = 0;
        $read = 0;
        $synced = 0;
        $batch = 0;

        while (true) {
            try {
                $count = $service->syncBatch($chunk, $offset);
            } catch (Throwable $e) {
                $this->error("Gagal sync pada offset {$offset}.");
                $this->line('Detail: ' . $this->sanitize($e->getMessage()));

                return self::FAILURE;
            }

            if ($count === 0) {
                break;
            }

            $batch++;
            $read += $count;
            $synced += $count;
            $offset += $count;

            $this->line("Batch {$batch}: {$count} baris tersync (offset akhir {$offset}, total tersync {$synced}" . ($totalSource > 0 ? " dari {$totalSource}" : '') . ').');

            if ($count < $chunk) {
                break;
            }
        }

        $this->info("Selesai membaca sumber: {$read} baris terbaca, {$synced} baris tersync dalam {$batch} batch.");

        if ($this->option('no-cleanup')) {
            $this->warn('Cleanup dilewati (--no-cleanup). Snapshot yang NIP-nya sudah tidak ada di sumber dibiarkan.');

            return self::SUCCESS;
        }

        try {
            $deleted = $service->cleanup();
        } catch (Throwable $e) {
            $this->error('Gagal membersihkan snapshot yang NIP-nya sudah tidak ada di sumber.');
            $this->line('Detail: ' . $this->sanitize($e->getMessage()));

            return self::FAILURE;
        }

        $this->info("Cleanup: {$deleted} baris snapshot dihapus.");

        return self::SUCCESS;
    }

    /**
     * Hapus kredensial (password koneksi sidawai) dari pesan error sebelum ditampilkan.
     */
    protected function sanitize(string $message): string
    {
        $password = (string) config('database.connections.sidawai.password', '');

        if ($password !== '') {
            $message = str_replace($password, '***', $message);
        }

        return $message;
    }
}
