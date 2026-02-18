<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TypeExam;
use App\Models\LevelExam;

class ExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create "Level Exam" type
        TypeExam::factory()->create([
            'name' => 'Level Exam'
        ]);

        // Create "HSK" type with 6 levels
        $hsk = TypeExam::factory()->create([
            'name' => 'HSK'
        ]);

        // Create 6 levels for HSK
        for ($i = 1; $i <= 6; $i++) {
            LevelExam::factory()->create([
                'type_exam_id' => $hsk->id,
                'level' => 'Level ' . $i
            ]);
        }
    }
}
