<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ダッシュボード') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>

    @can ('user.authority')
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="p-6">
            <a href="{{ route('stamping.index') }}">→打刻画面へ(stamping)</a>
        </div>
    </div>
    @endcan

    @can ('admin.authority')
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="p-6">
            <a href="{{ route('admin.index') }}">→管理者画面へ(Admin Index page)</a>
        </div>
    </div>
    @endcan

    <div class="py-4">
        <div class="py-4 text-center">
            <span class="text-xl">{{ today()->format('Y年n月j日') }}の勤怠</span>
        </div>
        <table class="mx-auto attendance">
            <thead>
                <tr>
                    <th>名前</th>
                    <th>出勤時間</th>
                    <th>退勤時間</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($attendance as $attendee)
                    <tr>
                        <td>{{ $attendee->user->name }}</td>
                        <td>{{ $attendee->round_start_time?->format('H:i') }}</td>
                        <td>{{ $attendee->round_finish_time?->format('H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">該当なし</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
