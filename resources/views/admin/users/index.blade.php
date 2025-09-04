@extends('layouts.admin')

@section('title', 'Quản lý người dùng')

@section('content')
    <div class="flex items-center mt-4" style="background-color: #f1f1f1; padding: 20px 16px;">
        <h2 class="text-md underline">Danh sách người dùng</h2>
    </div>

    <div class="p-4 mx-auto">
        <div class="flex justify-between mb-4">
            {{-- Nút thêm --}}
            <div>
                <a href="{{ route('users.create') }}" class="inline-block hover:text-blue-700">
                    <button class="bg-blue-600 text-white px-4 py-2 hover:bg-blue-700 transition duration-300 ease-in-out">
                        <i class="fas fa-plus mr-2"></i> Thêm người dùng
                    </button>
                </a>
            </div>

            <div class="flex items-center gap-10">
                {{-- Form lọc phòng ban và vai trò --}}
                <form action="{{ route('users.index') }}" method="GET" class="flex items-center gap-2">
                    {{-- Lọc theo phòng ban --}}
                    <select name="department_id" class="border border-gray-300 px-3 py-2">
                        <option value="">-- Lọc theo phòng ban --</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}"
                                {{ request()->department_id == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Lọc theo vai trò --}}
                    <select name="role_id" class="border border-gray-300 px-3 py-2">
                        <option value="">-- Lọc theo vai trò --</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" {{ request()->role_id == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Reset nếu có filter --}}
                    @if (request()->department_id || request()->role_id)
                        <a href="{{ route('users.index') }}" class="bg-red-600 text-white px-4 py-2 hover:bg-red-700"><i
                                class="fas fa-rotate"></i></a>
                    @endif

                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 hover:bg-blue-700">
                        <i class="fas fa-filter"></i>
                    </button>
                </form>

                {{-- Form tìm kiếm --}}
                <form action="{{ route('users.index') }}" method="GET" class="flex items-center">
                    <input type="text" name="search" placeholder="Tìm kiếm người dùng..."
                        class="border border-gray-300 px-4 py-2" style="width: 300px;"
                        value="{{ request()->get('search') }}">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 hover:bg-blue-700">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>

        {{-- Thông báo --}}
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- Bảng người dùng --}}
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-900">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 bg-white">Tên</th>
                        <th class="px-6 py-3 bg-white">Email</th>
                        <th class="px-6 py-3 bg-white">Phòng ban</th>
                        <th class="px-6 py-3 bg-white">Vai trò</th>
                        <th class="px-6 py-3 bg-white">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr class="border-b border-gray-200 bg-white">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $user->name }}</td>
                            <td class="px-6 py-4">{{ $user->email }}</td>
                            <td class="px-6 py-4">{{ $user->department->name ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $user->role->name ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('users.edit', $user) }}" class="text-blue-600 hover:underline">Sửa</a>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline-block"
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
            {{ $users->appends(request()->query())->links() }}
        </div>
    </div>
@endsection
