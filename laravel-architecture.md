# Laravel Architecture — ASN Dashboard v2

Dokumen ini merangkum pola koding, struktur database, dan konvensi routing yang dipakai di proyek ini.
Tujuannya buat referensi tim dan AI yang lanjut kerja di sini.

---

## 1. Struktur Direktori `app/`

```
app/
├── Console/           # Artisan commands (jika ada)
├── Exports/           # Kelas export (Excel, PDF)
├── Helpers/           # Static utility class (GolonganHelper)
├── Http/
│   ├── Controllers/   # 22 controller, satu per domain/fitur
│   └── Middleware/    # CheckModuleAccess, IsAdmin
├── Imports/           # Kelas import (Laravel Excel)
├── Jobs/              # Background job (ProcessPegawaiImport)
├── Models/            # 42 model Eloquent
├── Providers/         # AppServiceProvider, dll.
├── Services/          # 10 service class (logika berat)
└── View/              # View composers (jika ada)
```

---

## 2. Pola Koding

### 2.1 Controller

- **Satu controller per domain fitur**, misal: `IuranKorpriController`, `PegawaiImportController`, `DashboardController`.
- Controller **tidak berat logika** — kalau perhitungan kompleks, dipecah ke **private method** di controller itu sendiri (`calculateRealtime`, `formatArsipData`), atau kalau lintas-controller dipindah ke **Service class**.
- **Response pattern:**
  - View biasa → `return view('nama.view', compact(...))`
  - Ajax/API JSON → `return response()->json(['success' => true, 'message' => '...'])`
  - Cek `$request->ajax()` untuk partial view (contoh di `DashboardController::index`)
- **Transaksi database** selalu dibungkus `DB::beginTransaction()` / `DB::commit()` / `DB::rollBack()` pada operasi write yang critical (simpan, update massal).
- Validasi input pakai `$request->validate([...])` langsung di method controller.

**Contoh pola controller yang dipakai:**

```php
public function update(Request $request)
{
    $request->validate(['rates' => 'required|array', ...]);

    DB::beginTransaction();
    try {
        // tulis ke DB
        DB::commit();
        return response()->json(['success' => true, 'message' => '...']);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
```

### 2.2 Model

- Semua model pakai **`$fillable`** eksplisit (bukan `$guarded`).
- **`$casts`** dipakai untuk kolom date/datetime dan JSON (misal: `'modules' => 'array'`, `'breakdown_golongan' => 'array'`).
- **Tabel referensi** (prefix `Ref`) punya primary key `string`, bukan `bigint`, karena ID aslinya dari SIASN (misal: `'01'`, `'71'`, `'IV/a'`). Pola ini konsisten di seluruh tabel ref:
  ```php
  protected $keyType = 'string';
  public $incrementing = false;
  ```
- **Soft deletes** hanya ada di tabel referensi (`ref_agama`, `ref_golongan`, dll.). Tabel `pegawai` punya kolom `deleted_at` tapi **sudah tidak dipakai** — digantikan oleh `kedudukan_hukum_id = '17'` (Non Aktif).
- **Scope Eloquent** dipakai untuk filter yang sering diulang:
  ```php
  // Pegawai.php
  public function scopeAktif($query)
  {
      return $query->where(function ($q) {
          $q->whereIn('kedudukan_hukum_id', self::ACTIVE_KEDUDUKAN_HUKUM)
            ->orWhereNull('kedudukan_hukum_id');
      });
  }
  ```
  Dipanggil dengan: `Pegawai::aktif()->...`
- **Accessor** dipakai untuk logika display sederhana:
  - `getNamaLengkapAttribute()` — gabung gelar depan + nama + gelar belakang
  - `getGolonganPppkAttribute()` — ambil golongan dari `gol_akhir` langsung (hindari collision ID PPPK)
- Konstanta domain (misal `ACTIVE_KEDUDUKAN_HUKUM`) didefinisikan di dalam Model, bukan di config.

### 2.3 Service Class

Dipakai untuk logika yang **berat, lintas-model, atau multi-step**. Pattern-nya:

| Service | Tanggung Jawab |
|---|---|
| `PegawaiImportService` | Sync staging → pegawai, termasuk sync semua ref |
| `ReferenceSyncService` | `updateOrCreate` tiap tabel referensi dari data staging |
| `RiwayatSyncService` | Sync riwayat jabatan, golongan, pendidikan, status |
| `PegawaiDiffService` | Deteksi perubahan (changed/new/unchanged) antar import |
| `CsvImportService` | Parsing & validasi file CSV dari upload |
| `CsvSanitizerService` | Sanitasi karakter sebelum parsing CSV |
| `PegawaiSyncService` | Orkestrasi flow import end-to-end |
| `PegawaiValidationService` | Validasi data staging |
| `IuranKorpriGeneratorService` | Generate transaksi iuran per bulan |
| `ChatService` | Logika chatbot |

**Dependency injection** ke service lewat constructor (Laravel resolve otomatis):

```php
public function __construct(
    ReferenceSyncService $referenceSync,
    RiwayatSyncService $riwayatSync
) {
    $this->referenceSync = $referenceSync;
    $this->riwayatSync = $riwayatSync;
}
```

### 2.4 Jobs (Background Queue)

- Hanya ada satu Job: `ProcessPegawaiImport` (implements `ShouldQueue`).
- Job dipakai untuk proses sync CSV yang bisa memakan waktu lama.
- Pakai **`chunkById(500, ...)`** (bukan `chunk()`) untuk iterasi staging yang besar — mencegah data terlewat akibat cursor drift.
- Error per-record **tidak menghentikan job** — dicatch, di-log, dan dimark `is_processed = true` supaya progress bar tidak stuck.
- Status batch di-update secara real-time per chunk ke tabel `import_batches`.

### 2.5 Helper

- `App\Helpers\GolonganHelper::parseRoman(?string $key): float` — konversi golongan PNS/PPPK (misal `'III/b'`, `'IX'`) ke nilai float untuk sorting. Dipakai di controller dan model untuk `sortBy`.

### 2.6 Middleware

| Middleware | Alias | Fungsi |
|---|---|---|
| `CheckModuleAccess` | `module:xxx` | Cek apakah user punya akses ke modul (mari/masn/mesra) |
| `IsAdmin` | `admin` | Cek apakah `user->role === 'admin'` |

Logika akses modul ada di `User::hasModuleAccess(string $module)`:
- Role `admin` → bypass semua modul
- Role `user` → cek array `modules` di field user

---

## 3. Relasi Database

### 3.1 Skema Utama

```
stg_pegawai_import ──(batch_id)──> import_batches
       │
       │ (setelah sync)
       ▼
    pegawai ──(banyak FK)──> ref_* (tabel referensi)
       │
       ├──(hasMany)──> riwayat_jabatan
       ├──(hasMany)──> riwayat_golongan
       ├──(hasMany)──> riwayat_pendidikan
       ├──(hasMany)──> riwayat_status_pegawai
       └──(hasOne) ──> iuran_override
```

### 3.2 Tabel Referensi (`ref_*`)

Semua tabel ref punya struktur seragam:
- PK: `id` (string, dari SIASN)
- `nama` (string)
- `timestamps` + `softDeletes`

| Tabel | Dipakai Oleh |
|---|---|
| `ref_agama` | `pegawai.agama_id` |
| `ref_jenis_kawin` | `pegawai.jenis_kawin_id` |
| `ref_jenis_pegawai` | `pegawai.jenis_pegawai_id` |
| `ref_kedudukan_hukum` | `pegawai.kedudukan_hukum_id` |
| `ref_golongan` | `pegawai.golongan_id` |
| `ref_jabatan` | `pegawai.jabatan_id`, `riwayat_jabatan.jabatan_id` |
| `ref_jenis_jabatan` | `pegawai.jenis_jabatan_id` |
| `ref_pendidikan` | `pegawai.pendidikan_id` |
| `ref_tingkat_pendidikan` | `pegawai.tingkat_pendidikan_id` |
| `ref_unor` | `pegawai.unor_id` |
| `ref_instansi` | `pegawai.instansi_induk_id`, `pegawai.instansi_kerja_id` |
| `ref_lokasi` | `pegawai.lokasi_kerja_id` |
| `ref_kpkn` | `pegawai.kpkn_id` |

> **Catatan:** FK di tabel `pegawai` semuanya `onDelete('set null')` — supaya data pegawai tidak ikut hilang kalau referensi dihapus.

### 3.3 Tabel Modul MARI (Iuran Korpri)

```
iuran_korpri              — tarif per golongan (golongan_key: string)
ref_iuran_eselon          — tarif per eselon (eselon_key: string)
ref_eselon_mapping        — jabatan_id → eselon_key (untuk struktural)
ref_jabatan_kelas         — jabatan_id + unor_id → kelas_jabatan (mapping lama)
ref_jabatan_mapping       — jabatan_siasn_id → kelas_perbup_id (mapping baru, Perbup)
ref_kelas_perbup          — kelas jabatan berdasarkan Perbup
ref_jabatan_default       — fallback jabatan → kelas jika mapping tidak ada
iuran_override            — override manual per pegawai (golongan/eselon/opd)
iuran_override_log        — log historis perubahan override
iuran_korpri_transaksi    — transaksi iuran yang di-generate per bulan
rekap_iuran_bulanan       — snapshot rekap per OPD per bulan (ARSIP)
```

**Alur resolusi kelas jabatan** (di `IuranKorpriGeneratorService`):
1. Cek `ref_jabatan_mapping` (baru, Perbup) → `ref_kelas_perbup`
2. Fallback: `ref_jabatan_default`
3. Fallback: `ref_jabatan_kelas` (lama) dengan `unor_id`
4. Fallback: `ref_jabatan_kelas` (lama) tanpa `unor_id` (global)

**Logika iuran per pegawai:**
- Struktural PNS → basis **eselon** (`ref_eselon_mapping` → `ref_iuran_eselon`)
- Non-struktural / PPPK → basis **golongan** (`pegawai.gol_akhir` → `iuran_korpri`)
- Override per pegawai tersimpan di `iuran_override` (bisa override golongan, eselon, atau nama OPD)

### 3.4 Struktur Lengkap Tabel Referensi

Semua tabel ref (`ref_*`) memiliki skema yang seragam:

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | VARCHAR(50) PK | Kode dari SIASN (string, non-incrementing) |
| `nama` | VARCHAR(255) | Nama lengkap |
| `created_at` | TIMESTAMP | |
| `updated_at` | TIMESTAMP | |
| `deleted_at` | TIMESTAMP | SoftDeletes |

**Traits di Model Ref:**
```php
use HasFactory, SoftDeletes;
protected $keyType = 'string';
public $incrementing = false;
```

### 3.5 Complete Entity Relationship

```
pegawai ──hasMany──> riwayat_status_pegawai  (cascade)
pegawai ──hasMany──> riwayat_golongan         (cascade)
pegawai ──hasMany──> riwayat_jabatan          (cascade)
pegawai ──hasMany──> riwayat_pendidikan       (cascade)
pegawai ──hasMany──> iuran_korpri_transaksi   (cascade)
pegawai ──hasOne───> iuran_override           (cascade)
pegawai ──hasMany──> iuran_override_log       (cascade)

users ────hasMany──> chat_messages            (cascade)
users ────hasOne───> snapshot_pegawai         (via nip_baru)

pegawai ──belongsTo──> 13 tabel ref_*         (set null on delete)

ref_jabatan ──hasMany──> ref_eselon_mapping   (jabatan_id → eselon_key)
ref_jabatan ──hasMany──> ref_jabatan_kelas    (jabatan_id → kelas_jabatan)
ref_jabatan ──hasOne───> ref_jabatan_default  (jabatan_id → kelas_jabatan)
ref_jabatan ──hasMany──> ref_jabatan_mapping  (jabatan_siasn_id → kelas_perbup_id)

ref_unor ────hasMany──> ref_jabatan_kelas     (unor_id → kelas_jabatan)
ref_unor ────hasMany──> ref_opd_mapping       (unor_siasn_id → nama_opd_perbup)

ref_kelas_perbup ──hasMany──> ref_jabatan_mapping (kelas_perbup_id)
```

### 3.6 Namespacing ID Golongan (Mencegah Collision PNS vs PPPK)

SIASN memakai ID yang sama untuk golongan PNS dan PPPK. Solusinya:
- PNS: ID asli dari SIASN (misal `'2'`, `'3'`)
- PPPK: ID dinambahkan prefix `P` (misal `'P51'` = PPPK gol I, `'P52'` = PPPK gol II, dst.)
- Di `Pegawai` model, kolom `gol_akhir` (string teks, bukan FK) dipakai langsung untuk display/kalkulasi PPPK — hindari relasi `golongan_id` karena bisa salah mapping.
- `RefGolonganSeeder` men-seed 17 PNS + 17 PPPK records dengan ID yang sudah di-prefix.

### 3.7 Status Aktif Pegawai

Pegawai dianggap **aktif** jika `kedudukan_hukum_id` ada di daftar ini (atau NULL):

```
'01' = Aktif
'02' = CLTN
'03' = Tugas Belajar
'04' = Pemberhentian Sementara
'15' = Hukuman Disiplin
'71' = PPPK (aktif)
'73' = PPPK jenis lain
'101' = PPPK Purnawaktu
```

Pegawai dengan `kedudukan_hukum_id = '17'` dianggap **non-aktif** (pengganti soft delete).

---

## 4. Konvensi Routing

### 4.1 Struktur Umum

```php
// Auth (di luar guard)
Route::get('/login', ...)->name('login')->middleware('guest');
Route::post('/login', ...)->name('login.post')->middleware('guest');
Route::post('/logout', ...)->name('logout')->middleware('auth');

// Protected — semua di bawah auth
Route::middleware(['auth'])->group(function () {
    // Per modul, wrap dengan middleware module
    Route::middleware(['module:masn'])->group(function () {
        Route::prefix('masn')->name('masn.')->group(function () {
            // ...
        });
    });
});
```

### 4.2 Pola Naming Route

Format: **`{modul}.{resource}.{action}`**

Contoh:
```
masn.dashboard
masn.pegawai.import.index
masn.pegawai.import.upload
masn.snapshot.index
mari.iuran-korpri.index
mari.iuran-korpri.simpan
mari.rekon-iuran.index
mari.eselon-mapping.index
mesra.surat-masuk.index
```

### 4.3 Modul & Prefix

| Modul | Prefix URL | Middleware | Konten |
|---|---|---|---|
| MASN | `/masn` | `module:masn` | Dashboard ASN, sync data, snapshot |
| MARI | `/mari` | `module:mari` | Iuran Korpri, rekon, eselon mapping |
| MESRA | `/mesra` | `module:mesra` | Surat masuk, pengajuan cerai, chatbot |

### 4.4 Gaya Deklarasi Route

Tiga gaya dipakai tergantung kebutuhan:

**a) Manual (paling umum) — untuk custom action:**
```php
Route::get('/laporan/iuran-korpri', [IuranKorpriController::class, 'index'])
    ->name('iuran-korpri.index');
Route::post('/laporan/iuran-korpri/simpan', [IuranKorpriController::class, 'simpanRekap'])
    ->name('iuran-korpri.simpan');
Route::put('/laporan/iuran-korpri/update', [IuranKorpriController::class, 'updateBesaran'])
    ->name('iuran-korpri.update');
```

**b) `Route::resource` — untuk CRUD standar:**
```php
Route::resource('users', UserController::class);
Route::resource('surat-masuk', SuratMasukController::class);
```

**c) `Route::controller` — untuk grouping satu controller dengan banyak method:**
```php
Route::controller(PengajuanCeraiController::class)
    ->prefix('pengajuan-cerai')
    ->name('pengajuan-cerai.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/search-pegawai', 'searchPegawai')->name('search');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });
```

### 4.5 API Routes (`api.php`)

- Minimal, hanya untuk fitur **real-time/AJAX** (chatbot).
- Pakai middleware `['web', 'auth']` (bukan Sanctum token) karena masih sesi-based.
- Tidak ada versioning API.

```php
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/chat/history', [ChatController::class, 'history']);
    Route::post('/chat/send', [ChatController::class, 'send']);
    // ...
});
```

### 4.6 Route Name Collision (Peringatan)

Ada **dua group route** di MASN yang pakai prefix name `pegawai.import.` secara bersamaan:

```php
Route::prefix('pegawai/master')->name('pegawai.import.')->group(...);  // index, search-employee, profile
Route::prefix('sync-data')->name('pegawai.import.')->group(...);       // upload, history, status, diff, dll.
```

Karena Laravel **meng-overwrite** nama route yang sama, maka:
- `masn.pegawai.import.index` → mengarah ke `GET /masn/pegawai/master` (bukan sync-data)
- Route di group `sync-data` dengan nama `pegawai.import.upload` tidak bertumpuk dengan `index/search/profile`
- Tapi jika ada nama yang sama di kedua group, yang kedua akan menimpa yang pertama

> **Best practice:** Gunakan prefix name yang unik per group, misal `pegawai.master.` dan `sync-data.`, atau `pegawai.import.` hanya untuk satu group.

### 4.7 Route Declaration Order

Jika ada route spesifik yang perlu dideclare sebelum resource/prefix umum (misal `/snapshot/export/pdf` vs `/snapshot/{id}`), deklarasinya ditaruh **sebelum** route yang lebih generik di dalam group yang sama.

---

## 5. Pola Data Import (CSV Sync)

## 6. Pola Export (Excel / PDF)

Dua export class, keduanya pakai **Maatwebsite/Laravel Excel**:

| Export | Source Data | Format | Kolom |
|--------|------------|--------|-------|
| `SnapshotExport` | `Pegawai` (live) atau `HistoryPegawai` (arsip) | Excel + PDF | 15 kolom (NIP, Nama, Gol, Jabatan, OPD, dll.) |
| `PengajuanCeraiExport` | `PengajuanCerai` (filter tanggal) | Excel | 7 kolom (NIP, Nama, Jabatan, Tgl Surat, Jenis, UK, OPD) |

- Mapping kolom via `WithMapping` interface
- Header via `WithHeadings`
- Sheet title via `WithTitle`
- Auto size via `ShouldAutoSize`

PDF di-generate dari `Maatwebsite\LaravelNovaExcel\Excel` facade dengan opsi `->download('file.pdf', \Maatwebsite\Excel\Excel::MPDF)` — pakai MPDF wrapper.

Flow lengkap import data pegawai:

```
Upload CSV
    ↓
CsvSanitizerService  (sanitasi encoding)
    ↓
CsvImportService     (parsing, validasi kolom, hitung hash)
    ↓
PegawaiDiffService   (bandingkan hash dengan data master → new/changed/unchanged)
    ↓
stg_pegawai_import   (staging table — semua row dari file)
    ↓
ImportBatch          (record batch, track status & progress)
    ↓
[Queue Job] ProcessPegawaiImport
    ↓
PegawaiImportService.processStagingRecord()
    ├── ReferenceSyncService  (updateOrCreate semua tabel ref_*)
    ├── sync pegawai          (updateOrCreate by pns_id)
    └── RiwayatSyncService    (sync riwayat jabatan, golongan, dll.)
```

---

## 7. Modul MESRA (Surat & Cerai)

### 7.1 Surat Masuk

- Controller: `SuratMasukController` (Resource Controller)
- Model: `SuratMasuk` — kolom: `nomor_agenda`, `nomor_surat`, `pengirim`, `perihal`, `disposisi`, `keterangan`, `tanggal_terima`
- Filter: bulan & tahun via query string
- Route: `mesra.surat-masuk.{index,create,store,edit,update,destroy,print}`
- Input OPD pake autocomplete dari `SnapshotPegawai` (distinct OPD names)

### 7.2 Pengajuan Cerai

- Controller: `PengajuanCeraiController` — pakai `Route::controller()` pattern
- Model: `PengajuanCerai` — kolom: `nip`, `nama`, `jabatan`, `tanggal_surat`, `jenis_pengajuan` (ENUM: Penggugat/Tergugat), `unit_kerja`, `opd`, `keterangan`
- Search pegawai via AJAX dari `SnapshotPegawai` (by NIP/nama)
- Export Excel via `PengajuanCeraiExport` (Maatwebsite)

### 7.3 Chatbot / Helpdesk

- **Dual mode:** Bot (FAQ) atau Human (admin reply)
- **Session-based:** Setiap chat session punya `session_id` string, timeout 10 menit
- **Verifikasi NIP:** User bisa kirim NIP → bot verifikasi lewat `SnapshotPegawai` → simpan ke profil user
- **Admin chat:** Halaman admin di `/mesra/chat`, data via API (`ChatController`)
- **API endpoint** (stateful, middleware `['web', 'auth']`):
  - `GET /api/chat/history` — chat milik user sendiri
  - `POST /api/chat/send` — kirim pesan
  - `POST /api/chat/expire` — reset sesi
  - `POST /api/chat/mode` — switch bot/human mode
  - `GET /api/chat/conversations` — admin: daftar percakapan
  - `GET /api/chat/admin/history/{userId}` — admin: history user
  - `POST /api/chat/admin/reply` — admin: balas user
- **FAQ:** Tabel `faqs` dengan kolom `question` (judul), `keywords` (koma-separated), `answer` (markdown), `category`, `is_active`. Pencocokan via `LIKE %keyword%`.
- Model `Faq` punya scope `findByKeyword` dan static method `findAnswerOrNull`.

---

## 8. Console Commands

### 8.1 Eselon

| Command | Signature | Fungsi |
|---------|-----------|--------|
| `GenerateEselonMapping` | `eselon:generate-mapping {--dry-run}` | Generate mapping jabatan → eselon berdasarkan pola nama jabatan (Sekda→II/a, Kadis→II/b, Kabag→III/a, Kasubag→III/b, dll.) |
| `CheckEselonMapping` | `debug:check-eselon` | Debug: distribusi eselon, unmapped, sample |
| `CheckJabatanPattern` | `debug:check-jabatan` | Debug: sample pola nama jabatan |

### 8.2 Sync & Backfill

| Command | Signature | Fungsi |
|---------|-----------|--------|
| `SyncGolonganPegawai` | `pegawai:sync-golongan` | Sync `golongan_id` dari staging ke main pegawai table |
| `BackfillGolAkhirFromStaging` | `sync:backfill-gol-akhir` | Backfill `gol_akhir` column dari staging terbaru |
| `CheckTanpaOpd` | `debug:check-tanpa-opd` | Debug: analisis penyebab "Tanpa OPD" |

---

## 9. Seeder & Factory Patterns

### 9.1 Seeder

Semua seeder dipanggil dari `DatabaseSeeder` dengan urutan:
1. `RefGolonganSeeder` — 17 PNS + 17 PPPK records, `updateOrCreate` by `id`
2. `RefIuranEselonSeeder` — 6 eselon dengan besaran, `updateOrInsert` by `eselon_key`
3. `IuranKorpriSeeder` — tarif per golongan PNS (I-IV, ruang a-e) + PPPK, `updateOrInsert` by `golongan_key`
4. `UserSeeder` — 3 user (admin + 2 user biasa), `updateOrCreate` by `email`
5. `FaqsTableSeeder` — 7 FAQ entries, `truncate()` lalu `Faq::create()`

### 9.2 Factory

Hanya ada `UserFactory` (untuk testing). Factory lain belum dibuat karena data pegawai berasal dari import CSV / SIASN.

---

## 10. Snapshot & History Pegawai

### 10.1 snapshot_pegawai
- Sync dari database legacy **sidawai** (tabel `export_pegawai`) via `PegawaiSyncService`
- Kolom: `nip_baru` (unique), `nama_pegawai`, `tgl_lahir`, `eselon`, `golongan`, `jabatan`, `pd`, `sub_pd`, `jenikel`, `sts_peg`, `tk_pend`, `no_hp`
- Dipakai untuk: autocomplete OPD, search pegawai di MESRA, referensi display

### 10.2 history_pegawai
- Snapshot **bulanan** di-generate oleh `SnapshotController@store`
- Struktur kolom lebih lengkap dari snapshot_pegawai (termasuk `tempat_lahir`, `jenis_kelamin`, `agama`, `jenis_kawin`, dll.)
- Kolom `created_at` dipakai sebagai tanggal snapshot (bukan timestamp biasa)
- Limit: hanya 1 snapshot per bulan (dicegah duplikasi di controller)

### 10.3 pegawai_aktif
- **View/model read-only** (`$table = 'pegawai_aktif'`, string PK `nip_baru`)
- Tidak punja timestamps, `$guarded = []` (semua kolom fillable)

---

## 11. Access Control

| Role | Akses |
|---|---|
| `admin` | Semua modul, user management |
| `user` | Tergantung array `modules` di profil user |

- Field `modules` di tabel `users` di-cast ke `array` (JSON column).
- Default value untuk user biasa (saat dicek di model): `['mari']` jika `modules` null.
- Middleware `module:xxx` memanggil `User::hasModuleAccess(string $module)`.
- Middleware `admin` cek `user->role === 'admin'`, abort 403 jika bukan admin.

---

## 12. Catatan Penting / Gotcha

1. **`kedudukan_hukum_id`** adalah kolom kunci untuk semua filter aktif/non-aktif. Jangan pakai `deleted_at` untuk logika ini.
2. **ID golongan PPPK** pakai namespace khusus (misal `19.8`) — selalu gunakan kolom `gol_akhir` (string) daripada FK `golongan_id` untuk display atau kalkulasi iuran PPPK.
3. **`chunkById`**, bukan `chunk`, untuk iterasi tabel staging yang besar di queue job.
4. **Import batch** selalu dicreate sebelum job di-dispatch, dan di-update di dalam job. Jangan create batch di dalam job.
5. **Eselon fallback**: Jika `jabatan_id` tidak ada di `ref_eselon_mapping`, defaultnya `'IV/b'` dan di-log sebagai warning.
6. **Rekap arsip** (`rekap_iuran_bulanan`) dipakai sebagai snapshot — kalau ada arsip untuk bulan/tahun yang diminta, system tampilkan dari arsip, bukan hitung ulang. Parameter `hitung_ulang` bisa bypass ini.
