<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
       $this->call([
        OrganizerSeeder::class,
        UserSeeder::class,      // Buat User (relawan)
        EventSeeder::class,
        RegistrationSeeder::class, // Baru daftarkan setelah user dan event ada
    ]);
    }
}