<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Công cụ tìm kiếm Google với SerpAPI</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Chế độ tối */
        .dark-mode {
            background-color: #181818 !important;
            /* Màu nền tối cho body */
            color: #f7fafc !important;
            /* Màu chữ sáng */
        }

        /* Các phần tử khác trong chế độ tối */
        .dark-mode .bg-gray-100 {
            background-color: #212121;
            /* Màu nền cho các phần tử */
        }

        .dark-mode .border-gray-300 {
            border-color: #4a5568;
            /* Màu viền cho các phần tử */
        }

        .dark-mode .text-gray-600 {
            color: #e2e8f0;
            /* Màu chữ cho các phần tử */
        }

        .dark-mode .bg-blue-600 {
            background-color: #2b6cb0;
            /* Màu nút cho chế độ tối */
        }

        .dark-mode .bg-blue-700 {
            background-color: #2c5282;
            /* Màu nút hover cho chế độ tối */
        }

        body {
            background-color: #ffffff;
            /* Màu nền sáng cho body */
            color: #181818;
            /* Màu chữ sáng cho body */
        }
    </style>
</head>

<body class="relative bg-white text-gray-900 container mx-auto">

    <!-- Button to toggle dark mode -->
    <button id="toggle-theme"
        class="absolute top-4 right-4 border border-gray-600 text-gray-600 px-4 py-2 rounded-md hover:bg-gray-600 hover:text-white transition duration-200">
        <i class="fas fa-adjust"></i>
    </button>

    <!-- Button back home -->
    <a href="{{ route('home') }}"
        class="absolute top-4 left-4 border border-gray-600 text-gray-600 px-4 py-2 rounded-md hover:bg-gray-600 hover:text-white transition duration-200">
        <i class="fas fa-arrow-left"></i>
    </a>

    <div class="w-full mx-auto px-4 py-8 flex flex-col items-center">
        <h1 class="text-3xl sm:text-5xl mt-4 mb-6 font-bold text-center w-full">Công cụ
            tìm kiếm</h1>

        <form id="search-form" class="mb-6 gap-2 w-full flex flex-col sm:flex" action="{{ route('search.results') }}"
            method="get">
            <div class="w-full sm:w-2/2 flex flex-col sm:flex-row items-center">
                <div class="relative w-full">
                    <input type="text" id="searchBox" name="q" value="{{ $query ?? '' }}"
                        placeholder="Nhập từ khóa..." class="flex-grow p-2 w-full border focus:outline-none text-gray-900"
                        autocomplete="off">
                    <ul id="suggestionList"
                        class="absolute bg-white border border-gray-300 w-full mt-1 z-50 shadow-md hidden max-h-60 overflow-y-auto rounded-md"
                        aria-labelledby="searchBox">
                    </ul>
                </div>
                <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 mt-3 sm:mt-0 sm:ml-0 w-full sm:w-auto hover:bg-blue-700 transition duration-200">
                    <i class="fas fa-search"></i>
                </button>
            </div>

            <div class="flex gap-2 items-center w-full sm:w-auto text-gray-900">
                <select name="sentiment" id="sentiment" class="border p-2 w-full sm:w-auto rounded-md">
                    <option value="">Tất cả đánh giá</option>
                    <option value="Tốt" {{ request('sentiment') === 'Tốt' ? 'selected' : '' }}>Tốt</option>
                    <option value ="Trung bình" {{ request('sentiment') === 'Trung bình' ? 'selected' : '' }}>Trung bình
                    </option>
                    <option value="Xấu" {{ request('sentiment') === 'Xấu' ? 'selected' : '' }}>Xấu</option>
                </select>
                <select name="lang" id="lang" class="border p-2 w-full sm:w-auto rounded-md">
                    <option value="vi" {{ request('lang') === 'vi' ? 'selected' : '' }}>Tiếng Việt</option>
                    <option value="en" {{ request('lang') === 'en' ? 'selected' : '' }}>English</option>
                    <option value="ja" {{ request('lang') === 'ja' ? 'selected' : '' }}>日本語</option>
                    <option value="fr" {{ request('lang') === 'fr' ? 'selected' : '' }}>Français</option>
                    <option value="ko" {{ request('lang') === 'ko' ? 'selected' : '' }}>한국어</option>
                    <option value="zh" {{ request('lang') === 'zh' ? 'selected' : '' }}>中文</option>
                </select>
            </div>
        </form>

        @if (!empty($results) && is_iterable($results) && count($results) > 0)
            <div id="results" class="space-y-4 w-full">
                @foreach ($results as $result)
                    <div data-sentiment="{{ $result['sentiment'] }}"
                        class="search-result p-4 border rounded-md shadow bg-gray-100">
                        <div class="flex items-center mb-2">
                            <i class="{{ $result['platform_icon'] }} text-lg mr-2 {{ $result['platform_color'] }}"
                                aria-label="Nền tảng {{ $result['platform'] }}"></i>
                            @if (!empty($result['domain']))
                                <span class="text-sm text-gray-600">{{ $result['domain'] }}</span>
                            @endif
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
                                    <span class="text-green-600 font-semibold">{{ $result['sentiment'] }}</span>
                                @elseif ($result['sentiment'] === 'Xấu')
                                    <span class="text-red-600 font-semibold">{{ $result['sentiment'] }}</span>
                                @else
                                    <span class="text-gray-600 font-semibold">{{ $result['sentiment'] }}</span>
                                @endif
                            </p>
                        @endif
                    </div>
                @endforeach

                <div class="mt-6 flex justify-center">
                    {{ $paginator->appends(['q' => $query])->links() }}
                </div>
            </div>
        @elseif(empty($results) && !empty($query))
            <p>Không tìm thấy kết quả phù hợp với từ khóa: "{{ $query }}".</p>
        @endif
    </div>

    <!-- Loading Skeleton -->
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
    const loadingSkeleton = document.getElementById('loading-skeleton');
    const results = document.getElementById('results');
    const toggleThemeButton = document.getElementById('toggle-theme');

    form.addEventListener('submit', function() {
        if (results) {
            results.classList.add('hidden'); // Ẩn kết quả cũ
        }
        loadingSkeleton.classList.remove('hidden'); // Hiện khung skeleton
    });

    document.getElementById('sentiment').addEventListener('change', function() {
        document.getElementById('search-form').submit();
    });

    document.getElementById('lang').addEventListener('change', function() {
        document.getElementById('search-form').submit();
    });

    // Chức năng chuyển đổi chế độ sáng/tối
    toggleThemeButton.addEventListener('click', function() {
        document.body.classList.toggle('dark-mode');
        const isDarkMode = document.body.classList.contains('dark-mode');
        localStorage.setItem('dark-mode', isDarkMode); // Lưu trạng thái vào localStorage
    });

    // Kiểm tra trạng thái chế độ khi tải trang
    window.addEventListener('load', function() {
        const isDarkMode = localStorage.getItem('dark-mode') === 'true';
        if (isDarkMode) {
            document.body.classList.add('dark-mode');
        }
    });

    document.addEventListener("DOMContentLoaded", function() {
        const searchBox = document.getElementById("searchBox");
        const suggestionList = document.getElementById("suggestionList");

        searchBox.addEventListener("keyup", function() {
            let query = this.value.trim();

            if (query.length < 2) {
                suggestionList.classList.add("hidden");
                return;
            }

            fetch(`/autocomplete?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    suggestionList.innerHTML = "";
                    if (data.length === 0) {
                        suggestionList.classList.add("hidden");
                        return;
                    }

                    data.forEach(item => {
                        const li = document.createElement("li");
                        li.textContent = item;
                        li.className = "px-4 py-2 hover:bg-gray-100 cursor-pointer";
                        li.addEventListener("click", function() {
                            searchBox.value = item;
                            suggestionList.classList.add("hidden");
                        });
                        suggestionList.appendChild(li);
                    });

                    suggestionList.classList.remove("hidden");
                });
        });

        document.addEventListener("click", function(e) {
            if (!searchBox.contains(e.target) && !suggestionList.contains(e.target)) {
                suggestionList.classList.add("hidden");
            }
        });
    });
</script>
