<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'LoanEase') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-indigo-100 via-white to-purple-100">
            {{-- Background decoration --}}
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-indigo-200 to-purple-200 rounded-full opacity-50 blur-3xl"></div>
                <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-tr from-blue-200 to-indigo-200 rounded-full opacity-50 blur-3xl"></div>
            </div>

            <div class="relative z-10">
                <a href="/" class="flex flex-col items-center group">
                    <x-application-logo class="w-16 h-16 transition-transform duration-200 group-hover:scale-105" />
                    <span class="mt-2 text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        LoanEase
                    </span>
                </a>
            </div>

            <div class="relative z-10 w-full sm:max-w-md mt-6 px-8 py-8 bg-white/80 backdrop-blur-sm shadow-xl border border-white/50 overflow-hidden sm:rounded-2xl">
                {{ $slot }}
            </div>

            {{-- Footer --}}
            <p class="relative z-10 mt-6 text-sm text-slate-500">
                &copy; {{ date('Y') }} LoanEase. All rights reserved.
            </p>
        </div>
    </body>
</html>
