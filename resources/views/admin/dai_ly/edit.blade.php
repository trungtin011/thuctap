@extends('layouts.admin')

@section('title', 'Sửa Đại lý')

@section('content')
<div class="flex items-center mt-4" style="background-color: #f1f1f1; padding: 20px 16px;">
    <h2 class="text-md underline">Sửa Đại lý</h2>
</div>
<div class="p-4 mx-auto max-w-3xl">
    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" role="alert">
            {{ session('success') }}
        </div>
    @endif
    <div class="relative shadow-md sm:rounded-lg bg-white">
        <form action="{{ route('admin.dai_ly.update', $daiLy->id) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="ten_dai_ly" class="block text-sm font-medium text-gray-700 mb-1">Tên Đại lý</label>
                <input type="text" id="ten_dai_ly" name="ten_dai_ly" class="w-full border border-gray-300 rounded px-4 py-2 text-gray-900" required value="{{ old('ten_dai_ly', $daiLy->ten_dai_ly) }}">
                @error('ten_dai_ly')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="email" name="email" class="w-full border border-gray-300 rounded px-4 py-2 text-gray-900" required value="{{ old('email', $daiLy->email) }}">
                @error('email')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="mb-4">
                <label for="so_dien_thoai" class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                <input type="text" id="so_dien_thoai" name="so_dien_thoai" class="w-full border border-gray-300 rounded px-4 py-2 text-gray-900" required value="{{ old('so_dien_thoai', $daiLy->so_dien_thoai) }}">
                @error('so_dien_thoai')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="mb-4">
                <label for="dia_chi" class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
                <textarea id="dia_chi" name="dia_chi" class="w-full border border-gray-300 rounded px-4 py-2 text-gray-900">{{ old('dia_chi', $daiLy->dia_chi) }}</textarea>
                @error('dia_chi')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="flex justify-start">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-300 ease-in-out">
                    <i class="fas fa-save mr-2"></i> Cập nhật
                </button>
                <a href="{{ route('admin.dai_ly.index') }}" class="ml-4 inline-block bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition duration-300 ease-in-out">
                    <i class="fas fa-arrow-left mr-2"></i> Quay lại
                </a>
            </div>
        </form>
    </div>
</div>
@endsection