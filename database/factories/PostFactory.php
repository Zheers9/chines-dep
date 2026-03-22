<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $media = fake()->randomElement([
            ['mime' => 'image/jpeg', 'ext' => 'jpg'],
            ['mime' => 'image/png', 'ext' => 'png'],
            ['mime' => 'video/mp4', 'ext' => 'mp4'],
            ['mime' => 'audio/mpeg', 'ext' => 'mp3'],
            ['mime' => 'application/pdf', 'ext' => 'pdf'],
        ]);
        $withMedia = fake()->boolean(60);

        return [
            'title' => fake()->sentence(4),
            'description' => fake()->optional(0.85)->paragraphs(2, true),
            'user_id' => User::factory(),
            'file_path' => $withMedia ? 'uploads/posts/' . fake()->uuid() . '.' . $media['ext'] : null,
            'file_type' => $withMedia ? $media['mime'] : null,
            'section' => fake()->optional(0.7)->randomElement(['news', 'blog', 'announcement', 'resources']),
        ];
    }
}
