<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Volt\Component;

new class extends Component {
    public string $password = '';

    public function deleteUser(): void
    {
        $this->validate([
            'password' => ['required', 'string'],
        ]);

        $user = Auth::user();

        if (! Hash::check($this->password, $user->password)) {
            $this->addError('password', 'The provided password does not match your current password.');
            return;
        }

        $user->delete();

        Auth::logout();

        $this->redirect('/');
    }
}; ?>

<div>
    <x-slot name="heading">
        {{ __('Delete Account') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
    </x-slot>

    <div class="space-y-6">
        <x-danger-button
            x-data=""
            x-on:click.prevent="$wire.deleteUser"
        >
            {{ __('Delete Account') }}
        </x-danger-button>

        <x-modal name="confirmUserDeletion" :show="$errors->has('password')" focusable>
            <form wire:submit="deleteUser" class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Are you sure you want to delete your account?') }}
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                </p>

                <div class="mt-6">
                    <x-input-label for="password" :value="__('Password')" class="sr-only" />

                    <x-text-input
                        id="password"
                        type="password"
                        name="password"
                        class="mt-1 block w-3/4"
                        v-model="password"
                        placeholder="{{ __('Password') }}"
                        required
                        autocomplete="current-password"
                    />

                    <x-input-error class="mt-2" :messages="$errors->get('password')" />
                </div>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$close('confirmUserDeletion')">
                        {{ __('Cancel') }}
                    </x-secondary-button>

                    <x-danger-button class="ms-3">
                        {{ __('Delete Account') }}
                    </x-danger-button>
                </div>
            </form>
        </x-modal>
    </div>
</div>
