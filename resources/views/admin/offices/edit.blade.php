@extends('layouts.admin')

@section('title', 'Sửa Văn phòng')

@section('content')
<div class="container mx-auto mt-6">
    <div class="bg-white rounded shadow p-6 max-w-lg mx-auto">
        <h2 class="text-xl font-bold mb-4">Sửa Văn phòng</h2>
        <form method="POST" action="{{ route('offices.update', $office) }}">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Tên văn phòng</label>
                <input type="text" name="name" id="name" class="form-control" required value="{{ old('name', $office->name) }}">
                @error('name') <div class="text-danger mt-1">{{ $message }}</div> @enderror
            </div>
            <div class="flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Cập nhật</button>
                <a href="{{ route('offices.index') }}" class="btn btn-secondary">Quay lại</a>
            </div>
        </form>
    </div>
</div>
@endsection
