<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            RoleSeeder::class,
            ExamSeeder::class,
            NotionSeeder::class,
        ]);
        $user = User::factory()->create([
            'first_name' => 'Test',
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',

        ]);
        $user->roles()->attach(1); // Assign top admin role

        // Seed some associated data
        \App\Models\Register::factory(2)->create(['user_id' => $user->id]);
        \App\Models\fee::factory(3)->create(['user_id' => $user->id]);
        \App\Models\fee_payment::factory(3)->create(['user_id' => $user->id]);
    }
}
