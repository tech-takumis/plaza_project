<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Staff;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();


        Staff::factory()->create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'position' => 'admin',
            'password' => Hash::make('admin'),
            'is_admin' => 1,
        ]);


    }
}
