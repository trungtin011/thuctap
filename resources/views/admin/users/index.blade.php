@extends('layouts.admin')

@section('title', 'Quản lý người dùng')

@section('content')
<div class="mx-auto">
    <h2 class="text-2xl font-bold mb-6">Danh sách người dùng</h2>
    <a href="{{ route('users.create') }}" class="mb-4 inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Thêm người dùng</a>
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">{{ session('success') }}</div>
    @endif
    <table class="min-w-full bg-white rounded shadow">
        <thead>
            <tr>
                <th class="px-4 py-2">Tên</th>
                <th class="px-4 py-2">Email</th>
                <th class="px-4 py-2">Phòng ban</th>
                <th class="px-4 py-2">Vai trò</th>
                <th class="px-4 py-2">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr class="border-t text-center">
                <td class="px-4 py-2">{{ $user->name }}</td>
                <td class="px-4 py-2">{{ $user->email }}</td>
                <td class="px-4 py-2">{{ $user->department->name ?? '' }}</td>
                <td class="px-4 py-2">{{ $user->role->name ?? '' }}</td>
                <td class="px-4 py-2">
                    <a href="{{ route('users.edit', $user) }}" class="text-blue-600 hover:underline">Sửa</a>
                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn chắc chắn muốn xóa?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline ml-2">Xóa</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
@endsection
