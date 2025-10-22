@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-10 px-4 sm:px-6 lg:px-8">

    <!-- Notifikasi Sukses -->
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-4 rounded-xl mb-4 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Notifikasi Error Validasi (Untuk form tambah/edit) -->
    @if ($errors->any())
        <div class="bg-red-100 text-red-800 p-4 rounded-xl mb-4 shadow-sm">
            <h4 class="font-bold mb-2">Oops! Ada yang salah:</h4>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <p class="mt-2 text-sm">Silakan periksa kembali form tambah/edit kegiatan Anda.</p>
        </div>
    @endif

    <!-- Header Halaman -->
    <div class="flex items-center mb-6">
        <a href="{{ url('/') }}" class="text-gray-500 hover:text-blue-600 mr-4 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <h1 class="text-3xl font-bold text-gray-800">Manajemen <span class="text-blue-600">Kegiatan</span></h1>
    </div>

    <!-- Tombol Tambah & Judul Daftar -->
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-gray-700">Daftar Kegiatan Anda</h2>
        <button onclick="openAddModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center space-x-1 font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
              </svg>
            <span>Tambah Kegiatan</span>
        </button>
    </div>

    <!-- Tabel Daftar Kegiatan -->
    <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-200">
        <table class="min-w-full border-collapse">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-sm uppercase">#</th>
                    <th class="px-4 py-3 text-left font-semibold text-sm uppercase">Judul</th>
                    <th class="px-4 py-3 text-left font-semibold text-sm uppercase">Kategori</th>
                    <th class="px-4 py-3 text-left font-semibold text-sm uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-left font-semibold text-sm uppercase">Lokasi</th>
                    <th class="px-4 py-3 text-left font-semibold text-sm uppercase">Relawan</th>
                    <th class="px-4 py-3 text-left font-semibold text-sm uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $index => $event)
                    <tr class="border-t hover:bg-gray-50 text-sm text-gray-800">
                        <td class="px-4 py-3">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 font-medium">{{ $event->title }}</td>
                        <td class="px-4 py-3">{{ $event->category }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($event->date)->isoFormat('dddd, D MMMM Y') }}</td>
                        <td class="px-4 py-3">{{ $event->location }}</td>
                        <td class="px-4 py-3">{{ $event->registrations_count }}/{{ $event->volunteers_needed }}</td>
                        <td class="px-4 py-3 flex space-x-3">
                            {{-- Tombol Detail --}}
                            <button onclick="openDetailModal(
                                '{{ $event->title }}',
                                '{{ $event->category }}',
                                '{{ \Carbon\Carbon::parse($event->date)->isoFormat('dddd, D MMMM Y') }}',
                                '{{ $event->time }}',
                                '{{ $event->location }}',
                                `{!! nl2br(e($event->description)) !!}`,
                                '{{ $event->photo ? asset('storage/' . $event->photo) : '' }}',
                                '{{ $event->registrations_count }}/{{ $event->volunteers_needed }}',
                                '{{ $event->contact_phone }}',
                                '{{ $event->contact_email }}'
                            )" class="text-blue-600 hover:text-blue-800" title="Lihat Detail">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9 13a1 1 0 112 0v1a1 1 0 11-2 0v-1zm1-10a1 1 0 00-1 1v4a1 1 0 102 0V4a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                            </button>
                            {{-- Tombol Edit --}}
                            <button onclick="openEditModal(
                                '{{ $event->id }}',
                                '{{ $event->title }}',
                                '{{ $event->category }}',
                                '{{ $event->date }}',
                                '{{ $event->time }}',
                                '{{ $event->location }}',
                                `{!! e($event->description) !!}`,
                                '{{ $event->volunteers_needed }}',
                                '{{ $event->contact_phone }}',
                                '{{ $event->contact_email }}'
                            )" class="text-yellow-500 hover:text-yellow-700" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" /><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" /></svg>
                            </button>
                            {{-- Tombol Hapus --}}
                            <form method="POST" action="{{ route('organizer.events.destroy', $event->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Yakin ingin menghapus kegiatan \'{{ $event->title }}\' ini?')" title="Hapus">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-6 text-gray-500">
                            Anda belum membuat kegiatan apapun.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL TAMBAH --}}
<div id="addModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-3xl max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white px-6 py-4 border-b flex justify-between items-center z-10">
            <h2 class="text-xl font-bold text-gray-800">Tambah Kegiatan Baru</h2>
            <button type="button" onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600 text-3xl leading-none">&times;</button>
        </div>
        <form id="formTambah" method="POST" action="{{ route('organizer.events.store') }}" enctype="multipart/form-data" class="p-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="font-semibold block mb-1 text-sm">Judul Kegiatan <span class="text-red-500">*</span></label>
                    <input type="text" name="title" class="w-full border rounded-lg p-2 mt-1 text-sm" required>
                </div>
                <div>
                    <label class="font-semibold block mb-1 text-sm">Kategori <span class="text-red-500">*</span></label>
                    <select name="category" class="w-full border rounded-lg p-2 mt-1 text-sm" required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="Pendidikan">Pendidikan</option>
                        <option value="Lingkungan">Lingkungan</option>
                        <option value="Kesehatan">Kesehatan</option>
                    </select>
                </div>
                <div>
                    <label class="font-semibold block mb-1 text-sm">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="date" class="w-full border rounded-lg p-2 mt-1 text-sm" required>
                </div>
                <div>
                    <label class="font-semibold block mb-1 text-sm">Waktu <span class="text-red-500">*</span></label>
                    <input type="text" name="time" placeholder="contoh: 09:00 - 12:00" class="w-full border rounded-lg p-2 mt-1 text-sm" required>
                </div>
                <div>
                    <label class="font-semibold block mb-1 text-sm">Lokasi <span class="text-red-500">*</span></label>
                    <input type="text" name="location" class="w-full border rounded-lg p-2 mt-1 text-sm" required>
                </div>
                <div>
                    <label class="font-semibold block mb-1 text-sm">Volunteer Dibutuhkan <span class="text-red-500">*</span></label>
                    <input type="number" name="volunteers_needed" class="w-full border rounded-lg p-2 mt-1 text-sm" value="1" min="1" required>
                </div>

                <div>
                    <label class="font-semibold block mb-1 text-sm">Kontak Telepon <span class="text-red-500">*</span></label>
                    <input type="text" name="contact_phone" class="w-full border rounded-lg p-2 mt-1 text-sm" required>
                </div>
                <div>
                    <label class="font-semibold block mb-1 text-sm">Email Penyelenggara <span class="text-red-500">*</span></label>
                    <input type="email" name="contact_email" class="w-full border rounded-lg p-2 mt-1 text-sm" required>
                </div>

                <div class="md:col-span-2">
                    <label class="font-semibold block mb-1 text-sm">Foto/Poster Kegiatan <span class="text-gray-500">(Opsional)</span></label>
                    <input type="file" name="photo" class="w-full border rounded-lg p-2 mt-1 text-sm" accept="image/*">
                </div>
                <div class="md:col-span-2">
                    <label class="font-semibold block mb-1 text-sm">Deskripsi Kegiatan <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="4" class="w-full border rounded-lg p-2 mt-1 text-sm" required></textarea>
                </div>
            </div>

            <div class="flex justify-end space-x-2 mt-6 border-t pt-4">
                <button type="button" onclick="closeAddModal()" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 font-medium">Batal</button>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-medium">Simpan Kegiatan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT --}}
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-3xl max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white px-6 py-4 border-b flex justify-between items-center z-10">
            <h2 class="text-xl font-bold text-gray-800">Edit Kegiatan</h2>
            <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 text-3xl leading-none">&times;</button>
        </div>
        <form id="editForm" method="POST" action="" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="font-semibold block mb-1 text-sm">Judul Kegiatan <span class="text-red-500">*</span></label>
                    <input id="editJudul" type="text" name="title" class="w-full border rounded-lg p-2 mt-1 text-sm" required>
                </div>
                <div>
                    <label class="font-semibold block mb-1 text-sm">Kategori <span class="text-red-500">*</span></label>
                    <select id="editKategori" name="category" class="w-full border rounded-lg p-2 mt-1 text-sm" required>
                         <option value="Pendidikan">Pendidikan</option>
                         <option value="Lingkungan">Lingkungan</option>
                         <option value="Kesehatan">Kesehatan</option>
                    </select>
                </div>
                <div>
                    <label class="font-semibold block mb-1 text-sm">Tanggal <span class="text-red-500">*</span></label>
                    <input id="editTanggal" type="date" name="date" class="w-full border rounded-lg p-2 mt-1 text-sm" required>
                </div>
                <div>
                    <label class="font-semibold block mb-1 text-sm">Waktu <span class="text-red-500">*</span></label>
                    <input id="editWaktu" type="text" name="time" class="w-full border rounded-lg p-2 mt-1 text-sm" required>
                </div>
                <div>
                    <label class="font-semibold block mb-1 text-sm">Lokasi <span class="text-red-500">*</span></label>
                    <input id="editLokasi" type="text" name="location" class="w-full border rounded-lg p-2 mt-1 text-sm" required>
                </div>
                 <div>
                    <label class="font-semibold block mb-1 text-sm">Volunteer Dibutuhkan <span class="text-red-500">*</span></label>
                    <input id="editVolunteer" type="number" name="volunteers_needed" class="w-full border rounded-lg p-2 mt-1 text-sm" min="1" required>
                </div>

                <div>
                    <label class="font-semibold block mb-1 text-sm">Kontak Telepon <span class="text-red-500">*</span></label>
                    <input id="editKontakPhone" type="text" name="contact_phone" class="w-full border rounded-lg p-2 mt-1 text-sm" required>
                </div>
                <div>
                    <label class="font-semibold block mb-1 text-sm">Email Penyelenggara <span class="text-red-500">*</span></label>
                    <input id="editKontakEmail" type="email" name="contact_email" class="w-full border rounded-lg p-2 mt-1 text-sm" required>
                </div>

                <div class="md:col-span-2">
                    <label class="font-semibold block mb-1 text-sm">Foto/Poster Kegiatan <span class="text-gray-500">(Kosongkan jika tidak ingin ganti)</span></label>
                    <input type="file" name="photo" class="w-full border rounded-lg p-2 mt-1 text-sm" accept="image/*">
                    <p class="text-xs text-gray-500 mt-1">⚠️ Perhatian: File gambar baru akan menggantikan yang lama.</p>
                </div>
                <div class="md:col-span-2">
                    <label class="font-semibold block mb-1 text-sm">Deskripsi Kegiatan <span class="text-red-500">*</span></label>
                    <textarea id="editDeskripsi" name="description" rows="4" class="w-full border rounded-lg p-2 mt-1 text-sm" required></textarea>
                </div>
            </div>

            <div class="flex justify-end space-x-2 mt-6 border-t pt-4">
                <button type="button" onclick="closeEditModal()" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 font-medium">Batal</button>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-medium">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL DETAIL --}}
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-lg max-h-[90vh] overflow-y-auto">
         <div class="sticky top-0 bg-white px-6 py-4 border-b flex justify-between items-center z-10">
            <h2 class="text-xl font-bold text-gray-800">Detail Kegiatan</h2>
            <button type="button" onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600 text-3xl leading-none">&times;</button>
        </div>
        <div class="p-6 space-y-3">
            <div class="text-center mb-4">
                <img id="detailFoto" src="" alt="Foto Kegiatan" class="w-full h-auto rounded-lg object-cover mb-2 border p-1 max-h-60" style="display: none;">
                <p id="detailFotoPlaceholder" class="text-sm text-gray-500 py-10 bg-gray-100 rounded-lg">Tidak ada foto.</p>
            </div>
            <p><strong class="font-semibold">Judul:</strong> <span id="detailJudul"></span></p>
            <p><strong class="font-semibold">Kategori:</strong> <span id="detailKategori"></span></p>
            <p><strong class="font-semibold">Tanggal:</strong> <span id="detailTanggal"></span></p>
            <p><strong class="font-semibold">Waktu:</strong> <span id="detailWaktu"></span></p>
            <p><strong class="font-semibold">Lokasi:</strong> <span id="detailLokasi"></span></p>
            <p><strong class="font-semibold">Relawan:</strong> <span id="detailVolunteer"></span></p>
             <p><strong class="font-semibold">Kontak Telepon:</strong> <span id="detailKontakPhone"></span></p>
            <p><strong class="font-semibold">Kontak Email:</strong> <span id="detailKontakEmail"></span></p>
            <div>
                <strong class="font-semibold block mb-1">Deskripsi:</strong>
                <span id="detailDeskripsi" class="block bg-gray-50 p-3 rounded-lg text-sm whitespace-pre-wrap border"></span>
            </div>
        </div>
        <div class="flex justify-end mt-6 border-t p-4 sticky bottom-0 bg-white">
            <button onclick="closeDetailModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium">Tutup</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openAddModal() {
        // Reset form jika perlu
        document.getElementById('formTambah').reset();
        document.getElementById('addModal').classList.remove('hidden');
        document.getElementById('addModal').classList.add('flex'); // Gunakan flex untuk centering
    }
    function closeAddModal() {
        document.getElementById('addModal').classList.add('hidden');
        document.getElementById('addModal').classList.remove('flex');
    }

    function openEditModal(id, title, category, date, time, location, description, volunteers_needed, contact_phone, contact_email) {
        const form = document.getElementById('editForm');
        // Bangun URL action yang benar
        let actionUrl = "{{ route('organizer.events.update', ['event' => ':id']) }}";
        form.action = actionUrl.replace(':id', id);

        // Isi field form
        document.getElementById('editJudul').value = title;
        document.getElementById('editKategori').value = category; // Pastikan value select sesuai
        document.getElementById('editTanggal').value = date;
        document.getElementById('editWaktu').value = time;
        document.getElementById('editLokasi').value = location;
        document.getElementById('editDeskripsi').value = description; // Gunakan innerText atau value tergantung element
        document.getElementById('editVolunteer').value = volunteers_needed;
        document.getElementById('editKontakPhone').value = contact_phone;
        document.getElementById('editKontakEmail').value = contact_email;

        document.getElementById('editModal').classList.remove('hidden');
        document.getElementById('editModal').classList.add('flex'); // Gunakan flex untuk centering
    }
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.getElementById('editModal').classList.remove('flex');
         // Reset action url saat ditutup (opsional tapi baik)
        document.getElementById('editForm').action = "";
    }

    function openDetailModal(title, category, date, time, location, description, photo, volunteers, contact_phone, contact_email) {
        document.getElementById('detailJudul').innerText = title;
        document.getElementById('detailKategori').innerText = category;
        document.getElementById('detailTanggal').innerText = date; // Sudah diformat dari Blade
        document.getElementById('detailWaktu').innerText = time;
        document.getElementById('detailLokasi').innerText = location;
        document.getElementById('detailDeskripsi').innerHTML = description; // Gunakan innerHTML karena ada <br>
        document.getElementById('detailVolunteer').innerText = volunteers;
        document.getElementById('detailKontakPhone').innerText = contact_phone;
        document.getElementById('detailKontakEmail').innerText = contact_email;

        const detailFoto = document.getElementById('detailFoto');
        const detailFotoPlaceholder = document.getElementById('detailFotoPlaceholder');

        if (photo && photo !== 'null' && photo !== '') {
            detailFoto.src = photo; // URL sudah lengkap dari asset()
            detailFoto.style.display = 'block';
            detailFotoPlaceholder.style.display = 'none';
        } else {
            detailFoto.src = ''; // Kosongkan src
            detailFoto.style.display = 'none';
            detailFotoPlaceholder.style.display = 'block';
        }

        document.getElementById('detailModal').classList.remove('hidden');
        document.getElementById('detailModal').classList.add('flex'); // Gunakan flex untuk centering
    }
    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
        document.getElementById('detailModal').classList.remove('flex');
    }

     // Tambahkan event listener untuk menutup modal saat klik di luar area modal
    window.addEventListener('click', function(event) {
        const addModal = document.getElementById('addModal');
        const editModal = document.getElementById('editModal');
        const detailModal = document.getElementById('detailModal');

        if (event.target == addModal) {
            closeAddModal();
        }
        if (event.target == editModal) {
            closeEditModal();
        }
        if (event.target == detailModal) {
            closeDetailModal();
        }
    });

    // Cek jika ada error validasi setelah submit form modal, buka kembali modalnya
    @if ($errors->any())
        @if (old('_form_origin') === 'addModal') // Kita perlu tambahkan input tersembunyi untuk ini
             openAddModal();
        @elseif (old('_form_origin') === 'editModal') // Kita perlu tambahkan input tersembunyi untuk ini
            // Perlu cara untuk memanggil openEditModal lagi dengan data lama jika edit gagal
            // Ini lebih kompleks, mungkin lebih baik tampilkan error di atas tabel saja
        @endif
    @endif
</script>
@endpush

