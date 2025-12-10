<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'waha_url' => 'http://localhost:3000',
            'waha_api_key' => '',
            'waha_session' => 'default',
            'waha_group_id' => '', // ID Grup Toko
            'admin_wa' => '',      // No WA Owner
        ];

        foreach ($defaults as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}