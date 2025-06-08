@extends('layouts.admin')
@section('title', 'Quản lý Văn phòng')
@section('content')
<div class="container mx-auto mt-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold">Danh sách Văn phòng</h2>
        <a href="{{ route('offices.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i> Thêm văn phòng
        </a>
    </div>
    @if(session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
    @endif
    <div class="bg-white rounded shadow">
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:60px;">#</th>
                    <th>Tên văn phòng</th>
                    <th style="width:160px;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($offices as $office)
                    <tr>
                        <td>{{ $office->id }}</td>
                        <td>{{ $office->name }}</td>
                        <td>
                            <a href="{{ route('offices.edit', $office) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Sửa
                            </a>
                            <form action="{{ route('offices.destroy', $office) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Bạn chắc chắn muốn xóa?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" type="submit">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center text-muted">Chưa có văn phòng nào.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-3">
            {{ $offices->links() }}
        </div>
    </div>
</div>
@endsection
