<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-xl shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">
            Login Admin / Konselor
        </h2>

        {{-- Error --}}
        @if ($errors->any())
            <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf

            {{-- Email --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="admin@email.com"
                >
            </div>

            {{-- Password --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input
                    type="password"
                    name="password"
                    required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="••••••••"
                >
            </div>

            {{-- Submit --}}
            <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition"
            >
                Masuk
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-4">
            <a href="{{ route('login') }}" class="text-blue-500 hover:underline">
                ← Kembali ke Login User
            </a>
        </p>
    </div>

</body>
</html>