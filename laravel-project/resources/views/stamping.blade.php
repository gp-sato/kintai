<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('勤怠管理 - 打刻(stamping)') }}
        </h2>
    </x-slot>

    <div class="relative">
        <div class="my-container">
            @if (session('message'))
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    {{ session('message') }}
                </div>
            @endif
        
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                <h3 class="text-xl">{{ $user->name }}&nbsp;さん</h3>
            </div>
        
            <div class="py-4 text-center text-xl">
                <div class="py-4">
                    {{ today()->format("Y年m月d日") }}
                </div>
                <div class="py-4">
                    出勤時間(start&nbsp;time)：{{ $attendance?->round_start_time?->format("H:i") ?? "--:--" }}
                </div>
                <div class="py-4">
                    退勤時間(finish&nbsp;time)：{{ $attendance?->round_finish_time?->format("H:i") ?? "--:--" }}
                </div>
            </div>
        
            <div class="py-4 text-center">
                <form action="{{ route('stamping.store') }}" method="POST">
                    @csrf
                    <button type="submit" class="p-4 text-xl bg-white rounded-full" @if(!is_null($attendance?->finish_time)) disabled @endif>打刻(stamping)</button>
                </form>
            </div>
        </div>

        <div class="py-4 absolute top-0 right-8 attendance-wrap">
            <div class="py-4 text-center">
                <span class="text-xl">{{ today()->format('Y年n月j日') }}の勤怠</span>
            </div>
            <table class="mx-auto attendance stamping">
                <thead>
                    <tr>
                        <th>名前</th>
                        <th>出勤時間</th>
                        <th>退勤時間</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($userAll as $general_user)
                        <tr>
                            <td>{{ $general_user->name }}</td>
                            <td>{{ $general_user->string_round_start_time ?? '' }}</td>
                            <td>{{ $general_user->string_round_finish_time ?? '' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">該当なし</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
