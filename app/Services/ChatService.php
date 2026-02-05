<?php

namespace App\Services;

use App\Models\Faq;
use App\Models\SnapshotPegawai;
use App\Models\User;
use App\Models\ChatMessage;

class ChatService
{
    /**
     * Dapatkan respon bot berdasarkan pesan user.
     * 
     * @param string $message
     * @param User $user
     * @return string
     */
    public function getBotResponse(string $message, User $user): string
    {
        // 1. Cek NIP di profil User
        if (empty($user->nip)) {
            // Coba cek apakah message ini adalah NIP (angka 18 digit)
            if (preg_match('/^\d{18}$/', $message)) {
                return $this->verifyAndSaveNip($message, $user);
            }
            return "Halo! Untuk memulai layanan, mohon ketikkan NIP Anda (18 digit).";
        }

        // 2. Cari jawaban di FAQ
        $faq = Faq::findByKeyword($message)->first();

        if ($faq) {
            return $faq->answer;
        }

        // 3. Fallback jika tidak ada jawaban
        return "Mohon maaf, saya belum mengerti pertanyaan Anda. Pertanyaan ini akan diteruskan ke Admin untuk ditindaklanjuti. Mohon menunggu respon selanjutnya / silahkan tekan tombol Hubungi Admin di pojok kanan atas.";
    }

    private function verifyAndSaveNip(string $nip, User $user): string
    {
        $pegawai = SnapshotPegawai::where('nip_baru', $nip)->first();

        if ($pegawai) {
            // 1. Update User Profile
            $user->update(['nip' => $nip]);

            // 2. Sync Chat History Names
            // Update ALL messages from this user to reflect the real employee name
            ChatMessage::where('user_id', $user->id)
                ->update([
                    'nip_sender' => $pegawai->nip_baru,
                    'nama_sender' => $pegawai->nama_pegawai
                ]);

            return "Terima kasih Bpk/Ibu {$pegawai->nama_pegawai}, identitas Anda terverifikasi. Ada yang bisa kami bantu?";
        }

        return "Maaf, NIP tidak ditemukan dalam data pegawai. Mohon periksa kembali.";
    }
}
