@extends('layouts.admin')

@section('title', 'Quản lý loại chi phí')

@section('content')
    <div class="flex items-center mt-4" style="background-color: #f1f1f1; padding: 20px 16px;">
        <h2 class="text-md underline">
            Danh sách loại chi phí
        </h2>
    </div>
    <div class="p-4 mx-auto">
        <div class="flex justify-between mb-4">
            <div class="flex justify-between">
                <a href="{{ route('expense-types.create') }}" class="inline-block hover:text-blue-700">
                    <button class="bg-blue-600 text-white px-4 py-2 hover:bg-blue-700 transition duration-300 ease-in-out">
                        <i class="fas fa-plus mr-2"></i> Thêm loại chi phí
                    </button>
                </a>
            </div>
            <form action="{{ route('expense-types.index') }}" method="GET" class="flex items-center">
                <input type="text" name="search" placeholder="Tìm kiếm loại chi phí..."
                    class="border border-gray-300 px-4 py-2" style="width: 400px;" value="{{ request()->get('search') }}">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 hover:bg-blue-700"><i
                        class="fas fa-search"></i></button>
            </form>
        </div>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-900">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 bg-white">Tên loại chi phí</th>
                        <th scope="col" class="px-6 py-3 bg-white">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($expenseTypes as $expenseType)
                        <tr class="border-b border-gray-200 bg-white">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $expenseType->name }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('expense-types.edit', $expenseType) }}"
                                    class="font-medium text-blue-600 hover:underline">Sửa</a>
                                <form action="{{ route('expense-types.destroy', $expenseType) }}" method="POST"
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
            {{ $expenseTypes->links() }} <!-- Pagination links -->
        </div>
    </div>
@endsection
