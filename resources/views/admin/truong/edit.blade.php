@extends('layouts.admin')

@section('title', 'Chỉnh sửa trường dữ liệu')

@section('content')
<div class="p-6">
    <h2 class="text-lg font-semibold mb-4">Chỉnh sửa trường dữ liệu</h2>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.fields.update', $field->id) }}" method="POST" class="bg-white p-4 shadow rounded">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-gray-700">Tên trường</label>
            <input type="text" name="name" value="{{ old('name', $field->name) }}" class="w-full border px-4 py-2 rounded">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700">Loại dữ liệu</label>
            <select name="type" class="w-full border px-4 py-2 rounded">
                <option value="text" {{ $field->type == 'text' ? 'selected' : '' }}>Văn bản</option>
                <option value="number" {{ $field->type == 'number' ? 'selected' : '' }}>Số</option>
                <option value="email" {{ $field->type == 'email' ? 'selected' : '' }}>Email</option>
                <option value="date" {{ $field->type == 'date' ? 'selected' : '' }}>Ngày</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700">Phòng ban</label>
            <select name="department_id" class="w-full border px-4 py-2 rounded">
                <option value="">-- Chọn phòng ban --</option>
                @foreach ($departments as $department)
                    <option value="{{ $department->id }}" {{ $field->department_id == $department->id ? 'selected' : '' }}>
                        {{ $department->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Cập nhật</button>
        <a href="{{ route('admin.truong.fields.index') }}" class="ml-2 text-gray-600">Quay lại</a>
    </form>
</div>
@endsection
