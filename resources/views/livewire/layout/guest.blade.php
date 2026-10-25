<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @livewireStyles

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div class="flex min-h-screen flex-col items-center justify-center bg-slate-950 px-4 py-10 sm:py-0">
            <div>
                <a href="/" wire:navigate class="flex flex-col items-center gap-3">
                    <span class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 shadow-2xl shadow-indigo-950/50">
                        <x-application-logo class="h-9 w-9 fill-current text-white" />
                    </span>
                    <span class="text-sm font-bold uppercase tracking-[0.22em] text-white">StockWise</span>
                </a>
            </div>

            <div class="mt-8 w-full overflow-hidden rounded-2xl border border-slate-200/10 bg-white px-6 py-7 shadow-2xl shadow-black/30 sm:max-w-md sm:px-8">
                {{ $slot }}
            </div>
        </div>

        @livewireScripts
    </body>
</html>
