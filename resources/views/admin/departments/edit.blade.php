@extends('layouts.admin')

@section('title', 'Sửa phòng ban')

@section('content')
<div class="max-w-md mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Sửa phòng ban</h2>
    <form method="POST" action="{{ route('departments.update', $department) }}">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="block mb-1">Tên phòng ban</label>
            <input type="text" name="name" class="w-full border rounded px-3 py-2" required value="{{ old('name', $department->name) }}">
            @error('name')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1">Mô tả</label>
            <textarea name="description" class="w-full border rounded px-3 py-2">{{ old('description', $department->description) }}</textarea>
            @error('description')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Cập nhật</button>
        <a href="{{ route('departments.index') }}" class="ml-2 text-gray-600 hover:underline">Quay lại</a>
    </form>
</div>
@endsection
