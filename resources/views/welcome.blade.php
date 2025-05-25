@extends('layouts.navbar')

@section('title', 'Dashboard')

@section('content')
    <div class="max-w-4xl mx-auto">
        <h2 class="text-2xl font-bold mb-6">Chào mừng,
            @if (Auth::check())
                {{ Auth::user()->name }}!
            @endif
        </h2>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <p class="text-gray-700">Bạn đã đăng nhập thành công vào hệ thống quản lý doanh thu.</p>
            <p class="mt-4">Vai trò: {{ Auth::user()->role->name ?? 'N/A' }}</p>
            <p>Phòng ban: {{ Auth::user()->department->name ?? 'N/A' }}</p>
        </div>
    </div>
@endsection
