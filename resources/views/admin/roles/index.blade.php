@extends('layouts.admin')

@section('title', 'Quản lý vai trò')

@section('content')
<div class="mx-auto">
    <h2 class="text-2xl font-bold mb-6">Danh sách vai trò</h2>
    <a href="{{ route('roles.create') }}" class="mb-4 inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Thêm vai trò</a>
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">{{ session('success') }}</div>
    @endif
    <table class="min-w-full bg-white rounded shadow">
        <thead>
            <tr>
                <th class="px-4 py-2">Tên vai trò</th>
                <th class="px-4 py-2">Cấp bậc</th>
                <th class="px-4 py-2">Phòng ban</th>
                <th class="px-4 py-2">Mô tả</th>
                <th class="px-4 py-2">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($roles as $role)
            <tr class="border-t text-center">
                <td class="px-4 py-2">{{ $role->name }}</td>
                <td class="px-4 py-2">{{ $role->level }}</td>
                <td class="px-4 py-2">{{ $role->department->name ?? '' }}</td>
                <td class="px-4 py-2">{{ $role->description }}</td>
                <td class="px-4 py-2">
                    <a href="{{ route('roles.edit', $role) }}" class="text-blue-600 hover:underline">Sửa</a>
                    <form action="{{ route('roles.destroy', $role) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn chắc chắn muốn xóa?');">
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
        {{ $roles->links() }}
    </div>
</div>
@endsection
