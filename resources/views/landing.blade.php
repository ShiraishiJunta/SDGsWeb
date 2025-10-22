<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Relawan Aksi Sosial - Bersama untuk Kemanusiaan</title>

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="//unpkg.com/alpinejs" defer></script>

  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="bg-gray-50 text-gray-800">
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
          <a href="{{ route('kegiatan.index') }}"
            class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors">Kegiatan</a>
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
              <button @click.prevent="$dispatch('logout-confirm')"
                class="bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-red-700 transition-colors">
                Logout
              </button>
            </div>
          @else
            <div class="flex space-x-3">
              <a href="/organizer/login"
                class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Login</a>
              <a href="/organizer/register"
                class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition-colors">Daftar</a>
            </div>
          @endif
      </div>
    </div>
  </header>

  <!-- HERO SECTION -->
  <section class="bg-blue-50 py-16 text-center">
    <div class="max-w-4xl mx-auto px-6">
      <h2 class="text-4xl font-extrabold text-gray-900 mb-4">Bergabunglah dengan Komunitas Relawan</h2>
      <p class="text-lg text-gray-600 mb-8">
        Temukan kegiatan sosial dan kemanusiaan di seluruh Indonesia. Jadilah bagian dari perubahan positif untuk dunia
        yang lebih baik.
      </p>
      <a href="/organizer/register"
        class="bg-blue-600 text-white px-6 py-3 rounded-lg text-lg font-semibold hover:bg-blue-700 transition-all">Gabung
        Sekarang</a>
    </div>
  </section>

  <!-- FITUR UTAMA -->
  <section id="fitur" class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-6 text-center">
      <h3 class="text-3xl font-bold text-gray-900 mb-10">Fitur Utama</h3>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="p-8 bg-gray-50 rounded-2xl shadow-sm hover:shadow-md transition">
          <i data-lucide="heart-handshake" class="w-12 h-12 mx-auto text-blue-600 mb-4"></i>
          <h4 class="text-xl font-semibold mb-2">Kegiatan Sosial</h4>
          <p class="text-gray-600">Temukan berbagai kegiatan sosial dari berbagai organisasi di seluruh daerah.</p>
        </div>
        <div class="p-8 bg-gray-50 rounded-2xl shadow-sm hover:shadow-md transition">
          <i data-lucide="users" class="w-12 h-12 mx-auto text-green-600 mb-4"></i>
          <h4 class="text-xl font-semibold mb-2">Komunitas Relawan</h4>
          <p class="text-gray-600">Bergabunglah dalam komunitas relawan dan kolaborasi untuk dampak yang lebih besar.
          </p>
        </div>
        <div class="p-8 bg-gray-50 rounded-2xl shadow-sm hover:shadow-md transition">
          <i data-lucide="calendar" class="w-12 h-12 mx-auto text-purple-600 mb-4"></i>
          <h4 class="text-xl font-semibold mb-2">Manajemen Event</h4>
          <p class="text-gray-600">Atur dan promosikan kegiatan sosial Anda dengan sistem yang mudah digunakan.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- STATISTIK -->
  <section class="bg-blue-600 py-16 text-white">
    <div class="max-w-7xl mx-auto px-6 text-center">
      <h3 class="text-3xl font-bold mb-10">Bersama Kita Berdampak Nyata</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div>
          <h4 class="text-5xl font-bold mb-2">120+</h4>
          <p class="text-blue-100">Kegiatan Sosial</p>
        </div>
        <div>
          <h4 class="text-5xl font-bold mb-2">500+</h4>
          <p class="text-blue-100">Relawan Aktif</p>
        </div>
        <div>
          <h4 class="text-5xl font-bold mb-2">80+</h4>
          <p class="text-blue-100">Organisasi Terdaftar</p>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="py-16 bg-white text-center">
    <div class="max-w-3xl mx-auto px-6">
      <h3 class="text-3xl font-bold text-gray-900 mb-4">Siap Menjadi Bagian dari Aksi Sosial?</h3>
      <p class="text-lg text-gray-600 mb-8">Mari kita ciptakan perubahan kecil yang berdampak besar untuk masyarakat.
      </p>
      <a href="/organizer/register"
        class="bg-blue-600 text-white px-6 py-3 rounded-lg text-lg font-semibold hover:bg-blue-700 transition-all">Daftar
        Sekarang</a>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="bg-gray-100 py-10 border-t border-gray-200">
    <div class="max-w-7xl mx-auto px-6 text-center">
      <p class="text-gray-600 mb-4">&copy; 2025 Relawan Aksi Sosial. Semua Hak Dilindungi.</p>
      <div class="flex justify-center space-x-4 text-gray-500">
        <i data-lucide="facebook" class="w-5 h-5 hover:text-blue-600 cursor-pointer"></i>
        <i data-lucide="twitter" class="w-5 h-5 hover:text-sky-500 cursor-pointer"></i>
        <i data-lucide="instagram" class="w-5 h-5 hover:text-pink-500 cursor-pointer"></i>
        <i data-lucide="mail" class="w-5 h-5 hover:text-red-500 cursor-pointer"></i>
      </div>
    </div>
  </footer>

  <script>
    lucide.createIcons();
  </script>
  @include('partials.popup-error')
</body>

</html>