@extends('layouts.admin')

@section('title', 'Quản lý phòng ban')

@section('content')
    <div class="flex items-center mt-4" style="background-color: #f1f1f1; padding: 20px 16px;">
        <h2 class="text-md underline">
            Danh sách phòng ban
        </h2>
    </div>
    <div class="p-4 mx-auto">
        <div class="flex justify-between mb-4">
            <div class="flex justify-between">
                <a href="{{ route('departments.create') }}" class="inline-block hover:text-blue-700">
                    <button class="bg-blue-600 text-white px-4 py-2 hover:bg-blue-700 transition duration-300 ease-in-out">
                        <i class="fas fa-plus mr-2"></i> Thêm phòng ban
                    </button>
                </a>
            </div>
            <form action="{{ route('departments.index') }}" method="GET" class="flex items-center">
                <input type="text" name="search" placeholder="Tìm kiếm phòng ban..."
                    class="border border-gray-300 px-4 py-2" style="width: 400px;" value="{{ request()->get('search') }}">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 hover:bg-blue-700"><i
                        class="fas fa-search"></i></button>
            </form>
        </div>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-900">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 bg-white">Tên phòng ban</th>
                        <th scope="col" class="px-6 py-3 bg-white">Mô tả</th>
                        <th scope="col" class="px-6 py-3 bg-white">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($departments as $department)
                        <tr class="border-b border-gray-200 bg-white">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $department->name }}</td>
                            <td class="px-6 py-4 text-gray-900">{{ $department->description }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('departments.edit', $department) }}"
                                    class="font-medium text-blue-600 hover:underline">Sửa</a>
                                <form action="{{ route('departments.destroy', $department) }}" method="POST"
                                    class="inline-block" onsubmit="return confirm('Bạn chắc chắn muốn xóa?');">
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

        <div class="mt-4">
            {{ $departments->links() }} <!-- Pagination links -->
        </div>
    </div>
@endsection
