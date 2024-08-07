<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ __('勤怠管理：管理者 - ユーザー編集') }}
      </h2>
  </x-slot>

  <div class="container">
    <form method="POST" action="{{ route('admin.user.confirmEdit', $user) }}">
      @csrf
  
      <div>
        <x-input-label for="name" :value="__('名前')" />
        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" value="{{ old('name', $user->name) }}" required />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
      </div>

      <div class="mt-4">
        <x-input-label for="email" :value="__('メールアドレス')" />
        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" value="{{ old('email', $user->email) }}" required />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
      </div>

      <div class="mt-4">
        <x-input-label for="password" :value="__('パスワード（更新しない場合は空欄）')" />
        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" />
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
      </div>

      <div class="mt-4">
        <x-input-label for="password-confirm" :value="__('パスワード確認')" />
        <x-text-input id="password-confirm" class="block mt-1 w-full" type="password" name="password_confirmation" />
      </div>

      <div class="flex items-center justify-end mt-4">
        <button type="button" id="btn_clear">クリア</button>
        <x-primary-button class="ms-3">{{ __('確認') }}</x-primary-button>
      </div>
    </form>
    <form id="delete_form" method="POST" action="{{ route('admin.user.destroy', $user) }}">
      @method('DELETE')
      @csrf

      <div class="flex items-center justify-end mt-4">
        <button type="button" id="btn_delete" class="bg-red-500 text-white">削除</button>
      </div>
    </form>
  </div>

</x-app-layout>