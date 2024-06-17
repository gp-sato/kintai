<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('勤怠管理 - 打刻(stamping)') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h3 class="text-xl">{{ $user->name }}&nbsp;さん</h3>
    </div>

    <div class="py-6 text-center text-xl">
        <div class="py-4">
            {{ today()->format("Y年m月d日") }}
        </div>
        <div class="py-4">
            出勤時間(start&nbsp;time)：{{ $attendance?->start_time?->format("H:i") ?? "--:--" }}
        </div>
        <div class="py-4">
            退勤時間(finish&nbsp;time)：{{ $attendance?->finish_time?->format("H:i") ?? "--:--" }}
        </div>
    </div>

    <div class="py-6 text-center">
        <a href="#">
            <button class="p-4 text-xl bg-white rounded-full">打刻(stamping)</button>
        </a>
    </div>
</x-app-layout>
