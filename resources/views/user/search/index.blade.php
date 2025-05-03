@extends('layouts.navigation')

@section('content')
    <div class="w-full mx-auto px-4 py-8 flex flex-col items-center">
        <!-- Thêm CDN Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

        <h1 class="text-3xl font-bold mb-4 text-center w-full">Công cụ tìm kiếm</h1>

        <form id="search-form" class="mb-6 flex gap-2 w-full justify-center" action="/search" method="get">
            <input type="text" name="q" value="{{ $query ?? '' }}" placeholder="Nhập từ khóa..."
                class="flex-grow border p-2 shadow">
            <button type="submit" class="bg-gray-600 text-white px-4 py-2">Tìm</button>
        </form>

        @if (isset($results) && count($results) > 0)
            <div id="results" class="space-y-4 w-full">
                @foreach ($results as $result)
                    <div class="p-4 border rounded-none shadow bg-white">
                        <div class="flex items-center mb-2">
                            <!-- Hiển thị icon nền tảng với màu -->
                            <i class="{{ $result['platform_icon'] }} text-lg mr-2 {{ $result['platform_color'] }}"
                               aria-label="Nền tảng {{ $result['platform'] }}"></i>
                            <!-- Hiển thị tên nền tảng -->
                            <span class="text-sm text-gray-600">{{ $result['platform'] }}</span>
                        </div>
                        <a href="{{ $result['link'] }}" target="_blank"
                            class="text-lg text-blue-700 font-semibold hover:underline">{{ $result['title'] }}</a>

                        <!-- Hiển thị ảnh nếu có -->
                        @if (isset($result['thumbnail']))
                            <img src="{{ $result['thumbnail'] }}" alt="Image"
                                class="mt-2 mb-2 max-w-full h-auto">
                        @endif

                        <p class="text-gray-600 mt-1">{{ $result['snippet'] ?? '' }}</p>

                        <!-- Hiển thị ngày nếu có -->
                        @if (isset($result['date']))
                            <p class="text-sm text-gray-500 mt-2">Ngày đăng:
                                {{ \Carbon\Carbon::createFromTimestamp(strtotime($result['date']))->diffForHumans() }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @elseif(isset($results))
            <p>Không tìm thấy kết quả nào.</p>
        @endif
    </div>
@endsection