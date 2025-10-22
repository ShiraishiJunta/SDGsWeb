<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    // Tampilkan hanya event milik organizer yang sedang login
    public function index()
    {
        // AMBIL ID DARI SESSION
        $organizerId = session('organizer')->id;
        
        // --- TAMBAHKAN withCount DI SINI ---
        $events = Event::where('organizer_id', $organizerId)
                       ->withCount('registrations') // <-- TAMBAHKAN INI
                       ->latest()
                       ->get();
                       
        return view('organizers.events', compact('events'));
    }

    // Simpan event baru dengan ID organizer dari session
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:Pendidikan,Lingkungan,Kesehatan',
            'date' => 'required|date',
            'time' => 'required|string',
            'location' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'required|email|max:255',
        ]);

        // Ambil ID dari session, bukan dari input form
        $validated['organizer_id'] = session('organizer')->id;

        Event::create($validated);

        return redirect()->route('organizer.events')->with('success', 'Kegiatan berhasil ditambahkan!');
    }

    // Hapus event, tapi pastikan itu milik organizer yang benar
    public function destroy(Event $event)
    {
        if ($event->organizer_id != session('organizer')->id) {
            return back()->with('error', 'Aksi ditolak!');
        }

        $event->delete();
        return back()->with('success', 'Kegiatan berhasil dihapus!');
    }
}