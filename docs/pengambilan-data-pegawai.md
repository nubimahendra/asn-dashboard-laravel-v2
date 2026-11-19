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

## 6. Pencarian pegawai SLKS on-demand dari database intranet Penataran

Bagian ini menambahkan **jalur ketiga** yang khusus dipakai oleh pencarian NIP di modul
SIPUT (form Usul SLKS). Jalur CSV dan `snapshot:sync-pegawai` pada bagian 1–5 tetap ada
untuk kebutuhan lain; yang diganti hanyalah **cara pencarian pegawai SLKS mencari data**.

### 6.1 Keputusan: satu rantai data

Urusan ke BKN adalah urusan **penataran-api** saja — dialah yang memegang whitelist IP
dan token ketat ke BKN. Rantainya:

```
BKN  →  penataran-api  →  db_penataran.pegawai (PostgreSQL)  →  aplikasi hilir
```

Aplikasi hilir (si-mantap, ASN Dashboard) **tidak** memanggil BKN dan **tidak** memanggil
HTTP penataran-api. Aplikasi hilir hanya membaca data pegawai yang sudah disinkronkan
penataran-api ke database intranet Penataran (`db_penataran`, tabel `pegawai`),
**read-only**, per NIP.

Konsekuensinya: kesegaran data = kesegaran sync Penataran. Pegawai yang belum pernah
disinkronkan Penataran ke `db_penataran.pegawai` tidak akan ketemu di ASN Dashboard, dan
ASN Dashboard **tidak** memicu sync BKN. Itu memang desain satu rantai ini.

### 6.2 Alur pencarian SLKS (`UsulSlksController::searchPegawai`)

1. **Snapshot dulu.** Cari di `snapshot_pegawai` berdasarkan `nip_baru`. Bila ketemu,
   respons disusun dari snapshot (`source: snapshot`): nama lengkap =
   `gelar_depan` + `nama_pegawai` + `gelar_belakang`, pangkat =
   `getPangkatFromGolongan(golongan)`, kedudukan dari kolom `kedudukan_hukum_*`.
2. **Belum ada → tarik on-demand.** Panggil `PegawaiIntranetSyncService::syncByNip()`
   yang membaca satu baris `db_penataran.pegawai` lalu menulis ke `snapshot_pegawai`
   (`source: intranet_penataran`). Bila berhasil, respons disusun dari hasil simpan itu.
3. **NIP tidak ada di intranet** → `found: false`, `reason: not_found`, tidak ada baris
   baru.
4. **Sumber gagal** (belum terkonfigurasi / tidak terjangkau / permission denied) →
   `found: false`, `reason: source_error`, disertai `message` ramah. Ini dibedakan dari
   NIP yang memang tidak ditemukan.
5. **Filter kedudukan aktif.** Bila `kedudukan_hukum_id` ada dan tidak termasuk
   `Pegawai::ACTIVE_KEDUDUKAN_HUKUM` (`01, 02, 03, 04, 101, 15, 71, 73`) →
   `found: false`, `reason: not_active`.
6. **Fallback transisi.** Bila langkah 2 gagal dan baris lama masih ada di
   `stg_pegawai_import`, baris lama itu dipakai (`source: stg_fallback`). Fallback ini
   bersifat sementara dan akan dicabut pada paket berikutnya.

Kolom `riwayat` tetap diambil dari tabel `usul_slks` seperti sebelumnya.

### 6.3 Peta kolom intranet → snapshot

| snapshot_pegawai      | db_penataran.pegawai                          |
|-----------------------|-----------------------------------------------|
| `nip_baru`            | `nip_baru` (fallback `nip`)                   |
| `nama_pegawai`        | `nama` (fallback `nama_lengkap` tanpa gelar)  |
| `gelar_depan`         | `gelar_depan`                                 |
| `gelar_belakang`      | `gelar_belakang`                              |
| `golongan`            | `gol_ruang_akhir`                             |
| `jabatan`             | `jabatan_nama`                                |
| `eselon`              | `eselon`                                      |
| `pd`                  | `unor_induk_nama`                             |
| `sub_pd`              | `unor_nama`                                   |
| `jenikel`             | `jenis_kelamin`                               |
| `sts_peg`             | `status_pegawai`                              |
| `tk_pend`             | `tk_pendidikan_terakhir`                      |
| `tgl_lahir`           | `tgl_lahir`                                   |
| `no_hp`               | `no_hp`                                       |
| `unor_id`             | `unor_id`                                     |
| `mk_tahun`            | `mk_tahun`                                    |
| `mk_bulan`            | `mk_bulan`                                    |
| `kedudukan_hukum_id`  | `kedudukan_pns_id`                            |
| `kedudukan_hukum`     | `kedudukan_pns_nama`                          |
| `jenis_pegawai`       | `jenis_pegawai_nama`                          |
| `sumber`              | diisi tetap `intranet_penataran`              |

`IntranetKepegawaianService` bersifat **READ-ONLY mutlak** — tidak pernah menulis ke
koneksi `intranet_db`.

### 6.4 Syarat produksi

- VPS aplikasi berada di jaringan Kominfo sehingga bisa menjangkau host DB intranet
  (default `192.168.117.5:5432`).
- User DB **read-only** (`user_read_only`) dan punya `GRANT SELECT` pada tabel `pegawai`.
- Env `INTRANET_DB_*` diisi di `.env` server (host, port, database, username, password,
  schema, sslmode, table).
- Ekstensi PHP **`pgsql` + `pdo_pgsql`** tersedia di image aplikasi (Dockerfile sudah
  menambahkannya; image perlu di-rebuild).

### 6.5 Catatan skema

Migrasi `2026_11_19_000000_add_intranet_fields_to_snapshot_pegawai_table` menambahkan
kolom `gelar_depan`, `gelar_belakang`, `mk_tahun`, `mk_bulan`, `kedudukan_hukum_id`,
`kedudukan_hukum`, `jenis_pegawai`, dan `sumber` ke `snapshot_pegawai`.
