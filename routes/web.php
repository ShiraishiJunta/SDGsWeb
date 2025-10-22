<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OrganizerAuthController;

/*
|--------------------------------------------------------------------------
| RUTE PUBLIK
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
| RUTE AUTENTIKASI PENYELENGGARA
|--------------------------------------------------------------------------
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
*/
Route::prefix('organizer')->name('organizer.')
    //
    // --- TERAPKAN MIDDLEWARE DI SINI ---
    //
    ->middleware('organizer.auth')
    // ---------------------------------
    ->group(function () {

    Route::get('/events', [EventController::class, 'index'])->name('events');
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');

});
