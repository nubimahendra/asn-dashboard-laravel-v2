<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChatContact;
use App\Models\ChatMessage;
use App\Models\SnapshotPegawai;
use Carbon\Carbon;

class ChatDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Dummy Contacts
        $contacts = [
            ['name' => 'Budi Santoso', 'number' => '081234567890'],
            ['name' => 'Siti Aminah', 'number' => '089876543210'],
            ['name' => 'Rudi Hartono', 'number' => '081122334455'],
            ['name' => 'Lina Marlina', 'number' => '085566778899'],
            ['name' => 'Doni Irawan', 'number' => '087788990011'],
        ];

        foreach ($contacts as $contact) {
            ChatContact::firstOrCreate(
                ['number' => $contact['number']],
                [
                    'name' => $contact['name'],
                    'remote_id' => $contact['number'] . '@s.whatsapp.net',
                ]
            );

            // Also create dummy pegawai if not exists for cleaner UI
            SnapshotPegawai::firstOrCreate(
                ['no_hp' => $contact['number']],
                ['nama_pegawai' => $contact['name'], 'nip_baru' => '19900101202203100' . rand(1, 9), 'jabatan' => 'Staf']
            );
        }

        // 2. Create Dummy Messages
        $messages = [
            ['number' => '081234567890', 'msg' => 'Halo min, mau tanya info cuti?', 'dir' => 'in', 'read' => false, 'time' => Carbon::now()->subMinutes(10)],
            ['number' => '081234567890', 'msg' => 'Apa syaratnya ya?', 'dir' => 'in', 'read' => false, 'time' => Carbon::now()->subMinutes(9)],
            ['number' => '081234567890', 'msg' => 'Bisa cek di menu layanan kepegawaian ya pak.', 'dir' => 'out', 'read' => true, 'time' => Carbon::now()->subMinutes(5)],

            ['number' => '089876543210', 'msg' => 'Selamat siang', 'dir' => 'in', 'read' => true, 'time' => Carbon::now()->subHours(1)],
            ['number' => '089876543210', 'msg' => 'Siang bu, ada yang bisa dibantu?', 'dir' => 'out', 'read' => true, 'time' => Carbon::now()->subHours(1)->addMinutes(2)],

            ['number' => '081122334455', 'msg' => 'Test bot', 'dir' => 'in', 'read' => false, 'time' => Carbon::now()->subDays(1)],
        ];

        foreach ($messages as $msg) {
            ChatMessage::create([
                'sender_number' => $msg['number'],
                'message' => $msg['msg'],
                'direction' => $msg['dir'],
                'is_handled_by_bot' => false,
                'is_read' => $msg['read'],
                'created_at' => $msg['time'],
                'updated_at' => $msg['time'],
            ]);
        }
    }
}
