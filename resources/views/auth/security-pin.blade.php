<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>PIN Keamanan - Campus Care</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="min-h-screen font-[Poppins] relative overflow-hidden">
    <img src="{{ asset('img/bg.png') }}"
         class="absolute inset-0 w-full h-full object-cover z-0"
         alt="">

    <div class="absolute inset-0 bg-white/55 z-10"></div>

    <div class="absolute top-6 left-8 z-20 flex items-center gap-3">
        <img src="{{ asset('img/logo.png') }}" class="w-10 h-10 object-contain" alt="Campus Care">
        <div>
            <h1 class="text-sm font-semibold text-[#064E3B] leading-tight">
               Campus Care
            </h1>
            <p class="text-[11px] text-gray-500">
                Bimbingan & Konseling Digital
            </p>
        </div>
    </div>

    <div class="relative z-20 min-h-screen flex items-center justify-center">
        <div class="w-full max-w-md px-6">
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6 relative">
                <h2 class="text-lg font-semibold text-center mb-2">
                    {{ $mode === 'setup' ? 'Buat PIN Keamanan' : 'Masukkan PIN Keamanan' }}
                </h2>

                <p class="text-sm text-gray-500 text-center mb-5">
                    {{ $mode === 'setup'
                        ? 'Buat 6 digit PIN rahasia untuk melindungi akun Campus Care Anda.'
                        : 'Masukkan 6 digit PIN Campus Care Anda untuk melanjutkan.' }}
                </p>

                @if ($errors->any())
                    <div class="mb-3 rounded-lg border border-red-100 bg-red-50 px-3 py-2 text-sm text-red-600">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if ($mode === 'setup')
                    <div class="mb-4 rounded-lg border border-emerald-100 bg-emerald-50 px-3 py-2 text-xs leading-relaxed text-emerald-800">
                        PIN ini dibuat satu kali dan akan diminta setiap kali akun CIS Anda berhasil login.
                    </div>
                @endif

                <form method="POST" action="{{ route('security-pin.submit') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="pin" class="text-sm text-gray-600">
                            PIN 6 Digit
                        </label>
                        <input
                            type="password"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            name="pin"
                            id="pin"
                            maxlength="6"
                            autocomplete="one-time-code"
                            autofocus
                            class="w-full mt-1 px-3 py-3 border border-gray-200 rounded-lg text-center text-xl tracking-[.45em] focus:ring-2 focus:ring-[#5FAF9F] focus:outline-none"
                        >
                    </div>

                    @if ($mode === 'setup')
                        <div>
                            <label for="pin_confirmation" class="text-sm text-gray-600">
                                Konfirmasi PIN
                            </label>
                            <input
                                type="password"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                name="pin_confirmation"
                                id="pin_confirmation"
                                maxlength="6"
                                autocomplete="one-time-code"
                                class="w-full mt-1 px-3 py-3 border border-gray-200 rounded-lg text-center text-xl tracking-[.45em] focus:ring-2 focus:ring-[#5FAF9F] focus:outline-none"
                            >
                        </div>
                    @endif

                    <button type="submit"
                        class="w-full bg-[#5FAF9F] hover:bg-[#4e9c8d] text-white py-2.5 rounded-lg text-sm font-semibold">
                        {{ $mode === 'setup' ? 'Simpan PIN' : 'Masuk' }}
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="w-full text-sm text-gray-500 hover:text-[#064E3B]">
                        Keluar dari akun ini
                    </button>
                </form>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('input[inputmode="numeric"]').forEach(function (input) {
        input.addEventListener('input', function () {
            input.value = input.value.replace(/\D/g, '').slice(0, 6);
        });
    });
});
</script>
</body>
</html>
