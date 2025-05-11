@extends('layouts.navigation_admin')

@section('content')
    <div class="mx-auto mt-10" style="width: 1540px">
        <h1 class="text-2xl font-bold mb-5">Lịch sử tìm kiếm</h1>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-white bg-blue-600">
                    <tr>
                        <th scope="col" class="px-6 py-3">Từ khóa</th>
                        <th scope="col" class="px-6 py-3">Người dùng</th>
                        <th scope="col" class="px-6 py-3">Thời gian</th>
                        <th scope="col" class="px-6 py-3">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($searchHistories as $history)
                        <tr
                            class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $history->query }}
                            </td>
                            <td class="px-6 py-4 text-gray-900 dark:text-white">
                                {{ $history->user->name ?? 'Khách hàng' }}
                            </td>
                            <td class="px-6 py-4 text-gray-900 dark:text-white">
                                {{ \Carbon\Carbon::parse($history->created_at)->format('d/m/Y H:i') }}
                            </td>

                            <td class="px-6 py-4">
                                <form action="" method="POST"
                                    onsubmit="return confirm('Xóa lịch sử tìm kiếm này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">
                                        Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
