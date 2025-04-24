@extends('layouts.navigation')

@section('content')
    <div class="max-w-3xl mx-auto p-6">
        <form method="GET" action="{{ route('google.search') }}" class="mb-6">
            <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                <input type="text" name="keyword" value="{{ $keyword ?? '' }}" placeholder="Nhập từ khóa..." required
                    class="flex-grow p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-blue-500 text-white p-2 hover:bg-blue-600 transition duration-200">Tìm
                    kiếm</button>
            </div>
        </form>

        <h2 class="text-2xl font-semibold mb-4">Kết quả cho từ khóa: <span class="text-blue-600">{{ $keyword }}</span>
        </h2>

        @if (!empty($vnexpress))
            @foreach ($vnexpress as $item)
                <div class="bg-white text-black p-4 mb-4 rounded-lg shadow-md">
                    <h3 class="font-bold text-xl mb-2">
                        <a href="{{ $item['link'] }}" target="_blank"
                            class="text-blue-500 hover:underline">{{ $item['title'] }}</a>
                    </h3>
                    <p class="mb-2 leading-relaxed text-base">{{ $item['snippet'] }}</p>
                </div>
            @endforeach
        @else
            <p class="text-gray-500">Không tìm thấy kết quả phù hợp.</p>
        @endif
    </div>
@endsection
