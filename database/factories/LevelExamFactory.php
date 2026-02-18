<?php

namespace Database\Factories;

use App\Models\LevelExam;
use App\Models\TypeExam;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LevelExam>
 */
class LevelExamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type_exam_id' => TypeExam::factory(),
            'level' => $this->faker->randomElement(['Level 1', 'Level 2', 'Level 3']),
        ];
    }
}
