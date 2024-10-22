<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('名前')" />
            <select name="email" id="email" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" autofocus>
                <option value="">--</option>
                @foreach ($users as $user)
                    <option value="{{ $user->email }}" @if(old('email') == $user->email) selected @endif>{{ $user->name }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4 password-wrap">
            <x-input-label for="password" :value="__('パスワード')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <img id="password-eye-slash-solid" class="eye-slash-solid" src="{{ asset('images/eye-slash-solid.svg') }}">
            <img id="password-eye-solid" class="eye-solid" src="{{ asset('images/eye-solid.svg') }}" style="display: none">

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="ms-3">
                {{ __('ログイン') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
