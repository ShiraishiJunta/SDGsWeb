<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kegiatan - Relawan Aksi Sosial</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="max-w-6xl mx-auto py-10">
        
        <div class="flex items-center mb-6">
            <a href="{{ url('/') }}" class="text-gray-500 hover:text-blue-600 mr-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Manajemen <span class="text-blue-600">Kegiatan</span></h1>
        </div>
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Daftar Kegiatan</h2>
            <button onclick="openAddModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                + Tambah Kegiatan
            </button>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full border-collapse">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">#</th>
                        <th class="px-4 py-3 text-left font-semibold">Judul</th>
                        <th class="px-4 py-3 text-left font-semibold">Kategori</th>
                        <th class="px-4 py-3 text-left font-semibold">Tanggal</th>
                        <th class="px-4 py-3 text-left font-semibold">Lokasi</th>
                        <th class="px-4 py-3 text-left font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($events as $index => $event)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-medium">{{ $event->title }}</td>
                            <td class="px-4 py-3">{{ $event->category }}</td>
                            <td class="px-4 py-3">{{ $event->date }}</td>
                            <td class="px-4 py-3">{{ $event->location }}</td>
                            <td class="px-4 py-3 flex space-x-3">
                                <button onclick="openDetailModal(
                                    '{{ $event->title }}',
                                    '{{ $event->category }}',
                                    '{{ $event->date }}',
                                    '{{ $event->location }}',
                                    '{{ $event->description }}',
                                    '{{ $event->photo }}',
                                    '{{ $event->volunteers_needed }}'
                                )" class="text-blue-600 hover:text-blue-800">
                                    👁️
                                </button>
                                <button onclick="openEditModal(
                                    '{{ $event->id }}',
                                    '{{ $event->title }}',
                                    '{{ $event->category }}',
                                    '{{ $event->date }}',
                                    '{{ $event->time }}',
                                    '{{ $event->location }}',
                                    '{{ $event->description }}',
                                    '{{ $event->volunteers_needed }}',
                                    '{{ $event->contact_phone }}',
                                    '{{ $event->contact_email }}'
                                )" class="text-yellow-500 hover:text-yellow-700">
                                    ✏️
                                </button>
                                <form method="POST" action="{{ route('organizer.events.destroy', $event->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:text-red-800" onclick="return confirm('Yakin ingin menghapus kegiatan ini?')">
                                        🗑️
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <div id="addModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-3xl p-6"> 
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Tambah Kegiatan Baru</h2>
                <button type="button" onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
            </div>
            
            <form id="formTambah" method="POST" action="{{ route('organizer.events.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="font-semibold block mb-1">Judul Kegiatan</label>
                        <input type="text" name="title" class="w-full border rounded-lg p-2 mt-1" required>
                    </div>
                    <div>
                        <label class="font-semibold block mb-1">Kategori</label>
                        <select name="category" class="w-full border rounded-lg p-2 mt-1" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="Pendidikan">Pendidikan</option>
                            <option value="Lingkungan">Lingkungan</option>
                            <option value="Kesehatan">Kesehatan</option>
                        </select>
                    </div>
                    <div>
                        <label class="font-semibold block mb-1">Tanggal</label>
                        <input type="date" name="date" class="w-full border rounded-lg p-2 mt-1" required>
                    </div>
                    <div>
                        <label class="font-semibold block mb-1">Waktu</label>
                        <input type="text" name="time" placeholder="contoh: 09:00 - 12:00" class="w-full border rounded-lg p-2 mt-1" required>
                    </div>
                    <div>
                        <label class="font-semibold block mb-1">Lokasi</label>
                        <input type="text" name="location" class="w-full border rounded-lg p-2 mt-1" required>
                    </div>
                    <div class="md:col-span-1">
                        <label class="font-semibold block mb-1">Volunteer yang Dibutuhkan</label>
                        <input type="number" name="volunteers_needed" class="w-full border rounded-lg p-2 mt-1" value="0" min="0" required>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="font-semibold block mb-1">Gambar Kegiatan</label>
                        <input type="file" name="photo" class="w-full border rounded-lg p-2 mt-1" accept="image/*" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="font-semibold block mb-1">Deskripsi Kegiatan</label>
                        <textarea name="description" rows="4" class="w-full border rounded-lg p-2 mt-1" required></textarea>
                    </div>
                    
                    <div>
                        <label class="font-semibold block mb-1">Kontak Telepon</label>
                        <input type="text" name="contact_phone" class="w-full border rounded-lg p-2 mt-1" required>
                    </div>
                    <div>
                        <label class="font-semibold block mb-1">Email Penyelenggara</label>
                        <input type="email" name="contact_email" class="w-full border rounded-lg p-2 mt-1" required>
                    </div>
                    
                </div>
                
                <div class="flex justify-end space-x-2 mt-6 border-t pt-4">
                    <button type="button" onclick="closeAddModal()" class="bg-gray-200 px-6 py-2 rounded-lg">Batal</button>
                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">Simpan Kegiatan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-3xl p-6">
            <h2 class="text-xl font-bold mb-4">Edit Kegiatan</h2>
            <form id="editForm" method="POST" action="{{ route('organizer.events.update', ['event' => 0]) }}" enctype="multipart/form-data"> 
                @csrf
                @method('PUT') 
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="font-semibold block mb-1">Judul Kegiatan</label>
                        <input id="editJudul" type="text" name="title" class="w-full border rounded-lg p-2 mt-1">
                    </div>
                    <div>
                        <label class="font-semibold block mb-1">Kategori</label>
                        <input id="editKategori" type="text" name="category" class="w-full border rounded-lg p-2 mt-1">
                    </div>
                    <div>
                        <label class="font-semibold block mb-1">Tanggal</label>
                        <input id="editTanggal" type="date" name="date" class="w-full border rounded-lg p-2 mt-1">
                    </div>
                    <div>
                        <label class="font-semibold block mb-1">Waktu</label>
                        <input id="editWaktu" type="text" name="time" class="w-full border rounded-lg p-2 mt-1">
                    </div>
                    <div>
                        <label class="font-semibold block mb-1">Lokasi</label>
                        <input id="editLokasi" type="text" name="location" class="w-full border rounded-lg p-2 mt-1">
                    </div>
                    <div>
                        <label class="font-semibold block mb-1">Volunteer yang Dibutuhkan</label>
                        <input id="editVolunteer" type="number" name="volunteers_needed" class="w-full border rounded-lg p-2 mt-1" min="0">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="font-semibold block mb-1">Gambar Kegiatan (Kosongkan jika tidak ingin ganti)</label>
                        <input type="file" name="photo" class="w-full border rounded-lg p-2 mt-1" accept="image/*">
                        <p class="text-xs text-gray-500 mt-1">⚠️ Perhatian: File gambar baru akan menggantikan yang lama.</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="font-semibold block mb-1">Deskripsi Kegiatan</label>
                        <textarea id="editDeskripsi" name="description" rows="4" class="w-full border rounded-lg p-2 mt-1"></textarea>
                    </div>

                     <div>
                        <label class="font-semibold block mb-1">Kontak Telepon</label>
                        <input id="editKontakPhone" type="text" name="contact_phone" class="w-full border rounded-lg p-2 mt-1">
                    </div>
                    <div>
                        <label class="font-semibold block mb-1">Email Penyelenggara</label>
                        <input id="editKontakEmail" type="email" name="contact_email" class="w-full border rounded-lg p-2 mt-1">
                    </div>
                </div>
                
                <div class="flex justify-end space-x-2 mt-6 border-t pt-4">
                    <button type="button" onclick="closeEditModal()" class="bg-gray-200 px-6 py-2 rounded-lg">Batal</button>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
            <h2 class="text-xl font-bold mb-4 border-b pb-2">Detail Kegiatan</h2>
            <div class="space-y-3">
                <div class="text-center">
                    <img id="detailFoto" src="" alt="Foto Kegiatan" class="w-full h-auto rounded-lg object-cover mb-2 border p-1" style="max-height: 200px; display: none;">
                    <p id="detailFotoPlaceholder" class="text-sm text-gray-500">Tidak ada foto.</p>
                </div>
                <p><strong>Judul:</strong> <span id="detailJudul"></span></p>
                <p><strong>Kategori:</strong> <span id="detailKategori"></span></p>
                <p><strong>Tanggal:</strong> <span id="detailTanggal"></span></p>
                <p><strong>Lokasi:</strong> <span id="detailLokasi"></span></p>
                <p><strong>Volunteer Dibutuhkan:</strong> <span id="detailVolunteer"></span></p>
                <div>
                    <strong class="block">Deskripsi:</strong>
                    <span id="detailDeskripsi" class="block bg-gray-50 p-3 rounded-lg text-sm whitespace-pre-wrap"></span>
                </div>
            </div>
            <div class="flex justify-end mt-6 border-t pt-4">
                <button onclick="closeDetailModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Tutup</button>
            </div>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
        }
        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
        }

        // FUNGSI EDIT DENGAN PERBAIKAN ROUTE
        function openEditModal(id, title, category, date, time, location, description, volunteers_needed, contact_phone, contact_email) {
            // 1. Perbaikan Action Form: Mengganti ID placeholder (0) di route helper dengan ID yang sebenarnya
            const form = document.getElementById('editForm');
            let actionUrl = form.action.replace('/0', '/' + id);
            form.action = actionUrl;
            
            // 2. Isi Field
            document.getElementById('editJudul').value = title;
            document.getElementById('editKategori').value = category;
            document.getElementById('editTanggal').value = date;
            document.getElementById('editLokasi').value = location;
            document.getElementById('editWaktu').value = time;
            document.getElementById('editKontakPhone').value = contact_phone;
            document.getElementById('editKontakEmail').value = contact_email;
            document.getElementById('editDeskripsi').value = description;
            document.getElementById('editVolunteer').value = volunteers_needed;
            
            document.getElementById('editModal').classList.remove('hidden');
        }
        function closeEditModal() {
            document.getElementById('editForm').action = "{{ route('organizer.events.update', ['event' => 0]) }}"; // Reset action url saat ditutup
            document.getElementById('editModal').classList.add('hidden');
        }

        // FUNGSI DETAIL (tetap)
        function openDetailModal(title, category, date, location, description, photo, volunteers_needed) {
            document.getElementById('detailJudul').innerText = title;
            document.getElementById('detailKategori').innerText = category;
            document.getElementById('detailTanggal').innerText = date;
            document.getElementById('detailLokasi').innerText = location;
            document.getElementById('detailDeskripsi').innerText = description;
            document.getElementById('detailVolunteer').innerText = volunteers_needed;
            
            const detailFoto = document.getElementById('detailFoto');
            const detailFotoPlaceholder = document.getElementById('detailFotoPlaceholder');

            if (photo && photo !== 'null' && photo !== '') {
                // Pastikan path URL foto Anda benar
                detailFoto.src = photo; 
                detailFoto.style.display = 'block';
                detailFotoPlaceholder.style.display = 'none';
            } else {
                detailFoto.style.display = 'none';
                detailFotoPlaceholder.style.display = 'block';
            }
            
            document.getElementById('detailModal').classList.remove('hidden');
        }
        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }
    </script>
</body>
</html>