<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// PENTING: Beri nama alias 'ApiEventController' untuk menghindari konflik
use App\Http\Controllers\Api\EventController as ApiEventController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// PENTING: Gunakan nama alias di sini untuk menunjuk ke controller yang benar dan method 'index'
Route::get('/event', [ApiEventController::class, 'index']);