@extends('layouts.admin')

@section('title', 'Thêm người dùng')

@section('content')
<div class="max-w-md mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Thêm người dùng mới</h2>
    <form method="POST" action="{{ route('users.store') }}">
        @csrf
        <div class="mb-4">
            <label class="block mb-1">Tên</label>
            <input type="text" name="name" class="w-full border rounded px-3 py-2" required value="{{ old('name') }}">
            @error('name')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1">Email</label>
            <input type="email" name="email" class="w-full border rounded px-3 py-2" required value="{{ old('email') }}">
            @error('email')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1">Phòng ban</label>
            <select name="department_id" class="w-full border rounded px-3 py-2" required>
                <option value="">-- Chọn phòng ban --</option>
                @foreach($departments as $d)
                    <option value="{{ $d->id }}" @if(old('department_id') == $d->id) selected @endif>{{ $d->name }}</option>
                @endforeach
            </select>
            @error('department_id')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1">Vai trò</label>
            <select name="role_id" class="w-full border rounded px-3 py-2" required>
                <option value="">-- Chọn vai trò --</option>
                @foreach($roles as $r)
                    <option value="{{ $r->id }}" @if(old('role_id') == $r->id) selected @endif>{{ $r->name }}</option>
                @endforeach
            </select>
            @error('role_id')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1">Mật khẩu</label>
            <input type="password" name="password" class="w-full border rounded px-3 py-2" required>
            @error('password')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1">Xác nhận mật khẩu</label>
            <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2" required>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Tạo</button>
        <a href="{{ route('users.index') }}" class="ml-2 text-gray-600 hover:underline">Quay lại</a>
    </form>
</div>
@endsection
