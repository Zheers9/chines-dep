<?php

namespace Database\Seeders;

use App\Models\Notion;
use Illuminate\Database\Seeder;

class NotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $notions = [
            ['name_en' => 'Kurdish', 'name_ku' => 'کوردی', 'name_ar' => 'كردي'],
            ['name_en' => 'Turkish', 'name_ku' => 'تورکی', 'name_ar' => 'تركي'],
            ['name_en' => 'Arabic', 'name_ku' => 'عەرەبی', 'name_ar' => 'عربي'],
            ['name_en' => 'Persian', 'name_ku' => 'فارسی', 'name_ar' => 'فارسي'],
        ];

        foreach ($notions as $notion) {
            Notion::firstOrCreate(
                ['name_en' => $notion['name_en']],
                $notion
            );
        }
    }
}
