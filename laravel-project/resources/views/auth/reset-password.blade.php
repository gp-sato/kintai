<x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4 password-wrap">
            <x-input-label for="password" :value="__('Password（新しいパスワード）')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <img id="password-eye-slash-solid" class="eye-slash-solid" src="{{ asset('images/eye-slash-solid.svg') }}">
            <img id="password-eye-solid" class="eye-solid" src="{{ asset('images/eye-solid.svg') }}" style="display: none">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4 password-confirm-wrap">
            <x-input-label for="password-confirm" :value="__('Confirm Password（確認）')" />

            <x-text-input id="password-confirm" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />

            <img id="password-confirm-eye-slash-solid" class="eye-slash-solid" src="{{ asset('images/eye-slash-solid.svg') }}">
            <img id="password-confirm-eye-solid" class="eye-solid" src="{{ asset('images/eye-solid.svg') }}" style="display: none">

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Reset Password') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
