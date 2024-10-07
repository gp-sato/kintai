<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ __('勤怠管理：管理者 - CSV') }}
      </h2>
  </x-slot>

  <div class="my-container">
    <div class="py-4 text-right">
      <a href="{{ route('admin.index') }}"><button>戻る</button></a>
    </div>

    <div class="py-4">
      <span>CSVアップロード</span>
    </div>

    @if (session('error'))
      <p class="pb-4 text-red-500">
        {{ session('error') }}
      </p>
    @endif

    @if (session('importResult'))
      <p class="pb-4">
        {{ session('importResult') }}
      </p>
    @endif

    <form method="POST" action="{{ route('admin.csv.upload') }}" enctype="multipart/form-data" class="pb-4">
      @csrf

      <div>
        <x-input-label for="user_id" :value="__('名前')" />
        <select name="user_id" id="user_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            <option value="">--</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @if(old('user_id') == $user->id) selected @endif>{{ $user->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
      </div>

      <div class="mt-4">
        <x-input-label for="csv_file" :value="__('CSV選択')" />
        <input type="file" name="csv_file" id="csv_file" class="mt-1" accept=".csv">
        <x-input-error :messages="$errors->get('csv_file')" class="mt-2" />
      </div>

      <div class="flex items-center justify-start mt-4">
        <x-primary-button class="ms-3">{{ __('アップロード') }}</x-primary-button>
      </div>
    </form>

    <hr>

    <div class="py-4">
      <span>CSVダウンロード</span>
    </div>

    <form method="GET" action="{{ route('admin.csv.download') }}" class="pb-4">
      <div>
        <x-input-label for="download_user_id" :value="__('名前')" />
        <select name="download_user_id" id="download_user_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            <option value="">--</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @if(old('download_user_id') == $user->id) selected @endif>{{ $user->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('download_user_id')" class="mt-2" />
      </div>

      <div class="mt-4">
        <div class="flex">
          <x-input-label for="year" :value="__('年')" />
          <select name="year" id="year" class="mt-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
              <option value="">--</option>
              @foreach (range(2017, now()->year) as $year)
                  <option value="{{ $year }}" @if(old('year') == $year) selected @endif>{{ $year }}</option>
              @endforeach
          </select>
          &emsp;
          <x-input-label for="month" :value="__('月')" />
          <select name="month" id="month" class="mt-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
              <option value="">--</option>
              @foreach (range(1, 12) as $month)
                  <option value="{{ $month }}" @if(old('month') == $month) selected @endif>{{ $month }}</option>
              @endforeach
          </select>
        </div>
        <x-input-error :messages="$errors->get('year')" class="mt-2" />
        <x-input-error :messages="$errors->get('month')" class="mt-2" />
        <x-input-error :messages="$errors->get('yearMonth')" class="mt-2" />
      </div>

      <div class="flex items-center justify-start mt-4">
        <x-primary-button class="ms-3">{{ __('ダウンロード') }}</x-primary-button>
      </div>
    </form>
  </div>
</x-app-layout>
