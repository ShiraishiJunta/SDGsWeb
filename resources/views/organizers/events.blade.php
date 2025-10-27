@extends('layouts.app')

{{-- Menambahkan CSS kustom kecil HANYA untuk animasi shake --}}
@push('styles')
<style>
    @keyframes shake { 0%, 100% { transform: translateX(0); } 10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); } 20%, 40%, 60%, 80% { transform: translateX(5px); } }
    .shake-anim { animation: shake 0.5s ease-in-out; }

    /* !!! Rule .modal-content-area DIHAPUS dari sini !!! */
    /* Kita akan gunakan kelas Tailwind langsung */
</style>
@endpush

@section('content')
{{-- Ambil data event asli jika terjadi error validasi edit --}}
@php
    $lastEditedEventDataOnError = null;
    $eventIdOnError = session('event_id_on_edit_error'); // Ambil ID dari session flash
    // Cek apakah ada error di 'editBag' DAN ID event tersimpan di session
    if ($eventIdOnError && old('_form_origin') === 'editModal' && $errors->editBag->any() && isset($events)) {
        $eventWithError = $events->find($eventIdOnError); // Cari event berdasarkan ID
        if ($eventWithError) {
            // Simpan data asli event untuk repopulasi
            $lastEditedEventDataOnError = [
                'id' => $eventWithError->id,
                'title' => $eventWithError->title,
                'category' => $eventWithError->category,
                'date' => $eventWithError->date,
                'time' => $eventWithError->time,
                'location' => $eventWithError->location,
                'description' => $eventWithError->description,
                'volunteers_needed' => $eventWithError->volunteers_needed,
                'contact_phone' => $eventWithError->contact_phone,
                'contact_email' => $eventWithError->contact_email,
            ];
        }
    }
@endphp

{{-- Root element Alpine.js --}}
{{-- Berikan data event error ke Alpine --}}
<div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8"
     x-data="crudData({{ $lastEditedEventDataOnError ? json_encode($lastEditedEventDataOnError) : 'null' }})"
     x-init="initValidation()">

    {{-- Notifikasi Sukses/Error Umum --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition class="fixed bottom-5 right-5 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-[60] text-sm font-medium" x-cloak><i data-lucide="check-circle" class="inline w-4 h-4 mr-1"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition class="fixed bottom-5 right-5 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-[60] text-sm font-medium" x-cloak><i data-lucide="alert-circle" class="inline w-4 h-4 mr-1"></i> {{ session('error') }}</div>
    @endif

    {{-- Notifikasi Error Validasi Umum (jika BUKAN dari modal) --}}
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
                             {{-- Tombol Detail --}}
                             <button @click='openDetailModal({{ json_encode($event) }})'
                                     class="text-blue-600 hover:text-blue-800 p-1 rounded hover:bg-blue-100 transition duration-150" title="Lihat Detail">
                                 <i data-lucide="info" class="w-4 h-4"></i>
                             </button>
                             {{-- Tombol Edit --}}
                             {{-- Pastikan semua data yang dibutuhkan form ada di sini --}}
                             <button @click="openEditModal({
                                 id: {{ $event->id }},
                                 title: `{{ e($event->title) }}`,
                                 category: '{{ $event->category }}',
                                 date: '{{ $event->date }}', // Format YYYY-MM-DD
                                 time: `{{ e($event->time) }}`,
                                 location: `{{ e($event->location) }}`,
                                 description: `{{ e($event->description) }}`, // Kirim teks asli
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
    <div x-show="addModalOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @keydown.escape.window="closeAddModal()" x-cloak>
        {{-- Wrapper Modal (flex flex-col dan max-height) --}}
        <div x-show="addModalOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-xl shadow-lg w-full max-w-3xl flex flex-col" style="max-height: 90vh;" @click.away="closeAddModal()">
            {{-- Header Modal (Fixed - flex-shrink-0) --}}
            <div class="px-6 py-4 border-b flex justify-between items-center flex-shrink-0 rounded-t-xl">
                <h2 class="text-xl font-bold text-gray-800">Tambah Kegiatan Baru</h2>
                <button type="button" @click="closeAddModal()" class="text-gray-400 hover:text-gray-600 text-3xl leading-none">&times;</button>
            </div>
            {{-- Konten Form (Scrollable - flex-grow dan overflow-y-auto) --}}
            <div class="px-6 pt-6 flex-grow overflow-y-auto pb-4"> {{-- PERBAIKAN: Hapus kelas .modal-content-area, tambahkan kelas Tailwind --}}
                <form id="formTambahInner" method="POST" action="{{ route('organizer.events.store') }}" enctype="multipart/form-data">
                    @csrf
                    {{-- Tampilkan error validasi addBag DI DALAM modal --}}
                    @if ($errors->addBag->any() && old('_form_origin') === 'addModal')
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <strong class="font-bold">Oops!</strong>
                            <span class="block sm:inline">Ada beberapa kesalahan input:</span>
                            <ul class="mt-2 list-disc list-inside text-sm">
                                @foreach ($errors->addBag->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <input type="hidden" name="_form_origin" value="addModal">
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mb-4">
                        {{-- Field Judul --}}
                        <div>
                            <label for="add_title_inner_scroll" class="font-semibold block mb-1 text-sm text-gray-700">Judul Kegiatan <span class="text-red-500">*</span></label>
                            <input id="add_title_inner_scroll" type="text" name="title" class="w-full border rounded-lg p-2 mt-1 text-sm @error('title', 'addBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ old('title') }}" required>
                            @error('title', 'addBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        {{-- Kategori, Tanggal, Waktu, Lokasi, Volunteer, Kontak --}}
                        <div><label for="add_category_inner_scroll" class="font-semibold block mb-1 text-sm text-gray-700">Kategori <span class="text-red-500">*</span></label><select id="add_category_inner_scroll" name="category" class="w-full border rounded-lg p-2 mt-1 text-sm @error('category', 'addBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror focus:ring-2 focus:ring-blue-500 focus:border-transparent" required><option value="">-- Pilih Kategori --</option><option value="Pendidikan" {{ old('category') == 'Pendidikan' ? 'selected' : '' }}>Pendidikan</option><option value="Lingkungan" {{ old('category') == 'Lingkungan' ? 'selected' : '' }}>Lingkungan</option><option value="Kesehatan" {{ old('category') == 'Kesehatan' ? 'selected' : '' }}>Kesehatan</option></select>@error('category', 'addBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror</div>
                        <div><label for="add_date_inner_scroll" class="font-semibold block mb-1 text-sm text-gray-700">Tanggal <span class="text-red-500">*</span></label><input id="add_date_inner_scroll" type="date" name="date" class="w-full border rounded-lg p-2 mt-1 text-sm @error('date', 'addBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ old('date') }}" required min="{{ now()->toDateString() }}">@error('date', 'addBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror</div>
                        <div><label for="add_time_inner_scroll" class="font-semibold block mb-1 text-sm text-gray-700">Waktu <span class="text-red-500">*</span></label><input id="add_time_inner_scroll" type="text" name="time" placeholder="contoh: 09:00 - 12:00" class="w-full border rounded-lg p-2 mt-1 text-sm @error('time', 'addBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ old('time') }}" required>@error('time', 'addBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror</div>
                        <div><label for="add_location_inner_scroll" class="font-semibold block mb-1 text-sm text-gray-700">Lokasi <span class="text-red-500">*</span></label><input id="add_location_inner_scroll" type="text" name="location" class="w-full border rounded-lg p-2 mt-1 text-sm @error('location', 'addBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ old('location') }}" required>@error('location', 'addBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror</div>
                        <div><label for="add_volunteers_needed_inner_scroll" class="font-semibold block mb-1 text-sm text-gray-700">Volunteer Dibutuhkan <span class="text-red-500">*</span></label><input id="add_volunteers_needed_inner_scroll" type="number" name="volunteers_needed" class="w-full border rounded-lg p-2 mt-1 text-sm @error('volunteers_needed', 'addBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ old('volunteers_needed', 1) }}" min="1" required>@error('volunteers_needed', 'addBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror</div>
                        <div><label for="add_contact_phone_inner_scroll" class="font-semibold block mb-1 text-sm text-gray-700">Kontak Telepon <span class="text-red-500">*</span></label><input id="add_contact_phone_inner_scroll" type="text" name="contact_phone" class="w-full border rounded-lg p-2 mt-1 text-sm @error('contact_phone', 'addBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ old('contact_phone') }}" required>@error('contact_phone', 'addBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror</div>
                        <div><label for="add_contact_email_inner_scroll" class="font-semibold block mb-1 text-sm text-gray-700">Email Penyelenggara <span class="text-red-500">*</span></label><input id="add_contact_email_inner_scroll" type="email" name="contact_email" class="w-full border rounded-lg p-2 mt-1 text-sm @error('contact_email', 'addBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ old('contact_email') }}" required>@error('contact_email', 'addBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror</div>
                        <div class="md:col-span-2"><label for="add_photo_inner_scroll" class="font-semibold block mb-1 text-sm text-gray-700">Foto/Poster Kegiatan <span class="text-gray-500">(Opsional, Max 2MB)</span></label><input id="add_photo_inner_scroll" type="file" name="photo" class="w-full border rounded-lg p-2 mt-1 text-sm file:mr-4 file:py-1 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('photo', 'addBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror" accept="image/jpeg,image/png,image/jpg,image/gif">@error('photo', 'addBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror</div>
                        <div class="md:col-span-2"><label for="add_description_inner_scroll" class="font-semibold block mb-1 text-sm text-gray-700">Deskripsi Kegiatan <span class="text-red-500">*</span></label><textarea id="add_description_inner_scroll" name="description" rows="4" class="w-full border rounded-lg p-2 mt-1 text-sm @error('description', 'addBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>{{ old('description') }}</textarea>@error('description', 'addBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror</div>
                    </div>
                </form>
            </div>
             {{-- Footer Modal (Fixed - flex-shrink-0) --}}
            <div class="flex justify-end space-x-2 p-4 border-t sticky bottom-0 bg-white rounded-b-xl flex-shrink-0">
                <button type="button" @click="closeAddModal()" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 font-medium text-sm transition duration-150">Batal</button>
                <button type="submit" form="formTambahInner" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-medium text-sm transition duration-150">Simpan Kegiatan</button>
            </div>
        </div>
    </div>

    {{-- ============================================= --}}
    {{-- =========== MODAL EDIT KEGIATAN =========== --}}
    {{-- ============================================= --}}
    <div x-show="editModalOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @keydown.escape.window="closeEditModal()" x-cloak>
        {{-- Wrapper Modal (flex flex-col dan max-height) --}}
        <div x-show="editModalOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-xl shadow-lg w-full max-w-3xl flex flex-col" style="max-height: 90vh;" @click.away="closeEditModal()">
            {{-- Header Modal (Fixed - flex-shrink-0) --}}
            <div class="px-6 py-4 border-b flex justify-between items-center flex-shrink-0"><h2 class="text-xl font-bold text-gray-800">Edit Kegiatan</h2><button type="button" @click="closeEditModal()" class="text-gray-400 hover:text-gray-600 text-3xl leading-none">&times;</button></div>
             {{-- Konten Form (Scrollable - flex-grow dan overflow-y-auto) --}}
            <div class="px-6 pt-6 flex-grow overflow-y-auto pb-4"> {{-- PERBAIKAN: Hapus kelas .modal-content-area, tambahkan kelas Tailwind --}}
                {{-- Action form akan di-set oleh Alpine.js --}}
                <form id="editFormInner" x-ref="editFormRefInner" method="POST" action="" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    {{-- Tampilkan error validasi editBag DI DALAM modal --}}
                    @if ($errors->editBag->any() && old('_form_origin') === 'editModal')
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <strong class="font-bold">Oops!</strong>
                            <span class="block sm:inline">Ada beberapa kesalahan input:</span>
                             <ul class="mt-2 list-disc list-inside text-sm">
                                @foreach ($errors->editBag->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <input type="hidden" name="_form_origin" value="editModal">
                    {{-- Hidden input untuk menyimpan ID event --}}
                    <input type="hidden" name="event_id_on_edit_error" :value="editData.id">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mb-4">
                        {{-- Field Judul --}}
                        <div><label for="edit_title_inner_scroll" class="font-semibold block mb-1 text-sm text-gray-700">Judul Kegiatan <span class="text-red-500">*</span></label><input id="edit_title_inner_scroll" type="text" name="title" class="w-full border rounded-lg p-2 mt-1 text-sm @error('title', 'editBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror focus:ring-2 focus:ring-blue-500 focus:border-transparent" x-model="editData.title" required>@error('title', 'editBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror</div>
                        {{-- Field Kategori --}}
                        <div><label for="edit_category_inner_scroll" class="font-semibold block mb-1 text-sm text-gray-700">Kategori <span class="text-red-500">*</span></label><select id="edit_category_inner_scroll" name="category" x-model="editData.category" class="w-full border rounded-lg p-2 mt-1 text-sm @error('category', 'editBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror focus:ring-2 focus:ring-blue-500 focus:border-transparent" required><option value="Pendidikan">Pendidikan</option><option value="Lingkungan">Lingkungan</option><option value="Kesehatan">Kesehatan</option></select>@error('category', 'editBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror</div>
                        {{-- Tanggal, Waktu, Lokasi, Volunteer, Kontak --}}
                        <div><label for="edit_date_inner_scroll" class="font-semibold block mb-1 text-sm text-gray-700">Tanggal <span class="text-red-500">*</span></label><input id="edit_date_inner_scroll" type="date" name="date" x-model="editData.date" class="w-full border rounded-lg p-2 mt-1 text-sm @error('date', 'editBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror focus:ring-2 focus:ring-blue-500 focus:border-transparent" required min="{{ now()->toDateString() }}">@error('date', 'editBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror</div>
                        <div><label for="edit_time_inner_scroll" class="font-semibold block mb-1 text-sm text-gray-700">Waktu <span class="text-red-500">*</span></label><input id="edit_time_inner_scroll" type="text" name="time" x-model="editData.time" class="w-full border rounded-lg p-2 mt-1 text-sm @error('time', 'editBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>@error('time', 'editBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror</div>
                        <div><label for="edit_location_inner_scroll" class="font-semibold block mb-1 text-sm text-gray-700">Lokasi <span class="text-red-500">*</span></label><input id="edit_location_inner_scroll" type="text" name="location" x-model="editData.location" class="w-full border rounded-lg p-2 mt-1 text-sm @error('location', 'editBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>@error('location', 'editBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror</div>
                        <div><label for="edit_volunteers_needed_inner_scroll" class="font-semibold block mb-1 text-sm text-gray-700">Volunteer Dibutuhkan <span class="text-red-500">*</span></label><input id="edit_volunteers_needed_inner_scroll" type="number" name="volunteers_needed" x-model.number="editData.volunteers_needed" class="w-full border rounded-lg p-2 mt-1 text-sm @error('volunteers_needed', 'editBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror focus:ring-2 focus:ring-blue-500 focus:border-transparent" min="1" required>@error('volunteers_needed', 'editBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror</div>
                        <div><label for="edit_contact_phone_inner_scroll" class="font-semibold block mb-1 text-sm text-gray-700">Kontak Telepon <span class="text-red-500">*</span></label><input id="edit_contact_phone_inner_scroll" type="text" name="contact_phone" x-model="editData.contact_phone" class="w-full border rounded-lg p-2 mt-1 text-sm @error('contact_phone', 'editBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>@error('contact_phone', 'editBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror</div>
                        <div><label for="edit_contact_email_inner_scroll" class="font-semibold block mb-1 text-sm text-gray-700">Email Penyelenggara <span class="text-red-500">*</span></label><input id="edit_contact_email_inner_scroll" type="email" name="contact_email" x-model="editData.contact_email" class="w-full border rounded-lg p-2 mt-1 text-sm @error('contact_email', 'editBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>@error('contact_email', 'editBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror</div>
                        <div class="md:col-span-2"><label for="edit_photo_inner_scroll" class="font-semibold block mb-1 text-sm text-gray-700">Ganti Foto/Poster <span class="text-gray-500">(Opsional, Max 2MB)</span></label><input id="edit_photo_inner_scroll" type="file" name="photo" class="w-full border rounded-lg p-2 mt-1 text-sm file:mr-4 file:py-1 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('photo', 'editBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror" accept="image/jpeg,image/png,image/jpg,image/gif"><p class="text-xs text-gray-500 mt-1">⚠️ Kosongkan jika tidak ingin mengganti foto.</p>@error('photo', 'editBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror</div>
                        <div class="md:col-span-2"><label for="edit_description_inner_scroll" class="font-semibold block mb-1 text-sm text-gray-700">Deskripsi Kegiatan <span class="text-red-500">*</span></label><textarea id="edit_description_inner_scroll" name="description" rows="4" x-model="editData.description" class="w-full border rounded-lg p-2 mt-1 text-sm @error('description', 'editBag') border-red-500 ring-1 ring-red-500 shake-anim @else border-gray-300 @enderror focus:ring-2 focus:ring-blue-500 focus:border-transparent" required></textarea>@error('description', 'editBag') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror</div>
                    </div>
                </form>
            </div>
             {{-- Footer Modal (Fixed - flex-shrink-0) --}}
            <div class="flex justify-end space-x-2 p-4 border-t sticky bottom-0 bg-white rounded-b-xl flex-shrink-0">
                <button type="button" @click="closeEditModal()" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 font-medium text-sm transition duration-150">Batal</button>
                <button type="button" @click="submitEditForm()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-medium text-sm transition duration-150">Simpan Perubahan</button>
            </div>
        </div>
    </div>

     {{-- ============================================= --}}
    {{-- ========= MODAL DETAIL v2 (Dashboard Mini) ==== --}}
    {{-- ============================================= --}}
    <div x-show="detailModalOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @keydown.escape.window="closeDetailModal()" x-cloak>
        {{-- Wrapper Modal (flex flex-col dan max-height) --}}
        <div x-show="detailModalOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-xl shadow-lg w-full max-w-2xl flex flex-col" style="max-height: 90vh;" @click.away="closeDetailModal()">
             {{-- Header Modal (Fixed - flex-shrink-0) --}}
             <div class="px-6 py-4 border-b flex justify-between items-center flex-shrink-0">
                <h2 class="text-xl font-bold text-gray-800">Detail & Analitik Kegiatan</h2>
                <button type="button" @click="closeDetailModal()" class="text-gray-400 hover:text-gray-600 text-3xl leading-none">&times;</button>
            </div>
             {{-- Konten Detail (Scrollable - flex-grow dan overflow-y-auto) --}}
            <div class="p-6 space-y-5 text-sm flex-grow overflow-y-auto pb-4"> {{-- PERBAIKAN: Hapus kelas .modal-content-area, tambahkan kelas Tailwind --}}
                {{-- Bagian Info Utama & Foto --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                    <div class="md:col-span-1">
                        {{-- Menggunakan computed property photoUrl --}}
                        <img :src="detailData.photoUrl"
                             :alt="'Foto Kegiatan ' + detailData.title"
                             class="w-full h-auto rounded-lg object-cover border p-1 bg-gray-100 aspect-[4/3]">
                    </div>
                    <div class="md:col-span-2 space-y-1.5">
                        <h3 class="text-lg font-bold text-gray-900 mb-2" x-text="detailData.title"></h3>
                        <p><strong class="font-semibold w-24 inline-block text-gray-500">Kategori:</strong> <span x-text="detailData.category" class="text-gray-800 bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-medium"></span></p>
                        <p><strong class="font-semibold w-24 inline-block text-gray-500">Tanggal:</strong> <span x-text="formatDateAlpine(detailData.date)" class="text-gray-800"></span></p>
                        <p><strong class="font-semibold w-24 inline-block text-gray-500">Waktu:</strong> <span x-text="detailData.time" class="text-gray-800"></span></p>
                        <p><strong class="font-semibold w-24 inline-block text-gray-500">Lokasi:</strong> <span x-text="detailData.location" class="text-gray-800"></span></p>
                        <p><strong class="font-semibold w-24 inline-block text-gray-500">Kontak Telp:</strong> <span x-text="detailData.contact_phone" class="text-gray-800"></span></p>
                        <p><strong class="font-semibold w-24 inline-block text-gray-500">Kontak Email:</strong> <span x-text="detailData.contact_email" class="text-gray-800"></span></p>
                    </div>
                </div>

                 {{-- Bagian Deskripsi --}}
                 <div>
                    <strong class="font-semibold block mb-1 text-gray-600 text-sm">Deskripsi:</strong>
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
                          <div><p class="text-2xl font-bold text-gray-800" x-text="detailData.daysRemaining"></p><p class="text-xs text-gray-500 uppercase tracking-wider" x-text="detailData.daysRemaining === 1 ? 'Hari Tersisa' : 'Hari Tersisa'"></p></div>
                     </div>
                </div>

                 {{-- Bagian Daftar Relawan --}}
                 <div class="border-t pt-4">
                    <div class="flex justify-between items-center mb-3">
                         <h4 class="font-semibold text-gray-600 text-base">Daftar Pendaftar (<span x-text="detailData.volunteers ? detailData.volunteers.length : 0"></span>)</h4>
                         <input type="text" x-model="detailData.searchTerm" placeholder="Cari nama/email/telepon..."
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
                                <template x-if="!detailData.volunteers || filteredVolunteers.length === 0">
                                     <tr><td colspan="3" class="text-center py-4 text-gray-500" x-text="detailData.searchTerm ? 'Tidak ada pendaftar yang cocok.' : 'Belum ada pendaftar.'"></td></tr>
                                </template>
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
            {{-- Tombol Tutup (Fixed - flex-shrink-0) --}}
            <div class="flex justify-end p-4 border-t sticky bottom-0 bg-white rounded-b-xl flex-shrink-0">
                <button @click="closeDetailModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium text-sm transition duration-150 shadow-sm hover:shadow">Tutup</button>
            </div>
        </div>
    </div>

    {{-- ============================================= --}}
    {{-- ======== MODAL KONFIRMASI HAPUS =========== --}}
    {{-- ============================================= --}}
    <div x-show="deleteModalOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4" @keydown.escape.window="closeDeleteModal()" x-cloak>
        <div x-show="deleteModalOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-xl shadow-lg w-full max-w-md p-6 text-center" @click.away="closeDeleteModal()">
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
    function crudData(eventDataOnError = null) { // Data asli event jika ada error edit
        return {
            // Status Modal
            addModalOpen: false, editModalOpen: false, detailModalOpen: false, deleteModalOpen: false,

            // Data untuk Modal Edit (diisi saat tombol edit diklik atau saat reopen error)
            editData: {
                id: null, title: '', category: '', date: '', time: '', location: '',
                description: '', volunteers_needed: 1, contact_phone: '', contact_email: '',
                formAction: '' // Action URL form
            },

            // Data untuk Modal Detail
             detailData: {
                 id: null, title: '', category: '', date: '', time: '', location: '',
                 description: '', photo: null, volunteers_needed: 0, volunteers_count: 0,
                 volunteers: [], contact_phone: '', contact_email: '', organizer: {},
                 progressPercentage: 0, quotaRemaining: 0, daysRemaining: 'N/A', searchTerm: '',
                 // Computed property untuk URL gambar
                 get photoUrl() {
                     const defaultImage = 'https://placehold.co/800x600/e2e8f0/64748b?text=Foto+Belum+Ada&font=sans';
                     return this.photo ? `{{ asset('storage') }}/${this.photo}` : defaultImage;
                 }
             },

            // Data untuk Modal Hapus
            deleteEventId: null, deleteEventTitle: '', deleteFormAction: '',

            // Menyimpan data asli event jika terjadi error validasi edit (dari PHP)
            originalEventDataOnError: eventDataOnError,

            baseUrl: "{{ url('/') }}",

            // Computed property untuk filter volunteer (sudah benar)
            get filteredVolunteers() { /* ... logika filter ... */ },

            // === Fungsi Modal ===
            openAddModal() {
                this.addModalOpen = true;
                // Optional: Reset form tambah di sini jika perlu
                // this.$nextTick(() => document.getElementById('formTambahInner')?.reset());
            },
            closeAddModal() { this.addModalOpen = false; },

            openEditModal(event) {
                // 1. Salin data event ke editData (membuat salinan baru agar tidak mengubah data asli)
                this.editData = { ...event };
                // 2. Set action URL
                this.editData.formAction = `${this.baseUrl}/organizer/events/${event.id}`;
                // 3. Simpan data asli ke session storage (fallback)
                try { sessionStorage.setItem('lastEditEventData', JSON.stringify(event)); } catch (e) {}
                // 4. Buka modal
                this.editModalOpen = true;
                // 5. Isi textarea secara manual setelah modal render (penting!)
                 this.$nextTick(() => {
                    const textarea = document.getElementById('edit_description_inner_scroll'); // Sesuaikan ID jika perlu
                    if (textarea) textarea.value = this.editData.description || '';
                 });
            },
            closeEditModal() {
                this.editModalOpen = false;
                try { sessionStorage.removeItem('lastEditEventData'); } catch (e) {}
            },

            openDetailModal(eventData) { /* ... implementasi detail modal ... */ },
            closeDetailModal() { this.detailModalOpen = false; },

            openDeleteModal(id, title) { /* ... implementasi delete modal ... */ },
            closeDeleteModal() { /* ... implementasi close delete modal ... */ },

            // === Fungsi Submit ===
            submitEditForm() {
                const form = this.$refs.editFormRefInner;
                if (form && this.editData.formAction) {
                    form.action = this.editData.formAction; // Set action sebelum submit
                    console.log('Submitting edit form to:', form.action);
                    form.submit();
                } else {
                    console.error('Edit form reference or action URL is missing.');
                    alert('Terjadi kesalahan. Tidak dapat menyimpan perubahan.');
                }
            },
            submitDeleteForm() { /* ... implementasi submit delete ... */ },

            // === Helper ===
             nl2br(str) { /* ... implementasi nl2br ... */ },
             formatDateAlpine(dateString) { /* ... implementasi format date ... */ },

            // === Analitik untuk Modal Detail ===
            calculateDetailAnalytics() { /* ... implementasi kalkulasi analitik ... */ },

            // === Validasi & Reopen Modal on Error ===
            initValidation() {
                // Kondisi untuk membuka kembali modal EDIT
                const shouldReopenEditModal = {!! json_encode($errors->editBag->any() && old('_form_origin') === 'editModal' && session()->has('event_id_on_edit_error')) !!};
                // Kondisi untuk membuka kembali modal TAMBAH
                const shouldReopenAddModal = {!! json_encode($errors->addBag->any() && old('_form_origin') === 'addModal') !!};

                if (shouldReopenEditModal && this.originalEventDataOnError) {
                    console.log('Reopening edit modal due to validation errors for event ID:', this.originalEventDataOnError.id);
                    setTimeout(() => {
                        // 1. Buka modal edit dengan data ASLI sebelum diedit
                        this.openEditModal(this.originalEventDataOnError);

                        // 2. Setelah modal terbuka, TIMPA nilai field dengan OLD value dari PHP
                        this.$nextTick(() => {
                            console.log('Applying old values from PHP...');
                            const oldValues = {
                                title: {!! json_encode(old('title')) !!}, category: {!! json_encode(old('category')) !!},
                                date: {!! json_encode(old('date')) !!}, time: {!! json_encode(old('time')) !!},
                                location: {!! json_encode(old('location')) !!}, volunteers_needed: {!! json_encode(old('volunteers_needed')) !!},
                                contact_phone: {!! json_encode(old('contact_phone')) !!}, contact_email: {!! json_encode(old('contact_email')) !!},
                                description: {!! json_encode(old('description')) !!}
                            };

                            // Update state Alpine (editData) DAN nilai elemen DOM
                            for (const key in oldValues) {
                                if (oldValues[key] !== null) { // Hanya timpa jika ada old() value
                                    this.editData[key] = oldValues[key]; // Update state Alpine
                                    // Cari elemen berdasarkan ID unik yang baru
                                    const element = document.getElementById(`edit_${key}_inner_scroll`);
                                    if(element) {
                                         element.value = oldValues[key]; // Update nilai DOM
                                         // Trigger 'input' atau 'change' event agar Alpine (jika ada listener lain) tahu
                                         element.dispatchEvent(new Event('input', { bubbles: true }));
                                         if (element.tagName === 'SELECT') {
                                             element.dispatchEvent(new Event('change', { bubbles: true }));
                                         }
                                    }
                                }
                            }
                             console.log('Old values applied. Current editData:', JSON.parse(JSON.stringify(this.editData)));
                        });
                    }, 50); // Delay
                }
                else if (shouldReopenAddModal) {
                    console.log('Reopening add modal due to validation errors.');
                    setTimeout(() => this.openAddModal(), 50);
                }

                // Bersihkan session storage fallback
                setTimeout(() => {
                    try { sessionStorage.removeItem('lastEditEventData'); } catch(e) {}
                }, 500);
            }
        }
    }

    // Inisialisasi Lucide Icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    } else {
        console.warn('Lucide icons library not loaded.');
    }
</script>
@endpush

