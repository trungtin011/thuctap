<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tìm kiếm Google với SerpAPI</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="relative bg-gray-50 text-gray-800 container mx-auto">
    <!-- Button back home -->
    <a href="{{ route('home') }}"
        class="absolute top-4 left-4 border border-gray-600 text-gray-600 px-4 py-2 rounded-md hover:bg-gray-600 hover:text-white transition duration-200">
        <i class="fas fa-arrow-left"></i>
    </a>


    <div class="w-full mx-auto px-4 py-8 flex flex-col items-center">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

        <h1 class="text-2xl sm:text-3xl font-bold mb-6 text-center w-full">Công cụ tìm kiếm</h1>

        <form id="search-form" class="mb-6 flex gap-2 w-full justify-center" action="/search" method="get">
            <input type="text" name="q" value="{{ $query ?? '' }}" placeholder="Nhập từ khóa..."
                class="flex-grow border p-2 shadow">

            <select name="platform" class="border p-2 shadow">
                <option value="">Tất cả nền tảng</option>
                <option value="Facebook" {{ request('platform') == 'Facebook' ? 'selected' : '' }}>Facebook</option>
                <option value="YouTube" {{ request('platform') == 'YouTube' ? 'selected' : '' }}>YouTube</option>
                <option value="Shopee" {{ request('platform') == 'Shopee' ? 'selected' : '' }}>Shopee</option>
                <option value="Instagram" {{ request('platform') == 'Instagram' ? 'selected' : '' }}>Instagram</option>
                <option value="X" {{ request('platform') == 'X' ? 'selected' : '' }}>X</option>
                <option value="Tiki" {{ request('platform') == 'Tiki' ? 'selected' : '' }}>Tiki</option>
                <!-- Thêm các nền tảng khác tùy ý -->
            </select>

            <button type="submit" class="bg-gray-600 text-white px-4 py-2">Tìm</button>
        </form>


        @if (isset($results) && count($results) > 0)
            <div id="results" class="space-y-4 w-full">
                @foreach ($results as $result)
                    <div class="p-4 border rounded-md shadow bg-white text-sm sm:text-base">
                        <div class="flex items-center mb-2">
                            <i class="{{ $result['platform_icon'] }} text-lg mr-2 {{ $result['platform_color'] }}"
                                aria-label="Nền tảng {{ $result['platform'] }}"></i>
                            <span class="text-sm text-gray-600">{{ $result['platform'] }}</span>
                        </div>
                        <a href="{{ $result['link'] }}" target="_blank"
                            class="text-lg text-blue-700 font-semibold hover:underline">{{ $result['title'] }}</a>

                        <div class="mt-2 mb-2">
                            @if (isset($result['thumbnail']) && $result['thumbnail'])
                                <img src="{{ $result['thumbnail'] }}" alt="{{ $result['title'] }}"
                                    class="w-full h-auto object-contain sm:max-w-[150px]"
                                    onerror="this.src='{{ asset('images/placeholder.png') }}'">
                            @else
                                <img src="{{ asset('images/placeholder.png') }}" alt="No image"
                                    class="w-full h-auto object-contain sm:max-w-[150px]">
                            @endif
                        </div>

                        <p class="text-gray-600 mt-1">{{ $result['snippet'] ?? '' }}</p>

                        @if (isset($result['sentiment']))
                            <p class="text-sm mt-2">
                                Đánh giá:
                                @if ($result['sentiment'] === 'Tốt')
                                    <span class="text-green-500 font-semibold">{{ $result['sentiment'] }}</span>
                                @elseif ($result['sentiment'] === 'Xấu')
                                    <span class="text-red-500 font-semibold">{{ $result['sentiment'] }}</span>
                                @else
                                    <span class="text-gray-500 font-semibold">{{ $result['sentiment'] }}</span>
                                @endif
                            </p>
                        @endif
                    </div>
                @endforeach

                <!-- Hiển thị liên kết phân trang -->
                <div class="mt-6 flex justify-center">
                    {{ $paginator->appends(['q' => $query])->links() }}
                </div>
            </div>
        @elseif(isset($results))
            <p>Không tìm thấy kết quả nào.</p>
        @endif
    </div>
</body>

</html>
