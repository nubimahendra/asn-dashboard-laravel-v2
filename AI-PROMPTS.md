Skenario 1: Bikin Fitur CRUD Baru (Misal di Modul MESRA)
Katakanlah lu mau bikin fitur "Daftar Cuti" di modul MESRA. Prompt-nya begini:

"Bro, gue mau bikin fitur CRUD baru untuk 'Daftar Cuti' di modul MESRA.

[Copy-Paste isi AI-RULES.md di sini]

Tolong kerjakan sesuai dengan urutan langkah dan standar kode di SOP ini:
[Copy-Paste isi ai-skills/create-crud.md di sini]

Instruksi Spesifik:

Tabelnya bernama cuti_pegawai.

Kolom yang dibutuhkan: nip (berelasi ke tabel pegawai), tanggal_mulai, tanggal_selesai, alasan, dan status (enum: pending, disetujui, ditolak).

Tolong buatkan Migration, Model, Controller, dan struktur Route-nya dulu. Nanti View-nya kita kerjakan di iterasi selanjutnya."

Skenario 2: Bikin Fitur Export Excel Baru
Katakanlah bos lu minta rekap data usulan SLKS di modul SIPUT bisa di-download jadi Excel. Prompt-nya:

"Gue butuh fitur download Excel untuk data Usulan SLKS di modul SIPUT.

[Copy-Paste isi AI-RULES.md di sini]

Gunakan standar library dan interface sesuai panduan ini:
[Copy-Paste isi ai-skills/create-export.md di sini]

Instruksi Spesifik:
Tolong buatkan class UsulSlksExport. Kolom yang diexport adalah NIP, Nama Lengkap, Jabatan, dan Status Usulan. Ambil datanya menggunakan scope UsulSlks::usulan()."

Skenario 3: Ada Bug atau Modifikasi Logika Kompleks (Misal Iuran Korpri)
Kalau lu butuh ngubah sesuatu yang sensitif, lu bawa konteks spesifiknya biar AI nggak ngerusak logika yang udah jalan.

"Gue mau modifikasi sistem Iuran Korpri. Ada aturan baru dari Perbup soal potongan khusus.

[Copy-Paste isi AI-RULES.md di sini]

Pahami dulu alur resolusi kelas jabatannya dari dokumen ini sebelum ngasih solusi:
[Copy-Paste isi ai-rules/iuran-korpri.md di sini]

Instruksi Spesifik:
Gimana caranya gue nambahin logika potongan 5% khusus untuk pegawai yang punya kedudukan_hukum_id = '03' (Tugas Belajar) di dalam IuranKorpriGeneratorService?"

Kenapa Prompt Seperti Ini Sangat Overpowered?

Nggak Ada Asumsi: AI nggak akan ngarang bikin struktur Controller pakai format API Resource kalau lu udah ngasih SOP create-crud.md yang nyuruh dia bikin response pakai compact() untuk view.

Nggak Halu: AI tahu batasan modul lu (MASN, MARI, dll.) karena selalu diingatkan oleh AI-RULES.md.