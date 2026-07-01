<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Campus Care</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        .password-toggle-btn {
            position: absolute;
            right: .55rem;
            top: 50%;
            transform: translateY(-50%);
            width: 2rem;
            height: 2rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: .55rem;
            background: transparent;
            color: #64748b;
            cursor: pointer;
        }

        .password-toggle-btn:hover {
            background: #f0fdf4;
            color: #064E3B;
        }

        .password-toggle-btn svg {
            width: 1rem;
            height: 1rem;
        }
    </style>
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

            <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6 relative">

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

                    <p class="text-sm text-gray-500 text-center">
                        Gunakan akun CIS Anda untuk masuk
                    </p>

                    <div>
                        <label class="text-sm text-gray-600">Username CIS</label>
                        <input type="text" name="username"
                            id="loginUsername"
                            value="{{ old('username') }}"
                            placeholder=""
                            autocomplete="username"
                            autofocus
                            class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#5FAF9F] focus:outline-none">
                    </div>

                    <div>
                        <label class="text-sm text-gray-600">Password</label>
                        <div class="relative mt-1">
                            <input
                                type="password"
                                name="password"
                                id="loginPassword"
                                autocomplete="current-password"
                                class="w-full px-3 py-2 pr-11 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#5FAF9F] focus:outline-none"
                            >
                            <button type="button" class="password-toggle-btn" id="togglePassword" aria-label="Tampilkan password" aria-pressed="false">
                                <svg id="eyeIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-[#5FAF9F] hover:bg-[#4e9c8d] text-white py-2 rounded-lg text-sm">
                        Masuk
                    </button>
                </form>
            </div>

        </div>
    </div>

<script>
window.addEventListener('pageshow', function (event) {
    if (event.persisted) {
        // Form login dari browser back-forward cache harus memeriksa session terbaru.
        window.location.reload();
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const usernameInput = document.getElementById('loginUsername');
    const passwordInput = document.getElementById('loginPassword');
    const togglePassword = document.getElementById('togglePassword');
    const eyeIcon = document.getElementById('eyeIcon');

    usernameInput?.addEventListener('keydown', function (event) {
        if (event.key !== 'Enter') {
            return;
        }

        event.preventDefault();
        passwordInput?.focus();
    });

    togglePassword?.addEventListener('click', function () {
        const isPassword = passwordInput?.type === 'password';

        if (!passwordInput) {
            return;
        }

        passwordInput.type = isPassword ? 'text' : 'password';
        togglePassword.setAttribute('aria-pressed', isPassword ? 'true' : 'false');
        togglePassword.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Tampilkan password');

        eyeIcon.innerHTML = isPassword
            ? '<path d="M17.9 17.9A10.8 10.8 0 0 1 12 19C5.5 19 2 12 2 12a18.5 18.5 0 0 1 3.2-4.4"></path><path d="M9.9 4.2A10.9 10.9 0 0 1 12 4.9c6.5 0 10 7.1 10 7.1a18.3 18.3 0 0 1-1.9 3"></path><path d="M2 2l20 20"></path><path d="M9.5 9.5a3 3 0 0 0 4 4"></path>'
            : '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle>';
    });
});
</script>
</body>
</html>
