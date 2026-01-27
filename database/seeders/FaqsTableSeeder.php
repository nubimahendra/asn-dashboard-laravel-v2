<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Faq;

class FaqsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Bersihkan data lama agar tidak duplikat
        DB::table('faqs')->truncate();

        $faqs = [
            [
                'question' => 'Apa syarat pengajuan cuti tahunan?',
                'keywords' => 'syarat cuti tahunan prosedur pengajuan',
                'answer' => 'Syarat pengajuan cuti tahunan adalah: 1. Sudah bekerja minimal 1 tahun secara terus menerus. 2. Mengajukan surat permohonan minimal 1 minggu sebelum tanggal cuti. 3. Mendapatkan persetujuan dari atasan langsung.',
                'category' => 'Kepegawaian',
                'is_active' => true,
            ],
            [
                'question' => 'Bagaimana cara mengurus kenaikan pangkat?',
                'keywords' => 'urus kenaikan pangkat syarat berkas',
                'answer' => 'Untuk mengurus kenaikan pangkat, Anda perlu menyiapkan: SK Pangkat Terakhir, SKP 2 tahun terakhir dengan nilai minimal Baik, dan kelengkapan administrasi lainnya sesuai periode (April/Oktober). Silakan hubungi Subbag Umum & Kepegawaian untuk detailnya.',
                'category' => 'Karir',
                'is_active' => true,
            ],
            [
                'question' => 'Berapa besaran tunjangan kinerja bulan ini?',
                'keywords' => 'tukin tunjangan kinerja cair kapan besaran',
                'answer' => 'Besaran Tunjangan Kinerja (Tukin) bergantung pada capaian kinerja bulanan dan absensi Anda. Anda dapat melihat detail perhitungan di menu "Kinerja Saya" pada dashboard ini.',
                'category' => 'Keuangan',
                'is_active' => true,
            ],
            [
                'question' => 'Apakah ada pelayanan pengajuan cerai bagi ASN?',
                'keywords' => 'cerai gugat talak izin perceraian',
                'answer' => 'Ya, setiap ASN yang akan melakukan perceraian wajib memperoleh izin tertulis atau surat keterangan dari Pejabat Pembina Kepegawaian. Silakan ajukan melalui menu "Laporan > Pengajuan Cerai" di dashboard.',
                'category' => 'Hukum',
                'is_active' => true,
            ],
            [
                'question' => 'Jam berapa jam kerja kantor selama bulan puasa?',
                'keywords' => 'jam kerja puasa ramadhan masuk pulang',
                'answer' => 'Selama bulan Ramadan, jam kerja disesuaikan menjadi: Senin-Seni (08.00 - 15.00) dan Jumat (08.00 - 15.30). Istirahat dikurangi menjadi 30 menit.',
                'category' => 'Umum',
                'is_active' => true,
            ],
            [
                'question' => 'Bagaimana cara mendaftar taspen?',
                'keywords' => 'taspen daftar pensiun tabungan hari tua',
                'answer' => 'Pendaftaran TASPEN bisa dilakukan secara online melalui website resmi TASPEN atau datang langsung ke kantor cabang terdekat dengan membawa SK CPNS, SK PNS, SPMT, dan KTP.',
                'category' => 'Kesejahteraan',
                'is_active' => true,
            ],
            [
                'question' => 'Apa itu MyASN?',
                'keywords' => 'myasn aplikasi bkn update data',
                'answer' => 'MyASN adalah aplikasi dari BKN untuk memudahkan ASN dalam melakukan pemutakhiran data mandiri, monitoring layanan kepegawaian, dan melihat profil kepegawaian digital.',
                'category' => 'Aplikasi',
                'is_active' => true,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::create($faq);
        }
    }
}
