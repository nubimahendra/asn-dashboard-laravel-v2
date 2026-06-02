<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'invoice_logo', 'value' => null],
            ['key' => 'invoice_bank_nama', 'value' => ''],
            ['key' => 'invoice_bank_rekening', 'value' => ''],
            ['key' => 'invoice_bank_atas_nama', 'value' => ''],
            ['key' => 'invoice_batas_setor', 'value' => '10'],
        ];

        foreach ($settings as $setting) {
            \App\Models\AppSetting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }
    }
}
