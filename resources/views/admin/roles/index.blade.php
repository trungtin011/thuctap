@extends('layouts.admin')

@section('title', 'Quản lý vai trò')

@section('content')
    <div class="flex items-center mt-4" style="background-color: #f1f1f1; padding: 20px 16px;">
        <h2 class="text-md underline">Danh sách vai trò</h2>
    </div>

    <div class="p-4 mx-auto">
        <div class="flex justify-between mb-4">
            {{-- Nút thêm --}}
            <div>
                <a href="{{ route('roles.create') }}" class="inline-block hover:text-blue-700">
                    <button class="bg-blue-600 text-white px-4 py-2 hover:bg-blue-700 transition duration-300 ease-in-out">
                        <i class="fas fa-plus mr-2"></i> Thêm vai trò
                    </button>
                </a>
            </div>

            <div class="flex items-center gap-10">
                {{-- Form lọc phòng ban --}}
                <form action="{{ route('roles.index') }}" method="GET" class="flex items-center gap-2">
                    <select name="department_id" class="border border-gray-300 px-3 py-2">
                        <option value="">-- Lọc theo phòng ban --</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}"
                                {{ request()->department_id == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                    @if (request()->department_id)
                        <a href="{{ route('roles.index') }}" class="bg-red-600 text-white px-4 py-2 hover:bg-red-700"><i class="fas fa-rotate"></i></a>
                    @endif
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 hover:bg-blue-700">
                        <i class="fas fa-filter"></i>
                    </button>
                </form>
                {{-- Form tìm kiếm --}}
                <form action="{{ route('roles.index') }}" method="GET" class="flex items-center">
                    <input type="text" name="search" placeholder="Tìm kiếm vai trò..."
                        class="border border-gray-300 px-4 py-2" style="width: 300px;"
                        value="{{ request()->get('search') }}">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 hover:bg-blue-700">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>

        {{-- Thông báo thành công --}}
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">{{ session('success') }}</div>
        @endif

        {{-- Bảng vai trò --}}
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-900">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 bg-white">Tên vai trò</th>
                        <th class="px-6 py-3 bg-white">Cấp bậc</th>
                        <th class="px-6 py-3 bg-white">Phòng ban</th>
                        <th class="px-6 py-3 bg-white">Mô tả</th>
                        <th class="px-6 py-3 bg-white">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $role)
                        <tr class="border-b border-gray-200 bg-white">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $role->name }}</td>
                            <td class="px-6 py-4">{{ $role->level }}</td>
                            <td class="px-6 py-4">{{ $role->department->name ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $role->description }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('roles.edit', $role) }}" class="text-blue-600 hover:underline">Sửa</a>
                                <form action="{{ route('roles.destroy', $role) }}" method="POST" class="inline-block"
                                    onsubmit="return confirm('Bạn chắc chắn muốn xóa?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline ml-2">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Phân trang --}}
        <div class="mt-4">
            {{ $roles->appends(request()->query())->links() }}
        </div>
    </div>
@endsection
