<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Penyelenggara</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="//unpkg.com/alpinejs" defer></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
  <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md">
    <h2 class="text-2xl font-bold mb-6 text-center">Login Penyelenggara</h2>

    {{-- Ini bagian untuk menampilkan error jika login gagal --}}
    @error('email')
      <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
        <span>{{ $message }}</span>
      </div>
    @enderror

    <form action="/organizer/login" method="POST">
    @csrf
    <div class="mb-4">
        <label for="email" class="block text-gray-700 mb-2">Email</label>
        {{-- Menggunakan old('email') agar email tidak hilang jika login gagal --}}
        <input type="email" id="email" name="email" value="{{ old('email') }}" required class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
    </div>

    <div class="mb-6">
        <label for="password" class="block text-gray-700 mb-2">Password</label>
        <input type="password" id="password" name="password" required class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
    </div>

    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold transition-colors">
        Login
    </button>
    </form>

    <p class="text-center text-gray-600 mt-4 text-sm">
      Belum punya akun? <a href="/organizer/register" class="text-blue-600 hover:underline">Daftar di sini</a>
    </p>
  </div>
</body>
</html>