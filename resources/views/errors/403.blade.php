@extends('layouts.navbar')

@section('title', '403 Forbidden')
@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex flex-col items-center justify-center mt-20">
            <h1 class="text-6xl font-bold text-red-600">403</h1>
            <p class="mt-4 text-lg text-gray-700">Bị cấm: Bạn không có quyền truy cập vào tài nguyên này.</p>
            <a href="{{ url('/') }}" class="mt-6 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Go to
                Home</a>
        </div>
    </div>
@endsection
