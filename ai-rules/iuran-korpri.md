# Modul Iuran KORPRI (MARI)

Dokumen ini memuat detail implementasi logika bisnis dan kalkulasi tarif Iuran Korpri dalam modul MARI (Manajemen Analisis & Rekapitulasi Info).

## 1. Dual Parameter Perhitungan Iuran
Perhitungan iuran ASN didasarkan pada dua parameter yang berbeda sesuai dengan tipe pegawai:
- **Struktural PNS**: Dihitung berdasarkan nilai **Eselon**. Logikanya memetakan `ref_eselon_mapping` ke `ref_iuran_eselon`.
- **Non-Struktural & PPPK**: Dihitung berdasarkan nilai **Golongan**. Logikanya memetakan kolom `pegawai.gol_akhir` ke tabel `iuran_korpri`.

## 2. Alur Resolusi Kelas Jabatan
Logika resolusi penentuan kelas jabatan diurus secara terpusat oleh `IuranKorpriGeneratorService` dengan metode *fallback* bertingkat jika mapping pada satu tingkat tidak ditemukan. Urutannya sebagai berikut:
1. **Mapping Perbup Terbaru**: Mencari mapping pada `ref_jabatan_mapping` menggunakan data `jabatan_siasn_id` untuk menemukan relasi di `ref_kelas_perbup`.
2. **Fallback Default Jabatan**: Jika opsi 1 gagal, mengecek `ref_jabatan_default` untuk fallback kelas langsung menggunakan nama jabatannya.
3. **Fallback Spesifik UNOR (Lama)**: Jika opsi 2 gagal, menggunakan mapping skema lama `ref_jabatan_kelas` dengan memperhatikan kombinasi spesifik antara `jabatan_id` dan `unor_id`.
4. **Fallback Global (Lama)**: Jika opsi 3 gagal, menggunakan `ref_jabatan_kelas` (lama) tanpa memperhitungkan `unor_id` (berlaku secara global).

> **Catatan Eselon Fallback**: Jika `jabatan_id` seorang pejabat struktural tidak ditemukan dalam `ref_eselon_mapping`, sistem memberikan default eselon `'IV/b'` dan mencatat *warning log*.

## 3. Override Data Iuran (Penyesuaian Manual)
Aplikasi mendukung kemampuan override manual (pengecualian logika) per-pegawai.
- Data disimpan pada tabel `iuran_override`. Admin memiliki kewenangan untuk memanipulasi golongan, eselon, dan OPD pada entri milik individu tanpa mempengaruhi master referensi data si pegawai.
- **Audit Trail**: Seluruh tindakan memanipulasi, menyimpan, atau menghapus override tercatat di tabel log yaitu `iuran_override_log` untuk kebutuhan histori/audit.

## 4. Rekapitulasi Bulanan
- Arsip perolehan hitungan iuran dicatat di tabel `rekap_iuran_bulanan`.
- **Mekanisme Caching/Snapshot**: Rekap ini berfungsi sebagai *snapshot*. Jika pengguna meminta melihat rekap pada bulan dan tahun yang sebelumnya sudah diselesaikan/diarsip, aplikasi akan menampilkan data statis dari rekap tersebut **bukan menghitung ulang**. 
- Fitur hitung ulang (bypass mekanisme rekap) hanya dapat dipicu dengan mengirimkan parameter paksaan `hitung_ulang`.
