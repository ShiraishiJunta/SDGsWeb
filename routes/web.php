<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OrganizerAuthController;

/*
|--------------------------------------------------------------------------
| Rute Publik (Untuk Pengunjung)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('landing');
})->name('home');

Route::get('/kegiatan', function () {
    return view('kegiatan');
})->name('kegiatan.index');


/*
|--------------------------------------------------------------------------
| Rute Penyelenggara (Organizer)
|--------------------------------------------------------------------------
*/

// Grup untuk Autentikasi Penyelenggara
Route::prefix('organizer')->group(function () {
    // --- PERUBAHAN DI SINI ---
    // Tambahkan nama 'organizer.login.show'
    Route::get('/login', [OrganizerAuthController::class, 'showLogin'])->name('organizer.login.show');
    Route::post('/login', [OrganizerAuthController::class, 'login'])->name('organizer.login.attempt'); // Beri nama berbeda untuk POST

    // Tambahkan nama 'organizer.logout' jika belum ada
    Route::get('/logout', [OrganizerAuthController::class, 'logout'])->name('organizer.logout');

    // Tambahkan nama 'organizer.register'
    Route::get('/register', [OrganizerAuthController::class, 'showRegister'])->name('organizer.register');
    Route::post('/register', [OrganizerAuthController::class, 'register'])->name('organizer.register.attempt'); // Beri nama berbeda untuk POST
    // --- AKHIR PERUBAHAN ---
});


// Grup untuk Manajemen Event oleh Penyelenggara
// Pastikan rute ini dilindungi middleware agar hanya bisa diakses setelah login
Route::prefix('organizer')->middleware('organizer.auth')->group(function () {
    Route::get('/events', [EventController::class, 'index'])->name('organizer.events');
    Route::post('/events', [EventController::class, 'store'])->name('organizer.events.store');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('organizer.events.destroy');
    // Rute untuk update event (jika sudah dibuat)
    Route::put('/events/{event}', [EventController::class, 'update'])->name('organizer.events.update');
});

