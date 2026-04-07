<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="font-[Poppins] bg-[#F5F7F6] overflow-y-auto">

<!-- BG -->
<div class="fixed inset-0 -z-10">
    <img src="{{ asset('img/bg.png') }}" class="w-full h-full object-cover opacity-90">
</div>

<!-- CONTENT -->
<div class="min-h-screen flex flex-col px-5 md:px-10 py-8 ">

    <!-- LOGO -->
    <div class="flex items-center gap-3 mb-6">
        <img src="{{ asset('img/logo.png' ) }}" class="w-12 md:w-14">
        <div>
            <h1 class="text-lg font-semibold">Sahabat Konseling</h1>
            <p class="text-xs text-gray-600">Bimbingan & Konseling Digital</p>
        </div>
    </div>

    <!-- TITLE -->
    <div class="text-center mb-6">
        <h2 class="text-2xl md:text-3xl font-bold mb-2">
            Ayo Buat Akun Baru
        </h2>

        <p class="text-sm text-gray-600 max-w-md mx-auto">
            Bergabung untuk mendapatkan layanan konseling digital.
        </p>
    </div>

    <!-- CARD -->
    <div class="flex justify-center">
        <div class="w-full max-w-3xl bg-white/90 rounded-xl shadow-lg p-5 md:p-6">

            <form method="POST" action="{{ route('register.post') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf

                <!-- LEFT -->
                <div class="space-y-3">
                    <div>
                        <label class="text-xs text-gray-600">Nama Lengkap</label>
                        <input type="text" name="nama" value="{{ old('nama') }}"
                            class="w-full mt-1 px-3 py-2 rounded-lg border text-sm">
                        @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-xs text-gray-600">NIM</label>
                        <input type="text" name="nim" value="{{ old('nim') }}"
                            class="w-full mt-1 px-3 py-2 rounded-lg border text-sm">
                        @error('nim') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-xs text-gray-600">Program Studi</label>
                        <select name="jurusan" class="w-full mt-1 px-3 py-2 rounded-lg border text-sm text-gray-700 bg-white">
                            <option value="" disabled selected>Pilih Program Studi</option>
                            <option value="D3 Teknologi Informasi"                  {{ old('jurusan') == 'D3 Teknologi Informasi' ? 'selected' : '' }}>D3 Teknologi Informasi</option>
                            <option value="D3 Teknologi Komputer"                   {{ old('jurusan') == 'D3 Teknologi Komputer' ? 'selected' : '' }}>D3 Teknologi Komputer</option>
                            <option value="D4 Teknologi Rekayasa Perangkat Lunak"   {{ old('jurusan') == 'D4 Teknologi Rekayasa Perangkat Lunak' ? 'selected' : '' }}>D4 Teknologi Rekayasa Perangkat Lunak</option>
                            <option value="S1 Sistem Informasi"                     {{ old('jurusan') == 'S1 Sistem Informasi' ? 'selected' : '' }}>S1 Sistem Informasi</option>
                            <option value="S1 Manajemen Rekayasa"                   {{ old('jurusan') == 'S1 Manajemen Rekayasa' ? 'selected' : '' }}>S1 Manajemen Rekayasa</option>
                            <option value="S1 Teknik Elektro"                       {{ old('jurusan') == 'S1 Teknik Elektro' ? 'selected' : '' }}>S1 Teknik Elektro</option>
                            <option value="S1 Informatika"                          {{ old('jurusan') == 'S1 Informatika' ? 'selected' : '' }}>S1 Informatika</option>
                            <option value="S1 Teknik Bioproses"                     {{ old('jurusan') == 'S1 Teknik Bioproses' ? 'selected' : '' }}>S1 Teknik Bioproses</option>
                            <option value="S1 Bioteknologi"                         {{ old('jurusan') == 'S1 Bioteknologi' ? 'selected' : '' }}>S1 Bioteknologi</option>
                            <option value="S1 Metalurgi"                            {{ old('jurusan') == 'S1 Metalurgi' ? 'selected' : '' }}>S1 Metalurgi</option>
                        </select>
                        @error('jurusan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-xs text-gray-600">Angkatan</label>
                        <select name="angkatan" class="w-full mt-1 px-3 py-2 rounded-lg border text-sm text-gray-700 bg-white">
                            <option value="" disabled selected>Pilih Angkatan</option>
                            <option value="2021" {{ old('angkatan') == '2021' ? 'selected' : '' }}>2021</option>
                            <option value="2022" {{ old('angkatan') == '2022' ? 'selected' : '' }}>2022</option>
                            <option value="2023" {{ old('angkatan') == '2023' ? 'selected' : '' }}>2023</option>
                            <option value="2024" {{ old('angkatan') == '2024' ? 'selected' : '' }}>2024</option>
                            <option value="2025" {{ old('angkatan') == '2025' ? 'selected' : '' }}>2025</option>
                        </select>
                        @error('angkatan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- RIGHT -->
                <div class="space-y-3">
                    <div>
                        <label class="text-xs text-gray-600">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="w-full mt-1 px-3 py-2 rounded-lg border text-sm">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-xs text-gray-600">Password</label>
                        <input type="password" name="password"
                            class="w-full mt-1 px-3 py-2 rounded-lg border text-sm">
                        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-xs text-gray-600">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation"
                            class="w-full mt-1 px-3 py-2 rounded-lg border text-sm">
                    </div>
                </div>

                <!-- BUTTON -->
                <div class="md:col-span-2 flex flex-col items-center mt-4">
                    <button type="submit" class="w-full md:w-[250px] bg-[#5FAF9F] text-white py-2 rounded-lg text-sm">
                        Daftar
                    </button>

                    <div class="flex items-center gap-2 mt-5 w-full md:w-[300px]">
                        <div class="flex-1 h-[1px] bg-gray-200"></div>
                        <span class="text-gray-400 text-xs">
                            Sudah punya akun?
                            <a href="/" class="font-semibold text-[#5FAF9F]">Masuk</a>
                        </span>
                        <div class="flex-1 h-[1px] bg-gray-200"></div>
                    </div>
                </div>

            </form>

        </div>
    </div>

</div>

</body>
</html>