<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'academic_year' => '2025-2026',
                'start_date' => '2025-09-01 08:00:00',
                'end_date' => '2026-06-30 23:59:59',
                'active' => true,
            ],
            [
                'academic_year' => '2024-2025',
                'start_date' => '2024-09-01 08:00:00',
                'end_date' => '2025-06-30 23:59:59',
                'active' => false,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['academic_year' => $setting['academic_year']],
                $setting
            );
        }
    }
}
