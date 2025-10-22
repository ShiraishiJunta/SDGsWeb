<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OrganizerAuthController;

/*
|--------------------------------------------------------------------------
| RUTE PUBLIK (UNTUK PENGUNJUNG)
|--------------------------------------------------------------------------
| Rute-rute ini dapat diakses oleh siapa saja.
*/

// Rute untuk Landing Page
Route::get('/', function () {
    return view('landing');
})->name('home');

// Rute untuk halaman "Daftar Kegiatan"
Route::get('/kegiatan', function () {
    return view('kegiatan');
})->name('kegiatan.index');


/*
|--------------------------------------------------------------------------
| RUTE AUTENTIKASI PENYELENGGARA
|--------------------------------------------------------------------------
| Rute untuk proses login, register, dan logout penyelenggara.
| Semua diawali dengan /organizer
*/
Route::prefix('organizer')->group(function () {
    
    Route::get('/login', [OrganizerAuthController::class, 'showLogin'])->name('organizer.login.show');
    Route::post('/login', [OrganizerAuthController::class, 'login'])->name('organizer.login.submit');

    Route::get('/register', [OrganizerAuthController::class, 'showRegister'])->name('organizer.register.show');
    Route::post('/register', [OrganizerAuthController::class, 'register'])->name('organizer.register.submit');
    
    Route::get('/logout', [OrganizerAuthController::class, 'logout'])->name('organizer.logout');

});


/*
|--------------------------------------------------------------------------
| RUTE CRUD PENYELENGGARA (AREA PRIVAT)
|--------------------------------------------------------------------------
| Rute untuk manajemen (CRUD) event oleh penyelenggara.
| Semua diawali dengan /organizer dan dilindungi oleh middleware.
*/

// Kita gunakan 'prefix' agar semua URL diawali /organizer
// Kita gunakan 'name' agar semua nama rute diawali 'organizer.'
// Kita gunakan 'middleware' untuk melindungi halaman-halaman ini
Route::prefix('organizer')->name('organizer.')->middleware('auth.organizer') // Ganti 'auth.organizer' dengan nama middleware Anda
->group(function () {

    // URL: /organizer/events
    // Nama Rute: organizer.events
    Route::get('/events', [EventController::class, 'index'])->name('events');

    // URL: /organizer/events/create
    // Nama Rute: organizer.events.create
    // (Ini untuk menampilkan form tambah event)
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    
    // URL: /organizer/events
    // Nama Rute: organizer.events.store
    // (Ini untuk menyimpan event baru dari form)
    Route::post('/events', [EventController::class, 'store'])->name('events.store');

    // URL: /organizer/events/{event}/edit
    // Nama Rute: organizer.events.edit
    // (Ini untuk menampilkan form edit)
    Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');

    // URL: /organizer/events/{event}
    // Nama Rute: organizer.events.update
    // (Ini untuk menyimpan perubahan dari form edit)
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');

    // URL: /organizer/events/{event}
    // Nama Rute: organizer.events.destroy
    // (Ini untuk menghapus event)
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');

});


/*
|--------------------------------------------------------------------------
| RUTE-RUTE YANG DIHAPUS (REFERENSI)
|--------------------------------------------------------------------------
|
| Route::get('/api/events', ...); 
|   -> DIHAPUS: Rute API seharusnya hanya ada di routes/api.php.
|
| Route::get('/events/create', ...);
| Route::post('/events', ...);
| Route::delete('/events/{event}', ...);
|   -> DIHAPUS: Ini adalah rute duplikat yang menyebabkan konflik. 
|      Fungsinya sudah digantikan oleh grup /organizer/events di atas.
|
*/
