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
                   ->withCount('registrations') // <-- KEAJAIBANNYA DI SINI
                   ->latest()
                   ->get();

    return response()->json($events);
}
}