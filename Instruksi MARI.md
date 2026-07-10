# Instruksi dan Tata Cara Modul MARI (Manajemen Iuran KORPRI)

Dokumen ini berisi panduan teknis dan alur logika aplikasi mengenai cara perhitungan iuran KORPRI, penggunaan fitur rekon manual, serta prosedur penyimpanan iuran bulanan (closing).

---

## 1. Alur Logika Perhitungan Iuran KORPRI

Sistem melakukan perhitungan besaran iuran KORPRI secara otomatis (*real-time* jika belum diarsip) dengan tahapan logika sebagai berikut:

### A. Filter Pegawai Aktif
- Sistem hanya akan menghitung pegawai yang berstatus **Aktif**.
- Data yang ditampilkan akan menyesuaikan dengan **Hak Akses (Scope)** pengguna:
  - Admin Kabupaten: Melihat seluruh OPD.
  - Admin OPD (Dinas): Hanya melihat pegawai dalam scope OPD-nya sendiri.
- **Status Kepegawaian**:
  - Secara default (*toggle* PNS aktif), sistem memfilter PNS/CPNS (Kedudukan Hukum 01, 02, 03, 04, 15).
  - PPPK dapat dihitung dengan mengaktifkan *toggle* PPPK pada dashboard.

### B. Penentuan Dasar Tagihan Iuran (Hierarki)
Sistem menentukan besaran iuran setiap pegawai berdasarkan urutan prioritas (hierarki) berikut:

1. **Tagihan Non-Aktif (Pengecualian)**
   Jika pada menu **Rekon Iuran Manual**, status tagihan pegawai diset menjadi **Non Aktif** (`is_active = false`), maka pegawai tersebut dikecualikan dari total tagihan, terlepas dari eselon atau golongannya.

2. **Pejabat Struktural & Override Eselon**
   Jika pegawai menduduki Jabatan Struktural (Jenis Jabatan = 1) ATAU memiliki override Eselon di *Rekon Manual*:
   - Sistem akan memetakan Jabatan ID ke Eselon (misal: II/a, III/b, IV/a).
   - Jika belum terpetakan di sistem, akan menggunakan default (IV/b).
   - Besaran iuran ditarik dari pengaturan tarif **Eselon**.

3. **Pegawai Non-Struktural (Berdasarkan Golongan)**
   Jika bukan struktural, sistem akan melihat data Golongan/Ruang pegawai:
   - Jika ada override golongan di *Rekon Manual*, sistem akan menggunakan golongan tersebut.
   - Jika tidak, menggunakan golongan asli dari data BKN (diambil format romawinya, cth: "III/c").
   - Besaran iuran ditarik dari pengaturan tarif **Golongan**.

---

## 2. Fitur Rekon Iuran Manual

Menu **Rekon Iuran Manual** digunakan untuk menyesuaikan tagihan KORPRI yang tidak sesuai dengan data master BKN tanpa merusak data asli pegawai.

- **Ubah Eselon / Golongan**: Digunakan jika tarif yang dikenakan tidak sesuai dengan jabatan/golongan aktual di daerah.
- **Pindah OPD Tagihan**: Digunakan jika seorang pegawai secara administratif berada di OPD A (di BKN), namun kewajiban iuran KORPRI-nya ditagihkan di OPD B.
- **Status Aktif/Non Aktif**: Menonaktifkan tagihan iuran KORPRI bagi pegawai tertentu (misal: Cuti Diluar Tanggungan Negara).
- **Tombol "Generate" OPD**: Sebuah fitur otomatisasi (AI berbasis aturan) untuk memberikan rekomendasi OPD tagihan. Contoh aturan:
  - Pegawai dengan nama jabatan mengandung kata "Guru" akan direkomendasikan ke **Dinas Pendidikan**.
  - "Perawat", "Bidan", "Dokter" direkomendasikan ke **Dinas Kesehatan**.
- **Sync Reset**: Mengembalikan seluruh data tagihan pegawai ke data murni BKN dan menghapus semua riwayat override.

---

## 3. Cara Menyimpan Iuran (Closing Bulan Berkenaan)

Agar proses administrasi berjalan rapi dan loading aplikasi lebih cepat, tagihan KORPRI setiap bulan **WAJIB DISIMPAN** (diarsipkan).

Berikut langkah-langkah menyimpan tagihan bulan berkenaan:

1. Buka halaman **Dashboard Iuran KORPRI** (Admin).
2. Pastikan filter **Bulan** dan **Tahun** sudah sesuai dengan bulan tagihan saat ini.
3. Lakukan pengecekan angka. Selama data belum disimpan, sistem melakukan perhitungan secara *real-time* ke ribuan data pegawai (sehingga loading mungkin butuh waktu beberapa detik).
4. Jika nominal dan rincian OPD dirasa sudah benar, klik tombol **"Simpan Iuran Bulan Ini"**.
5. Konfirmasi proses penyimpanan.
6. **Selesai**. 

### Apa yang terjadi setelah disimpan?
- Sistem menyalin seluruh rekap (Total Pegawai, Nominal per OPD, Breakdown Golongan/Eselon) ke dalam tabel Arsip (`RekapIuranBulanan`).
- Saat Anda membuka bulan tersebut di lain waktu, sistem membaca label **[Mode Arsip]**. Halaman akan termuat sangat cepat secara instan karena tidak lagi melakukan kalkulasi *real-time* satu-per-satu.
- Jika di pertengahan jalan ditemukan kesalahan dan ada perbaikan data (misal: mengubah Rekon Manual), Anda dapat menekan opsi **Hitung Ulang** pada bulan tersebut, dan menyimpan ulang hasilnya untuk menimpa arsip yang lama.
