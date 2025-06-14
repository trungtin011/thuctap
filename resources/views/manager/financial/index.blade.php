@extends('layouts.admin')

@section('title', 'Bản ghi đang chờ duyệt')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Bản ghi đang chờ duyệt</h1>
        <div class="mb-4 text-gray-600 italic">Dưới đây là danh sách các bản ghi chưa được phê duyệt của nhân viên Kinh Doanh. Vui lòng xem xét và duyệt từng bản ghi.</div>

        @if ($pendingRecords->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300 text-sm text-left text-gray-700">
                    <thead class="bg-gray-100 text-gray-800">
                        <tr>
                            <th class="border px-4 py-2">ID</th>
                            <th class="border px-4 py-2">Phòng ban</th>
                            <th class="border px-4 py-2">Ngày</th>
                            <th class="border px-4 py-2">Tuyến</th>
                            <th class="border px-4 py-2">Doanh thu</th>
                            <th class="border px-4 py-2">Hoa hồng</th>
                            <th class="border px-4 py-2">Tuyến</th> <!-- Thay "Nguồn doanh thu" bằng "Tuyến" -->
                            <th class="border px-4 py-2">Người gửi</th>
                            <th class="border px-4 py-2">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pendingRecords as $record)
                            <tr>
                                <td class="border px-4 py-2">{{ $record->id }}</td>
                                <td class="border px-4 py-2">{{ $record->department->name ?? 'N/A' }}</td>
                                <td class="border px-4 py-2">{{ \Carbon\Carbon::parse($record->record_date)->format('d/m/Y') }}</td>
                                <td class="border px-4 py-2">{{ $record->route ? $record->route->name : 'N/A' }}</td>
                                <td class="border px-4 py-2">{{ number_format($record->revenue) }} VND</td>
                                <td class="border px-4 py-2">{{ number_format($record->commission) }} VND</td>
                                <td class="border px-4 py-2">{{ $record->route ? $record->route->name : 'N/A' }}</td> <!-- Lặp lại tuyến -->
                                <td class="border px-4 py-2">{{ $record->submittedBy->name ?? 'N/A' }}</td>
                                <td class="border px-4 py-2 text-end">
                                    <a href="{{ route('manager.financial.show', $record->id) }}"
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye" title="Xem bản ghi"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Không có bản ghi nào đang chờ duyệt từ nhân viên Kinh Doanh.
            </div>
        @endif
    </div>
@endsection