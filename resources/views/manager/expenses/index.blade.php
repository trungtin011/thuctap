
@extends('layouts.admin')

@section('title', 'Phê duyệt chi phí')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Phê duyệt chi phí</h1>
        <div class="mb-4 text-gray-600 italic">Danh sách các bản ghi chi phí chưa được phê duyệt từ nhân viên Marketing. Vui lòng xem xét chi tiết từng bản ghi.</div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if ($expenses->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300 text-sm text-left text-gray-700">
                    <thead class="bg-gray-100 text-gray-800">
                        <tr>
                            <th class="border px-4 py-2">ID</th>
                            <th class="border px-4 py-2">Số tiền</th>
                            <th class="border px-4 py-2">Mô tả</th>
                            <th class="border px-4 py-2">Loại chi phí</th>
                            <th class="border px-4 py-2">Người gửi</th>
                            <th class="border px-4 py-2">Trạng thái</th>
                            <th class="border px-4 py-2">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($expenses as $expense)
                            <tr>
                                <td class="border px-4 py-2">{{ $expense->id }}</td>
                                <td class="border px-4 py-2">{{ number_format($expense->amount, 0, ',', '.') }} VND</td>
                                <td class="border px-4 py-2">{{ $expense->description ?? 'N/A' }}</td>
                                <td class="border px-4 py-2">{{ $expense->expenseType->name ?? 'N/A' }}</td>
                                <td class="border px-4 py-2">{{ $expense->financialRecord->submittedBy->name ?? 'N/A' }}</td>
                                <td class="border px-4 py-2">{{ $expense->status }}</td>
                                
                                <td class="border px-4 py-2 text-end">
                                    <a href="{{ route('manager.expenses.show', $expense->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye" title="Xem chi tiết"></i> Xem
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Không có bản ghi chi phí nào đang chờ duyệt từ nhân viên Marketing.
            </div>
        @endif
    </div>
@endsection
