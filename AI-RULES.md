# 🤖 AI-RULES: Panduan Utama AI Assistant ASN Dashboard v2

Dokumen ini adalah aturan wajib dan panduan utama (System Prompt) untuk AI Assistant yang bekerja di repository ini. 
**BACA INI SEBELUM MENULIS ATAU MENGUBAH KODE APAPUN.**

---

## 1. Tech Stack Utama
- **Backend:** Laravel 12.x, PHP 8.2+
- **Frontend:** Tailwind CSS 4.0, Alpine.js 3.x, Blade Templates
- **Build Tool:** Vite 7.x
- **Database:** MySQL 8.0+

---

## 2. Perintah Terminal Wajib (Menjalankan Project)
Untuk menjalankan project di lingkungan development lokal, gunakan perintah berikut:
- **`npm run dev`** : Menjalankan Vite dev server untuk memproses aset frontend (Tailwind/JS).
- **`composer dev`** : Menjalankan server Laravel, Queue worker, Pail, dan Vite secara concurrent.

---

## 3. Pola Koding Dasar (Coding Patterns)

### Controller
- **Satu Controller Per Domain:** Setiap controller harus fokus pada satu fitur atau domain spesifik (contoh: `DashboardController`, `IuranKorpriController`).
- **Logika Ringan:** Controller tidak boleh berisi logika bisnis yang berat. Pindahkan logika kompleks ke Service class atau private method di dalam controller tersebut.
- **DB Transactions:** Wajib menggunakan `DB::beginTransaction()`, `DB::commit()`, dan `DB::rollBack()` untuk semua operasi *write* database (simpan, update, delete) yang critical.
- **Validasi:** Lakukan validasi input secara langsung di method controller menggunakan `$request->validate([...])`.

### Service Class
- Gunakan Service class (berada di `app/Services/`) untuk menangani logika bisnis yang berat, lintas-model, atau multi-step (contoh: export/import, sinkronisasi data, kalkulasi otomatis).
- Manfaatkan Dependency Injection di constructor untuk me-resolve Service class.

### Model & Database
- Selalu gunakan **`$fillable`** secara eksplisit, jangan gunakan `$guarded`.
- Semua tabel referensi (prefix `ref_`) menggunakan Primary Key bertipe **`string`** (non-incrementing).
- Hindari penggunaan soft delete pada data pegawai utama, gunakan identifier status aktif/non-aktif pada relasi kedudukan hukum.

---

## 4. Sistem 4 Modul Aplikasi

Aplikasi terbagi menjadi 4 modul terpisah. Setiap modul dilindungi oleh middleware khusus dan memiliki prefix routing sendiri:

1. **MASN (Manajemen ASN)** 
   - Prefix URL: `/masn` 
   - Middleware: `module:masn`
2. **MARI (Manajemen Analisis & Rekapitulasi Info)**
   - Prefix URL: `/mari` 
   - Middleware: `module:mari`
3. **MESRA (Manajemen Evaluasi & Sinkronisasi Referensi ASN)**
   - Prefix URL: `/mesra` 
   - Middleware: `module:mesra`
4. **SIPUT (Sistem Pengusulan)**
   - Prefix URL: `/siput` 
   - Middleware: `module:siput`

*Catatan Middleware:* Middleware `CheckModuleAccess` digunakan untuk memverifikasi hak akses. Role `admin` bebas mengakses seluruh modul, sementara role `user` dibatasi sesuai array `modules` pada database user terkait.
