<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sahabat Konseling</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="min-h-screen font-[Poppins]">

<div class="grid grid-cols-1 lg:grid-cols-2 min-h-screen">

    <!-- LEFT -->
    <div class="relative flex items-center justify-center">

        <!-- BG -->
        <img src="{{ asset('img/bg.png') }}" class="absolute inset-0 w-full h-full object-cover opacity-90">

        <!-- CONTENT -->
        <div class="relative z-10 w-full max-w-[480px] px-5 md:px-8">

            <!-- LOGO -->
            <div class="flex items-center gap-12 mb-6">
                <img src="{{ asset('img/logo.png') }}" class="w-12 md:w-20">
                <div>
                    <h1 class="text-lg font-semibold">Sahabat Konseling</h1>
                    <p class="text-xs text-gray-600">Bimbingan & Konseling Digital</p>
                </div>
            </div>
            
            <!-- TITLE -->
            <h2 class="text-2xl md:text-3xl font-bold gap-10 mb-4">
                Selamat Datang!
            </h2>
            

            <p class="text-sm text-gray-600 mb-6">
                Silahkan masuk untuk melanjutkan sesi konseling Anda.
            </p>

            <!-- CARD -->
            <div class="bg-white/90 p-5 md:p-6 rounded-xl shadow-lg">

                <h3 class="text-base font-semibold mb-4">Pesan Untukmu</h3>

                <form method="POST" action="{{ route('login.post') }}" class="space-y-3">
                    @csrf

                    <div>
                        <label class="text-xs text-gray-600">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="w-full mt-1 px-3 py-2 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-[#5FAF9F]">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-xs text-gray-600">Password</label>
                        <input type="password" name="password"
                            class="w-full mt-1 px-3 py-2 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-[#5FAF9F]">
                    </div>

                    <p class="text-xs text-gray-600 -mt-1">
                        Belum punya akun?
                        <a href="/register" class="font-semibold text-[#5FAF9F] hover:underline">Daftar</a>
                    </p>

                    <div class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="ingat" class="accent-[#5FAF9F] w-3 h-3">
                        <span class="text-gray-600">Ingat saya</span>
                    </div>

                    <button type="submit" class="w-full bg-[#5FAF9F] hover:bg-[#4e9c8d] text-white py-2 rounded-lg text-sm transition">
                        Masuk
                    </button>

                </form>
            </div>

        </div>
    </div>

    <!-- RIGHT -->
    <div class="flex items-center justify-center bg-[#EAF3F1]">

        <div class="w-full max-w-[400px] px-6 text-center">

            <div class="relative mb-6">
                <div class="absolute w-[240px] h-[240px] bg-white/40 rounded-full blur-2xl"></div>

                <img src="{{ asset('img/dokter.png') }}"
                     class="relative w-[180px] md:w-[220px] mx-auto">
            </div>

            <h2 class="text-xl md:text-2xl font-bold mb-2">
                Ayo Buat Akun Baru
            </h2>

            <p class="text-sm text-gray-600 mb-5">
                Bergabung untuk mendapatkan layanan konseling digital.
            </p>

        </div>

    </div>

</div>

</body>
</html>
