<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('livewire.layout.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    {{-- Heading --}}
    <div class="mb-7">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            Welcome back!
        </h1>

        <p class="mt-2 text-sm text-slate-500">
            Sign in to access your inventory dashboard.
        </p>
    </div>

    {{-- Session status --}}
    <x-auth-session-status
        class="mb-4"
        :status="session('status')"
    />

    <form wire:submit="login" class="space-y-5">

        {{-- Email --}}
        <div>
            <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">
                Email address
            </label>

            <input
                wire:model="form.email"
                id="email"
                type="email"
                name="email"
                required
                autofocus
                autocomplete="username"
                placeholder="Enter your email address"
                class="block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10"
            >

            <x-input-error
                :messages="$errors->get('form.email')"
                class="mt-2"
            />
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">
                Password
            </label>

            <input
                wire:model="form.password"
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="Enter your password"
                class="block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10"
            >

            <x-input-error
                :messages="$errors->get('form.password')"
                class="mt-2"
            />
        </div>

        {{-- Remember me and forgot password --}}
        <div class="flex flex-wrap items-center justify-between gap-3">

            <label for="remember" class="inline-flex cursor-pointer items-center gap-2">
                <input
                    wire:model="form.remember"
                    id="remember"
                    type="checkbox"
                    name="remember"
                    class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                >

                <span class="text-sm text-slate-600">
                    Remember me
                </span>
            </label>

            @if (Route::has('password.request'))
                <a
                    href="{{ route('password.request') }}"
                    wire:navigate
                    class="text-sm font-semibold text-indigo-600 transition hover:text-indigo-800"
                >
                    Forgot password?
                </a>
            @endif

        </div>

        {{-- Login button --}}
        <button
            type="submit"
            wire:loading.attr="disabled"
            wire:target="login"
            class="flex w-full items-center justify-center rounded-xl bg-indigo-600 px-4 py-3.5 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 disabled:cursor-not-allowed disabled:opacity-60"
        >
            <span wire:loading.remove wire:target="login">
                Sign in to your account
            </span>

            <span wire:loading wire:target="login">
                Signing in...
            </span>
        </button>

    </form>

    <div class="mt-7 border-t border-slate-100 pt-5 text-center">
        <p class="text-xs text-slate-400">
            Secure access to your StockWise inventory system.
        </p>
    </div>
</div>
