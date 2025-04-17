@extends('layouts.navigation_admin')

@section('content')
    <div class="max-w-2xl mx-auto mt-10 bg-white shadow-md rounded-lg p-6">
        <h1 class="text-2xl font-bold mb-4">Thông tin người dùng</h1>

        <div class="mb-4">
            <label class="block font-semibold text-gray-700">Tên người dùng:</label>
            <p class="text-gray-900">{{ $user->name }}</p>
        </div>

        <div class="mb-4">
            <label class="block font-semibold text-gray-700">Email:</label>
            <p class="text-gray-900">{{ $user->email }}</p>
        </div>

        <div class="mb-4">
            <label class="block font-semibold text-gray-700">Số dư:</label>
            <p class="text-gray-900">
                {{ number_format($user->balance, 0, ',', '.') }} VNĐ
            </p>
        </div>

        <div class="mb-4">
            <label class="block font-semibold text-gray-700">Ngày tạo:</label>
            <p class="text-gray-900">{{ $user->created_at }}</p>
        </div>

        <div class="flex justify-between mt-6">
            <a href="{{ route('admin.users.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Quay lại
            </a>

            <a href="{{ route('admin.users.edit', $user->id) }}"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Sửa thông tin
            </a>
        </div>
    </div>
@endsection
