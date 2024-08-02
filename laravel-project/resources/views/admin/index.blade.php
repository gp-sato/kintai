<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ __('勤怠管理：管理者') }}
      </h2>
  </x-slot>

  <div class="py-4 text-center">
    <a href="{{ route('admin.user.create') }}"><button>ユーザー新規登録</button></a>
    <a href="#"><button>管理者編集</button></a>
    <a href="#"><button>CSV</button></a>
  </div>

  <div class="py-4 text-center">
    <form method="GET" action="{{ route('admin.index') }}">
      <div class="row">
        <label for="name">名前</label>
        <input type="text" id="name" name="name" value="{{ isset($name) ? $name : '' }}">
        <label for="email">メールアドレス</label>
        <input type="text" id="email" name="email" value="{{ isset($email) ? $email : '' }}">
        <button type="submit">検索</button>
        <button type="button" id="btn_clear">クリア</button>
      </div>
    </form>
  </div>

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
        @forelse ($users as $user)
        <tr>
          <td>{{ $user->name }}</td>
          <td>{{ $user->email }}</td>
          <td>
            <a href="{{ route('admin.attendance.index', $user) }}"><button>勤怠</button></a>
            <a href="{{ route('admin.user.edit', $user) }}"><button>編集</button></a>
          </td>
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
