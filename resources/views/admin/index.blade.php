@extends('layouts.admin')

@section('title', 'Quản lý doanh thu')

@section('content')
    <div class="max-w-4xl mx-auto">
        {{-- Thống kê nhanh --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded shadow p-4 text-center">
                <div class="text-3xl font-bold text-indigo-700">120</div>
                <div class="text-gray-600">Tổng nhân viên</div>
            </div>
            <div class="bg-white rounded shadow p-4 text-center">
                <div class="text-3xl font-bold text-green-600">8</div>
                <div class="text-gray-600">Phòng ban</div>
            </div>
            <div class="bg-white rounded shadow p-4 text-center">
                <div class="text-3xl font-bold text-blue-600">1,200,000,000₫</div>
                <div class="text-gray-600">Tổng doanh thu</div>
            </div>
        </div>
        <h2 class="text-2xl font-bold mb-6">Quản lý doanh thu</h2>
        <div class="bg-white p-6 rounded-lg shadow-md">
            @php
                $user = Auth::user();
                $role = $user->role;
                $department = $user->department;
            @endphp
            @if ($role && $role->level === 'admin')
                <div class="mb-6">
                    <h3 class="font-semibold mb-2">Menu quản trị</h3>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('users.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Quản lý người dùng</a>
                        {{-- Thêm các menu khác nếu có --}}
                    </div>
                </div>
                <p class="text-gray-700 font-semibold">
                    Bạn đang đăng nhập với quyền 
                    <span class="text-blue-600">Admin</span>
                    (Tên vai trò: <span class="text-indigo-700">{{ $role->name }}</span>,
                    Phòng ban: <span class="text-indigo-700">{{ $department->name ?? 'Chưa có' }}</span>).
                    Bạn có thể quản lý toàn bộ hệ thống, phân quyền, phòng ban, nhân viên, doanh thu, chi phí...
                </p>
            @elseif ($role && $role->level === 'manager')
                <p class="text-gray-700 font-semibold">
                    Bạn đang đăng nhập với quyền 
                    <span class="text-green-600">Quản lý</span>
                    (Tên vai trò: <span class="text-indigo-700">{{ $role->name }}</span>,
                    Phòng ban: <span class="text-indigo-700">{{ $department->name ?? 'Chưa có' }}</span>).
                    Bạn có thể duyệt và quản lý doanh thu, chi phí của phòng ban mình.
                </p>
            @else
                <p class="text-gray-700">Bạn không có quyền truy cập trang này.</p>
            @endif
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        // Add any JavaScript code here if needed
    </script>
@endsection