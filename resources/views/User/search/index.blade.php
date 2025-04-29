<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tìm kiếm Google với SerpAPI</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-4">Tìm kiếm Google (SerpAPI)</h1>

        <form id="search-form" class="mb-6 flex gap-2" action="/search" method="get">
            <input type="text" name="q" value="{{ $query ?? '' }}" placeholder="Nhập từ khóa..." class="w-full border p-2 rounded shadow">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Tìm</button>
        </form>

        @if(isset($results) && count($results) > 0)
            <div id="results" class="space-y-4">
                @foreach($results as $result)
                    <div class="p-4 border rounded shadow bg-white">
                        <a href="{{ $result['link'] }}" target="_blank" class="text-lg text-blue-700 font-semibold hover:underline">{{ $result['title'] }}</a>
                        
                        <!-- Hiển thị ảnh nếu có -->
                        @if(isset($result['thumbnail']))
                            <img src="{{ $result['thumbnail'] }}" alt="Image" class="mt-2 mb-2 max-w-full h-auto rounded">
                        @endif

                        <p class="text-gray-600 mt-1">{{ $result['snippet'] ?? '' }}</p>

                        <!-- Hiển thị ngày nếu có -->
                        @if(isset($result['date']))
                            <p class="text-sm text-gray-500 mt-2">Ngày đăng: {{ \Carbon\Carbon::createFromTimestamp(strtotime($result['date']))->diffForHumans() }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @elseif(isset($results))
            <p>Không tìm thấy kết quả nào.</p>
        @endif
    </div>
</body>
</html>
