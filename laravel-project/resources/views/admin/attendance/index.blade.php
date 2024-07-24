<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ __('勤怠管理：管理者 - 勤怠詳細') }}
      </h2>
  </x-slot>

  <div class="py-4 space-around">
    <span class="user-name">{{ $user->name }}</span>
    <a href="{{ route('admin.index') }}"><button>ユーザー一覧へ</button></a>
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
            <a href="#"><button>編集</button></a>
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
</x-app-layout>
