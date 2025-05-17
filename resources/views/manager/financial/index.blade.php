@extends('layouts.navbar')

@section('content')
<div class="container">
    <h2>Bản ghi đang chờ duyệt</h2>
    @foreach($pendingRecords as $record)
        <div class="card mt-3">
            <div class="card-body">
                <strong>Ngày:</strong> {{ $record->record_date }}<br>
                <strong>Phòng ban:</strong> {{ $record->department->name ?? 'N/A' }}<br>
                <strong>Doanh thu:</strong> {{ number_format($record->revenue) }} VND<br>
                <a href="{{ route('manager.financial.show', $record->id) }}" class="btn btn-primary mt-2">Xem chi tiết</a>
            </div>
        </div>
    @endforeach
</div>
@endsection
