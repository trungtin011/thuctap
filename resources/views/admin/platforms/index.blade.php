@extends('layouts.admin')

@section('title', 'Quản lý nền tảng')

@section('content')
    <div class="flex items-center mt-4" style="background-color: #f1f1f1; padding: 20px 16px;">
        <h2 class="text-md underline">
            Danh sách nền tảng
        </h2>
    </div>

    <div class="p-4 mx-auto">
        <div class="flex justify-between mb-4">
            <div class="flex justify-between">
                <a href="{{ route('platforms.create') }}" class="inline-block hover:text-blue-700">
                    <button class="bg-blue-600 text-white px-4 py-2 hover:bg-blue-700 transition duration-300 ease-in-out">
                        <i class="fas fa-plus mr-2"></i> Thêm nền tảng
                    </button>
                </a>
            </div>

            <div class="flex flex-wrap items-center gap-4">
                <!-- Form lọc theo ngày -->
                <form action="{{ route('platforms.index') }}" method="GET" class="flex items-center gap-2">
                    <input type="hidden" name="search" value="{{ request()->get('search') }}"> {{-- giữ lại từ khóa tìm kiếm --}}

                    <input type="date" name="start_date" value="{{ request()->get('start_date') }}"
                        class="border border-gray-300 px-2 py-2">
                    <span>-</span>
                    <input type="date" name="end_date" value="{{ request()->get('end_date') }}"
                        class="border border-gray-300 px-2 py-2">

                    <div class="flex items-center">
                        @if (request()->get('start_date') || request()->get('end_date'))
                            <a href="{{ route('platforms.index') }}"
                                class="bg-red-600 text-white px-4 py-2 hover:bg-red-700 transition duration-300 mr-2"><i class="fa-solid fa-rotate"></i></a>
                        @endif
                        
                        <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 hover:bg-blue-700 transition duration-300">
                            <i class="fas fa-filter mr-1"></i>
                        </button>
                    </div>
                </form>

                <!-- Form tìm kiếm theo tên -->
                <form action="{{ route('platforms.index') }}" method="GET" class="flex items-center">
                    <input type="text" name="search" placeholder="Tìm kiếm tên nền tảng..."
                        class="border border-gray-300 px-4 py-2" style="width: 240px;"
                        value="{{ request()->get('search') }}">

                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 hover:bg-blue-700 transition duration-300">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-900">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 bg-white text-center">ID</th>
                        <th class="px-6 py-3 bg-white text-center">Tên nền tảng</th>
                        <th class="px-6 py-3 bg-white text-center">Ngày tạo</th>
                        <th class="px-6 py-3 bg-white text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($platforms as $platform)
                        <tr class="border-b bg-white">
                            <td class="px-6 py-4 text-center">{{ $platform->id }}</td>
                            <td class="px-6 py-4 text-center">{{ $platform->name }}</td>
                            <td class="px-6 py-4 text-center">
                                {{ $platform->created_at ? $platform->created_at->format('d/m/Y H:i') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('platforms.edit', $platform->id) }}"
                                    class="text-blue-600 hover:underline">Sửa</a>
                                <form action="{{ route('platforms.destroy', $platform->id) }}" method="POST"
                                    class="inline-block" onsubmit="return confirm('Bạn chắc chắn muốn xóa nền tảng này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline ml-2">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">Không tìm thấy nền tảng nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $platforms->withQueryString()->links() }}
        </div>
    </div>
@endsection
