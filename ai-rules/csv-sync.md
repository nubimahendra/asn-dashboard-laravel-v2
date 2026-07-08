# Mesin Sinkronisasi Data CSV (CSV Sync Engine)

Dokumen ini mendeskripsikan secara menyeluruh aliran data yang memproses, mensanitasi, hingga merelasikan data pegawai berbasis CSV (bersumber dari SIDAWAI) ke sistem.

## Alur Pipeline Pemrosesan CSV

Pekerjaan sinkronisasi dieksekusi secara berurutan dan asinkronus (dilegasi via Queue). Berikut adalah rangkaian lengkap tahapan data dari *Upload* hingga *Sync*:

1. **Upload CSV (Pengguna)**: File CSV diunggah oleh admin modul MASN ke server.
2. **`CsvSanitizerService`**: Bertugas menormalkan isu-isu *character encoding*, merapikan spasi berlebih, atau anomali string mentah bawaan dari DB/sistem lawas.
3. **`CsvImportService`**: Bertugas memparsing baris CSV. Memvalidasi eksistensi dan konsistensi header/kolom, serta menghitung *hash* setiap *record* untuk tujuan diferensiasi.
4. **`PegawaiDiffService`**: Membandingkan hash kalkulasi sebelumnya dengan data hash pada master DB. Baris diidentifikasi menjadi tiga kategori status: **Baru (New)**, **Ada Perubahan (Changed)**, atau **Tetap (Unchanged)**.
5. **Staging Table (`stg_pegawai_import`)**: Semua *record* dari file CSV direkam sementara di tabel staging terlepas status transformasinya.
6. **Pembuatan Import Batch (`ImportBatch`)**: Entitas batch sinkronisasi dicatat dan ditugasi status awal (mis. `pending`). Entitas melacak metrik durasi maupun total baris keseluruhan yang akan dikalkulasi progress-nya. 
7. **Queue Dispatch (`ProcessPegawaiImport`)**: Proses pendelegasian via Queue Laravel (*ShouldQueue*) untuk eksekusi secara *background*.
8. **Pipeline Resolusi Record (`PegawaiImportService`)**: Queue Job melakukan iterasi per baris pada staging table. Proses setiap barisnya mendelegasikan kepada tiga *sub-services* utama:
   - **`ReferenceSyncService`**: Menjamin semua tabel referensi (seperti `ref_agama`, `ref_unor`, dsb.) dieksekusi perbaris dengan perintah `updateOrCreate` untuk menjaga keutuhan master referensi SIASN.
   - **Sync Master Pegawai**: Record disinkronkan ke tabel `pegawai` (menggunakan operasi `updateOrCreate` berdasarkan referensi *identifier* `pns_id`).
   - **`RiwayatSyncService`**: Merelasikan tabel spesifik historis pegawai, termasuk memperbarui log sinkronisasi riwayat jabatan, golongan, status, hingga riwayat pendidikan milik pegawai ke dalam database.

## Standarisasi Import Batch dan Eksekusi Queue

- **Siklus Pembuatan Batch**: Objek model `ImportBatch` WAJIB dibuat (disimpan ke database) **sebelum** `ProcessPegawaiImport` didelegasikan ke queue (*dispatched*). Sangat dilarang untuk membuat instance Batch ID di dalam blok kelas Job itu sendiri.
- **Pembaruan Status Real-time**: Progress dan status sinkronisasi diperbarui *real-time* per blok pemrosesan (chunk).
- **Penanggulangan Iterasi Besar (Cursor Drift)**: Iterasi pengambilan staging records harus menggunakan **`chunkById(500, ...)`**. Dilarang menggunakan fungsi konvensional `chunk()` untuk menghindari kehilangan referensi cursor pada saat memanipulasi iterasi volume data masif.
- **Isolasi Error Per-Record**: *Exception* maupun kegagalan pada satu baris individu **tidak boleh** menghentikan total berjalannya Queue Job. Error wajib di-*catch*, di-*log*, dan record yang gagal ditandai (`is_processed = true`) untuk memastikan eksekusi batch berlanjut hingga selesai secara *graceful*.
