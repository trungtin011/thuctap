@extends('layouts.admin')

@section('title', 'Chi tiết bản ghi tài chính')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Chi tiết bản ghi tài chính #{{ $financialRecord['id'] }}</h1>
        <div class="card mt-4">
            <div class="card-body">
                <p><strong>Phòng ban:</strong> {{ $financialRecord['department']['name'] ?? 'N/A' }}</p>
                <p><strong>Ngày:</strong> {{ \Carbon\Carbon::parse($financialRecord['record_date'])->format('d/m/Y') }}</p>
                <p><strong>Tổng số tiền:</strong> {{ number_format($financialRecord['total_amount'], 2) }} VND</p>
                <p><strong>Chuyển khoản:</strong> {{ number_format($financialRecord['transferred_amount'], 2) }} VND</p>
                <p><strong>Phí:</strong> {{ number_format($financialRecord['fee'], 2) }} VND</p>
          
                @if ($financialRecord['note'])
                    <p><strong>Ghi chú:</strong> {{ $financialRecord['note'] }}</p>
                @endif
                <a href="{{ route('manager.marketing.index') }}" class="btn btn-primary mt-4">Quay lại danh sách</a>
            </div>
        </div>
    </div>
@endsection