<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // <-- Tambahkan ini untuk mengelola file
use Illuminate\Validation\Rule; // <-- Tambahkan ini untuk validasi unik

class EventController extends Controller
{
    // Menampilkan daftar event milik organizer yang login
    public function index()
    {
        // Ambil ID organizer dari session
        $organizerId = session('organizer')->id;

        // Ambil event, hitung jumlah relawan, DAN ambil data relawan terkait
        $events = Event::where('organizer_id', $organizerId)
                    ->withCount('volunteers') // Menghasilkan 'volunteers_count'
                    ->with('volunteers')      // <-- TAMBAHKAN INI: Mengambil data relawan
                    ->latest()
                    ->get();

        return view('organizers.events', compact('events'));
    }

    // Menyimpan event baru
    public function store(Request $request)
    {
        // Gunakan Error Bag kustom agar error kembali ke modal yang benar
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:Pendidikan,Lingkungan,Kesehatan',
            'date' => 'required|date|after_or_equal:today', // Tanggal tidak boleh di masa lalu
            'time' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'required|email|max:255',
            'description' => 'required|string',
            'volunteers_needed' => 'required|integer|min:1',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
        ], [], [
             // Custom attribute names (opsional)
             'volunteers_needed' => 'volunteer dibutuhkan',
             'contact_phone' => 'kontak telepon',
             'contact_email' => 'email penyelenggara',
        ]);

        $validated['organizer_id'] = session('organizer')->id;

        // Logika Upload Foto
        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada (tidak relevan untuk store, tapi penting untuk update)
            // Simpan file baru dan dapatkan path-nya
            $path = $request->file('photo')->store('event_photos', 'public');
            $validated['photo'] = $path;
        }

        Event::create($validated);

        // Redirect kembali dengan pesan sukses
        return redirect()->route('organizer.events')->with('success', 'Kegiatan berhasil ditambahkan!');

    } // Akhir method store

    // Mengupdate data event
    public function update(Request $request, Event $event)
    {
         // Pastikan event ini milik organizer yang sedang login (keamanan tambahan)
         if ($event->organizer_id != session('organizer')->id) {
             return redirect()->route('organizer.events')->with('error', 'Aksi ditolak!');
         }

        // Gunakan Error Bag kustom agar error kembali ke modal yang benar
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:Pendidikan,Lingkungan,Kesehatan',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'required|email|max:255',
            'description' => 'required|string',
            'volunteers_needed' => 'required|integer|min:1',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [], [
            'volunteers_needed' => 'volunteer dibutuhkan',
            'contact_phone' => 'kontak telepon',
            'contact_email' => 'email penyelenggara',
       ]);


        // Logika Update Foto
        if ($request->hasFile('photo')) {
            // 1. Hapus foto lama jika ada
            if ($event->photo) {
                Storage::disk('public')->delete($event->photo);
            }
            // 2. Simpan foto baru
            $path = $request->file('photo')->store('event_photos', 'public');
            $validated['photo'] = $path;
        } else {
            // Jika tidak ada file baru diunggah, jangan ubah kolom photo
             unset($validated['photo']);
        }


        $event->update($validated);

        // Redirect kembali dengan pesan sukses
        return redirect()->route('organizer.events')->with('success', 'Kegiatan berhasil diperbarui!');

    } // Akhir method update

    // Menghapus event
    public function destroy(Event $event)
    {
        // Pastikan event ini milik organizer yang sedang login
        if ($event->organizer_id != session('organizer')->id) {
            return redirect()->route('organizer.events')->with('error', 'Aksi ditolak!');
        }

        // Hapus foto terkait jika ada sebelum menghapus record event
        if ($event->photo) {
            Storage::disk('public')->delete($event->photo);
        }

        $event->delete();

        // Redirect kembali dengan pesan sukses
        return redirect()->route('organizer.events')->with('success', 'Kegiatan berhasil dihapus!');
    }
}

