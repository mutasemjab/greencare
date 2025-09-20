<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class SettingSeeder extends Seeder
{
    public function run()
    {
        DB::table('settings')->insert([
            'logo' => 'logo/company-logo.png',
            'phone' => '+962 6 123 4567',
            'email' => 'info@company.com',
            'address' => 'Amman, Jordan - Rainbow Street, Building 123',
            'google_map' => 'https://maps.google.com/embed?pb=!1m18!1m12!1m3!1d3384.8442!2d35.9106!3d31.9539!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x151ca1c9c0f8c5f5%3A0x8c8c8c8c8c8c8c8c!2sAmman%2C%20Jordan!5e0!3m2!1sen!2s!4v1234567890',
            'twitter' => 'https://twitter.com/company',
            'instagram' => 'https://instagram.com/company',
            'facebook' => 'https://facebook.com/company',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
