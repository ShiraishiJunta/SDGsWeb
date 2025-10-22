<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\OrganizerAuthController;

Route::get('/', function () {
    return view('landing'); // Pastikan nama file Anda 'landing.blade.php'
})->name('home');

// Rute BARU untuk halaman daftar kegiatan
Route::get('/kegiatan', function () {
    return view('kegiatan'); // Mengarah ke file 'kegiatan.blade.php'
})->name('kegiatan.index');

// Route::get('/', [EventController::class, 'index'])->name('events.index');

// Route::get('/', [EventController::class, 'publicView'])->name('home');
Route::get('/api/events', [EventController::class, 'apiEvents']); // untuk fetch JSON
Route::get('/organizer/form', [EventController::class, 'organizerForm'])->name('organizer.form');

Route::get('/api/events', [EventController::class, 'api']);
Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
Route::post('/events', [EventController::class, 'store'])->name('events.store');
Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');

Route::get('/organizer/events', [EventController::class, 'index'])->name('organizer.events');
Route::post('/organizer/events', [EventController::class, 'store'])->name('organizer.events.store');
Route::delete('/organizer/events/{event}', [EventController::class, 'destroy'])->name('organizer.events.destroy');

Route::get('/organizer/login', [OrganizerAuthController::class, 'showLogin']);
Route::post('/organizer/login', [OrganizerAuthController::class, 'login']);

Route::get('/organizer/register', [OrganizerAuthController::class, 'showRegister']);
Route::post('/organizer/register', [OrganizerAuthController::class, 'register']);

Route::get('/organizer/logout', [OrganizerAuthController::class, 'logout']);

Route::put('organizer/events/{event}', [App\Http\Controllers\Organizer\EventController::class, 'update'])->name('organizer.events.update');
