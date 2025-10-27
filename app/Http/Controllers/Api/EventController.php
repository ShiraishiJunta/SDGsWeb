<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with('organizer')
                       // --- UBAH INI ---
                       ->withCount('volunteers') // Hitung relasi 'volunteers'
                       // --- AKHIR PERUBAHAN ---
                       ->latest()
                       ->get();

        // Ganti nama properti count agar sesuai dengan Javascript (jika perlu, atau ubah JS)
        // Di sini kita biarkan 'volunteers_count'
        return response()->json($events);
    }
}
