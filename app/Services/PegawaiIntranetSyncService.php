<?php

namespace App\Services;

use App\Models\SnapshotPegawai;
use Carbon\Carbon;

/**
 * Menyinkronkan satu pegawai dari database intranet Penataran ke tabel lokal
 * snapshot_pegawai secara on-demand (per NIP).
 */
class PegawaiIntranetSyncService
{
    public function __construct(
        protected IntranetKepegawaianService $intranet
    ) {
    }

    /**
     * Tarik satu pegawai dari intranet lalu simpan ke snapshot_pegawai.
     *
     * Mengembalikan null bila intranet tidak menemukan NIP tersebut (tidak ada
     * yang ditulis). RuntimeException dari IntranetKepegawaianService dibiarkan
     * naik agar pemanggil bisa membedakan source_error dari not_found.
     */
    public function syncByNip(string $nip): ?SnapshotPegawai
    {
        $row = $this->intranet->getPegawaiByNip($nip);

        if ($row === null) {
            return null;
        }

        $nipKanonis = $this->nipKanonis($row, $nip);

        return SnapshotPegawai::updateOrCreate(
            ['nip_baru' => $nipKanonis],
            $this->mapRow($row, $nipKanonis)
        );
    }

    /**
     * NIP kanonis baris intranet: nip_baru, lalu nip, lalu NIP yang dicari.
     *
     * @param  array<string, mixed>  $row
     */
    private function nipKanonis(array $row, string $nip): string
    {
        foreach (['nip_baru', 'nip'] as $kolom) {
            $nilai = trim((string) ($row[$kolom] ?? ''));
            if ($nilai !== '') {
                return $nilai;
            }
        }

        return $nip;
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    private function mapRow(array $row, string $nipKanonis): array
    {
        $nama = trim((string) ($row['nama'] ?? ''));
        if ($nama === '') {
            $nama = $this->namaTanpaGelar($row);
        }

        return [
            'nip_baru' => $nipKanonis,
            'nama_pegawai' => $nama,
            'gelar_depan' => $row['gelar_depan'] ?? null,
            'gelar_belakang' => $row['gelar_belakang'] ?? null,
            'golongan' => $row['gol_ruang_akhir'] ?? null,
            'jabatan' => $row['jabatan_nama'] ?? null,
            'eselon' => $row['eselon'] ?? null,
            'pd' => $row['unor_induk_nama'] ?? null,
            'sub_pd' => $row['unor_nama'] ?? null,
            'jenikel' => $row['jenis_kelamin'] ?? null,
            'sts_peg' => $row['status_pegawai'] ?? null,
            'tk_pend' => $row['tk_pendidikan_terakhir'] ?? null,
            'tgl_lahir' => $row['tgl_lahir'] ?? null,
            'no_hp' => $row['no_hp'] ?? null,
            'unor_id' => $row['unor_id'] ?? null,
            'mk_tahun' => $row['mk_tahun'] ?? null,
            'mk_bulan' => $row['mk_bulan'] ?? null,
            'kedudukan_hukum_id' => $row['kedudukan_pns_id'] ?? null,
            'kedudukan_hukum' => $row['kedudukan_pns_nama'] ?? null,
            'jenis_pegawai' => $row['jenis_pegawai_nama'] ?? null,
            'sumber' => 'intranet_penataran',
            'last_sync_at' => Carbon::now(),
        ];
    }

    /**
     * Ambil nama_lengkap lalu buang gelar depan/belakang darinya.
     *
     * @param  array<string, mixed>  $row
     */
    private function namaTanpaGelar(array $row): string
    {
        $nama = trim((string) ($row['nama_lengkap'] ?? ''));

        foreach (['gelar_depan', 'gelar_belakang'] as $kolom) {
            $gelar = trim((string) ($row[$kolom] ?? ''));
            if ($gelar !== '') {
                $nama = str_replace($gelar, '', $nama);
            }
        }

        return trim(preg_replace('/\s+/', ' ', $nama));
    }
}
