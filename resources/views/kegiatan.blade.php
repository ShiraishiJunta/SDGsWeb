<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Relawan Aksi Sosial - Platform Kemanusiaan & Komunitas</title>

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="//unpkg.com/alpinejs" defer></script>

  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>

  <style>
    /* Tambahan opsional */
    .line-clamp-2 {
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
  </style>
</head>

<body class="bg-gray-50">
  {{-- Letakkan di dalam file index.blade.php atau file layout utama (app.blade.php) --}}

  @if (session('show_success_popup'))
    <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300"
      x-transition:enter-start="opacity-0 transform scale-90" x-transition:enter-end="opacity-100 transform scale-100"
      x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100"
      x-transition:leave-end="opacity-0 transform scale-90"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
      <div class="bg-white rounded-2xl shadow-xl p-8 max-w-sm w-full text-center">

        <div class="mx-auto w-20 h-20 mb-4">
          <svg class="w-full h-full" viewBox="0 0 52 52">
            <circle class="stroke-current text-green-100" cx="26" cy="26" r="25" fill="none" stroke-width="2" />
            <path class="stroke-current text-green-500" fill="none" stroke-linecap="round" stroke-linejoin="round"
              stroke-width="3" d="M14.1 27.2l7.1 7.2 16.7-16.8" stroke-dasharray="48" stroke-dashoffset="48"
              x-init="$el.style.transition = 'stroke-dashoffset 1s ease-out 0.5s'; $el.style.strokeDashoffset = 0;">
            </path>
          </svg>
        </div>

        <h2 class="text-2xl font-bold text-gray-800 mb-2">Selamat Datang Kembali!</h2>

        <p class="text-gray-500 mb-6">Anda berhasil masuk.</p>

        <button @click="show = false"
          class="w-full bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg
                     hover:bg-blue-700 transform hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
          Mulai Menjelajah
        </button>
      </div>
    </div>
  @endif
  {{-- Letakkan kode ini di dalam index.blade.php --}}

  <!-- HEADER -->
  <header class="bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
            <i data-lucide="users" class="text-white w-6 h-6"></i>
          </div>
          <div>
            <h1 class="text-xl font-bold text-gray-900">Relawan Aksi Sosial</h1>
            <p class="text-sm text-gray-500">Platform Kemanusiaan & Komunitas</p>
          </div>
        </div>
        <nav class="hidden md:flex space-x-6">
          <button class="tab-btn text-blue-600 bg-blue-50 px-3 py-2 rounded-md text-sm font-medium transition-colors"
            data-tab="events">Kegiatan</button>
          <nav class="hidden md:flex space-x-6">
            <a href="{{ route('organizer.events') }}"
              class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors">Penyelenggara</a>
          </nav>

          @php
            $organizer = session('organizer');
          @endphp

          @if ($organizer)
            <div class="flex items-center space-x-3">
              <span class="text-gray-700 text-sm font-medium">Halo, {{ $organizer->name }}</span>
              <a href="/organizer/logout"
                class="bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-red-700 transition-colors">Logout</a>
            </div>
          @else
            <div class="flex space-x-3">
              <a href="/organizer/login"
                class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Login</a>
              <a href="/organizer/register"
                class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition-colors">Daftar</a>
            </div>
          @endif
        </nav>
      </div>
    </div>
  </header>

  <!-- MAIN -->
  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Hero -->
    <div class="text-center mb-12">
      <h2 class="text-4xl font-bold text-gray-900 mb-4">Jadilah Bagian dari Perubahan</h2>
      <p class="text-xl text-gray-600 max-w-3xl mx-auto">
        Temukan kegiatan sosial dan kemanusiaan di sekitarmu. Bergabunglah sebagai relawan dan berikan dampak positif
        bagi masyarakat.
      </p>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-8">
      <div class="flex flex-col md:flex-row gap-4">
        <div class="flex-1 relative">
          <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5"></i>
          <input id="search" type="text" placeholder="Cari kegiatan sosial..."
            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <div class="flex items-center space-x-2">
          <i data-lucide="filter" class="text-gray-400 w-5 h-5"></i>
          <select id="categoryFilter"
            class="border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <option value="all">Semua Kategori</option>
            <option value="Pendidikan">Pendidikan</option>
            <option value="Lingkungan">Lingkungan</option>
            <option value="Kesehatan">Kesehatan</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Grid Kegiatan -->
    <div id="eventGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></div>

    <!-- Jika Tidak Ada -->
    <div id="noResult" class="hidden text-center py-12">
      <div class="text-gray-400 mb-4">
        <i data-lucide="search" class="w-16 h-16 mx-auto"></i>
      </div>
      <h3 class="text-xl font-medium text-gray-900 mb-2">Tidak ada kegiatan ditemukan</h3>
      <p class="text-gray-600">Coba ubah kata kunci pencarian atau filter kategori</p>
    </div>
  </main>

  <!-- FOOTER -->
  <footer class="bg-gray-900 text-white py-12 mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <div>
          <div class="flex items-center space-x-3 mb-4">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
              <i data-lucide="users" class="w-5 h-5 text-white"></i>
            </div>
            <span class="text-xl font-bold">Relawan Aksi Sosial</span>
          </div>
          <p class="text-gray-400">Platform untuk menghubungkan relawan dengan kegiatan sosial dan kemanusiaan.</p>
        </div>
        <div>
          <h4 class="font-semibold mb-4">Tentang</h4>
          <ul class="space-y-2 text-gray-400">
            <li><a href="#" class="hover:text-white">Tentang Kami</a></li>
            <li><a href="#" class="hover:text-white">SDG 10 & 17</a></li>
            <li><a href="#" class="hover:text-white">Kemitraan</a></li>
          </ul>
        </div>
        <div>
          <h4 class="font-semibold mb-4">Untuk Relawan</h4>
          <ul class="space-y-2 text-gray-400">
            <li><a href="#" class="hover:text-white">Cari Kegiatan</a></li>
            <li><a href="#" class="hover:text-white">Panduan</a></li>
            <li><a href="#" class="hover:text-white">FAQ</a></li>
          </ul>
        </div>
        <div>
          <h4 class="font-semibold mb-4">Untuk Penyelenggara</h4>
          <ul class="space-y-2 text-gray-400">
            <li><a href="#" class="hover:text-white">Daftar</a></li>
            <li><a href="#" class="hover:text-white">Verifikasi</a></li>
            <li><a href="#" class="hover:text-white">Buat Kegiatan</a></li>
          </ul>
        </div>
      </div>
      <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
        <p>&copy; 2024 Relawan Aksi Sosial. Semua hak dilindungi.</p>
      </div>
    </div>
  </footer>

  <script>
    const eventGrid = document.getElementById('eventGrid');
    const searchInput = document.getElementById('search');
    const categoryFilter = document.getElementById('categoryFilter');
    const noResult = document.getElementById('noResult');

    let events = [];

    async function fetchEvents() {
      const res = await fetch('/api/event');
      events = await res.json();
      console.log(events); // Boleh dihapus jika sudah yakin
      renderEvents(events);
    }

    function formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    }

    function renderEvents(list) {
      eventGrid.innerHTML = '';
      if (list.length === 0) {
        noResult.classList.remove('hidden');
        return;
      }
      noResult.classList.add('hidden');

      list.forEach(ev => {
        const div = document.createElement('div');
        div.className = 'bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden border border-gray-100';
        div.innerHTML = `
      <div class="p-6">
        <span class="px-3 py-1 rounded-full text-xs font-medium ${ev.category === 'Pendidikan' ? 'bg-blue-100 text-blue-700' :
            ev.category === 'Lingkungan' ? 'bg-green-100 text-green-700' :
              'bg-purple-100 text-purple-700'
          }">${ev.category}</span>
        <h3 class="text-xl font-bold text-gray-900 mb-2">${ev.title}</h3>
        <p class="text-gray-600 mb-4 line-clamp-2">${ev.description}</p>
        <div class="space-y-2 mb-4 text-gray-600 text-sm">
          <div><i data-lucide="calendar" class="inline w-4 h-4 mr-2"></i>${formatDate(ev.date)}</div>
          <div><i data-lucide="clock" class="inline w-4 h-4 mr-2"></i>${ev.time}</div>
          <div><i data-lucide="map-pin" class="inline w-4 h-4 mr-2"></i>${ev.location}</div>
          <div><i data-lucide="users" class="inline w-4 h-4 mr-2"></i>${ev.registrations_count}/${ev.volunteers_needed} relawan</div>
        </div>
        <div class="flex justify-between items-center">
          <div class="text-sm font-medium text-gray-700">Oleh: ${ev.organizer.name}</div>
          <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">Saya Tertarik</button>
        </div>
      </div>`;
        eventGrid.appendChild(div);
      });

      lucide.createIcons();
    }

    function filterEvents() {
      const keyword = searchInput.value.toLowerCase();
      const category = categoryFilter.value;
      const filtered = events.filter(ev => {
        // ... kode filterEvents
        const matchesSearch =
          ev.title.toLowerCase().includes(keyword) ||
          // ev.description.toLowerCase().includes(keyword) || // Deskripsi belum ada
          ev.organizer.name.toLowerCase().includes(keyword);
        // ...
        const matchesCategory = category === 'all' || ev.category === category;
        return matchesSearch && matchesCategory;
      });
      renderEvents(filtered);
    }

    searchInput.addEventListener('input', filterEvents);
    categoryFilter.addEventListener('change', filterEvents);

    // Fetch data saat halaman dibuka
    fetchEvents();
  </script>
  <script>lucide.createIcons();</script>

</body>

</html>