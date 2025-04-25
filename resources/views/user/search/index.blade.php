@extends('layouts.navigation')

@section('content')
    <div class="bg-white text-gray-900 font-sans p-4 max-w-4xl mx-auto">
        {{-- <form method="GET" action="{{ route('google.search') }}" class="mb-6">
            <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                <input type="text" name="keyword" value="{{ $keyword ?? '' }}" placeholder="Nhập từ khóa..." required
                    class="flex-grow p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-blue-500 text-white p-2 hover:bg-blue-600 transition duration-200">Tìm
                    kiếm</button>
            </div>
        </form> --}}
        <form method="GET" action="{{ route('google.search') }}"
            class="flex items-center space-x-4 border border-gray-300 rounded-md px-3 py-2 mb-6">
            <input aria-label="Search" name="keyword" value="{{ $keyword ?? '' }}" placeholder="Nhập từ khóa..."
                class="flex-grow outline-none text-sm text-gray-900" type="search" />
            <button aria-label="Search button" class="text-gray-500 text-lg" type="submit">
                <i class="fas fa-search">
                </i>
            </button>
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
            <p class="text-gray-500 mb-2">Không tìm thấy kết quả phù hợp.</p>
        @endif

        <div class="space-y-6">

            @if (!empty($vnexpress))
                @foreach ($vnexpress as $item)
                    <article class="flex space-x-4">
                        <img alt="{{ $item['title'] }}"
                            class="flex-shrink-0 object-cover rounded" height="90"
                            src=""
                            width="160" />
                        <div class="flex flex-col">
                            <h2 class="font-bold text-gray-900 text-base leading-tight mb-1">
                                <a href="{{ $item['link'] }}" target="_blank"
                                    class="text-blue-500 hover:underline">{{ $item['title'] }}</a>
                            </h2>
                            <p class="text-gray-600 text-sm leading-relaxed max-w-xl">
                                {{ $item['snippet'] }}
                            </p>
                        </div>
                    </article>
                @endforeach
            @endif
        </div>
    </div>
@endsection
