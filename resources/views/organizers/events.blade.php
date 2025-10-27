@extends('layouts.app')

{{-- Menambahkan CSS kustom kecil --}}
@push('styles')
<style>
    @keyframes shake { 0%, 100% { transform: translateX(0); } 10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); } 20%, 40%, 60%, 80% { transform: translateX(5px); } }
    .shake-anim { animation: shake 0.5s ease-in-out; }

    /* Area konten modal yang bisa di-scroll */
    .modal-content-area {
        overflow-y: auto;
        /* max-height: calc(85vh - 150px); */ /* Dihapus karena sudah diatur flex-grow */
        padding-bottom: 1rem; /* Ruang di bawah konten sebelum footer modal */
    }
</style>
@endpush

@section('content')
{{-- Ambil data event terakhir yang error jika ada (untuk membuka modal edit otomatis) --}}
@php
    $lastEditedEventData = null;
    $lastEditedEventIdOnError = session('event_id_on_edit_error');
    // Pastikan $events ada sebelum diakses
    if ($lastEditedEventIdOnError && old('_form_origin') === 'editModal' && isset($events)) {
        $lastEvent = $events->firstWhere('id', $lastEditedEventIdOnError);
        if ($lastEvent) {
            $lastEditedEventData = [
                'id' => $lastEvent->id, 
                'title' => $lastEvent->title, 
                'category' => $lastEvent->category,
                'date' => $lastEvent->date, 
                'time' => $lastEvent->time, 
                'location' => $lastEvent->location,
                'description' => $lastEvent->description, 
                'volunteers_needed' => $lastEvent->volunteers_needed,
                'contact_phone' => $lastEvent->contact_phone, 
                'contact_email' => $lastEvent->contact_email,
            ];
        }
    }
@endphp

{{-- Root element Alpine.js --}}
<div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8"
     x-data="crudData({{ $lastEditedEventData ? json_encode($lastEditedEventData) : 'null' }})"
     x-init="initValidation()">

    {{-- Notifikasi --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition class="fixed bottom-5 right-5 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-[60] text-sm font-medium" x-cloak><i data-lucide="check-circle" class="inline w-4 h-4 mr-1"></i> {{ session('success') }}</div>
    @endif
     @if(session('error'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition class="fixed bottom-5 right-5 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-[60] text-sm font-medium" x-cloak><i data-lucide="alert-circle" class="inline w-4 h-4 mr-1"></i> {{ session('error') }}</div>
    @endif
    {{-- Notifikasi Error Validasi Umum (jika modal tidak terbuka) --}}
    @if ($errors->any() && !old('_form_origin'))
        <div class="bg-red-100 text-red-800 p-4 rounded-xl mb-4 shadow-sm">
            <h4 class="font-bold mb-2">Oops! Ada yang salah:</h4>
            <ul class="list-disc list-inside text-sm">
                 @foreach ($errors->all() as $error)
                     <li>{{ $error }}</li>
                 @endforeach
             </ul>
             <p class="mt-2 text-sm">Silakan periksa kembali form Anda.</p>
        </div>
    @endif


    {{-- Header Halaman --}}
    <div class="flex items-center mb-6">
         <a href="{{ url('/') }}" class="text-gray-500 hover:text-blue-600 mr-4 flex items-center" title="Kembali ke Beranda"><i data-lucide="arrow-left" class="w-6 h-6"></i></a>
         <h1 class="text-3xl font-bold text-gray-800">Manajemen <span class="text-blue-600">Kegiatan</span></h1>
    </div>

    {{-- Tombol Tambah & Judul Daftar --}}
     <div class="flex justify-between items-center mb-4">
         <h2 class="text-lg font-semibold text-gray-700">Daftar Kegiatan Anda</h2>
         <button @click="openAddModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center space-x-1 font-medium text-sm transition duration-150 ease-in-out shadow-sm hover:shadow"><i data-lucide="plus" class="w-5 h-5"></i><span>Tambah Kegiatan</span></button>
     </div>

    {{-- Tabel Daftar Kegiatan --}}
    <div class="bg-white shadow rounded-lg overflow-x-auto border border-gray-200">
        <table class="min-w-full border-collapse align-top">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-xs uppercase tracking-wider">#</th>
                    <th class="px-4 py-3 text-left font-semibold text-xs uppercase tracking-wider min-w-[60px]">Foto</th>
                    <th class="px-4 py-3 text-left font-semibold text-xs uppercase tracking-wider min-w-[150px]">Judul</th>
                    <th class="px-4 py-3 text-left font-semibold text-xs uppercase tracking-wider">Kategori</th>
                    <th class="px-4 py-3 text-left font-semibold text-xs uppercase tracking-wider min-w-[160px]">Tanggal</th>
                    <th class="px-4 py-3 text-left font-semibold text-xs uppercase tracking-wider">Lokasi</th>
                    <th class="px-4 py-3 text-left font-semibold text-xs uppercase tracking-wider">Relawan</th>
                    <th class="px-4 py-3 text-left font-semibold text-xs uppercase tracking-wider min-w-[100px]">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($events as $event)
                    <tr class="hover:bg-gray-50 text-sm text-gray-800">
                        <td class="px-4 py-3">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3"><img src="{{ $event->photo ? asset('storage/' . $event->photo) : 'https://placehold.co/80x60/e2e8f0/64748b?text=N/A&font=sans' }}" alt="Foto {{ $event->title }}" class="w-16 h-12 rounded object-cover border bg-gray-100"></td>
                        <td class="px-4 py-3 font-medium">{{ $event->title }}</td>
                        <td class="px-4 py-3">{{ $event->category }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($event->date)->isoFormat('dddd, D MMMM YYYY') }}</td>
                        <td class="px-4 py-3">{{ $event->location }}</td>
                        <td class="px-4 py-3">{{ $event->volunteers_count }}/{{ $event->volunteers_needed }}</td>
                        <td class="px-4 py-3 flex space-x-1 items-center">
                             {{-- Tombol Detail: Kirim seluruh data event (termasuk volunteers) ke Alpine --}}
                             <button @click='openDetailModal({{ json_encode($event) }})'
                                     class="text-blue-600 hover:text-blue-800 p-1 rounded hover:bg-blue-100 transition duration-150" title="Lihat Detail">
                                 <i data-lucide="info" class="w-4 h-4"></i>
                             </button>
                             {{-- Tombol Edit: Kirim data yang relevan untuk form edit --}}
                             <button @click="openEditModal({
                                 id: {{ $event->id }},
                                 title: `{{ e($event->title) }}`,
                                 category: '{{ $event->category }}',
                                 date: '{{ $event->date }}',
                                 time: `{{ e($event->time) }}`,
                                 location: `{{ e($event->location) }}`,
                                 description: `{{ e($event->description) }}`,
                                 volunteers_needed: {{ $event->volunteers_needed }},
                                 contact_phone: `{{ e($event->contact_phone) }}`,
                                 contact_email: `{{ e($event->contact_email) }}`
                             })" class="text-yellow-500 hover:text-yellow-700 p-1 rounded hover:bg-yellow-100 transition duration-150" title="Edit">
                                  <i data-lucide="edit-3" class="w-4 h-4"></i>
                              </button>
                             {{-- Tombol Hapus --}}
                             <button @click="openDeleteModal({{ $event->id }}, `{{ e($event->title) }}`)"
                                     class="text-red-600 hover:text-red-800 p-1 rounded hover:bg-red-100 transition duration-150" title="Hapus">
                                 <i data-lucide="trash-2" class="w-4 h-4"></i>
                             </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-10 text-gray-500"><div class="flex flex-col items-center"><i data-lucide="calendar-x" class="w-12 h-12 text-gray-400 mb-2"></i><span>Anda belum membuat kegiatan apapun.</span></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ============================================= --}}
    {{-- ========= MODAL TAMBAH KEGIATAN =========== --}}
    {{-- ============================================= --}}
    <div x-show="addModalOpen" x-transition class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @keydown.escape.window="closeAddModal()" x-cloak>
        {{-- Wrapper Modal dengan max-height dan flex column --}}
        <div class="bg-white rounded-xl shadow-lg w-full max-w-3xl flex flex-col" style="max-height: 90vh;" @click.away="closeAddModal()">
            {{-- Header Modal (Fixed, tidak scroll) --}}
            <div class="px-6 py-4 border-b flex justify-between items-center flex-shrink-0 rounded-t-xl">
                <h2 class="text-xl font-bold text-gray-800">Tambah Kegiatan Baru</h2>
                <button type="button" @click="closeAddModal()" class="text-gray-400 hover:text-gray-600 text-3xl leading-none">&times;</button>
            </div>
            {{-- Konten Form (Scrollable Area) --}}
            <div class="modal-content-area px-6 pt-6 flex-grow"> {{-- Tambahkan flex-grow --}}
                <form id="formTambahInner" method="POST" action="{{ route('organizer.events.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_form_origin" value="addModal">
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mb-4">
                        {{-- Field Judul --}}
                        <div>
                            <label for="add_title_inner" class="font-semibold block mb-1 text-sm text-gray-700">Judul Kegiatan <span class="text-red-500">*</span></label>
                            <input id="add_title_inner" type="text" name="title" class="w-full border rounded-lg p-2 mt-1 text-sm @error('title', 'addBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ old('title') }}" required>
                            @error('title', 'addBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        {{-- Kategori, Tanggal, Waktu, Lokasi, Volunteer, Kontak --}}
                        <div><label for="add_category_inner" class="font-semibold block mb-1 text-sm text-gray-700">Kategori <span class="text-red-500">*</span></label><select id="add_category_inner" name="category" class="w-full border rounded-lg p-2 mt-1 text-sm @error('category', 'addBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror focus:ring-2 focus:ring-blue-500 focus:border-transparent" required><option value="">-- Pilih Kategori --</option><option value="Pendidikan" {{ old('category') == 'Pendidikan' ? 'selected' : '' }}>Pendidikan</option><option value="Lingkungan" {{ old('category') == 'Lingkungan' ? 'selected' : '' }}>Lingkungan</option><option value="Kesehatan" {{ old('category') == 'Kesehatan' ? 'selected' : '' }}>Kesehatan</option></select>@error('category', 'addBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror</div>
                        <div><label for="add_date_inner" class="font-semibold block mb-1 text-sm text-gray-700">Tanggal <span class="text-red-500">*</span></label><input id="add_date_inner" type="date" name="date" class="w-full border rounded-lg p-2 mt-1 text-sm @error('date', 'addBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ old('date') }}" required min="{{ now()->toDateString() }}">@error('date', 'addBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror</div>
                        <div><label for="add_time_inner" class="font-semibold block mb-1 text-sm text-gray-700">Waktu <span class="text-red-500">*</span></label><input id="add_time_inner" type="text" name="time" placeholder="contoh: 09:00 - 12:00" class="w-full border rounded-lg p-2 mt-1 text-sm @error('time', 'addBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ old('time') }}" required>@error('time', 'addBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror</div>
                        <div><label for="add_location_inner" class="font-semibold block mb-1 text-sm text-gray-700">Lokasi <span class="text-red-500">*</span></label><input id="add_location_inner" type="text" name="location" class="w-full border rounded-lg p-2 mt-1 text-sm @error('location', 'addBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ old('location') }}" required>@error('location', 'addBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror</div>
                        <div><label for="add_volunteers_needed_inner" class="font-semibold block mb-1 text-sm text-gray-700">Volunteer Dibutuhkan <span class="text-red-500">*</span></label><input id="add_volunteers_needed_inner" type="number" name="volunteers_needed" class="w-full border rounded-lg p-2 mt-1 text-sm @error('volunteers_needed', 'addBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ old('volunteers_needed', 1) }}" min="1" required>@error('volunteers_needed', 'addBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror</div>
                        <div><label for="add_contact_phone_inner" class="font-semibold block mb-1 text-sm text-gray-700">Kontak Telepon <span class="text-red-500">*</span></label><input id="add_contact_phone_inner" type="text" name="contact_phone" class="w-full border rounded-lg p-2 mt-1 text-sm @error('contact_phone', 'addBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ old('contact_phone') }}" required>@error('contact_phone', 'addBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror</div>
                        <div><label for="add_contact_email_inner" class="font-semibold block mb-1 text-sm text-gray-700">Email Penyelenggara <span class="text-red-500">*</span></label><input id="add_contact_email_inner" type="email" name="contact_email" class="w-full border rounded-lg p-2 mt-1 text-sm @error('contact_email', 'addBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ old('contact_email') }}" required>@error('contact_email', 'addBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror</div>
                        <div class="md:col-span-2"><label for="add_photo_inner" class="font-semibold block mb-1 text-sm text-gray-700">Foto/Poster Kegiatan <span class="text-gray-500">(Opsional, Max 2MB)</span></label><input id="add_photo_inner" type="file" name="photo" class="w-full border rounded-lg p-2 mt-1 text-sm file:mr-4 file:py-1 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('photo', 'addBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror" accept="image/jpeg,image/png,image/jpg,image/gif">@error('photo', 'addBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror</div>
                        <div class="md:col-span-2"><label for="add_description_inner" class="font-semibold block mb-1 text-sm text-gray-700">Deskripsi Kegiatan <span class="text-red-500">*</span></label><textarea id="add_description_inner" name="description" rows="4" class="w-full border rounded-lg p-2 mt-1 text-sm @error('description', 'addBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>{{ old('description') }}</textarea>@error('description', 'addBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror</div>
                    </div>
                </form>
            </div>
             {{-- Footer Modal (Fixed) --}}
            <div class="flex justify-end space-x-2 p-4 border-t sticky bottom-0 bg-white rounded-b-xl flex-shrink-0">
                <button type="button" @click="closeAddModal()" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 font-medium text-sm transition duration-150">Batal</button>
                <button type="submit" form="formTambahInner" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-medium text-sm transition duration-150">Simpan Kegiatan</button>
            </div>
        </div>
    </div>

    {{-- ============================================= --}}
    {{-- =========== MODAL EDIT KEGIATAN =========== --}}
    {{-- ============================================= --}}
    <div x-show="editModalOpen" x-transition class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @keydown.escape.window="closeEditModal()" x-cloak>
        {{-- Wrapper Modal --}}
        <div class="bg-white rounded-xl shadow-lg w-full max-w-3xl flex flex-col" style="max-height: 90vh;" @click.away="closeEditModal()">
            {{-- Header Modal --}}
            <div class="px-6 py-4 border-b flex justify-between items-center flex-shrink-0"><h2 class="text-xl font-bold text-gray-800">Edit Kegiatan</h2><button type="button" @click="closeEditModal()" class="text-gray-400 hover:text-gray-600 text-3xl leading-none">&times;</button></div>
             {{-- Konten Form (scrollable) --}}
            <div class="modal-content-area px-6 pt-6 flex-grow"> {{-- Area konten yang bisa scroll --}}
                {{-- Action form di-set oleh Alpine saat modal dibuka --}}
                <form id="editFormInner" x-ref="editFormRefInner" method="POST" action="" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_form_origin" value="editModal">
                    <input type="hidden" name="event_id_on_edit_error" :value="editData.id">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mb-4">
                        {{-- Field Judul --}}
                        <div><label for="edit_title_inner" class="font-semibold block mb-1 text-sm text-gray-700">Judul Kegiatan <span class="text-red-500">*</span></label><input id="edit_title_inner" type="text" name="title" class="w-full border rounded-lg p-2 mt-1 text-sm @error('title', 'editBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror focus:ring-2 focus:ring-blue-500 focus:border-transparent" :value="old('title', editData.title)" required>@error('title', 'editBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror</div>
                        {{-- Field Kategori --}}
                        <div><label for="edit_category_inner" class="font-semibold block mb-1 text-sm text-gray-700">Kategori <span class="text-red-500">*</span></label><select id="edit_category_inner" name="category" class="w-full border rounded-lg p-2 mt-1 text-sm @error('category', 'editBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror focus:ring-2 focus:ring-blue-500 focus:border-transparent" required><option value="Pendidikan" :selected="old('category', editData.category) == 'Pendidikan'">Pendidikan</option><option value="Lingkungan" :selected="old('category', editData.category) == 'Lingkungan'">Lingkungan</option><option value="Kesehatan" :selected="old('category', editData.category) == 'Kesehatan'">Kesehatan</option></select>@error('category', 'editBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror</div>
                        {{-- Tanggal, Waktu, Lokasi, Volunteer, Kontak --}}
                        <div><label for="edit_date_inner" class="font-semibold block mb-1 text-sm text-gray-700">Tanggal <span class="text-red-500">*</span></label><input id="edit_date_inner" type="date" name="date" class="w-full border rounded-lg p-2 mt-1 text-sm @error('date', 'editBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror focus:ring-2 focus:ring-blue-500 focus:border-transparent" :value="old('date', editData.date)" required min="{{ now()->toDateString() }}">@error('date', 'editBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror</div>
                        <div><label for="edit_time_inner" class="font-semibold block mb-1 text-sm text-gray-700">Waktu <span class="text-red-500">*</span></label><input id="edit_time_inner" type="text" name="time" class="w-full border rounded-lg p-2 mt-1 text-sm @error('time', 'editBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror focus:ring-2 focus:ring-blue-500 focus:border-transparent" :value="old('time', editData.time)" required>@error('time', 'editBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror</div>
                        <div><label for="edit_location_inner" class="font-semibold block mb-1 text-sm text-gray-700">Lokasi <span class="text-red-500">*</span></label><input id="edit_location_inner" type="text" name="location" class="w-full border rounded-lg p-2 mt-1 text-sm @error('location', 'editBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror focus:ring-2 focus:ring-blue-500 focus:border-transparent" :value="old('location', editData.location)" required>@error('location', 'editBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror</div>
                        <div><label for="edit_volunteers_needed_inner" class="font-semibold block mb-1 text-sm text-gray-700">Volunteer Dibutuhkan <span class="text-red-500">*</span></label><input id="edit_volunteers_needed_inner" type="number" name="volunteers_needed" class="w-full border rounded-lg p-2 mt-1 text-sm @error('volunteers_needed', 'editBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror focus:ring-2 focus:ring-blue-500 focus:border-transparent" :value="old('volunteers_needed', editData.volunteers_needed)" min="1" required>@error('volunteers_needed', 'editBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror</div>
                        <div><label for="edit_contact_phone_inner" class="font-semibold block mb-1 text-sm text-gray-700">Kontak Telepon <span class="text-red-500">*</span></label><input id="edit_contact_phone_inner" type="text" name="contact_phone" class="w-full border rounded-lg p-2 mt-1 text-sm @error('contact_phone', 'editBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror focus:ring-2 focus:ring-blue-500 focus:border-transparent" :value="old('contact_phone', editData.contact_phone)" required>@error('contact_phone', 'editBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror</div>
                        <div><label for="edit_contact_email_inner" class="font-semibold block mb-1 text-sm text-gray-700">Email Penyelenggara <span class="text-red-500">*</span></label><input id="edit_contact_email_inner" type="email" name="contact_email" class="w-full border rounded-lg p-2 mt-1 text-sm @error('contact_email', 'editBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror focus:ring-2 focus:ring-blue-500 focus:border-transparent" :value="old('contact_email', editData.contact_email)" required>@error('contact_email', 'editBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror</div>
                        <div class="md:col-span-2"><label for="edit_photo_inner" class="font-semibold block mb-1 text-sm text-gray-700">Ganti Foto/Poster <span class="text-gray-500">(Opsional, Max 2MB)</span></label><input id="edit_photo_inner" type="file" name="photo" class="w-full border rounded-lg p-2 mt-1 text-sm file:mr-4 file:py-1 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('photo', 'editBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror" accept="image/jpeg,image/png,image/jpg,image/gif"><p class="text-xs text-gray-500 mt-1">⚠️ Kosongkan jika tidak ingin mengganti foto.</p>@error('photo', 'editBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror</div>
                        <div class="md:col-span-2"><label for="edit_description_inner" class="font-semibold block mb-1 text-sm text-gray-700">Deskripsi Kegiatan <span class="text-red-500">*</span></label><textarea id="edit_description_inner" name="description" rows="4" class="w-full border rounded-lg p-2 mt-1 text-sm @error('description', 'editBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>{{-- Diisi oleh Alpine atau old() --}}</textarea>@error('description', 'editBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror</div>
                    </div>
                </form>
            </div>
             {{-- Footer Modal (Fixed) --}}
            <div class="flex justify-end space-x-2 p-4 border-t sticky bottom-0 bg-white rounded-b-xl flex-shrink-0">
                <button type="button" @click="closeEditModal()" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 font-medium text-sm transition duration-150">Batal</button>
                {{-- Tombol submit untuk form di dalam area scrollable --}}
                <button type="button" @click="submitEditForm()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-medium text-sm transition duration-150">Simpan Perubahan</button>
            </div>
        </div>
    </div>

     {{-- ============================================= --}}
    {{-- ========= MODAL DETAIL v2 (Dashboard Mini) ==== --}}
    {{-- ============================================= --}}
    <div x-show="detailModalOpen" x-transition class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @keydown.escape.window="closeDetailModal()" x-cloak>
        {{-- Wrapper Modal --}}
        <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl flex flex-col" style="max-height: 90vh;" @click.away="closeDetailModal()">
             {{-- Header Modal --}}
             <div class="px-6 py-4 border-b flex justify-between items-center flex-shrink-0">
                <h2 class="text-xl font-bold text-gray-800">Detail & Analitik Kegiatan</h2>
                <button type="button" @click="closeDetailModal()" class="text-gray-400 hover:text-gray-600 text-3xl leading-none">&times;</button>
            </div>
             {{-- Konten Detail (Scrollable Area) --}}
            <div class="modal-content-area p-6 space-y-5 text-sm flex-grow"> {{-- PERBAIKAN: Menambahkan 'flex-grow' --}}
                {{-- Bagian Info Utama & Foto --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                    <div class="md:col-span-1">
                        <img :src="detailData.photo ? '{{ asset('storage') }}/' + detailData.photo : 'https://placehold.co/800x600/e2e8f0/64748b?text=Foto+Belum+Ada&font=sans'"
                             :alt="'Foto Kegiatan ' + detailData.title"
                             class="w-full h-auto rounded-lg object-cover border p-1 bg-gray-100 aspect-[4/3]">
                    </div>
                    <div class="md:col-span-2 space-y-1.5">
                        <h3 class="text-lg font-bold text-gray-900 mb-2" x-text="detailData.title"></h3>
                        <p><strong class="font-semibold w-24 inline-block text-gray-500">Kategori:</strong> <span x-text="detailData.category" class="text-gray-800 bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-medium"></span></p>
                        <p><strong class="font-semibold w-24 inline-block text-gray-500">Tanggal:</strong> <span x-text="formatDateAlpine(detailData.date)" class="text-gray-800"></span></p> {{-- Format tanggal --}}
                        <p><strong class="font-semibold w-24 inline-block text-gray-500">Waktu:</strong> <span x-text="detailData.time" class="text-gray-800"></span></p>
                        <p><strong class="font-semibold w-24 inline-block text-gray-500">Lokasi:</strong> <span x-text="detailData.location" class="text-gray-800"></span></p>
                        <p><strong class="font-semibold w-24 inline-block text-gray-500">Kontak Telp:</strong> <span x-text="detailData.contact_phone" class="text-gray-800"></span></p>
                        <p><strong class="font-semibold w-24 inline-block text-gray-500">Kontak Email:</strong> <span x-text="detailData.contact_email" class="text-gray-800"></span></p>
                    </div>
                </div>

                 {{-- Bagian Deskripsi --}}
                 <div>
                    <strong class="font-semibold block mb-1 text-gray-600 text-sm">Deskripsi:</strong>
                    {{-- Deskripsi sekarang menggunakan x-html karena nl2br dilakukan di Alpine --}}
                    <div x-html="detailData.description" class="block bg-gray-50 p-3 rounded-lg text-sm whitespace-pre-wrap border text-gray-800 min-h-[60px]"></div>
                </div>

                {{-- Bagian Progress & Statistik --}}
                <div class="border-t pt-4">
                     <h4 class="font-semibold text-gray-600 text-base mb-3">Analitik Relawan</h4>
                     <div class="mb-4">
                         <div class="flex justify-between text-xs text-gray-500 mb-1">
                             <span>Progress Pendaftaran</span>
                             <span x-text="`${detailData.volunteers_count} / ${detailData.volunteers_needed} (${detailData.progressPercentage.toFixed(0)}%)`"></span>
                         </div>
                         <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                             <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-500 ease-out" :style="'width: ' + detailData.progressPercentage + '%'"></div>
                         </div>
                     </div>
                     <div class="grid grid-cols-3 gap-4 text-center">
                         <div><p class="text-2xl font-bold text-gray-800" x-text="detailData.volunteers_count ?? 0"></p><p class="text-xs text-gray-500 uppercase tracking-wider">Pendaftar</p></div>
                         <div><p class="text-2xl font-bold text-gray-800" x-text="detailData.quotaRemaining"></p><p class="text-xs text-gray-500 uppercase tracking-wider">Sisa Kuota</p></div>
                          <div><p class="text-2xl font-bold text-gray-800" x-text="detailData.daysRemaining"></p><p class="text-xs text-gray-500 uppercase tracking-wider" x-text="detailData.daysRemaining == 1 ? 'Hari Tersisa' : 'Hari Tersisa'"></p></div>
                     </div>
                </div>

                 {{-- Bagian Daftar Relawan --}}
                 <div class="border-t pt-4">
                    <div class="flex justify-between items-center mb-3">
                         <h4 class="font-semibold text-gray-600 text-base">Daftar Pendaftar (<span x-text="detailData.volunteers ? detailData.volunteers.length : 0"></span>)</h4>
                         {{-- Input pencarian hanya muncul jika ada relawan --}}
                         <input type="text" x-model="detailData.searchTerm" placeholder="Cari nama/email..."
                                class="border rounded-lg px-2 py-1 text-xs w-48 focus:ring-1 focus:ring-blue-500 focus:border-transparent transition duration-150"
                                x-show="detailData.volunteers && detailData.volunteers.length > 0">
                    </div>
                     <div class="max-h-60 overflow-y-auto border rounded-lg bg-gray-50">
                        <table class="min-w-full divide-y divide-gray-200">
                             <thead class="bg-gray-100 sticky top-0 z-10">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telepon</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 text-xs">
                                {{-- Tampilkan pesan jika tidak ada pendaftar atau hasil search --}}
                                <template x-if="!detailData.volunteers || filteredVolunteers.length === 0">
                                     <tr><td colspan="3" class="text-center py-4 text-gray-500" x-text="detailData.searchTerm ? 'Tidak ada pendaftar yang cocok.' : 'Belum ada pendaftar.'"></td></tr>
                                </template>
                                {{-- Loop daftar relawan yang sudah difilter --}}
                                <template x-for="volunteer in filteredVolunteers" :key="volunteer.id">
                                    <tr>
                                        <td class="px-3 py-2 whitespace-nowrap" x-text="volunteer.name"></td>
                                        <td class="px-3 py-2 whitespace-nowrap" x-text="volunteer.email"></td>
                                        <td class="px-3 py-2 whitespace-nowrap" x-text="volunteer.phone"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                 {{-- Bagian Aksi Cepat --}}
                 <div class="border-t pt-4">
                      <h4 class="font-semibold text-gray-600 text-base mb-3">Aksi Cepat</h4>
                      <button disabled class="w-full bg-green-100 text-green-700 px-4 py-2 rounded-lg text-sm font-medium flex items-center justify-center space-x-1 opacity-60 cursor-not-allowed transition duration-150" title="Fitur Segera Hadir"><i data-lucide="download" class="w-4 h-4"></i><span>Unduh Data Relawan (CSV) - Segera Hadir</span></button>
                </div>

            </div>
            {{-- Tombol Tutup (Fixed) --}}
            <div class="flex justify-end p-4 border-t sticky bottom-0 bg-white rounded-b-xl flex-shrink-0">
                <button @click="closeDetailModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium text-sm transition duration-150 shadow-sm hover:shadow">Tutup</button>
            </div>
        </div>
    </div>

    {{-- ============================================= --}}
    {{-- ======== MODAL KONFIRMASI HAPUS =========== --}}
    {{-- ============================================= --}}
    <div x-show="deleteModalOpen" x-transition class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @keydown.escape.window="closeDeleteModal()" x-cloak>
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6 text-center" @click.away="closeDeleteModal()">
             <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4"><i data-lucide="alert-triangle" class="h-6 w-6 text-red-600"></i></div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Hapus Kegiatan?</h3>
            <p class="text-sm text-gray-500 mb-6">Anda yakin ingin menghapus kegiatan "<strong x-text="deleteEventTitle"></strong>"? <br> Tindakan ini tidak dapat dibatalkan.</p>
             <form id="deleteForm" x-ref="deleteFormRef" method="POST" :action="deleteFormAction">
                @csrf @method('DELETE')
                 <div class="flex justify-center gap-4">
                     <button type="button" @click="closeDeleteModal()" class="w-full bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 font-medium text-sm transition duration-150">Batal</button>
                     <button type="button" @click="submitDeleteForm()" class="w-full bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 font-medium text-sm transition duration-150">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function crudData(lastEditedEvent = null) {
        return {
            // Status Modal
            addModalOpen: false, editModalOpen: false, detailModalOpen: false, deleteModalOpen: false,
            // Data Modal
            editData: { id: null, title: '', category: '', date: '', time: '', location: '', description: '', volunteers_needed: 1, contact_phone: '', contact_email: '', formAction: '' },
            detailData: { id: null, title: '', category: '', date: '', time: '', location: '', description: '', photo: null, volunteers_needed: 0, volunteers_count: 0, volunteers: [], contact_phone: '', contact_email: '', organizer: {}, progressPercentage: 0, quotaRemaining: 0, daysRemaining: 'N/A', searchTerm: '', get photoUrl() { return this.photo ? `{{ asset('storage') }}/${this.photo}` : ''; } },
            deleteEventId: null, deleteEventTitle: '', deleteFormAction: '',
            lastEditedEventDataOnError: lastEditedEvent,
            baseUrl: "{{ url('/') }}",

            // Computed property
             get filteredVolunteers() {
                if (!this.detailData.volunteers || this.detailData.volunteers.length === 0) return [];
                if (!this.detailData.searchTerm || this.detailData.searchTerm.trim() === '') return this.detailData.volunteers;
                const lowerSearchTerm = this.detailData.searchTerm.toLowerCase();
                return this.detailData.volunteers.filter(v => (v.name && v.name.toLowerCase().includes(lowerSearchTerm)) || (v.email && v.email.toLowerCase().includes(lowerSearchTerm)));
            },

            // Fungsi Modal
            openAddModal() { this.addModalOpen = true; }, 
            closeAddModal() { this.addModalOpen = false; },
            
            openEditModal(data) {
                this.editData = Object.assign({}, this.editData, data);
                this.editData.formAction = `${this.baseUrl}/organizer/events/${data.id}`;
                 sessionStorage.setItem('lastEditEventData', JSON.stringify(data));
                this.editModalOpen = true;
                // Isi textarea secara manual setelah modal terbuka dan DOM ter-render
                 this.$nextTick(() => {
                    const textarea = document.getElementById('edit_description_inner');
                    if (textarea) {
                        // Prioritaskan old() jika ada, fallback ke data asli
                        const oldDescription = {!! json_encode($errors->editBag->first('description') ? old('description') : null) !!};
                        textarea.value = oldDescription !== null ? oldDescription : (data.description || '');
                    }
                });
            },
            closeEditModal() {
                this.editModalOpen = false;
                 sessionStorage.removeItem('lastEditEventData');
            },
            
            openDetailModal(eventData) {
                 // Format deskripsi dengan nl2br di sini
                 let processedData = {
                    ...this.detailData, // Ambil struktur default
                    ...eventData,       // Timpa dengan data baru
                    description: this.nl2br(eventData.description || ''), // Ubah newline jadi <br>
                    // Pastikan volunteers_count diambil dari eventData jika ada, jika tidak, hitung dari array
                    volunteers_count: eventData.volunteers_count !== undefined ? eventData.volunteers_count : (eventData.volunteers ? eventData.volunteers.length : 0),
                };
                this.detailData = processedData;
                this.calculateDetailAnalytics(); // Panggil *setelah* this.detailData di-set
                this.detailData.searchTerm = '';
                this.detailModalOpen = true;
            },
            closeDetailModal() { this.detailModalOpen = false; },
            
            openDeleteModal(id, title) { 
                this.deleteEventId = id; 
                this.deleteEventTitle = title; 
                this.deleteFormAction = `${this.baseUrl}/organizer/events/${id}`; 
                this.deleteModalOpen = true; 
            },
            closeDeleteModal() { 
                this.deleteModalOpen = false; 
                this.deleteEventId = null; 
                this.deleteEventTitle = ''; 
            },

            // Fungsi Submit
            submitEditForm() { 
                if (this.editData && this.editData.id) { 
                    this.$refs.editFormRefInner.action = `${this.baseUrl}/organizer/events/${this.editData.id}`; 
                    this.$refs.editFormRefInner.submit(); 
                } else { 
                    console.error('Edit data is missing ID.'); 
                    // Ganti alert() dengan notifikasi yang lebih baik jika memungkinkan
                    alert('Terjadi kesalahan saat menyimpan perubahan.'); 
                } 
            },
            submitDeleteForm() { 
                if (this.deleteEventId) { 
                    this.$refs.deleteFormRef.action = `${this.baseUrl}/organizer/events/${this.deleteEventId}`; 
                    this.$refs.deleteFormRef.submit(); 
                } else { 
                    console.error('Delete Event ID is missing.'); 
                     // Ganti alert() dengan notifikasi yang lebih baik jika memungkinkan
                     alert('Terjadi kesalahan saat menghapus data.'); 
                } 
            },

            // Helper
             nl2br(str) { 
                if (typeof str === 'undefined' || str === null) { return ''; } 
                // Encode HTML entities untuk keamanan sebelum mengganti newline
                var encodedStr = String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); 
                return encodedStr.replace(/(\r\n|\n\r|\r|\n)/g, '<br>'); 
             },
             formatDateAlpine(dateString) { 
                if (!dateString) return 'N/A'; 
                try { 
                    const date = new Date(dateString); 
                    if (isNaN(date.getTime())) return 'Tgl Tdk Valid'; 
                    return date.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }); 
                } catch (e) { 
                    console.error("Error formatting date:", e); return dateString; 
                } 
             },

            // Analitik
            calculateDetailAnalytics() { 
                const needed = parseInt(this.detailData.volunteers_needed || 0);
                const count = parseInt(this.detailData.volunteers_count || 0); 
                
                this.detailData.progressPercentage = needed > 0 ? (count / needed) * 100 : (count > 0 ? 100 : 0);
                if (this.detailData.progressPercentage > 100) this.detailData.progressPercentage = 100;
                
                this.detailData.quotaRemaining = Math.max(0, needed - count);
                
                if(this.detailData.date){ 
                    try { 
                        const eventDate = new Date(this.detailData.date); 
                        const today = new Date(); 
                        today.setHours(0, 0, 0, 0); 
                        if (!isNaN(eventDate.getTime())) { 
                            const diffTime = eventDate - today; 
                            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
                            this.detailData.daysRemaining = Math.max(0, diffDays); 
                        } else { 
                            this.detailData.daysRemaining = 'N/A'; 
                        } 
                    } catch(e) { 
                        this.detailData.daysRemaining = 'N/A'; 
                        console.error("Error calculating days:", e); 
                    } 
                } else { 
                    this.detailData.daysRemaining = 'N/A'; 
                }
            },

            // Validasi & Reopen Modal on Error
            initValidation() {
                @if ($errors->any())
                    var formOrigin = "{{ old('_form_origin') }}";
                    if (formOrigin === 'addModal') {
                         setTimeout(() => this.openAddModal(), 50);
                    } else if (formOrigin === 'editModal') {
                        if(this.lastEditedEventDataOnError) { // Prioritaskan data dari PHP
                             setTimeout(() => {
                                this.openEditModal(this.lastEditedEventDataOnError);
                                // Isi textarea manual dari old() karena x-bind:value tidak update dari old() saat reopen
                                const textarea = document.getElementById('edit_description_inner');
                                if (textarea) textarea.value = {!! json_encode(old('description', $lastEditedEventDataOnError['description'] ?? '')) !!}; // Fallback ke data asli jika old() kosong
                            }, 50);
                        } else { // Fallback ke session storage jika data PHP tidak ada (seharusnya jarang terjadi)
                            const lastData = sessionStorage.getItem('lastEditEventData');
                            if(lastData) {
                                 setTimeout(() => {
                                    try {
                                        const parsedData = JSON.parse(lastData);
                                        this.openEditModal(parsedData);
                                        const textarea = document.getElementById('edit_description_inner');
                                        if (textarea) textarea.value = {!! json_encode(old('description', '')) !!}; // Tetap utamakan old() jika ada
                                    } catch(e) {
                                         console.error("Error parsing session storage data for edit modal:", e);
                                    }
                                }, 50);
                            } else { 
                                console.error("No data found to reopen edit modal on validation error."); 
                            }
                        }
                    }
                @endif
                // Hapus data session storage setelah beberapa saat untuk kebersihan
                setTimeout(() => sessionStorage.removeItem('lastEditEventData'), 500);
            }
        }
    }
</script>
@endpush

