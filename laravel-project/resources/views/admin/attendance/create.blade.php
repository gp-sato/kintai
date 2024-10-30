<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ __('勤怠管理：管理者 - 勤怠登録') }}
      </h2>
  </x-slot>

  <div class="my-container">
    <div class="py-4 text-right">
      <a href="{{ route('admin.attendance.index', $user) }}"><button>戻る</button></a>
    </div>

    <div class="mb-4">
      <span>以下の内容で勤怠を登録します。</span>
    </div>

    <form method="POST" action="{{ route('admin.attendance.store', $user) }}">
      @csrf

      <div class="py-4">
        <div>
          <span class="inline-block text-xl w-24">名前</span>：
          <span class="user-name">{{ $user->name }}</span>
        </div>
        <div class="mt-4">
          <span class="inline-block text-xl w-24">勤務日</span>：
          <select name="labor_year" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            @foreach (range(2017, now()->year) as $item)
              <option value="{{ $item }}" @if ($item == old('labor_year', now()->year)) selected  @endif>{{ $item }}</option>
            @endforeach
          </select>
          <span>年</span>
          <select name="labor_month" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            @foreach (range(1, 12) as $item)
              <option value="{{ $item }}" @if ($item == old('labor_month', now()->month)) selected @endif>{{ $item }}</option>
            @endforeach
          </select>
          <span>月</span>
          <select name="labor_day" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            @foreach (range(1, 31) as $item)
              <option value="{{ $item }}" @if ($item == old('labor_day', now()->day)) selected @endif>{{ $item }}</option>
            @endforeach
          </select>
          <span>日</span>
          @if ($errors->has('working_day'))
            <ul>
              @foreach ($errors->get('working_day') as $message)
                <li class="text-red-500">{{ $message }}</li>
              @endforeach
            </ul>
          @endif
        </div>
        <div class="mt-4">
          <span class="inline-block text-xl w-24">出勤時間</span>：
          <select name="start_hour" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            @foreach (range(0, 23) as $item)
              <option value="{{ $item }}" @if ($item == old('start_hour')) selected  @endif>{{ $item }}</option>
            @endforeach
          </select>
          <span>時</span>
          <select name="start_minute" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            @foreach (range(0, 59) as $item)
              <option value="{{ $item }}" @if ($item == old('start_minute')) selected @endif>{{ $item }}</option>
            @endforeach
          </select>
          <span>分</span>
        </div>
        <div class="mt-4">
          <span class="inline-block text-xl w-24">退勤時間</span>：
          <select name="finish_hour" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            <option value="" @if (is_null(old('finish_hour'))) selected @endif>--</option>
            @foreach (range(0, 23) as $item)
              <option value="{{ $item }}" @if (! is_null(old('finish_hour')) && $item == old('finish_hour')) selected  @endif>{{ $item }}</option>
            @endforeach
          </select>
          <span>時</span>
          <select name="finish_minute" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            <option value="" @if (is_null(old('finish_minute'))) selected @endif>--</option>
            @foreach (range(0, 59) as $item)
              <option value="{{ $item }}" @if (! is_null(old('finish_minute')) && $item == old('finish_minute')) selected @endif>{{ $item }}</option>
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
        <x-primary-button class="ms-3">{{ __('登録') }}</x-primary-button>
      </div>
    </form>
  </div>
</x-app-layout>
