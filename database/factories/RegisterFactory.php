<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Register>
 */
class RegisterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'exam_sub_type_id' => \App\Models\ExamSubType::factory(),
            'paid_status' => $this->faker->boolean(),
            'image' => $this->faker->imageUrl(),
        ];
    }
}
