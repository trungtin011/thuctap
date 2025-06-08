@extends('layouts.admin')

@section('title', 'Danh sách nền tảng')

@section('content')
    <div class="p-4 mx-auto">
        <h2 class="text-2xl font-bold mb-6">Danh sách nền tảng</h2>

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
                                class="bg-red-600 text-white px-4 py-2 hover:bg-red-700 transition duration-300 mr-2"><i
                                    class="fa-solid fa-rotate"></i></a>
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

        <!-- Bảng nền tảng -->
        <div class="bg-white rounded shadow overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên nền
                            tảng</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chỉ số
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày tạo
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($platforms as $platform)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $platform->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @foreach ($platform->metrics as $metric)
                                    <div>{{ $metric->name }} ({{ $metric->unit ?? 'Không có đơn vị' }},
                                        {{ $metric->data_type }})</div>
                                @endforeach
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $platform->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('platforms.edit', $platform) }}"
                                    class="text-blue-600 hover:underline">Sửa</a>
                                <form action="{{ route('platforms.destroy', $platform) }}" method="POST"
                                    class="inline-block" onsubmit="return confirm('Bạn có chắc muốn xóa?');">
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

        <!-- Phân trang -->
        {{ $platforms->links() }}
    </div>
@endsection
