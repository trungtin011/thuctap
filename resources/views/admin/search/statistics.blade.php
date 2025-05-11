@extends('layouts.navigation_admin')

@section('content')
    <div class="mx-auto mt-10 px-4" style="width: 1540px;">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Thống kê tìm kiếm</h1>

        <!-- Thống kê tìm kiếm chung trong một bảng -->
        <table
            class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 shadow-lg rounded-lg border-collapse">
            <thead class="text-xs text-white bg-blue-600">
                <tr>
                    <th scope="col" class="px-6 py-3">Danh mục</th>
                    <th scope="col" class="px-6 py-3">Truy vấn</th>
                    <th scope="col" class="px-6 py-3 text-center">Số lần</th>
                </tr>
            </thead>
            <tbody>
                <!-- Các truy vấn phổ biến -->
                @foreach ($topQueries as $query)
                    <tr
                        class="odd:bg-white even:bg-gray-50 dark:odd:bg-gray-900 dark:even:bg-gray-800 border-b border-gray-200">
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">Truy vấn</td>
                        <td class="px-6 py-4 text-gray-900 dark:text-white">{{ $query->query }}</td>
                        <td class="px-6 py-4 text-center text-gray-900 dark:text-white">{{ $query->total }}</td>
                    </tr>
                @endforeach
            </tbody>
            <thead class="text-xs text-white bg-blue-700">
                <tr>
                    <th scope="col" class="px-6 py-3">Danh mục</th>
                    <th scope="col" class="px-6 py-3">Cảm xúc</th>
                    <th scope="col" class="px-6 py-3 text-center">Số lần</th>
                </tr>
            </thead>
            <tbody>
                <!-- Thống kê cảm xúc -->
                @foreach ($sentimentStats as $sentiment)
                    <tr
                        class="odd:bg-white even:bg-gray-50 dark:odd:bg-gray-900 dark:even:bg-gray-800 border-b border-gray-200">
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">Cảm xúc</td>
                        <td class="px-6 py-4 text-gray-900 dark:text-white">{{ ucfirst($sentiment->sentiment) }}</td>
                        <td class="px-6 py-4 text-center text-gray-900 dark:text-white">{{ $sentiment->total }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
