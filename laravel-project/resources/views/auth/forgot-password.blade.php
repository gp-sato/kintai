<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('パスワードをリセットします。メールアドレスを入力してください。受信したメールのリンクからアクセスしたページで新しいパスワードを入力してください。') }}
        <br /><br />
        {{ __('Input your email if you forgot your password. We will send you a password reset link to help you choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
