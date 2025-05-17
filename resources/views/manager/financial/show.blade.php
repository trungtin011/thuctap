@extends('layouts.navbar')

@section('content')
<div class="container mt-4">
    <h2>📄 Chi tiết bản ghi tài chính</h2>

    <div class="mb-3">
        <p><strong>📅 Ngày:</strong> {{ $financialRecord->record_date }}</p>
        <p><strong>⏰ Giờ:</strong> {{ $financialRecord->record_time }}</p>
        <p><strong>🏢 Phòng ban:</strong> {{ $financialRecord->department->name }}</p>
        <p><strong>🖥️ Nền tảng:</strong> {{ $financialRecord->platform->name }}</p>
        <p><strong>🧾 Doanh thu tổng:</strong> {{ number_format($financialRecord->revenue) }} VND</p>
        <p><strong>👤 Người gửi:</strong> {{ $financialRecord->submittedBy->name ?? 'N/A' }}</p>
        <p><strong>📝 Ghi chú:</strong> {{ $financialRecord->note }}</p>
    </div>

    @if($financialRecord->revenueDetails && count($financialRecord->revenueDetails))
    <h4>💰 Chi tiết doanh thu</h4>
    <ul>
        @foreach($financialRecord->revenueDetails as $item)
            <li>
                {{ $item->description }}: {{ number_format($item->amount) }} VND
            </li>
        @endforeach
    </ul>
    @endif
<h4>📊 Tổng quan bản ghi tài chính</h4>
<table class="table table-bordered mb-4">
    <thead class="table-light">
        <tr>
            <th>Phòng ban</th>
            <th>Nền tảng</th>
            <th>Doanh thu (VND)</th>
            <th>Tổng chi phí (VND)</th>
           
        </tr>
    </thead>
    <tbody>
        @php
            $totalExpense = $financialRecord->expenses->sum('amount');
            $roas = $totalExpense > 0 ? round(($financialRecord->revenue / $totalExpense) * 100, 2) : 'N/A';
        @endphp
        <tr>
            <td>{{ $financialRecord->department->name ?? 'N/A' }}</td>
            <td>{{ $financialRecord->platform->name ?? 'N/A' }}</td>
            <td>{{ number_format($financialRecord->revenue) }}</td>
            <td>{{ number_format($totalExpense) }}</td>
            
        </tr>
    </tbody>
</table>


    <div class="my-3">
        <form action="{{ route('manager.financial.approve', $financialRecord->id) }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-success">✔️ Phê duyệt</button>
        </form>

        <form action="{{ route('manager.financial.reject', $financialRecord->id) }}" method="POST" class="d-inline">
            @csrf
            <input type="text" name="note" class="form-control d-inline w-25" placeholder="Lý do từ chối" required>
            <button class="btn btn-danger">❌ Từ chối</button>
        </form>
    </div>

    <a href="{{ route('manager.financial.index') }}" class="btn btn-secondary">🔙 Quay lại</a>
</div>
@endsection
