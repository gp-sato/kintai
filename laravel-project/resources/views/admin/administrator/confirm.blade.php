<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ __('勤怠管理：管理者 - 管理者編集確認') }}
      </h2>
  </x-slot>

  <div class="my-container">
    <div class="mb-4">
      <span>以下の内容で管理者を更新します。</span>
    </div>
    <form method="POST" action="{{ route('admin.administrator.update') }}">
      @method('PUT')
      @csrf

      <div>
        <span class="inline-block w-24">名前</span>：{{ $formData['name'] }}
        <input type="hidden" name="name" value="{{ $formData['name'] }}">
      </div>

      <div class="mt-4">
        <span class="inline-block w-24">メールアドレス</span>：{{ $formData['email'] }}
        <input type="hidden" name="email" value="{{ $formData['email'] }}">
      </div>

      <div class="mt-4">
        <span class="inline-block w-24">パスワード</span>：********
        <input type="hidden" name="password" value="{{ $formData['password'] }}">
      </div>

      <div class="flex items-center justify-end mt-4">
        <button type="submit" name="back" value="back">戻る</button>
        <x-primary-button class="ms-3">{{ __('更新') }}</x-primary-button>
      </div>
    </form>
  </div>

</x-app-layout>
