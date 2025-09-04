@extends('layouts.admin')
@section('title', 'Nhập liệu tự động từ Excel')
@section('content')
    <div class="container mt-4">
        <h2>Nhập liệu tự động từ Excel</h2>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <form method="POST" action="{{ route('import.excel') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label>Chọn loại dữ liệu:</label>
                <select name="type" class="form-control" required>
                    <option value="financial_records">Doanh thu (financial_records)</option>
                    <option value="office_revenues">Phòng hàng (office_revenues)</option>
                    <option value="trips_passengers">Tuyến đường (trips_passengers)</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Chọn file Excel:</label>
                <input type="file" name="file" class="form-control" required accept=".xlsx,.xls">
            </div>
            <button type="submit" class="btn btn-primary">Nhập dữ liệu</button>
        </form>
    </div>
@endsection
