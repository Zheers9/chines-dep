<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TypeExam;
use App\Models\ExamSubType;
use App\Models\ExamSubLevel;

class ExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create "Language Exam" type
        $langExam = TypeExam::factory()->create([
            'name' => 'Language Exam'
        ]);

        // Create "HSK" subtype for Language Exam
        $hsk = ExamSubType::factory()->create([
            'name' => 'HSK',
            'type_exam_id' => $langExam->id,
            'is_image' => false,
        ]);

        // Create 6 sub levels for HSK
        for ($i = 1; $i <= 6; $i++) {
            ExamSubLevel::factory()->create([
                'exam_sub_type_id' => $hsk->id,
                'name' => 'Level ' . $i
            ]);
        }
    }
}
