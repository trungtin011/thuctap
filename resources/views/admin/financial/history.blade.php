@extends('layouts.admin')

@section('title', 'Lịch sử đơn đã được admin phê duyệt')

@section('content')
<div class="container mt-4">
    <h2>Lịch sử đơn đã được admin phê duyệt</h2>

    @if($financialRecords->isEmpty())
        <p>Không có đơn nào đã được phê duyệt.</p>
    @else
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Phòng ban</th>
                <th>Nền tảng</th>
                <th>Doanh thu</th>
                <th>Trạng thái</th>
                <th>Ngày phê duyệt</th>
            </tr>
        </thead>
        <tbody>
            @foreach($financialRecords as $record)
                <tr>
                    <td>{{ $record->id }}</td>
                    <td>{{ $record->department->name ?? 'N/A' }}</td>
                    <td>{{ $record->platform->name ?? 'N/A' }}</td>
                    <td>{{ number_format($record->revenue) }} VND</td>
                    <td><span class="badge bg-success">Admin đã duyệt</span></td>
                    <td>{{ $record->updated_at->format('d/m/Y H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection
