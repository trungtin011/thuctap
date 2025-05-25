@extends('layouts.admin')

@section('title', 'Danh sách Đại lý')

@section('content')
    <div class="flex items-center mt-4" style="background-color: #f1f1f1; padding: 20px 16px;">
        <h2 class="text-md underline">
            Danh sách Đại lý
        </h2>
    </div>
    <div class="p-4 mx-auto max-w-7xl">
        <div class="flex justify-between mb-4">
            <div class="flex justify-between">
                <a href="{{ route('admin.dai_ly.create') }}" class="inline-block hover:text-blue-700">
                    <button class="bg-blue-600 text-white px-4 py-2 hover:bg-blue-700 transition duration-300 ease-in-out">
                        <i class="fas fa-plus mr-2"></i> Thêm Đại lý mới
                    </button>
                </a>
            </div>
            <form action="{{ route('admin.dai_ly.index') }}" method="GET" class="flex items-center">
                <input type="text" name="search" placeholder="Tìm kiếm đại lý..."
                    class="border border-gray-300 px-4 py-2" style="width: 400px;" value="{{ request()->get('search') }}">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 hover:bg-blue-700">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
        
        @if($daiLys->isEmpty())
            <div class="text-gray-500 text-center my-8">Không có đại lý nào được tìm thấy.</div>
        @else
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-900">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 bg-white">ID</th>
                            <th scope="col" class="px-6 py-3 bg-white">Tên Đại lý</th>
                            <th scope="col" class="px-6 py-3 bg-white">Email</th>
                            <th scope="col" class="px-6 py-3 bg-white">Số điện thoại</th>
                            <th scope="col" class="px-6 py-3 bg-white">Địa chỉ</th>
                            <th scope="col" class="px-6 py-3 bg-white">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($daiLys as $daiLy)
                            <tr class="border-b border-gray-200 bg-white">
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $daiLy->id }}</td>
                                <td class="px-6 py-4 text-gray-900">{{ $daiLy->ten_dai_ly }}</td>
                                <td class="px-6 py-4 text-gray-900">{{ $daiLy->email }}</td>
                                <td class="px-6 py-4 text-gray-900">{{ $daiLy->so_dien_thoai }}</td>
                                <td class="px-6 py-4 text-gray-900">{{ $daiLy->dia_chi }}</td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('admin.dai_ly.edit', $daiLy->id) }}"
                                       class="font-medium text-blue-600 hover:underline">Sửa</a>
                                    <form action="{{ route('admin.dai_ly.destroy', $daiLy->id) }}" method="POST"
                                          class="inline-block" onsubmit="return confirm('Bạn có chắc muốn xóa đại lý này?');">
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
                {{ $daiLys->links() }}
            </div>
        @endif
    </div>
@endsection 