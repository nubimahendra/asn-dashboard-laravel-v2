<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Akses READ-ONLY ke database intranet Penataran (PostgreSQL) untuk mencari
 * data pegawai berdasarkan NIP. Service ini TIDAK PERNAH menulis ke intranet.
 */
class IntranetKepegawaianService
{
    /**
     * Nama koneksi database di config/database.php
     */
    protected string $connection = 'intranet_db';

    /**
     * Nama tabel kepegawaian di database intranet
     */
    protected string $tableName;

    public function __construct()
    {
        $this->tableName = env('INTRANET_DB_TABLE', 'pegawai');
    }

    /**
     * Cek apakah konfigurasi database intranet aktif.
     */
    public function isConfigured(): bool
    {
        $cfg = config("database.connections.{$this->connection}");

        return ! empty($cfg['host']) && ! empty($cfg['database']) && ! empty($cfg['username']);
    }

    /**
     * Ambil data satu pegawai berdasarkan NIP langsung dari DB PostgreSQL Intranet.
     */
    public function getPegawaiByNip(string $nip): ?array
    {
        $cfg = config("database.connections.{$this->connection}");
        if (empty($cfg['password'])) {
            throw new \RuntimeException('Password database intranet belum diisi pada file .env (INTRANET_DB_PASSWORD). Silakan isi password pada file .env.');
        }

        $cleanNip = trim($nip);

        try {
            $row = DB::connection($this->connection)
                ->table($this->tableName)
                ->where('nip', $cleanNip)
                ->orWhere('nip_baru', $cleanNip)
                ->orWhere('nip_lama', $cleanNip)
                ->first();

            if (! $row) {
                return null;
            }

            return (array) $row;
        } catch (\Throwable $e) {
            Log::error("Gagal query database intranet ({$this->connection}) untuk NIP {$cleanNip}: ".$e->getMessage());

            if (str_contains($e->getMessage(), 'permission denied')) {
                throw new \RuntimeException('Akses ditolak (permission denied) pada tabel '.$this->tableName.'. User '.$cfg['username'].' memerlukan izin GRANT SELECT pada tabel tersebut.');
            }

            throw $e;
        }
    }
}
