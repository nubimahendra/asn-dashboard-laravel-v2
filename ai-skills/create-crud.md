# SOP: Membuat Halaman CRUD Baru

Panduan langkah demi langkah (checklist) untuk membuat halaman CRUD baru di dalam modul yang sudah ada. Ikuti urutan ini untuk menjaga konsistensi arsitektur proyek.

## 📝 Langkah Eksekusi (Checklist)

- [ ] **1. Buat Migration**
  Jalankan perintah: `php artisan make:migration create_nama_tabel_table`
  - Pastikan primary key sesuai (string jika tabel referensi).
  - Tambahkan kolom `nullable()` untuk data yang tidak wajib.
  - Set constraint foreign key dengan benar: referensi (`onDelete('set null')`), child data (`onDelete('cascade')`).

- [ ] **2. Buat Model**
  Jalankan perintah: `php artisan make:model NamaModel`
  - Deklarasikan array `$fillable` secara eksplisit (dilarang menggunakan `$guarded`).
  - Tambahkan array `$casts` untuk tipe data khusus (date, datetime, boolean, array).
  - Tulis fungsi relasi (`belongsTo`, `hasMany`, dsb).
  - Jika tabel referensi, tambahkan: `$keyType = 'string'; public $incrementing = false;`

- [ ] **3. Buat Controller**
  Jalankan perintah: `php artisan make:controller NamaModelController`
  - Terapkan validasi input (`$request->validate()`) langsung di dalam method.
  - Bungkus operasi write (create/update/delete) di dalam `DB::beginTransaction()`, `DB::commit()`, dan `DB::rollBack()`.
  - Pastikan tidak menaruh logika bisnis yang rumit; delegasikan ke Service Class jika perlu.

- [ ] **4. Daftarkan Route**
  Buka file `routes/web.php`.
  - Letakkan route baru di dalam *group middleware modul* yang tepat (misal: `module:masn`).
  - Gunakan konvensi penamaan route: `{modul}.{resource}.{action}` (contoh: `masn.pegawai.index`).
  - Hindari duplikasi/bentrok nama route (name collision).

- [ ] **5. Buat View (Blade)**
  Buat file blade di `resources/views/{modul}/{folder_fitur}/`.
  - Wajib meng-extend layout modul yang sesuai: `@extends('layouts.{modul}')`.
  - Sesuaikan struktur HTML dengan desain yang sudah ada (Tailwind 4.0).

- [ ] **6. Update Sidebar Navigation**
  Buka file layout navigasi di `resources/views/layouts/{modul}.blade.php`.
  - Tambahkan link menu untuk halaman CRUD yang baru dibuat menggunakan nama route yang sudah didaftarkan.

---

## 🔎 Checklist Review Kode (Quality Assurance)

Gunakan daftar periksa ini sebelum menyelesaikan tugas untuk memastikan standar kode terpenuhi:

### Model
- [ ] `$fillable` lengkap untuk semua kolom yang dipakai.
- [ ] `$casts` untuk date, datetime, JSON, boolean sudah tepat.
- [ ] Relasi (`belongsTo`/`hasMany`) sudah didefinisikan.
- [ ] Scope disertakan untuk filter query yang sering dipakai.
- [ ] (Khusus Tabel Referensi): Memiliki `$keyType = 'string'`, `$incrementing = false`.

### Controller
- [ ] Validasi input ketat dengan `$request->validate()`.
- [ ] Write operations dibungkus `DB::beginTransaction()`, `commit()`, `rollBack()`.
- [ ] Return JSON untuk permintaan AJAX, dan View untuk request reguler.
- [ ] Logika bisnis berat sudah dipindahkan ke Service Class.

### Route
- [ ] Berada dalam group middleware modul yang tepat.
- [ ] Penamaan Route (Route Name) terstruktur (`{modul}.{resource}.{action}`).
- [ ] Tidak menimpa route dari fitur lain (name collision aman).

### View
- [ ] Meng-extend layout modul yang tepat.
- [ ] Responsif untuk tampilan mobile (jika diperlukan).

### Migration
- [ ] Foreign Key (FK) menggunakan `onDelete('set null')` untuk referensi.
- [ ] Primary Key tipe `string` untuk tabel referensi.
