@extends('layouts.admin')

@section('title', 'Lịch sử đơn đã được admin phê duyệt')

@section('content')
<div style="max-width:1000px;margin:0 auto;">
    <h2 style="margin: 24px 0 18px 0; text-align:center;">Lịch sử đơn đã được admin phê duyệt</h2>

    @php
        $total = $financialRecords->sum('revenue');
    @endphp

    <div style="margin-bottom: 18px; text-align:center;">
        <span style="font-size: 1.15em; font-weight: bold; color: #28a745;">
            Tổng doanh thu đã duyệt: {{ number_format($total) }} VNĐ
        </span>
    </div>

    @if($financialRecords->isEmpty())
        <div style="color:#888;text-align:center;margin:32px 0;">Không có đơn nào đã được phê duyệt.</div>
    @else
    <div style="overflow-x:auto;">
    <table style="width:100%;margin-bottom:24px;border-collapse:collapse;background:#fff;box-shadow:0 2px 8px #eee;">
        <thead>
            <tr style="background:#f0f0f0;">
                <th style="padding:10px 12px;">ID</th>
                <th style="padding:10px 12px;">Phòng ban</th>
                <th style="padding:10px 12px;">Nền tảng</th>
                <th style="padding:10px 12px;">Doanh thu</th>
                <th style="padding:10px 12px;">Trạng thái</th>
                <th style="padding:10px 12px;">Ngày phê duyệt</th>
            </tr>
        </thead>
        <tbody>
            @foreach($financialRecords as $record)
                <tr>
                    <td style="padding:8px 12px;">{{ $record->id }}</td>
                    <td style="padding:8px 12px;">{{ $record->department->name ?? 'N/A' }}</td>
                    <td style="padding:8px 12px;">{{ $record->platform->name ?? 'N/A' }}</td>
                    <td style="padding:8px 12px;">{{ number_format($record->revenue) }} VNĐ</td>
                    <td style="padding:8px 12px;">
                        <span style="padding:4px 10px;border-radius:12px;font-size:0.95em;color:#fff;background:#28a745;">
                            Admin đã duyệt
                        </span>
                    </td>
                    <td style="padding:8px 12px;">{{ $record->updated_at ? $record->updated_at->format('d/m/Y H:i') : '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    @endif
</div>
@endsection
