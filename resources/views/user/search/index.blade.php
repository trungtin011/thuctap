@extends('layouts.navigation')

@section('content')
    <form method="GET" action="{{ route('google.search') }}">
        <input type="text" name="keyword" value="{{ $keyword ?? '' }}" placeholder="Nhập từ khóa..." required>
        <button type="submit">Tìm kiếm</button>
    </form>

    <h2>Kết quả cho từ khóa: {{ $keyword }}</h2>

    @if (!empty($vnexpress))
        @foreach ($vnexpress as $item)
            <div>
                <h3><a href="{{ $item['link'] }}" target="_blank">{{ $item['title'] }}</a></h3>
                <p>{{ $item['snippet'] }}</p>
            </div>
        @endforeach
    @else
        <p>Không tìm thấy kết quả phù hợp.</p>
    @endif
@endsection
