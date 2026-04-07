<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">

    {{-- Navbar --}}
    <nav class="bg-blue-700 text-white px-6 py-4 flex justify-between items-center shadow">
        <h1 class="text-xl font-bold">Panel Admin / Konselor</h1>
        <div class="flex items-center gap-4">
            <span class="text-sm">{{ Auth::user()->nama }}</span>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button
                    type="submit"
                    class="bg-white text-blue-700 text-sm font-semibold px-4 py-1.5 rounded hover:bg-gray-100 transition"
                >
                    Logout
                </button>
            </form>
        </div>
    </nav>

    {{-- Konten --}}
    <div class="max-w-6xl mx-auto mt-10 px-4">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Dashboard</h2>

        {{-- Kartu statistik --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <div class="bg-white rounded-xl shadow p-6">
                <p class="text-sm text-gray-500">Total Booking</p>
                <p class="text-3xl font-bold text-blue-600 mt-1">0</p>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <p class="text-sm text-gray-500">Menunggu Konfirmasi</p>
                <p class="text-3xl font-bold text-yellow-500 mt-1">0</p>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <p class="text-sm text-gray-500">Selesai</p>
                <p class="text-3xl font-bold text-green-500 mt-1">0</p>
            </div>

        </div>

        {{-- Tabel booking (placeholder) --}}
        <div class="bg-white rounded-xl shadow mt-8 p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Data Booking Terbaru</h3>
            <p class="text-gray-400 text-sm">Belum ada data. Hubungkan dengan BookingController.</p>
        </div>
    </div>

</body>
</html>