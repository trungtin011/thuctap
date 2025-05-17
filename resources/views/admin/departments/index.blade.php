@extends('layouts.admin')

@section('title', 'Quản lý phòng ban')

@section('content')
<div class="max-w-4xl mx-auto">
    <h2 class="text-2xl font-bold mb-6">Danh sách phòng ban</h2>
    <a href="{{ route('departments.create') }}" class="mb-4 inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Thêm phòng ban</a>
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">{{ session('success') }}</div>
    @endif
    <table class="min-w-full bg-white rounded shadow">
        <thead>
            <tr>
                <th class="px-4 py-2">Tên phòng ban</th>
                <th class="px-4 py-2">Mô tả</th>
                <th class="px-4 py-2">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($departments as $department)
            <tr class="border-t">
                <td class="px-4 py-2">{{ $department->name }}</td>
                <td class="px-4 py-2">{{ $department->description }}</td>
                <td class="px-4 py-2">
                    <a href="{{ route('departments.edit', $department) }}" class="text-blue-600 hover:underline">Sửa</a>
                    <form action="{{ route('departments.destroy', $department) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn chắc chắn muốn xóa?');">
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
        {{ $departments->links() }}
    </div>
</div>
@endsection
