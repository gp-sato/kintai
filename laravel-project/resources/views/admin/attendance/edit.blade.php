<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ __('勤怠管理：管理者 - 勤怠編集') }}
      </h2>
  </x-slot>

  <div class="my-container">
    <div class="py-4 space-around">
      <span class="user-name">{{ $attendance->user->name }}</span>
      <span class="working-date">{{ $attendance->start_time->format('Y年n月j日') }}</span>
      <a href="{{ route('admin.attendance.index', ['user' => $attendance->user, 'year' => $attendance->start_time->format('Y'), 'month' => $attendance->start_time->format('n')]) }}"><button>戻る</button></a>
    </div>

    <div class="mb-4">
      <span>以下の内容で勤怠を更新します。</span>
    </div>

    <form method="POST" action="{{ route('admin.attendance.update', $attendance) }}">
      @method('PUT')
      @csrf

      <div class="py-4 text-center">
        <div>
          <span class="inline-block text-xl w-24">出勤時間</span>：
          <select name="start_hour" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            @foreach (range(0, 23) as $item)
              <option value="{{ $item }}" @if ($item == old('start_hour', $attendance->start_time?->format('H'))) selected  @endif>{{ $item }}</option>
            @endforeach
          </select>
          <span>時</span>
          <select name="start_minute" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            @foreach (range(0, 59) as $item)
              <option value="{{ $item }}" @if ($item == old('start_minute', $attendance->start_time?->format('i'))) selected @endif>{{ $item }}</option>
            @endforeach
          </select>
          <span>分</span>
        </div>
        <div class="mt-4">
          <span class="inline-block text-xl w-24">退勤時間</span>：
          <select name="finish_hour" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            @php $finish_hour = old('finish_hour', $attendance->finish_time?->format('H')); @endphp
            <option value="" @if (is_null($finish_hour)) selected @endif>--</option>
            @foreach (range(0, 23) as $item)
              <option value="{{ $item }}" @if (! is_null($finish_hour) && $item == $finish_hour) selected  @endif>{{ $item }}</option>
            @endforeach
          </select>
          <span>時</span>
          <select name="finish_minute" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            @php $finish_minute = old('finish_minute', $attendance->finish_time?->format('i')); @endphp
            <option value="" @if (is_null($finish_minute)) @endif>--</option>
            @foreach (range(0, 59) as $item)
              <option value="{{ $item }}" @if (! is_null($finish_minute) && $item == $finish_minute) selected @endif>{{ $item }}</option>
            @endforeach
          </select>
          <span>分</span>
          @if ($errors->has('finish_time'))
            <ul>
              @foreach ($errors->get('finish_time') as $message)
                <li class="text-red-500">{{ $message }}</li>
              @endforeach
            </ul>
          @endif
        </div>
      </div>

      <div class="flex items-center justify-end mt-4">
        <x-primary-button class="ms-3">{{ __('更新') }}</x-primary-button>
      </div>
    </form>
  </div>
</x-app-layout>
