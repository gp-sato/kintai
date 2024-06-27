<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ __('勤怠管理：管理者') }}
      </h2>
  </x-slot>

  <div class="py-4">
    <table class="mx-auto">
      <thead>
        <tr>
          <th>名前</th>
          <th>メールアドレス</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach ($users as $user)
        <tr>
          <td>{{ $user->name }}</td>
          <td>{{ $user->email }}</td>
          <td>
            <a href="#"><button>勤怠</button></a>
            <a href="#"><button>編集</button></a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</x-app-layout>
