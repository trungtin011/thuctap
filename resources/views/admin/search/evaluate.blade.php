@extends('layouts.navigation_admin')

@section('content')
    <h2>Đánh giá URL thủ công</h2>

    <form method="POST" action="{{ route('admin.search.evaluate') }}">
        @csrf
        <input type="text" name="url" placeholder="Nhập URL cần đánh giá" required>
        <textarea name="snippet" placeholder="Nhập mô tả ngắn nếu có (snippet)"></textarea>
        <button type="submit">Đánh giá</button>
    </form>

    @if (isset($sentiment))
        <p>Kết quả đánh giá: <strong>{{ $sentiment }}</strong></p>
    @endif
@endsection
