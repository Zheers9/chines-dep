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
        // 1. Disable FK checks to allow truncate
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        
        // 2. Clear old data
        Role::truncate();

        // 3. Insert the 6 specific roles we need
        Role::insert([
            ['id' => 1, 'name' => 'top admin'],
            ['id' => 2, 'name' => 'admin of dep'],
            ['id' => 3, 'name' => 'admin of hsk'],
            ['id' => 4, 'name' => 'staff of registration'],
            ['id' => 5, 'name' => 'accounting staff'],
            ['id' => 6, 'name' => 'user'],
        ]);

        // 4. Re-enable FK checks
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
    }
}
