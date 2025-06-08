@extends('layouts.admin')

@section('title', 'Thêm Đại lý')

@section('content')
<div class="max-w-md mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Thêm Đại lý mới</h2>
    <form method="POST" action="{{ route('admin.dai_ly.store') }}">
        @csrf
        <div class="mb-4">
            <label class="block mb-1">Tên Đại lý</label>
            <input type="text" name="ten_dai_ly" class="w-full border rounded px-3 py-2" required value="{{ old('ten_dai_ly') }}">
            @error('ten_dai_ly')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1">Email</label>
            <input type="email" name="email" class="w-full border rounded px-3 py-2" required value="{{ old('email') }}">
            @error('email')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1">Số điện thoại</label>
            <input type="text" name="so_dien_thoai" class="w-full border rounded px-3 py-2" required value="{{ old('so_dien_thoai') }}">
            @error('so_dien_thoai')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1">Địa chỉ</label>
            <textarea name="dia_chi" class="w-full border rounded px-3 py-2">{{ old('dia_chi') }}</textarea>
            @error('dia_chi')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Tạo</button>
        <a href="{{ route('admin.dai_ly.index') }}" class="ml-2 text-gray-600 hover:underline">Quay lại</a>
    </form>
</div>
@endsection