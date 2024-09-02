<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ __('勤怠管理：管理者 - 勤怠編集') }}
      </h2>
  </x-slot>

  <div class="container">
    <div class="py-4 space-around">
      <span class="user-name">{{ $user->name }}</span>
      <span class="working-date">{{ $date }}</span>
    </div>

    <div class="mb-4">
      <span>以下の内容で勤怠を更新します。</span>
    </div>

    <form method="POST" action="">
      @method('PUT')
      @csrf

      <div class="py-4 text-center">
        <div>
          <span class="inline-block text-xl w-24">出勤時間</span>：{{ $attendance->start_time?->format('H:i') ?? '' }}
        </div>
        <div class="mt-4">
          <span class="inline-block text-xl w-24">退勤時間</span>：{{ $attendance->finish_time?->format('H:i') ?? '' }}
        </div>
      </div>

      <div class="flex items-center justify-end mt-4">
        <button type="submit" name="back" value="back">戻る</button>
        <x-primary-button class="ms-3">{{ __('更新') }}</x-primary-button>
      </div>
    </form>
  </div>
</x-app-layout>
