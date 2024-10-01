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

    <form method="POST" action="{{ route('admin.csv.upload') }}" enctype="multipart/form-data">
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
  </div>
</x-app-layout>
