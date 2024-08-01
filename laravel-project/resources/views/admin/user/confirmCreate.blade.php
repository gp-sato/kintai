<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ __('勤怠管理：管理者 - ユーザー新規登録確認') }}
      </h2>
  </x-slot>

  <div class="container">
    <div class="mb-4">
      <span>以下の内容でユーザーを新規登録します。</span>
    </div>
    <form method="POST" action="{{ route('admin.user.store') }}">
      @csrf

      <div>
        <span style="display: inline-block; width: 90px;">名前</span>：{{ $formData['name'] }}
        <input type="hidden" name="name" value="{{ $formData['name'] }}">
      </div>

      <div class="mt-4">
        <span style="display: inline-block; width: 90px;">メールアドレス</span>：{{ $formData['email'] }}
        <input type="hidden" name="email" value="{{ $formData['email'] }}">
      </div>

      <div class="mt-4">
        <span style="display: inline-block; width: 90px;">パスワード</span>：********
        <input type="hidden" name="password" value="{{ $formData['password'] }}">
      </div>

      <div class="flex items-center justify-end mt-4">
        <button type="submit" name="back" value="back">戻る</button>
        <x-primary-button class="ms-3">{{ __('登録') }}</x-primary-button>
      </div>
    </form>
  </div>

</x-app-layout>