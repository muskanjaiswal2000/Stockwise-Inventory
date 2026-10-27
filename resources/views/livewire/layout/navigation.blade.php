<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect(route('login'), navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="relative z-30">
    <div class="flex h-16 items-center justify-between bg-[#073f43] px-4 text-white lg:hidden">
        <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#19b9c2] shadow-lg shadow-black/20">
                <x-application-logo class="h-6 w-6 fill-current text-white" />
            </span>
            <span>
                <span class="block text-sm font-extrabold">StockWise</span>
                <span class="block text-[9px] uppercase tracking-[0.18em] text-teal-200">Inventory OS</span>
            </span>
        </a>
        <button @click="open = !open" class="rounded-lg p-2 text-teal-100 hover:bg-white/10" aria-label="Toggle navigation">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path :class="{ 'hidden': open, 'inline-flex': !open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <aside class="fixed inset-y-0 left-0 hidden w-64 flex-col bg-gradient-to-b from-[#0d7771] to-[#07504f] px-4 py-5 text-white shadow-xl shadow-teal-950/10 lg:flex">
        <a href="{{ route('dashboard') }}" wire:navigate class="mb-8 flex items-center gap-3 px-2">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#20c3c9] shadow-lg shadow-teal-950/20">
                <x-application-logo class="h-6 w-6 fill-current text-white" />
            </span>
            <span>
                <span class="block text-sm font-extrabold">StockWise</span>
                <span class="block text-[9px] uppercase tracking-[0.18em] text-teal-100">Inventory OS</span>
            </span>
        </a>

        <div class="space-y-1">
            @php
                $links = [
                    ['route' => 'dashboard', 'label' => 'Dashboard', 'pattern' => 'dashboard', 'icon' => '⌂'],
                    ['route' => 'inventory.categories.index', 'label' => 'Categories', 'pattern' => 'inventory.categories.*', 'icon' => '◇'],
                    ['route' => 'inventory.products.index', 'label' => 'Products', 'pattern' => 'inventory.products.*', 'icon' => '▣'],
                    ['route' => 'inventory.suppliers.index', 'label' => 'Suppliers', 'pattern' => 'inventory.suppliers.*', 'icon' => '▱'],
                    ['route' => 'inventory.stock.movement', 'label' => 'Stock Movement', 'pattern' => 'inventory.stock.movement', 'icon' => '↔'],
                    ['route' => 'inventory.stock.adjustment', 'label' => 'Adjust Stock', 'pattern' => 'inventory.stock.adjustment', 'icon' => '+'],
                    ['route' => 'inventory.stock.history', 'label' => 'Stock History', 'pattern' => 'inventory.stock.history', 'icon' => '◷'],
                ];
            @endphp

            @foreach ($links as $link)
                <a href="{{ route($link['route']) }}" wire:navigate
                    class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-xs font-semibold transition {{ request()->routeIs($link['pattern']) ? 'bg-white/20 text-white ring-1 ring-white/20' : 'text-teal-50/85 hover:bg-white/10 hover:text-white' }}">
                    <span class="flex h-5 w-5 items-center justify-center text-base leading-none">{{ $link['icon'] }}</span>
                    {{ $link['label'] }}
                </a>
            @endforeach

            @if(auth()->user()?->hasRole('admin'))
                <a href="{{ route('inventory.staff.permissions') }}" wire:navigate
                    class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-xs font-semibold transition {{ request()->routeIs('inventory.staff.*') ? 'bg-white/20 text-white ring-1 ring-white/20' : 'text-teal-50/85 hover:bg-white/10 hover:text-white' }}">
                    <span class="flex h-5 w-5 items-center justify-center text-base leading-none">♙</span>
                    Staff management
                </a>
            @endif
        </div>

        <div class="mt-auto border-t border-white/15 pt-4">
            <div class="flex items-center gap-3 px-2">
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-[#19b9c2] text-sm font-bold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </span>
                <div class="min-w-0">
                    <p class="truncate text-xs font-bold">{{ auth()->user()->name }}</p>
                    <p class="truncate text-[11px] text-teal-100/70">{{ auth()->user()->email }}</p>
                </div>
                <button wire:click="logout" class="ml-auto text-teal-100 hover:text-white" title="Log out" aria-label="Log out">➜]</button>
            </div>
            <a href="{{ route('profile') }}" wire:navigate
                class="mt-3 block rounded-xl px-3 py-2 text-xs font-semibold text-teal-50/85 transition hover:bg-white/10 hover:text-white">
                Edit profile
            </a>
        </div>
    </aside>

    <div x-show="open" x-cloak class="border-b border-teal-900/20 bg-[#07504f] px-4 pb-4 text-white lg:hidden">
        <div class="space-y-1 pt-2">
            @foreach ($links as $link)
                <a href="{{ route($link['route']) }}" wire:navigate @click="open = false"
                    class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold {{ request()->routeIs($link['pattern']) ? 'bg-white/20' : 'hover:bg-white/10' }}">
                    <span class="w-5 text-center">{{ $link['icon'] }}</span>{{ $link['label'] }}
                </a>
            @endforeach
            @if(auth()->user()?->hasRole('admin'))
                <a href="{{ route('inventory.staff.permissions') }}" wire:navigate @click="open = false" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold hover:bg-white/10"><span class="w-5 text-center">⚙</span>Staff management</a>
            @endif
            <a href="{{ route('profile') }}" wire:navigate class="block rounded-xl px-3 py-2.5 text-sm font-semibold hover:bg-white/10">Profile</a>
            <button wire:click="logout" class="block w-full rounded-xl px-3 py-2.5 text-left text-sm font-semibold hover:bg-white/10">Log out</button>
        </div>
    </div>
</nav>
