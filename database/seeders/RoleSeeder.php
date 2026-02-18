<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::insert([
            ['id' => 1, 'name' => 'top admin'],
            ['id' => 2, 'name' => 'admin accounting'],
            ['id' => 3, 'name' => 'admin register'],
            ['id' => 4, 'name' => 'user'],
        ]);
    }
}
