# Aturan Database & Model

Dokumen ini memuat panduan komprehensif mengenai perancangan database dan Eloquent Model pada aplikasi ASN Dashboard v2.

## 1. Aturan Dasar Model
- **`$fillable`**: Semua model harus menggunakan deklarasi `$fillable` secara eksplisit untuk keamanan Mass Assignment. Dilarang keras menggunakan `$guarded = []`.
- **`$casts`**: Gunakan array `$casts` untuk melakukan konversi tipe data otomatis. Kolom yang berupa tanggal harus di-cast menjadi `date` atau `datetime`, dan kolom yang menampung JSON array di-cast sebagai `array` (contoh: `'modules' => 'array'`, `'breakdown_golongan' => 'array'`).

## 2. Tabel Referensi (`ref_*`)
Tabel referensi merupakan master data yang ID-nya diambil langsung dari SIASN (contoh: ID `'01'`, `'71'`, `'IV/a'`).
- **Primary Key**: Primary key pada tabel referensi bertipe `string` dan non-incrementing.
- **Konfigurasi Model**: Terapkan pengaturan berikut pada seluruh model referensi:
  ```php
  protected $keyType = 'string';
  public $incrementing = false;
  ```
- **Penghapusan**: Gunakan fitur `SoftDeletes` untuk mengarsipkan data lama tanpa memecah relasi (contoh tabel `ref_agama`, `ref_golongan`).
- **Foreign Key**: Foreign key pada tabel relasional (seperti tabel `pegawai`) yang mengarah ke tabel referensi harus dikonfigurasi dengan constraint `onDelete('set null')`. Hal ini mencegah hilangnya data utama apabila data referensi dihapus.

## 3. Penamaan ID Golongan (PPPK vs PNS)
Karena sistem SIASN menggunakan ID yang sama (konflik) untuk membedakan beberapa level golongan antara PNS dan PPPK, diterapkan skema *namespacing* sebagai solusi:
- **Golongan PNS**: Menggunakan ID asli dari SIASN (misalnya: `'2'`, `'3'`).
- **Golongan PPPK**: Menggunakan awalan karakter `P` pada ID (misalnya: `'P51'` untuk PPPK golongan I, `'P52'` untuk PPPK golongan II, dan seterusnya).
- **Aturan Implementasi**: Untuk operasi kalkulasi dan visualisasi khusus PPPK, **dilarang menggunakan Foreign Key `golongan_id`**. Sebagai gantinya, wajib menggunakan kolom `gol_akhir` bertipe string langsung dari tabel `Pegawai` demi menghindari kegagalan mapping.

## 4. Status Aktif / Kedudukan Hukum Pegawai
Konsep "Soft Delete" (kolom `deleted_at`) **tidak berlaku** pada entitas pegawai. Status pegawai ditentukan sepenuhnya oleh relasi `kedudukan_hukum_id`.

**Status Aktif** diberikan pada pegawai dengan nilai `kedudukan_hukum_id` berupa `NULL` atau ID berikut:
- `'01'` (Aktif)
- `'02'` (CLTN)
- `'03'` (Tugas Belajar)
- `'04'` (Pemberhentian Sementara)
- `'15'` (Hukuman Disiplin)
- `'71'` (PPPK Aktif)
- `'73'` (PPPK Jenis Lain)
- `'101'` (PPPK Purnawaktu)

**Status Non-Aktif**:
- Pegawai dideklarasikan Non-Aktif jika memiliki `kedudukan_hukum_id = '17'`. Data ini bertindak layaknya *soft-deleted record*.
