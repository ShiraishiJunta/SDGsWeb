<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegistrationSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = DB::table('users')->pluck('id');
        $eventIds = DB::table('events')->pluck('id');

        foreach ($eventIds as $eventId) {
            // Daftarkan 1 sampai 5 relawan acak ke setiap acara
            $volunteers = $userIds->random(rand(1, 5));
            foreach ($volunteers as $userId) {
                DB::table('registrations')->updateOrInsert(
                    ['user_id' => $userId, 'event_id' => $eventId],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }
}