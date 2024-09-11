<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ __('勤怠管理：管理者 - 勤怠詳細') }}
      </h2>
  </x-slot>

  <div class="container">
    <div class="py-4 space-around">
      <span class="user-name">{{ $user->name }}</span>
      <a href="{{ route('admin.index') }}"><button>ユーザー一覧へ</button></a>
    </div>

    <div class="py-4 text-center">
      <form method="GET" action="{{ route('admin.attendance.index', $user) }}">
        <div class="row">
          <select name="year" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            @foreach (range(2017, now()->year) as $year)
              <option value="{{ $year }}" @if ($year == $selectYear) selected @endif>{{ $year }}</option>
            @endforeach
          </select>
          <span>年</span>
          <select name="month" class="ml-3 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            @foreach (range(1, 12) as $month)
              <option value="{{ $month }}" @if ($month == $selectMonth) selected @endif>{{ $month }}</option>
            @endforeach
          </select>
          <span>月</span>
          <button type="submit">検索</button>
        </div>
      </form>
    </div>

    <div class="py-4">
      <table class="mx-auto attendance">
        <thead>
          <tr>
            <th>勤務日</th>
            <th>出勤時間</th>
            <th>退勤時間</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @forelse ($attendance as $day)
          <tr>
            <td>{{ $day->start_time?->format('j') }}</td>
            <td>{{ $day->start_time?->format('H:i') }}</td>
            <td>{{ $day->finish_time?->format('H:i') }}</td>
            <td>
              <a href="{{ route('admin.attendance.edit', ['attendance' => $day]) }}"><button>編集</button></a>
              <form method="POST" action="{{ route('admin.attendance.destroy', ['attendance' => $day]) }}" class="inline delete_attendance_form">
                @method('DELETE')
                @csrf

                  <button type="button" class="bg-red-500 text-white delete_attendance_btn">削除</button>
              </form>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="4">該当なし</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</x-app-layout>
