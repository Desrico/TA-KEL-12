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

<body class="min-h-screen font-[Poppins] relative overflow-hidden">

    <!-- Background -->
    <img src="{{ asset('img/bg.png') }}"
         class="absolute inset-0 w-full h-full object-cover z-0">

    <div class="absolute inset-0 bg-white/55 z-10"></div>

    <!-- Logo -->
    <div class="absolute top-6 left-8 z-20 flex items-center gap-3">
        <img src="{{ asset('img/logo.png') }}" class="w-10 h-10 object-contain">
        <div>
            <h1 class="text-sm font-semibold text-[#064E3B] leading-tight">
               Campus Care
            </h1>
            <p class="text-[11px] text-gray-500">
                Bimbingan & Konseling Digital
            </p>
        </div>
    </div>

    <!-- Login Card -->
    <div class="relative z-20 min-h-screen flex items-center justify-center">
        <div class="w-full max-w-md px-6">

            <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-center mb-5">
                    Masuk ke Akun
                </h2>

                <!-- ERROR GLOBAL -->
                @if ($errors->any())
                    <div class="mb-3 text-sm text-red-500">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                    @csrf

                    @if ($errors->any())
                        <div class="mb-3 text-sm text-red-500">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <div>
                        <label class="text-sm text-gray-600">Username CIS</label>
                        <input type="text" name="username"
                            value="{{ old('username') }}"
                            placeholder="Contoh: johannes / if420086"
                            class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#5FAF9F]">
                    </div>

                    <div>
                        <label class="text-sm text-gray-600">Password</label>
                        <input type="password" name="password"
                            class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#5FAF9F]">
                    </div>

                    <p class="text-sm text-gray-500 text-center">
                        Gunakan akun CIS Anda untuk login
                    </p>

                    <button type="submit"
                        class="w-full bg-[#5FAF9F] hover:bg-[#4e9c8d] text-white py-2 rounded-lg text-sm">
                        Masuk
                    </button>
                </form>
            </div>

        </div>
    </div>

</body>
</html>