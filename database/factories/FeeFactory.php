<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\fee>
 */
class FeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'setting_id' => \App\Models\Setting::factory(),
            'exam_sub_type_id' => \App\Models\ExamSubType::factory(),
            'user_id' => \App\Models\User::factory(),
            'payment_amount' => $this->faker->randomFloat(2, 50, 500),
        ];
    }
}
