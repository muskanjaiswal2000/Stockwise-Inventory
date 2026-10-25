<?php

use Livewire\Volt\Component;

new class extends Component
{
}; ?>

<nav class="flex items-center justify-end gap-3">
    @auth
        <a href="{{ route('dashboard') }}" wire:navigate
           class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Dashboard
        </a>
    @else
        <a href="{{ route('login') }}" wire:navigate
           class="rounded-lg px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-100 hover:text-gray-900">
            Log in
        </a>

        @if (Route::has('register'))
            <a href="{{ route('register') }}" wire:navigate
               class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Register
            </a>
        @endif
    @endauth
</nav>
