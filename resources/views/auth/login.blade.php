<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login | StockWise</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#080d1b] font-sans antialiased">

    <div class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-12">

        <!-- Background effects -->
        <div class="pointer-events-none absolute -left-40 -top-40 h-[450px] w-[450px] rounded-full bg-indigo-600/20 blur-[120px]"></div>

        <div class="pointer-events-none absolute -bottom-40 -right-40 h-[450px] w-[450px] rounded-full bg-cyan-500/15 blur-[120px]"></div>

        <div class="pointer-events-none absolute left-1/2 top-1/2 h-[500px] w-[500px] -translate-x-1/2 -translate-y-1/2 rounded-full border border-white/[0.04]"></div>

        <!-- Main container -->
        <div class="relative z-10 w-full max-w-[440px]">

            <!-- Brand section -->
            <div class="mb-8 text-center">

                <div class="mx-auto flex h-[68px] w-[68px] items-center justify-center rounded-[20px] bg-gradient-to-br from-indigo-500 via-blue-500 to-cyan-500 shadow-lg shadow-indigo-500/30 ring-1 ring-white/20">

                    <x-application-logo class="h-10 w-10 fill-current text-white" />

                </div>

                <h1 class="mt-5 text-3xl font-extrabold tracking-[0.18em] text-white">
                    STOCK<span class="text-cyan-400">WISE</span>
                </h1>

                <p class="mt-2 text-sm tracking-wide text-slate-400">
                    Smart inventory management, simplified.
                </p>

            </div>

            <!-- Login card -->
            <div class="rounded-3xl border border-white/10 bg-white/[0.97] p-7 shadow-2xl shadow-black/30 sm:p-9">

                <!-- Heading -->
                <div class="mb-7">

                    <div class="mb-3 inline-flex items-center gap-2 rounded-full bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-600">

                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        SECURE LOGIN
                    </div>

                    <h2 class="text-2xl font-bold tracking-tight text-slate-900">
                        Welcome back!
                    </h2>
                    <p class="mt-2 text-sm leading-6 text-slate-500">
                        Sign in to your account to manage your inventory and business operations.
                    </p>
                </div>

                <!-- Login form -->
                <form method="POST" action="{{ route('login.store') }}" class="space-y-5" novalidate>

                    @csrf

                    <!-- Email -->
                    <div>

                        <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">
                            Email address
                        </label>

                        <div class="relative">

                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">

                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                                        d="M3 7l9 6 9-6M5 5h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z" />
                                </svg>

                            </div>

                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="name@example.com"
                                class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-3.5 pl-12 pr-4 text-sm text-slate-900 placeholder:text-slate-400 outline-none transition-all duration-200 hover:border-slate-300 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10">

                        </div>
                        @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                    </div>

                    <!-- Password -->
                    <div>

                        <div class="mb-2 flex items-center justify-between">

                            <label for="password" class="block text-sm font-semibold text-slate-700">
                                Password
                            </label>

                        </div>

                        <div class="relative">

                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                                        d="M7 10V7a5 5 0 0110 0v3m-11 0h12a2 2 0 012 2v8a2 2 0 01-2 2H6a2 2 0 01-2-2v-8a2 2 0 012-2zm5 4v4" />
                                </svg>
                            </div>

                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="Enter your password"
                                class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-3.5 pl-12 pr-4 text-sm text-slate-900 placeholder:text-slate-400 outline-none transition-all duration-200 hover:border-slate-300 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10">

                        </div>
                        @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                    </div>

                    <!-- Remember and forgot password -->
                    <div class="flex flex-wrap items-center justify-between gap-3 pt-1">

                        <label for="remember" class="flex cursor-pointer items-center gap-2.5">

                            <input
                                id="remember"
                                type="checkbox"
                                name="remember"
                                value="1"
                                class="h-4 w-4 cursor-pointer rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">

                            <span class="text-sm text-slate-600">
                                Remember me
                            </span>

                        </label>

                        @if (Route::has('password.request'))

                        <a href="{{ route('password.request') }}"
                            class="text-sm font-semibold text-indigo-600 transition hover:text-indigo-800">
                            Forgot password?
                        </a>

                        @endif

                    </div>

                    <!-- Submit button -->
                    <button
                        type="submit"
                        class="group flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 px-5 py-3.5 text-sm font-bold text-white shadow-lg shadow-indigo-500/20 transition-all duration-300 hover:-translate-y-0.5 hover:from-indigo-700 hover:to-blue-700 hover:shadow-xl hover:shadow-indigo-500/25 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 active:translate-y-0">

                        Sign in to your account

                        <svg class="h-4 w-4 transition-transform duration-200 group-hover:translate-x-1"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 12h14m-7-7l7 7-7 7" />
                        </svg>

                    </button>

                </form>

                <!-- Security information -->
                <div class="mt-7 border-t border-slate-100 pt-5">

                    <div class="flex items-center justify-center gap-2 text-xs text-slate-400">

                        <svg class="h-4 w-4 text-emerald-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4" />
                        </svg>

                        Your account and inventory data are protected.

                    </div>

                </div>

            </div>

            <!-- Footer -->
            <div class="mt-7 text-center">

                <p class="text-xs text-slate-500">
                    &copy; {{ date('Y') }} <span class="font-semibold text-slate-400">StockWise</span>.
                    All rights reserved.
                </p>

            </div>

        </div>

    </div>

</body>

</html>