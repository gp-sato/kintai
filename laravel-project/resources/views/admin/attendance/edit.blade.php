<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ __('勤怠管理：管理者 - 勤怠編集') }}
      </h2>
  </x-slot>

  <div class="container">
    <div class="py-4 space-around">
      <span class="user-name">{{ $user->name }}</span>
      @php
        $dt = new \Carbon\Carbon($date);
      @endphp
      <span class="working-date">{{ $dt->format('Y年n月j日') }}</span>
      <a href="{{ route('admin.attendance.index', ['user' => $user, 'year' => $dt->format('Y'), 'month' => $dt->format('n')]) }}"><button>戻る</button></a>
    </div>

    <div class="mb-4">
      <span>以下の内容で勤怠を更新します。</span>
    </div>

    <form method="POST" action="{{ route('admin.attendance.update', ['user' => $user, 'date' => $date]) }}">
      @method('PUT')
      @csrf

      <div class="py-4 text-center">
        <div>
          @php
            $startHour = $attendance->start_time?->format('H') ?? 0;
            $startMinute = $attendance->start_time?->format('i') ?? 0;
          @endphp
          <span class="inline-block text-xl w-24">出勤時間</span>：
          <select name="start_hour" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            @foreach (range(0, 23) as $item)
              <option value="{{ $item }}" @if ($item == $startHour) selected  @endif>{{ $item }}</option>
            @endforeach
          </select>
          <span>時</span>
          <select name="start_minute" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            @foreach (range(0, 59) as $item)
              <option value="{{ $item }}" @if ($item == $startMinute) selected @endif>{{ $item }}</option>
            @endforeach
          </select>
          <span>分</span>
        </div>
        <div class="mt-4">
          @php
            $finishHour = $attendance->finish_time?->format('H') ?? 0;
            $finishMinute = $attendance->finish_time?->format('i') ?? 0;
          @endphp
          <span class="inline-block text-xl w-24">退勤時間</span>：
          <select name="finish_hour" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            @foreach (range(0, 23) as $item)
              <option value="{{ $item }}" @if ($item == $finishHour) selected  @endif>{{ $item }}</option>
            @endforeach
          </select>
          <span>時</span>
          <select name="finish_minute" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            @foreach (range(0, 59) as $item)
              <option value="{{ $item }}" @if ($item == $finishMinute) selected @endif>{{ $item }}</option>
            @endforeach
          </select>
          <span>分</span>
        </div>
      </div>

      <div class="flex items-center justify-end mt-4">
        <x-primary-button class="ms-3">{{ __('更新') }}</x-primary-button>
      </div>
    </form>
  </div>
</x-app-layout>
