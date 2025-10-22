<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organizerIds = DB::table('organizers')->pluck('id');
        $categories = ['Pendidikan', 'Lingkungan', 'Kesehatan'];

        for ($i = 0; $i < 15; $i++) {
            DB::table('events')->insert([
                'title' => 'Kegiatan Sosial #' . ($i + 1),
                'category' => $categories[array_rand($categories)],
                'date' => now()->addDays(rand(5, 30)),
                'time' => '09:00 - 12:00',
                'location' => 'Kota Madiun',
                'description' => 'Ini adalah deskripsi lengkap untuk ' . 'Kegiatan Sosial #' . ($i + 1) . '. Bergabunglah dengan kami...',
                
                // --- HAPUS URL PICSUM ---
                'photo' => null, // Biarkan null untuk data dummy
                // -------------------------

                'volunteers_needed' => rand(10, 50),
                'contact_phone' => '08123456789' . $i,
                'contact_email' => 'event' . $i . '@example.com',
                'organizer_id' => $organizerIds->random(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
