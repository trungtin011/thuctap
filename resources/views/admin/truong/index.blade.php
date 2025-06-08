@extends('layouts.admin')

@section('title', 'Danh sách Trường Dữ Liệu')

@section('content')
<div class="flex items-center mt-4" style="background-color: #f1f1f1; padding: 20px 16px;">
    <h2 class="text-md underline">
        Danh sách Trường Dữ Liệu
    </h2>
</div>

<div class="p-4 mx-auto max-w-7xl">
    <div class="flex justify-between mb-4">
        <div>
            <a href="{{ route('admin.fields.create') }}" class="inline-block hover:text-blue-700">
                <button class="bg-blue-600 text-white px-4 py-2 hover:bg-blue-700 transition duration-300 ease-in-out">
                    <i class="fas fa-plus mr-2"></i> Thêm Trường mới
                </button>
            </a>
        </div>
        {{-- Nếu cần tìm kiếm trường dữ liệu thì thêm form ở đây --}}
    </div>

    @if(session('success'))
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
        {{ session('success') }}
    </div>
    @endif

    @if($fields->isEmpty())
    <div class="text-gray-500 text-center my-8">Chưa có trường dữ liệu nào được tạo.</div>
    @else
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-900">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 bg-white">ID</th>
                    <th scope="col" class="px-6 py-3 bg-white">Tên trường</th>
                    <th scope="col" class="px-6 py-3 bg-white">Loại</th>
                    <th scope="col" class="px-6 py-3 bg-white">Phòng ban</th>
                    <th scope="col" class="px-6 py-3 bg-white">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($fields as $field)
                <tr class="border-b border-gray-200 bg-white">
                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $field->id }}</td>
                    <td class="px-6 py-4 text-gray-900">{{ $field->name }}</td>
                    <td class="px-6 py-4 text-gray-900">{{ $field->type }}</td>
                    <td class="px-6 py-4 text-gray-900">
                        {{ $field->department ? $field->department->name : 'Chưa có' }}
                    </td>

                    <td class="px-6 py-4">
                        <a href="{{ route('admin.fields.edit', $field->id) }}" class="text-blue-600 hover:underline mr-2">
                            Sửa
                        </a>
                        <form action="{{ route('admin.fields.destroy', $field->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Xóa</button>
                        </form>
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection