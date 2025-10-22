<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relawan Aksi Sosial - Bersama untuk Kemanusiaan</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Alpine.js dipindah ke bawah --}}

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        /* Tambahan opsional */
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        [x-cloak] { display: none !important; } /* Sembunyikan elemen Alpine saat loading */
    </style>
</head>

<body class="bg-gray-50 text-gray-800">
    <!-- HEADER KONSISTEN -->
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                {{-- Logo & Judul --}}
                <div class="flex items-center space-x-3">
                    <a href="{{ route('home') }}" class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                            <i data-lucide="users" class="text-white w-6 h-6"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">Relawan Aksi Sosial</h1>
                            <p class="text-sm text-gray-500">Platform Kemanusiaan & Komunitas</p>
                        </div>
                    </a>
                </div>

                {{-- Navigasi & Status Login --}}
                <div class="flex items-center space-x-6">
                    <nav class="hidden md:flex space-x-4">
                        {{-- Link Kegiatan dengan Cek Rute Aktif --}}
                        <a href="{{ route('kegiatan.index') }}"
                           class="{{ Request::routeIs('kegiatan.index') ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:text-gray-900' }} px-3 py-2 rounded-md text-sm font-medium transition-colors">
                           Kegiatan
                        </a>
                        {{-- Link Penyelenggara dengan Cek Rute Aktif --}}
                        <a href="{{ route('organizer.events') }}"
                           class="{{ Request::routeIs('organizer.*') ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:text-gray-900' }} px-3 py-2 rounded-md text-sm font-medium transition-colors">
                           Penyelenggara
                        </a>
                    </nav>

                    {{-- Logika Tampilan Login/Logout --}}
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
                            <a href="{{ route('organizer.login') }}"
                                class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Login</a>
                            <a href="{{ route('organizer.register') }}"
                                class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition-colors">Daftar</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </header>

    <!-- KONTEN HALAMAN DINAMIS -->
    <main>
        @yield('content')
    </main>

    <!-- FOOTER KONSISTEN -->
    <footer class="bg-gray-100 py-10 border-t border-gray-200 mt-16">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-gray-600 mb-4">&copy; {{ date('Y') }} Relawan Aksi Sosial. Semua Hak Dilindungi.</p>
            <div class="flex justify-center space-x-4 text-gray-500">
                <i data-lucide="facebook" class="w-5 h-5 hover:text-blue-600 cursor-pointer"></i>
                <i data-lucide="twitter" class="w-5 h-5 hover:text-sky-500 cursor-pointer"></i>
                <i data-lucide="instagram" class="w-5 h-5 hover:text-pink-500 cursor-pointer"></i>
                <i data-lucide="mail" class="w-5 h-5 hover:text-red-500 cursor-pointer"></i>
            </div>
        </div>
    </footer>

    <!-- ============================================== -->
    <!-- SEMUA POP-UP DITARUH DI SINI -->
    <!-- ============================================== -->

    <!-- Pop-up Sukses Login -->
    @if (session('show_success_popup'))
        <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90" x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-90"
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" x-cloak
             @keydown.escape.window="show = false">
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
                        class="w-full bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg hover:bg-blue-700 transform hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                    Mulai Menjelajah
                </button>
            </div>
        </div>
    @endif

    <!-- Pop-up Konfirmasi Logout (YANG HILANG) -->
    <div x-data="{ show: false }" @keydown.escape.window="show = false" @logout-confirm.window="show = true" x-show="show"
        class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4" style="display: none;" x-cloak>
        <!-- Wadah Pop-up -->
        <div x-show="show" x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
             class="bg-white rounded-xl shadow-xl p-8 max-w-sm w-full text-center">
            <!-- Ikon Konfirmasi -->
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-8 w-8 text-red-600">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
            </div>
            <!-- Judul -->
            <h2 class="text-xl font-bold text-gray-800 mb-2">Konfirmasi Logout</h2>
            <!-- Pesan Singkat -->
            <p class="text-gray-600 mb-6">Anda yakin ingin keluar dari sesi ini?</p>
            <!-- Tombol Aksi -->
            <div class="flex justify-center gap-4">
                <button @click="show = false"
                        class="w-full bg-gray-200 text-gray-800 font-semibold py-2 px-6 rounded-lg hover:bg-gray-300 transition-colors duration-200">
                    Batal
                </button>
                <a href="{{ route('organizer.logout') }}" {{-- Pastikan nama rute logout benar --}}
                   class="w-full bg-red-600 text-white font-semibold py-2 px-6 rounded-lg hover:bg-red-700 transition-colors duration-200">
                    Ya, Logout
                </a>
            </div>
        </div>
    </div>

    <!-- Pop-up Error Middleware (dari partials) -->
    @include('partials.popup-error')

    <!-- ============================================== -->
    <!-- SEMUA SKRIP DITARUH DI BAWAH SINI -->
    <!-- ============================================== -->
    <script>
        // Inisialisasi Lucide Icons sekali di layout utama
        lucide.createIcons();
    </script>

    {{-- Tempat untuk skrip tambahan dari halaman spesifik --}}
    @stack('scripts')

    {{-- Alpine.js ditaruh paling akhir sebelum </body> --}}
    <script src="//unpkg.com/alpinejs" defer></script>

</body>
</html>

