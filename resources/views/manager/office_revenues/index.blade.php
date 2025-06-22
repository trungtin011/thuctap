@extends('layouts.admin')

@section('title', 'Phê duyệt Doanh thu Văn phòng')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Phê duyệt Doanh thu Văn phòng</h1>
        <div class="mb-4 text-gray-600 italic">Danh sách các bản ghi doanh thu văn phòng chưa được phê duyệt từ nhân viên Kế Toán. Vui lòng xem xét chi tiết từng bản ghi.</div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if ($officeRevenues->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300 text-sm text-left text-gray-700">
                    <thead class="bg-gray-100 text-gray-800">
                        <tr>
                            <th class="border px-4 py-2">ID</th>
                            <th class="border px-4 py-2">Phòng ban</th>
                            <th class="border px-4 py-2">Ngày</th>
                            <th class="border px-4 py-2">Tên người nhập</th>
                            <th class="border px-4 py-2">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($officeRevenues as $revenue)
                            <tr>
                                <td class="border px-4 py-2">{{ $revenue->id }}</td>
                                <td class="border px-4 py-2">{{ $revenue->department->name ?? 'N/A' }}</td>
                                <td class="border px-4 py-2">{{ \Carbon\Carbon::parse($revenue->record_date)->format('d/m/Y') }}</td>
                                <td class="border px-4 py-2">{{ $revenue->submittedBy->name ?? 'N/A' }}</td>
                                <td class="border px-4 py-2 text-end">
                                    <a href="{{ route('manager.office_revenues.show', $revenue->id) }}"
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye" title="Xem bản ghi"></i> Xem
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Không có bản ghi doanh thu văn phòng nào đang chờ duyệt từ nhân viên Kế Toán.
            </div>
        @endif
    </div>
@endsection