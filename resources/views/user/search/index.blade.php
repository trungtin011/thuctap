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

        <h1 class="text-2xl sm:text-3xl font-bold mb-4 text-center w-full">Công cụ tìm kiếm</h1>

        <form id="search-form" class="mb-6 gap-2 w-full justify-center" action="{{ route('search.results') }}"
            method="get">
            <div class="w-full flex mb-3">
                <input type="text" name="q" value="{{ $query ?? '' }}" placeholder="Nhập từ khóa..."
                    class="flex-grow border p-2 shadow">

                <button type="submit" class="bg-gray-600 text-white px-4 py-2">Tìm</button>
            </div>

            <div class="flex gap-2 items-center">
                <select name="sentiment" id="sentiment" class="border p-2 shadow">
                    <option value="">Tất cả cảm xúc</option>
                    <option value="Tốt" {{ request('sentiment') === 'Tốt' ? 'selected' : '' }}>Tốt</option>
                    <option value="Xấu" {{ request('sentiment') === 'Xấu' ? 'selected' : '' }}>Xấu</option>
                    <option value="Trung bình" {{ request('sentiment') === 'Trung bình' ? 'selected' : '' }}>Trung bình
                    </option>
                </select>
            </div>
        </form>


        @if (!empty($results) && is_iterable($results) && count($results) > 0)
            <div id="results" class="space-y-4 w-full">
                @foreach ($results as $result)
                    <div data-sentiment="{{ $result['sentiment'] }}"
                        class="search-result p-4 border rounded-none shadow bg-white">
                        <div class="flex items-center mb-2">
                            <i class="{{ $result['platform_icon'] }} text-lg mr-2 {{ $result['platform_color'] }}"
                                aria-label="Nền tảng {{ $result['platform'] }}"></i>
                            <span class="text-sm text-gray-600">{{ $result['platform'] }}</span>
                        </div>
                        <a href="{{ $result['link'] }}" target="_blank"
                            class="text-lg text-blue-700 font-semibold hover:underline">{{ $result['title'] }}</a>

                        <div class="mt-2 mb-2">
                            @if (!empty($result['thumbnail']))
                                <img src="{{ $result['thumbnail'] }}" alt="{{ $result['title'] }}"
                                    class="w-full sm:max-w-[200px] h-auto object-contain"
                                    onerror="this.src='{{ asset('images/placeholder.png') }}'">
                            @else
                                <img src="{{ asset('images/placeholder.png') }}" alt="No image"
                                    class="w-full sm:max-w-[200px] h-auto object-contain">
                            @endif
                        </div>

                        <p class="text-gray-600 mt-1">{{ $result['snippet'] ?? '' }}</p>

                        @if (isset($result['sentiment']))
                            <p class="rating-text text-sm mt-2 font-semibold">
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
        @elseif(empty($results) && !empty($query))
            <p>Không tìm thấy kết quả phù hợp với từ khóa: "{{ $query }}".</p>
        @endif
    </div>
    <!-- Thêm khối này vào trước khi kết quả hiển thị -->
    <div id="loading-skeleton" class="w-full mx-auto {{ isset($results) ? 'hidden' : '' }}">
        @for ($i = 0; $i < 3; $i++)
            <div class="p-4 border rounded shadow bg-white animate-pulse">
                <div class="h-4 bg-gray-300 rounded w-1/3 mb-2"></div>
                <div class="h-6 bg-gray-400 rounded w-2/3 mb-2"></div>
                <div class="h-3 bg-gray-200 rounded w-full mb-2"></div>
                <div class="h-40 bg-gray-100 rounded"></div>
            </div>
        @endfor
    </div>
</body>

</html>
<script>
    const form = document.getElementById('search-form');
    const loadingOverlay = document.getElementById('loading-overlay');
    const loadingSkeleton = document.getElementById('loading-skeleton');
    const results = document.getElementById('results');

    form.addEventListener('submit', function() {
        if (results) {
            results.classList.add('hidden'); // Ẩn kết quả cũ
        }
        loadingSkeleton.classList.remove('hidden'); // Hiện khung skeleton
        loadingOverlay?.classList.remove('hidden'); // Hiện overlay
    });

    // document.getElementById('sentiment').addEventListener('change', function() {
    //     const form = document.getElementById('search-form');
    //     form.submit(); // Tự động submit form khi người dùng chọn cảm xúc
    // });

    document.getElementById('sentiment').addEventListener('change', function() {
        document.getElementById('search-form').submit();
    });

    document.getElementById('platform').addEventListener('change', function() {
        document.getElementById('search-form').submit();
    });
</script>
