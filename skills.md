# 🧠 ASN Dashboard v2 — Skills / Knowledge Base

> **Tujuan:** Dokumen ini adalah referensi utama untuk AI agent dan developer yang bekerja di project ini.
> Baca file ini **sebelum** menulis kode apapun. Dokumen ini menghilangkan kebutuhan untuk membaca ulang seluruh codebase setiap kali ada pengembangan baru.

**Terakhir diupdate:** 2026-06-16

---

## Daftar Isi

1. [Ringkasan Project](#1-ringkasan-project)
2. [Arsitektur & Struktur Direktori](#2-arsitektur--struktur-direktori)
3. [Technology Stack](#3-technology-stack)
4. [Modul Aplikasi](#4-modul-aplikasi)
5. [Pola Koding (Coding Patterns)](#5-pola-koding-coding-patterns)
6. [Database & Model](#6-database--model)
7. [Routing & Middleware](#7-routing--middleware)
8. [Alur Data Import (CSV Sync Engine)](#8-alur-data-import-csv-sync-engine)
9. [Sistem Iuran KORPRI](#9-sistem-iuran-korpri)
10. [Fitur Chatbot / Helpdesk](#10-fitur-chatbot--helpdesk)
11. [Export (Excel / PDF)](#11-export-excel--pdf)
12. [Console Commands](#12-console-commands)
13. [Gotcha & Catatan Penting](#13-gotcha--catatan-penting)
14. [Cara Menambah Fitur Baru (Step-by-Step)](#14-cara-menambah-fitur-baru-step-by-step)
15. [Checklist Review Kode](#15-checklist-review-kode)

---

## 1. Ringkasan Project

**ASN Dashboard v2** adalah aplikasi manajemen data Aparatur Sipil Negara (ASN) yang mencakup:
- Statistik pegawai interaktif (komposisi, generasi, Top 10 OPD)
- Import & sinkronisasi data dari SIDAWAI via CSV
- Perhitungan iuran KORPRI otomatis (dual parameter: Eselon & Golongan)
- Manajemen surat masuk, pengajuan cerai, chatbot helpdesk
- Pengusulan SLKS (Surat Keterangan Lulus Kinerja) untuk PNS/PPPK
- Multi-module access control (MASN, MARI, MESRA, SIPUT)

---

## 2. Arsitektur & Struktur Direktori

```
app/
├── Console/Commands/       # 6 Artisan command (eselon, sync, debug)
├── Exports/                # 2 export class (SnapshotExport, PengajuanCeraiExport)
├── Helpers/                # GolonganHelper (parse roman numeral → float)
├── Http/
│   ├── Controllers/        # 24 controller (1 per domain/fitur)
│   └── Middleware/         # CheckModuleAccess, IsAdmin
├── Imports/                # 3 import class (PegawaiImport, KelasJabatan*)
├── Jobs/                   # 1 Job: ProcessPegawaiImport (ShouldQueue)
├── Models/                 # 45 model Eloquent
├── Providers/              # AppServiceProvider (minimal, clean)
├── Services/               # 10 service class (business logic berat)
└── View/Components/        # 1 Blade component (SearchableSelect)

resources/views/
├── layouts/                # 5 layout: app, masn, mari, mesra, siput
├── components/             # toast-notification, chat-widget, searchable-select
├── partials/               # chat-widget, employee-table
├── admin/                  # 16 subdirektori fitur (iuran-korpri, sync, snapshot, dll.)
├── auth/                   # login.blade.php
├── mari/                   # dashboard MARI
├── mesra/                  # dashboard MESRA
├── pegawai/import/         # views terkait pegawai master
├── siput/                  # dashboard SIPUT, usul-slks, manage, laporan
├── dashboard.blade.php     # Main MASN dashboard
├── hub.blade.php           # Hub pemilih modul
└── welcome.blade.php       # Landing page (tidak diproteksi)
```

---

## 3. Technology Stack

| Layer | Teknologi | Versi |
|-------|-----------|-------|
| **Backend** | Laravel | 12.x |
| **PHP** | PHP | ≥ 8.2 |
| **Frontend** | Blade + Tailwind CSS | 4.0 |
| **JS Framework** | Alpine.js | 3.x |
| **Charts** | ApexCharts | (inline CDN) |
| **Autocomplete** | Tom Select | 2.5 |
| **Build Tool** | Vite | 7.x |
| **Database** | MySQL | 8.0+ |
| **Queue** | Laravel Queue (database driver) | — |
| **Excel Import/Export** | Maatwebsite/Excel | 3.1 |
| **PDF** | barryvdh/laravel-dompdf | 3.1 |
| **Realtime Component** | Livewire | 4.1 (minimal usage) |
| **API Auth** | Laravel Sanctum | 4.0 (session-based, bukan token) |
| **CSS Strategy** | Tailwind CSS 4.0 via `@tailwindcss/vite` plugin | — |

### NPM Scripts Penting

```bash
npm run dev          # Start Vite dev server
npm run build        # Build production assets
composer dev         # Start semua: serve + queue + pail + vite (concurrent)
composer setup       # Full setup: install, key:generate, migrate, npm build
```

---

## 4. Modul Aplikasi

Aplikasi dibagi menjadi **4 modul** dengan akses terkontrol via middleware:

| Modul | Singkatan | URL Prefix | Middleware | Deskripsi |
|-------|-----------|------------|-----------|-----------|
| **MASN** | Manajemen ASN | `/masn` | `module:masn` | Dashboard utama, master data pegawai, sync CSV, snapshot/history, user management |
| **MARI** | Manajemen Analisis & Rekapitulasi Info | `/mari` | `module:mari` | Dashboard analitik, iuran KORPRI (golongan + eselon), rekon, override, tarif |
| **MESRA** | Manajemen Evaluasi & Sinkronisasi Referensi ASN | `/mesra` | `module:mesra` | Surat masuk, pengajuan cerai, chatbot helpdesk |
| **SIPUT** | (Sistem Pengusulan) | `/siput` | `module:siput` | Usul SLKS, dashboard statistik per jenis pegawai |

### Hub

Halaman hub (`/`) menampilkan daftar modul yang bisa diakses user. Admin melihat semua modul.

### Layout per Modul

Setiap modul punya layout blade sendiri di `resources/views/layouts/`:
- `masn.blade.php` — sidebar MASN
- `mari.blade.php` — sidebar MARI
- `mesra.blade.php` — sidebar MESRA
- `siput.blade.php` — sidebar SIPUT
- `app.blade.php` — layout global (base)

---

## 5. Pola Koding (Coding Patterns)

### 5.1 Controller

- **Satu controller per domain fitur** (misal: `IuranKorpriController`, `PegawaiImportController`)
- Logika kompleks dipecah ke **private method** di controller atau **Service class**
- **Validasi**: `$request->validate([...])` langsung di method
- **Response patterns**:
  ```php
  // View biasa
  return view('nama.view', compact('data'));
  
  // AJAX/JSON
  return response()->json(['success' => true, 'message' => '...']);
  
  // Partial (cek ajax)
  if ($request->ajax()) return view('partials.table', compact('data'));
  ```
- **Transaksi DB** selalu dibungkus `DB::beginTransaction() / commit() / rollBack()` untuk operasi write

### 5.2 Model

- Semua model pakai `$fillable` eksplisit (bukan `$guarded`)
- `$casts` untuk kolom date/datetime dan JSON
- **Tabel referensi** (`Ref*`): primary key `string`, `$incrementing = false`
  ```php
  protected $keyType = 'string';
  public $incrementing = false;
  ```
- **Scope Eloquent** untuk filter yang sering diulang:
  ```php
  Pegawai::aktif()->...  // filter kedudukan_hukum_id aktif
  UsulSlks::riwayat()    // filter status = 'riwayat'
  UsulSlks::usulan()     // filter status IN ('draft_usulan', 'diajukan')
  ```
- **Accessor** untuk display:
  - `getNamaLengkapAttribute()` — gelar depan + nama + gelar belakang
  - `getGolonganPppkAttribute()` — ambil gol_akhir (hindari collision ID)
- **Konstanta domain** didefinisikan di Model (bukan config):
  ```php
  const ACTIVE_KEDUDUKAN_HUKUM = ['01', '02', '03', '04', '101', '15', '71', '73'];
  ```

### 5.3 Service Class

Dipakai untuk logika **berat, lintas-model, multi-step**:

| Service | Tanggung Jawab |
|---------|----------------|
| `CsvImportService` | Parsing & validasi file CSV dari upload |
| `CsvSanitizerService` | Sanitasi karakter encoding sebelum parsing |
| `PegawaiImportService` | Sync staging → pegawai, termasuk sync semua ref |
| `PegawaiDiffService` | Deteksi perubahan (changed/new/unchanged) antar import |
| `PegawaiSyncService` | Orkestrasi flow import end-to-end |
| `PegawaiValidationService` | Validasi data staging |
| `ReferenceSyncService` | `updateOrCreate` tiap tabel referensi dari staging |
| `RiwayatSyncService` | Sync riwayat jabatan, golongan, pendidikan, status |
| `IuranKorpriGeneratorService` | Generate transaksi iuran per bulan |
| `ChatService` | Logika chatbot |

**Dependency injection** via constructor (Laravel resolve otomatis):
```php
public function __construct(
    ReferenceSyncService $referenceSync,
    RiwayatSyncService $riwayatSync
) { ... }
```

### 5.4 Cara Membuat Service Baru

```php
// 1. Buat file di app/Services/NamaService.php
namespace App\Services;

class NamaService
{
    public function __construct(
        // inject dependency jika perlu
    ) {}
    
    public function prosesUtama(): void
    {
        // logika bisnis
    }
}

// 2. Inject di controller
public function __construct(NamaService $service)
{
    $this->service = $service;
}
```

Tidak perlu register di `AppServiceProvider` — Laravel auto-resolve.

### 5.5 Jobs (Background Queue)

- Hanya ada 1 Job: `ProcessPegawaiImport` (`ShouldQueue`)
- Pakai `chunkById(500, ...)` (BUKAN `chunk()`) untuk iterasi staging besar
- Error per-record di-catch, di-log, dan tetap lanjut (tidak menghentikan job)
- Status batch di-update real-time per chunk ke `import_batches`

### 5.6 AppSetting (Key-Value Config)

Untuk konfigurasi runtime yang bisa diubah user (bukan hardcode di `.env`):
```php
// Baca
AppSetting::getValue('invoice_header_text', 'Default');

// Tulis
AppSetting::setValue('invoice_header_text', 'Nilai Baru');
```

---

## 6. Database & Model

### 6.1 Skema Utama

```
stg_pegawai_import ──(batch_id)──→ import_batches
       │
       │ (setelah sync)
       ▼
    pegawai ──(banyak FK)──→ ref_* (tabel referensi, string PK)
       │
       ├──(hasMany)──→ riwayat_jabatan
       ├──(hasMany)──→ riwayat_golongan
       ├──(hasMany)──→ riwayat_pendidikan
       ├──(hasMany)──→ riwayat_status_pegawai
       ├──(hasMany)──→ iuran_korpri_transaksi
       ├──(hasOne) ──→ iuran_override
       └──(hasMany)──→ iuran_override_log
```

### 6.2 Tabel Referensi (`ref_*`)

Semua 13 tabel ref punya struktur seragam:

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

FK di tabel `pegawai` semuanya `onDelete('set null')` — data pegawai tidak hilang kalau referensi dihapus.

### 6.3 Namespace ID Golongan (PNS vs PPPK)

SIASN memakai ID yang sama untuk golongan PNS dan PPPK. Solusinya:
- **PNS:** ID asli SIASN (misal `'2'`, `'3'`)
- **PPPK:** Prefix `P` (misal `'P51'` = PPPK gol I)
- Kolom `gol_akhir` (string teks, bukan FK) dipakai untuk display/kalkulasi PPPK
- **JANGAN pakai `golongan_id`** untuk PPPK — bisa salah mapping

### 6.4 Status Aktif Pegawai

Pegawai aktif = `kedudukan_hukum_id` ada di daftar ini (atau NULL):

| ID | Status |
|----|--------|
| `01` | Aktif |
| `02` | CLTN |
| `03` | Tugas Belajar |
| `04` | Pemberhentian Sementara |
| `15` | Hukuman Disiplin |
| `71` | PPPK (aktif) |
| `73` | PPPK jenis lain |
| `101` | PPPK Purnawaktu |

`kedudukan_hukum_id = '17'` = **Non-aktif** (pengganti soft delete).

### 6.5 Snapshot & History

| Tabel | Sumber | Kegunaan |
|-------|--------|----------|
| `snapshot_pegawai` | Sync dari DB legacy SIDAWAI | Autocomplete OPD, search pegawai MESRA |
| `history_pegawai` | Snapshot bulanan dari `SnapshotController@store` | Arsip data bulanan, limit 1 per bulan |
| `pegawai_aktif` | View/model read-only | Referensi query ringan |

### 6.6 Tabel Modul SIPUT

| Tabel | Model | Kegunaan |
|-------|-------|----------|
| `usul_slks` | `UsulSlks` | Usulan SLKS (Surat Keterangan Lulus Kinerja) |
| `ref_kode_wilayah` | `RefKodeWilayah` | Referensi kode wilayah kabupaten/kota |

### 6.7 Seeder (Urutan Penting)

```php
// DatabaseSeeder.php — urutan eksekusi:
1. RefGolonganSeeder        // 17 PNS + 17 PPPK (updateOrCreate)
2. RefIuranEselonSeeder     // 6 eselon (updateOrInsert)
3. IuranKorpriSeeder        // tarif per golongan (updateOrInsert)
4. UserSeeder               // 3 user: admin + 2 user (updateOrCreate)
5. FaqsTableSeeder          // 7 FAQ entries (truncate lalu create)
6. RefKodeWilayahSeeder     // Kode wilayah (insert)
```

### 6.8 Konvensi Membuat Migration Baru

```bash
php artisan make:migration create_nama_tabel_table
php artisan make:migration add_kolom_to_nama_tabel_table
```

Pola yang diikuti:
- Tabel referensi: `create_ref_{nama}_table`
- Alter/add kolom: `add_{kolom}_to_{tabel}_table`
- FK ke referensi: `->foreign('xxx_id')->references('id')->on('ref_xxx')->onDelete('set null')`
- FK ke pegawai: `->foreign('pegawai_id')->references('id')->on('pegawai')->onDelete('cascade')`

---

## 7. Routing & Middleware

### 7.1 Struktur Route

```php
// Auth (di luar guard)
Route::get('/login', ...)->middleware('guest');
Route::post('/login', ...)->middleware('guest');
Route::post('/logout', ...)->middleware('auth');

// Protected
Route::middleware(['auth'])->group(function () {
    Route::get('/', [HubController::class, 'index'])->name('hub');
    
    Route::middleware(['module:masn'])->group(function () {
        Route::prefix('masn')->name('masn.')->group(function () { ... });
    });
    Route::middleware(['module:mari'])->group(function () {
        Route::prefix('mari')->name('mari.')->group(function () { ... });
    });
    Route::middleware(['module:mesra'])->group(function () {
        Route::prefix('mesra')->name('mesra.')->group(function () { ... });
    });
    Route::middleware(['module:siput'])->group(function () {
        Route::prefix('siput')->name('siput.')->group(function () { ... });
    });
});
```

### 7.2 Pola Naming Route

Format: **`{modul}.{resource}.{action}`**

Contoh:
```
masn.dashboard
masn.pegawai.import.index
masn.pegawai.import.upload
mari.iuran-korpri.index
mesra.surat-masuk.index
siput.usul-slks.index
```

### 7.3 Middleware

| Middleware | Alias | Registrasi | Fungsi |
|-----------|-------|------------|--------|
| `CheckModuleAccess` | `module:xxx` | `bootstrap/app.php` | Cek akses modul user |
| `IsAdmin` | `admin` | `bootstrap/app.php` | Cek `role === 'admin'` |

### 7.4 Tiga Gaya Deklarasi Route

```php
// a) Manual — paling umum untuk custom action
Route::get('/path', [Controller::class, 'method'])->name('name');

// b) Route::resource — untuk CRUD standar
Route::resource('users', UserController::class);

// c) Route::controller — grouping satu controller banyak method
Route::controller(Controller::class)
    ->prefix('prefix')->name('prefix.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
    });
```

### 7.5 API Routes (`routes/api.php`)

- Minimal, hanya untuk fitur real-time/AJAX (chatbot)
- Middleware: `['web', 'auth']` (bukan token Sanctum, masih session-based)
- Tidak ada versioning API

### 7.6 Access Control

| Role | Akses |
|------|-------|
| `admin` | Semua modul, user management |
| `user` | Tergantung array `modules` di profil user |

- Field `modules` di-cast ke `array` (JSON column)
- Default: `['mari']` jika `modules` null
- Logic: `User::hasModuleAccess(string $module)`

---

## 8. Alur Data Import (CSV Sync Engine)

```
Upload CSV (user)
    ↓
CsvSanitizerService     → sanitasi encoding
    ↓
CsvImportService        → parsing, validasi kolom, hitung hash
    ↓
PegawaiDiffService      → bandingkan hash dengan data master → new/changed/unchanged
    ↓
stg_pegawai_import      → staging table (semua row dari file)
    ↓
ImportBatch             → record batch, track status & progress
    ↓
[Queue Job] ProcessPegawaiImport
    ↓
PegawaiImportService.processStagingRecord()
    ├── ReferenceSyncService   → updateOrCreate semua tabel ref_*
    ├── sync pegawai           → updateOrCreate by pns_id
    └── RiwayatSyncService     → sync riwayat jabatan, golongan, dll.
```

**Key points:**
- Batch dicreate **sebelum** job di-dispatch (bukan di dalam job)
- `chunkById(500, ...)` untuk mencegah cursor drift
- Error per-record di-catch dan di-log, tidak menghentikan job
- Status batch di-update real-time per chunk

---

## 9. Sistem Iuran KORPRI

### 9.1 Dual Parameter (Eselon + Golongan)

| Tipe Pegawai | Basis Perhitungan | Alur Resolusi |
|-------------|-------------------|---------------|
| **Struktural PNS** | Eselon | `ref_eselon_mapping` → `ref_iuran_eselon` |
| **Non-struktural / PPPK** | Golongan | `pegawai.gol_akhir` → `iuran_korpri` |

### 9.2 Alur Resolusi Kelas Jabatan (di `IuranKorpriGeneratorService`)

1. Cek `ref_jabatan_mapping` (baru, Perbup) → `ref_kelas_perbup`
2. Fallback: `ref_jabatan_default`
3. Fallback: `ref_jabatan_kelas` (lama) dengan `unor_id`
4. Fallback: `ref_jabatan_kelas` (lama) tanpa `unor_id` (global)

### 9.3 Override & Rekon

- `iuran_override` — override manual per pegawai (golongan, eselon, atau nama OPD)
- `iuran_override_log` — log historis perubahan override
- `rekap_iuran_bulanan` — snapshot arsip per OPD per bulan

### 9.4 Tabel Terkait Iuran

```
iuran_korpri              — tarif per golongan (golongan_key: string)
ref_iuran_eselon          — tarif per eselon (eselon_key: string)
ref_eselon_mapping        — jabatan_id → eselon_key (struktural)
ref_jabatan_kelas         — jabatan_id + unor_id → kelas_jabatan (lama)
ref_jabatan_mapping       — jabatan_siasn_id → kelas_perbup_id (baru, Perbup)
ref_kelas_perbup          — kelas jabatan berdasarkan Perbup
ref_jabatan_default       — fallback jabatan → kelas jika mapping tidak ada
ref_opd_mapping           — unor SIASN → nama OPD Perbup
iuran_override            — override manual per pegawai
iuran_override_log        — log historis override
iuran_korpri_transaksi    — transaksi iuran yang di-generate per bulan
rekap_iuran_bulanan       — snapshot rekap per OPD per bulan (ARSIP)
```

---

## 10. Fitur Chatbot / Helpdesk

- **Dual mode:** Bot (FAQ matching) atau Human (admin reply)
- **Session-based:** `session_id` string, timeout 10 menit
- **Verifikasi NIP:** User kirim NIP → bot verifikasi via `SnapshotPegawai`
- **FAQ matching:** `LIKE %keyword%` di tabel `faqs` (kolom `keywords` koma-separated)
- **Admin panel:** `/mesra/chat` — melihat semua percakapan, bisa reply

### API Endpoints (stateful, session-based)

```
GET  /api/chat/history                    → chat milik user sendiri
POST /api/chat/send                       → kirim pesan
POST /api/chat/expire                     → reset sesi
POST /api/chat/mode                       → switch bot/human mode
GET  /api/chat/conversations              → admin: daftar percakapan
GET  /api/chat/admin/history/{userId}     → admin: history user
POST /api/chat/admin/reply                → admin: balas user
```

---

## 11. Export (Excel / PDF)

Semua export pakai **Maatwebsite/Laravel Excel**:

| Export | Source | Format | Interface |
|--------|--------|--------|-----------|
| `SnapshotExport` | `Pegawai` (live) atau `HistoryPegawai` (arsip) | Excel + PDF | `WithMapping`, `WithHeadings`, `WithTitle`, `ShouldAutoSize` |
| `PengajuanCeraiExport` | `PengajuanCerai` (filter tanggal) | Excel | `WithMapping`, `WithHeadings` |

### Cara Membuat Export Baru

```php
// 1. Buat file di app/Exports/NamaExport.php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class NamaExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection() { return Model::all(); }
    public function headings(): array { return ['Kolom 1', 'Kolom 2']; }
    public function map($row): array { return [$row->kolom1, $row->kolom2]; }
}

// 2. Download di controller
return Excel::download(new NamaExport(), 'filename.xlsx');
```

---

## 12. Console Commands

### Eselon

| Command | Signature | Fungsi |
|---------|-----------|--------|
| `GenerateEselonMapping` | `eselon:generate-mapping {--dry-run}` | Generate mapping jabatan → eselon berdasarkan pola nama |
| `CheckEselonMapping` | `debug:check-eselon` | Debug: distribusi eselon, unmapped, sample |
| `CheckJabatanPattern` | `debug:check-jabatan` | Debug: sample pola nama jabatan |

### Sync & Backfill

| Command | Signature | Fungsi |
|---------|-----------|--------|
| `SyncGolonganPegawai` | `pegawai:sync-golongan` | Sync `golongan_id` dari staging ke pegawai |
| `BackfillGolAkhirFromStaging` | `sync:backfill-gol-akhir` | Backfill `gol_akhir` dari staging terbaru |
| `CheckTanpaOpd` | `debug:check-tanpa-opd` | Debug: analisis "Tanpa OPD" |

---

## 13. Gotcha & Catatan Penting

> ⚠️ **Baca bagian ini sebelum coding!**

1. **`kedudukan_hukum_id`** adalah kolom kunci untuk filter aktif/non-aktif. **JANGAN pakai `deleted_at`** untuk logika ini.

2. **ID golongan PPPK** pakai namespace khusus (prefix `P`). Selalu gunakan kolom **`gol_akhir`** (string) daripada FK `golongan_id` untuk display/kalkulasi iuran PPPK.

3. **`chunkById`**, bukan `chunk`, untuk iterasi tabel staging besar di queue job — mencegah cursor drift.

4. **Import batch** selalu dicreate **sebelum** job di-dispatch. Jangan create batch di dalam job.

5. **Eselon fallback**: Jika `jabatan_id` tidak ada di `ref_eselon_mapping`, default = `'IV/b'` (di-log warning).

6. **Rekap arsip** (`rekap_iuran_bulanan`) → kalau ada arsip untuk bulan/tahun yang diminta, tampilkan dari arsip. Parameter `hitung_ulang` bisa bypass ini.

7. **Dual database**: Ada koneksi `sidawai` di `config/database.php` untuk sync dari DB legacy. Pakai env `DB_REMOTE_*` (bukan `DB_SIDAWAI_*`).

8. **File `public/hot`**: Di VPS, pastikan file ini **dihapus** setelah deploy. Jika ada, Laravel akan mencari Vite dev server lokal → aset JS/CSS tidak termuat.

9. **Soft delete di tabel ref** tapi **TIDAK di tabel `pegawai`**. Tabel pegawai pakai `kedudukan_hukum_id = '17'` untuk non-aktif.

10. **Route name collision**: Dua group route di MASN pakai prefix `pegawai.import.` yang sama. Hati-hati kalau menambah route baru di area ini.

11. **Timezone**: Set `APP_TIMEZONE="Asia/Jakarta"` di `.env` untuk production.

---

## 14. Cara Menambah Fitur Baru (Step-by-Step)

### 14.1 Menambah Halaman CRUD Baru di Modul yang Sudah Ada

```bash
# 1. Buat migration
php artisan make:migration create_nama_tabel_table

# 2. Buat model
php artisan make:model NamaTabel

# 3. Buat controller
php artisan make:controller NamaTabelController

# 4. Tambah routes di web.php (dalam group modul yang sesuai)

# 5. Buat view Blade di resources/views/{modul}/

# 6. Tambah menu sidebar di resources/views/layouts/{modul}.blade.php
```

**Checklist:**
- [ ] Model: `$fillable`, `$casts`, relasi
- [ ] Controller: validasi, DB transaction, response pattern
- [ ] Route: dalam group middleware modul yang benar
- [ ] Route name: ikuti format `{modul}.{resource}.{action}`
- [ ] View: extend layout modul yang benar (`@extends('layouts.masn')`)
- [ ] Sidebar: tambah link di layout modul

### 14.2 Menambah Modul Baru

```php
// 1. Tambah route group di routes/web.php
Route::middleware(['module:nama_modul'])->group(function () {
    Route::prefix('nama-modul')->name('nama-modul.')->group(function () {
        Route::get('/', [NamaModulDashboardController::class, 'index'])->name('dashboard');
        // ... routes lainnya
    });
});

// 2. Buat layout blade baru di resources/views/layouts/nama-modul.blade.php
//    Copy dari layout modul terdekat (misal: siput.blade.php), lalu edit sidebar

// 3. Tambah modul di hub view (resources/views/hub.blade.php)
//    Tambah card modul baru dengan icon & deskripsi

// 4. Update User model / CheckModuleAccess middleware jika perlu
//    Modul baru otomatis dikenali selama User->modules array berisi nama modul

// 5. Buat controller, model, migration, view sesuai kebutuhan
```

### 14.3 Menambah Tabel Referensi Baru

```php
// 1. Migration
Schema::create('ref_nama', function (Blueprint $table) {
    $table->string('id', 50)->primary();
    $table->string('nama');
    $table->timestamps();
    $table->softDeletes();
});

// 2. Model (copy pola dari RefAgama.php)
class RefNama extends Model {
    use HasFactory, SoftDeletes;
    protected $table = 'ref_nama';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['id', 'nama'];
}

// 3. FK di tabel pegawai (migration baru)
$table->string('nama_id', 50)->nullable();
$table->foreign('nama_id')->references('id')->on('ref_nama')->onDelete('set null');

// 4. Relasi di Pegawai model
public function nama() {
    return $this->belongsTo(RefNama::class, 'nama_id');
}

// 5. Sync di ReferenceSyncService (jika data berasal dari CSV import)
```

### 14.4 Menambah Export Baru

Lihat [Bagian 11](#11-export-excel--pdf) untuk template.

### 14.5 Menambah Kolom ke Tabel Pegawai

```bash
# 1. Buat migration
php artisan make:migration add_kolom_baru_to_pegawai_table

# 2. Tambah kolom di migration
$table->string('kolom_baru')->nullable();

# 3. Tambah di Model Pegawai $fillable array

# 4. Jika dari CSV, update:
#    - CsvImportService (mapping kolom)
#    - StgPegawaiImport model ($fillable)
#    - PegawaiImportService (sync logic)
#    - stg_pegawai_import migration (tambah kolom juga)

# 5. Jika perlu di-display, update view terkait
```

### 14.6 Menambah Background Job Baru

```php
// 1. Buat job
php artisan make:job NamaJob

// 2. Implement ShouldQueue
class NamaJob implements ShouldQueue
{
    use Queueable;
    
    public function __construct(
        public int $batchId
    ) {}
    
    public function handle(): void
    {
        // Pakai chunkById untuk data besar
        Model::where('batch_id', $this->batchId)
            ->chunkById(500, function ($records) {
                foreach ($records as $record) {
                    try {
                        // proses
                    } catch (\Exception $e) {
                        Log::error("Error: {$e->getMessage()}");
                        // mark error, jangan throw
                    }
                }
            });
    }
}

// 3. Dispatch dari controller
NamaJob::dispatch($batchId);
```

---

## 15. Checklist Review Kode

Gunakan checklist ini sebelum commit:

### Model
- [ ] `$fillable` lengkap untuk semua kolom yang dipakai
- [ ] `$casts` untuk date, datetime, JSON, boolean
- [ ] Relasi (belongsTo/hasMany) sudah didefinisikan
- [ ] Scope untuk filter yang sering dipakai
- [ ] Jika tabel referensi: `$keyType = 'string'`, `$incrementing = false`

### Controller
- [ ] Validasi input dengan `$request->validate()`
- [ ] Write operations dibungkus `DB::beginTransaction/commit/rollBack`
- [ ] Return JSON untuk AJAX, view untuk halaman biasa
- [ ] Tidak ada logika bisnis berat di controller (pindahkan ke Service)

### Route
- [ ] Dalam group middleware modul yang benar
- [ ] Route name mengikuti konvensi `{modul}.{resource}.{action}`
- [ ] Tidak ada name collision dengan route existing

### View
- [ ] Extend layout yang benar (`@extends('layouts.{modul}')`)
- [ ] Dark mode support (jika mengikuti UI existing)
- [ ] Responsive (mobile-friendly)

### Migration
- [ ] FK pakai `onDelete('set null')` untuk referensi, `onDelete('cascade')` untuk child data
- [ ] String PK untuk tabel referensi
- [ ] Kolom baru yang opsional: `->nullable()`

### Queue / Import
- [ ] Pakai `chunkById`, bukan `chunk`
- [ ] Error handling per-record (catch, log, lanjut)
- [ ] Batch dicreate sebelum job dispatch

---

> 📌 **Tips:** Jika menambah fitur baru, cari fitur existing yang mirip dan copy polanya.
> Contoh: Untuk CRUD baru, lihat `SuratMasukController` + view-nya.
> Untuk fitur dengan search pegawai, lihat `UsulSlksController`.
> Untuk fitur dengan import, lihat `PegawaiImportController` + service-nya.
