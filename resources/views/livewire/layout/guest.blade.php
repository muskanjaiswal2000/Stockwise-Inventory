<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @livewireStyles

    <title>{{ config('app.name', 'StockWise') }} - Login</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-950 font-sans antialiased text-slate-900">

    <main class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-10">

        {{-- Background decoration --}}
        <div class="pointer-events-none absolute -left-32 -top-32 h-96 w-96 rounded-full bg-indigo-600/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-32 -right-32 h-96 w-96 rounded-full bg-violet-600/20 blur-3xl"></div>

        <div class="relative z-10 w-full max-w-md">

            {{-- Brand --}}
            <div class="mb-8 text-center">
                <a href="/" wire:navigate class="inline-flex flex-col items-center gap-3">

                    <span class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 shadow-xl shadow-indigo-950/40">
                        <x-application-logo class="h-9 w-9 fill-current text-white" />
                    </span>

                    <span class="text-lg font-bold tracking-[0.18em] text-white">
                        STOCKWISE
                    </span>
                </a>

                <p class="mt-2 text-sm text-slate-400">
                    Inventory management made simple
                </p>
            </div>

            {{-- Login card --}}
            <div class="rounded-2xl border border-white/10 bg-white p-6 shadow-2xl shadow-black/30 sm:p-9">
                {{ $slot }}
            </div>

            <p class="mt-6 text-center text-xs text-slate-500">
                &copy; {{ date('Y') }} StockWise. All rights reserved.
            </p>

        </div>
    </main>

    @livewireScripts
</body>
</html>
