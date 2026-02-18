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
            'Kurdish',
            'Turkish',
            'Arabic',
            'Persian',
        ];

        foreach ($notions as $notion) {
            Notion::firstOrCreate(['name' => $notion]);
        }
    }
}
