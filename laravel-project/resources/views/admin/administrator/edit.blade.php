<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ __('勤怠管理：管理者 - 管理者編集') }}
      </h2>
  </x-slot>

  <div class="my-container">
    <div class="py-4 text-right">
      <a href="{{ route('admin.index') }}"><button>戻る</button></a>
    </div>

    <form method="POST" action="{{ route('admin.administrator.confirm') }}">
      @csrf
  
      <div>
        <x-input-label for="name" :value="__('名前')" />
        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" value="{{ old('name', $admin->name) }}" required />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
      </div>

      <div class="mt-4">
        <x-input-label for="email" :value="__('メールアドレス')" />
        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" value="{{ old('email', $admin->email) }}" required />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
      </div>

      <div class="mt-4">
        <x-input-label for="password" :value="__('パスワード（更新しない場合は空欄）')" />
        <x-password-input>
          <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" />
        </x-password-input>
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
      </div>

      <div class="mt-4">
        <x-input-label for="password-confirm" :value="__('パスワード確認')" />
        <x-password-input>
          <x-text-input id="password-confirm" class="block mt-1 w-full" type="password" name="password_confirmation" />
        </x-password-input>
      </div>

      <div class="flex items-center justify-end mt-4">
        <button type="button" id="btn_clear">クリア</button>
        <x-primary-button class="ms-3">{{ __('確認') }}</x-primary-button>
      </div>
    </form>
  </div>

</x-app-layout>
