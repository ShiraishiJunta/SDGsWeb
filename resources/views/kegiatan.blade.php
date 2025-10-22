@extends('layouts.app')

@section('content')
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Hero -->
    <div class="text-center mb-12">
      <h2 class="text-4xl font-bold text-gray-900 mb-4">Temukan Kesempatan Beraksi</h2>
      <p class="text-xl text-gray-600 max-w-3xl mx-auto">
        Jelajahi berbagai kegiatan sosial dan kemanusiaan yang membutuhkan partisipasi Anda.
      </p>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-8 border border-gray-200">
      <div class="flex flex-col md:flex-row gap-4">
        <div class="flex-1 relative">
          <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5"></i>
          <input id="search" type="text" placeholder="Cari judul atau nama penyelenggara..."
            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
        </div>
        <div class="flex items-center space-x-2">
          <i data-lucide="filter" class="text-gray-400 w-5 h-5"></i>
          <select id="categoryFilter"
            class="border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
            <option value="all">Semua Kategori</option>
            <option value="Pendidikan">Pendidikan</option>
            <option value="Lingkungan">Lingkungan</option>
            <option value="Kesehatan">Kesehatan</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Grid Kegiatan -->
    <div id="eventGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {{-- Konten akan diisi oleh JavaScript --}}
    </div>

    <!-- Loading Indicator -->
    <div id="loadingIndicator" class="text-center py-12 text-gray-500">
        <svg class="animate-spin h-8 w-8 text-blue-600 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
        <p>Memuat kegiatan...</p>
    </div>

    <!-- Jika Tidak Ada Hasil -->
    <div id="noResult" class="hidden text-center py-12">
      <div class="text-gray-400 mb-4">
        <i data-lucide="search-x" class="w-16 h-16 mx-auto"></i>
      </div>
      <h3 class="text-xl font-medium text-gray-900 mb-2">Tidak ada kegiatan ditemukan</h3>
      <p class="text-gray-600">Coba ubah kata kunci pencarian atau filter kategori Anda.</p>
    </div>
</main>
@endsection

@push('scripts')
<script>
    const eventGrid = document.getElementById('eventGrid');
    const searchInput = document.getElementById('search');
    const categoryFilter = document.getElementById('categoryFilter');
    const noResult = document.getElementById('noResult');
    const loadingIndicator = document.getElementById('loadingIndicator');

    let events = []; // Simpan data asli

    // --- Fungsi fetchEvents ---
    async function fetchEvents() {
      loadingIndicator.style.display = 'block'; // Tampilkan loading
      eventGrid.innerHTML = ''; // Kosongkan grid
      noResult.classList.add('hidden'); // Sembunyikan pesan "tidak ada hasil"

      try {
        const res = await fetch('/api/event'); // Panggil API Anda
        if (!res.ok) {
           throw new Error(`HTTP error! status: ${res.status}`);
        }
        events = await res.json();
        renderEvents(events); // Tampilkan semua event awal
      } catch (error) {
          console.error("Gagal mengambil data event:", error);
          eventGrid.innerHTML = '<p class="text-center text-red-600 col-span-full">Terjadi kesalahan saat memuat data.</p>';
          noResult.classList.add('hidden');
      } finally {
          loadingIndicator.style.display = 'none'; // Sembunyikan loading
      }
    }

    // --- Fungsi formatDate ---
    function formatDate(dateString) {
      if (!dateString) return 'Tanggal tidak valid';
      try {
          const date = new Date(dateString);
          return date.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
      } catch (e) {
          return 'Format tanggal salah';
      }
    }

    // --- Fungsi renderEvents ---
    function renderEvents(list) {
      eventGrid.innerHTML = ''; // Kosongkan grid sebelum render
      if (list.length === 0) {
        noResult.classList.remove('hidden');
        loadingIndicator.style.display = 'none'; // Pastikan loading disembunyikan
        return;
      }
      noResult.classList.add('hidden');

      list.forEach(ev => {
        const div = document.createElement('div');
        div.className = 'bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden border border-gray-100 flex flex-col';

        // --- BAGIAN FOTO YANG DIPERBAIKI ---
        const photoUrl = ev.photo ? `/storage/${ev.photo}` : 'https://placehold.co/800x600/e0e0e0/7c7c7c?text=Kegiatan';
        // ------------------------------------

        div.innerHTML = `
          <img src="${photoUrl}" alt="Foto Kegiatan ${ev.title}" class="w-full h-48 object-cover">
          <div class="p-6 flex flex-col flex-grow">
            <span class="px-3 py-1 rounded-full text-xs font-medium self-start ${
              ev.category === 'Pendidikan' ? 'bg-blue-100 text-blue-700' :
              ev.category === 'Lingkungan' ? 'bg-green-100 text-green-700' :
              'bg-purple-100 text-purple-700'
            }">${ev.category}</span>
            <h3 class="text-lg font-bold text-gray-900 mt-2 mb-2 flex-grow">${ev.title}</h3>
            <p class="text-gray-600 mb-4 line-clamp-2 text-sm">${ev.description || 'Tidak ada deskripsi.'}</p>
            <div class="space-y-1.5 mb-4 text-gray-600 text-xs border-t pt-3 mt-auto">
              <div><i data-lucide="calendar" class="inline w-3.5 h-3.5 mr-1.5 relative -top-px"></i>${formatDate(ev.date)}</div>
              <div><i data-lucide="clock" class="inline w-3.5 h-3.5 mr-1.5 relative -top-px"></i>${ev.time || '-'}</div>
              <div><i data-lucide="map-pin" class="inline w-3.5 h-3.5 mr-1.5 relative -top-px"></i>${ev.location || '-'}</div>
              <div><i data-lucide="users" class="inline w-3.5 h-3.5 mr-1.5 relative -top-px"></i>${ev.registrations_count || 0}/${ev.volunteers_needed || '?'} relawan</div>
            </div>
            <div class="flex justify-between items-center border-t pt-3">
              <div class="text-xs font-medium text-gray-700">Oleh: ${ev.organizer ? ev.organizer.name : 'Tidak Diketahui'}</div>
              <button class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold">Saya Tertarik</button>
            </div>
          </div>`;
        eventGrid.appendChild(div);
      });

      lucide.createIcons(); // Panggil ulang untuk ikon di dalam konten dinamis
      loadingIndicator.style.display = 'none'; // Sembunyikan loading setelah render
    }

    // --- Fungsi filterEvents ---
    function filterEvents() {
      const keyword = searchInput.value.toLowerCase();
      const category = categoryFilter.value;
      const filtered = events.filter(ev => {
        const titleMatch = ev.title.toLowerCase().includes(keyword);
        const organizerMatch = ev.organizer && ev.organizer.name.toLowerCase().includes(keyword);
        const categoryMatch = category === 'all' || ev.category === category;

        return (titleMatch || organizerMatch) && categoryMatch;
      });
      renderEvents(filtered);
    }

    // --- Event Listeners ---
    searchInput.addEventListener('input', filterEvents);
    categoryFilter.addEventListener('change', filterEvents);

    // --- Panggil fetchEvents saat halaman dimuat ---
    fetchEvents();
</script>
@endpush

