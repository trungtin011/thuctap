@extends('layouts.admin')

@section('title', 'Sửa người dùng')

@section('content')
<div class="max-w-md mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Sửa người dùng</h2>
    <form method="POST" action="{{ route('users.update', $user) }}">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="block mb-1">Tên</label>
            <input type="text" name="name" class="w-full border rounded px-3 py-2" required value="{{ old('name', $user->name) }}">
            @error('name')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1">Email</label>
            <input type="email" name="email" class="w-full border rounded px-3 py-2" required value="{{ old('email', $user->email) }}">
            @error('email')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1">Phòng ban</label>
            <select name="department_id" class="w-full border rounded px-3 py-2" required>
                @foreach($departments as $d)
                    <option value="{{ $d->id }}" @if(old('department_id', $user->department_id) == $d->id) selected @endif>{{ $d->name }}</option>
                @endforeach
            </select>
            @error('department_id')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1">Vai trò</label>
            <select name="role_id" class="w-full border rounded px-3 py-2" required>
                @foreach($roles as $r)
                    <option value="{{ $r->id }}" @if(old('role_id', $user->role_id) == $r->id) selected @endif>{{ $r->name }}</option>
                @endforeach
            </select>
            @error('role_id')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1">Mật khẩu mới (bỏ qua nếu không đổi)</label>
            <input type="password" name="password" class="w-full border rounded px-3 py-2">
            @error('password')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1">Xác nhận mật khẩu mới</label>
            <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Cập nhật</button>
        <a href="{{ route('users.index') }}" class="ml-2 text-gray-600 hover:underline">Quay lại</a>
    </form>
</div>
@endsection
