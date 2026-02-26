<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\fee_payment>
 */
class fee_paymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'fee_id' => \App\Models\Fee::factory(),
            'user_id' => \App\Models\User::factory(),
            'exam_sub_type_id' => \App\Models\ExamSubType::factory(),
            'pay' => $this->faker->randomFloat(2, 10, 500),
        ];
    }
}
