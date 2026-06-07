<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title') - {{ config('app.name', 'Laravel') }}</title>
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-900">
        <div class="min-h-screen flex flex-col justify-center items-center px-6 py-12">
            <div class="max-w-md w-full text-center">
                <!-- Illustration / Icon -->
                <div class="mb-8 flex justify-center">
                    <div class="relative">
                        <h1 class="text-9xl font-extrabold text-indigo-600 dark:text-indigo-400 drop-shadow-sm">
                            @yield('code')
                        </h1>
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white dark:bg-gray-800 px-3 py-1 text-sm font-bold rounded-md -rotate-12 border-2 border-indigo-600 dark:border-indigo-400 text-indigo-600 dark:text-indigo-400 shadow-lg">
                            Oops!
                        </div>
                    </div>
                </div>
                
                <!-- Message -->
                <h2 class="mt-4 text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                    @yield('message')
                </h2>
                
                <p class="mt-4 text-base text-gray-600 dark:text-gray-400">
                    Maaf, sepertinya terjadi masalah atau halaman yang Anda cari tidak tersedia.
                </p>
                
                <!-- Action Button -->
                <div class="mt-10 flex items-center justify-center gap-x-6">
                    <a href="{{ url('/') }}" class="rounded-md bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all duration-200 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                        </svg>
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </body>
</html>
