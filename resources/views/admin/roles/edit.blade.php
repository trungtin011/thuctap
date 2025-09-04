@extends('layouts.admin')

@section('title', 'Sửa vai trò')

@section('content')
<div class="max-w-md mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Sửa vai trò</h2>
    <form method="POST" action="{{ route('roles.update', $role) }}">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="block mb-1">Tên vai trò</label>
            <input type="text" name="name" class="w-full border rounded px-3 py-2" required value="{{ old('name', $role->name) }}">
            @error('name')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1">Cấp bậc</label>
            <select name="level" class="w-full border rounded px-3 py-2" required>
                <option value="employee" @if(old('level', $role->level)=='employee') selected @endif>Nhân viên</option>
                <option value="manager" @if(old('level', $role->level)=='manager') selected @endif>Quản lý</option>
                <option value="admin" @if(old('level', $role->level)=='admin') selected @endif>Admin</option>
            </select>
            @error('level')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1">Phòng ban</label>
            <select name="department_id" class="w-full border rounded px-3 py-2" required>
                @foreach($departments as $d)
                    <option value="{{ $d->id }}" @if(old('department_id', $role->department_id) == $d->id) selected @endif>{{ $d->name }}</option>
                @endforeach
            </select>
            @error('department_id')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1">Mô tả</label>
            <textarea name="description" class="w-full border rounded px-3 py-2">{{ old('description', $role->description) }}</textarea>
            @error('description')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Cập nhật</button>
        <a href="{{ route('roles.index') }}" class="ml-2 text-gray-600 hover:underline">Quay lại</a>
    </form>
</div>
@endsection
