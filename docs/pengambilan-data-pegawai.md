# Pengambilan Data Pegawai (Opsi C)

Dokumen ini mencatat keputusan produk dan cara teknis pengambilan data pegawai di
aplikasi ini. Ada **dua jalur** dengan peran yang berbeda dan tidak saling menggantikan.

## 1. Keputusan: Opsi C

- **Master pegawai** hanya diisi lewat **import CSV di halaman Sync Data** (jalur resmi).
- **Snapshot ringkas** (`snapshot_pegawai`) boleh diisi langsung dari DB Sidawai sebagai
  **pelengkap**, lewat Artisan command `snapshot:sync-pegawai`.

Alasannya: master pegawai punya banyak kolom turunan dan aturan validasi yang hanya
dijaga oleh alur import CSV (staging → diff → konfirmasi). Menyedot master langsung dari
DB sumber akan melewati aturan itu. Sebaliknya, snapshot ringkas hanya butuh beberapa
kolom dan memang bersifat "foto" data sumber, jadi aman ditarik langsung.

## 2. Jalur utama: import CSV lewat Sync Data

Halaman: **Sync Data** (`/sync-data`, route `sync.index`).

Alur: unggah CSV → baris masuk tabel staging (`stg_pegawai_import`) → lihat diff/anomali →
konfirmasi → job import memperbarui tabel `pegawai` (master).

- Ini satu-satunya cara sah mengubah **master pegawai**.
- Perilaku alur ini tidak berubah oleh paket Opsi C.

## 3. Jalur pelengkap: `snapshot:sync-pegawai`

```bash
php artisan snapshot:sync-pegawai
php artisan snapshot:sync-pegawai --chunk=1000
php artisan snapshot:sync-pegawai --no-cleanup
```

Yang dilakukan command:

1. Menghitung total baris sumber (`export_pegawai` di koneksi `sidawai`).
2. Menarik data per batch (`--chunk`, default 500) memakai
   `PegawaiSyncService::syncBatch()`, lalu `updateOrCreate` per `nip_baru` ke
   `snapshot_pegawai` (kolom `golongan` diisi dari `golru`, plus `unor_id`).
3. Kecuali `--no-cleanup`, menjalankan `cleanup()`: menghapus baris snapshot yang
   `nip_baru`-nya sudah tidak ada lagi di sumber.
4. Menampilkan progres per batch dan ringkasan akhir (total di sumber, total terbaca,
   total tersync, jumlah baris yang dibersihkan).

Syarat pemakaian:

- Dijalankan di **VPS/server aplikasi**, bukan di laptop developer — server itu yang
  punya rute jaringan ke DB Sidawai.
- Koneksi `sidawai` di `config/database.php` terisi lewat env:
  `DB_REMOTE_HOST`, `DB_REMOTE_PORT`, `DB_REMOTE_DATABASE`, `DB_REMOTE_USERNAME`,
  `DB_REMOTE_PASSWORD`.
- Tabel sumber `export_pegawai` berisi kolom: `nip_baru`, `nama_pegawai`, `tgl_lahir`,
  `eselon`, `jabatan`, `pd`, `sub_pd`, `jenikel`, `sts_peg`, `tk_pend`, `golru`,
  `unor_id`.

Sifat operasi: **idempoten** — aman dijalankan berulang; baris dengan `nip_baru` sama
diperbarui, bukan diduplikasi. Bila koneksi sumber gagal, command berhenti dengan status
non-zero dan pesan yang tidak memuat kredensial.

Catatan: `cleanup()` tidak menghapus apa pun bila sumber mengembalikan nol NIP — supaya
gangguan di sisi sumber tidak mengosongkan snapshot.

## 4. Peringatan penting

`snapshot:sync-pegawai` **hanya mengisi tabel `snapshot_pegawai`**. Command ini
**BUKAN** pengganti import CSV dan **tidak menyentuh tabel `pegawai`** (master).
Jangan memakai snapshot sebagai sumber kebenaran master pegawai; untuk itu tetap
lewat halaman Sync Data.

## 5. Perubahan skema terkait

Migrasi `2026_11_05_220716_add_unor_id_to_snapshot_pegawai_table` menambahkan kolom
`unor_id` (string, nullable) ke `snapshot_pegawai`. Sebelum migrasi ini, service sync
sudah menulis `unor_id` sehingga sync gagal dengan error SQL.
