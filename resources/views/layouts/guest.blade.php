<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-900">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Platform DKST') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-full font-sans antialiased text-slate-900 bg-slate-950 flex flex-col justify-center py-12 sm:px-6 lg:px-8 relative overflow-hidden">
        
        <!-- Background Ambient Glow -->
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center relative z-10">
            <a href="/" class="inline-block transition-transform hover:scale-105 duration-200">
                <x-application-logo class="h-20 w-auto" />
            </a>
            <h2 class="mt-4 text-center text-2xl font-bold tracking-tight text-white">
                Digital & AI Platform
            </h2>
            <p class="text-xs text-blue-400 font-semibold tracking-wider uppercase mt-0.5">
                Direktorat Kawasan Sains & Teknologi ITB
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md relative z-10 px-4 sm:px-0">
            <div class="bg-white py-8 px-6 shadow-2xl rounded-3xl sm:px-10 border border-slate-100">
                {{ $slot }}
            </div>

            <p class="mt-6 text-center text-xs text-slate-500">
                &copy; {{ date('Y') }} DKST Institut Teknologi Bandung. Seluruh hak cipta dilindungi.
            </p>
        </div>
    </body>
</html>
