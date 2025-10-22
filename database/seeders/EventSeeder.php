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
        // Ambil semua ID dari organizer yang ada
        $organizerIds = DB::table('organizers')->pluck('id');
        $categories = ['Pendidikan', 'Lingkungan', 'Kesehatan'];

        for ($i = 0; $i < 15; $i++) {
            DB::table('events')->insert([
                'title' => 'Kegiatan Sosial #' . ($i + 1),
                'category' => $categories[array_rand($categories)],
                'date' => now()->addDays(rand(5, 30)),
                'time' => '09:00 - 12:00',
                'location' => 'Kota Madiun',

                // --- PERUBAHAN DIMULAI DI SINI ---

                // 1. Tambahkan deskripsi dummy
                'description' => 'Ini adalah deskripsi lengkap untuk ' . 'Kegiatan Sosial #' . ($i + 1) . '. Bergabunglah dengan kami untuk memberikan dampak positif bagi masyarakat sekitar. Detail lebih lanjut akan diumumkan mendekati tanggal acara.',

                // 2. Tambahkan link gambar placeholder
                'photo' => 'https://picsum.photos/seed/' . ($i + 1) . '/800/600',

                // 3. Tambahkan jumlah relawan yang dibutuhkan secara acak
                'volunteers_needed' => rand(10, 50),

                // --- AKHIR PERUBAHAN ---

                'contact_phone' => '08123456789' . $i,
                'contact_email' => 'event' . $i . '@example.com',
                'organizer_id' => $organizerIds->random(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}