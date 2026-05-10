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

                @if ($errors->any())
                    <div class="mb-3 rounded-lg border border-red-100 bg-red-50 px-3 py-2 text-sm text-red-600">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="text-sm text-gray-600">Username CIS</label>
                        <input type="text" name="username"
                            value="{{ old('username') }}"
                            placeholder="Contoh: johannes / if420086"
                            class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#5FAF9F] focus:outline-none">
                    </div>

                    <div>
                        <label class="text-sm text-gray-600">Password</label>
                        <div class="relative mt-1">
                            <input
                                type="password"
                                name="password"
                                id="loginPassword"
                                class="w-full px-3 py-2 pr-11 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#5FAF9F] focus:outline-none"
                            >
                            <button
                                type="button"
                                id="toggleLoginPassword"
                                class="absolute inset-y-0 right-0 inline-flex w-11 items-center justify-center text-gray-400 transition hover:text-[#065F46]"
                                aria-label="Tampilkan password"
                                aria-pressed="false"
                            >
                                <svg id="loginPasswordEyeOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                <svg id="loginPasswordEyeClosed" xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.584 10.587A2 2 0 0 0 13.412 13.4" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.88 5.09A10.94 10.94 0 0 1 12 4.9c4.478 0 8.268 2.943 9.542 7a11.04 11.04 0 0 1-4.043 5.134M6.228 6.228A10.96 10.96 0 0 0 2.458 12c1.274 4.057 5.065 7 9.542 7 1.566 0 3.056-.36 4.384-1.002" />
                                </svg>
                            </button>
                        </div>
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

<script>
(() => {
    const passwordInput = document.getElementById('loginPassword');
    const toggleButton = document.getElementById('toggleLoginPassword');
    const openIcon = document.getElementById('loginPasswordEyeOpen');
    const closedIcon = document.getElementById('loginPasswordEyeClosed');

    if (!passwordInput || !toggleButton || !openIcon || !closedIcon) {
        return;
    }

    toggleButton.addEventListener('click', () => {
        const isHidden = passwordInput.type === 'password';
        passwordInput.type = isHidden ? 'text' : 'password';
        toggleButton.setAttribute('aria-pressed', isHidden ? 'true' : 'false');
        toggleButton.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Tampilkan password');
        openIcon.classList.toggle('hidden', isHidden);
        closedIcon.classList.toggle('hidden', !isHidden);
    });
})();
</script>

</body>
</html>
