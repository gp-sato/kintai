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
</x-app-layout>
