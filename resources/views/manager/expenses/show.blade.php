
@extends('layouts.admin')

@section('title', 'Chi tiết chi phí')

@section('content')
    <div class="container-fluid px-4 py-6">
        <div class="mb-4">
            <a href="{{ route('manager.expenses.index') }}" class="px-4 py-2 italic text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left mr-2"></i> Quay lại
            </a>
        </div>

        <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
            <i class="fas fa-file-alt text-blue-500"></i> Chi tiết chi phí #{{ $expense->id }}
        </h2>

        <div class="bg-white p-6 border border-gray-300 rounded-lg shadow-sm mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
                <div>
                    <strong><i class="fas fa-id-card text-blue-500 mr-2"></i>ID:</strong>
                    {{ $expense->id }}
                </div>
                <div>
                    <strong><i class="fas fa-building text-blue-500 mr-2"></i>Phòng ban:</strong>
                    {{ $expense->financialRecord->department->name ?? 'N/A' }}
                </div>
                <div>
                    <strong><i class="fas fa-calendar-alt text-blue-500 mr-2"></i>Ngày:</strong>
                    {{ \Carbon\Carbon::parse($expense->financialRecord->record_date)->format('d/m/Y') }}
                </div>
                <div>
                    <strong><i class="fas fa-route text-blue-500 mr-2"></i>Tuyến:</strong>
                    {{ $expense->financialRecord->route->name ?? 'N/A' }}
                </div>
                <div>
                    <strong><i class="fas fa-globe text-blue-500 mr-2"></i>Nền tảng:</strong>
                    {{ $expense->financialRecord->platform->name ?? 'N/A' }}
                </div>
                <div>
                    <strong><i class="fas fa-coins text-blue-500 mr-2"></i>Chi phí:</strong>
                    {{ number_format($expense->amount, 0, ',', '.') }} VND
                </div>
                <div>
                    <strong><i class="fas fa-list text-blue-500 mr-2"></i>Loại chi phí:</strong>
                    {{ $expense->expenseType->name ?? 'N/A' }}
                </div>
                <div>
                    <strong><i class="fas fa-user text-blue-500 mr-2"></i>Người gửi:</strong>
                    {{ $expense->financialRecord->submittedBy->name ?? 'N/A' }}
                </div>
                <div>
                    <strong><i class="fas fa-info-circle text-blue-500 mr-2"></i>Trạng thái:</strong>
                    {{ $expense->status === 'canceled' ? 'Đã hủy' : ($expense->status === 'manager_approved' ? 'Đã phê duyệt' : 'Chờ duyệt') }}
                </div>
                @if ($expense->status === 'canceled' && $expense->reject_reason)
                    <div>
                        <strong><i class="fas fa-comment-slash text-blue-500 mr-2"></i>Lý do từ chối:</strong>
                        {{ $expense->reject_reason }}
                    </div>
                @endif
                <div class="col-span-2">
                    <strong><i class="fas fa-sticky-note text-blue-500 mr-2"></i>Mô tả:</strong>
                    {{ $expense->description ?? 'N/A' }}
                </div>
            </div>
        </div>

        @if ($expense->status === 'pending')
            <div class="flex flex-col md:flex-row md:items-center gap-4">
                <!-- Nút phê duyệt -->
                <form action="{{ route('manager.expenses.approve', $expense->id) }}" method="POST" id="approve-form">
                    @csrf
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded inline-flex items-center">
                        <i class="fas fa-check-circle mr-2"></i> Phê duyệt
                    </button>
                </form>

                <!-- Nút hủy -->
                <button id="show-reject-form" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded inline-flex items-center">
                    <i class="fas fa-times-circle mr-2"></i> Hủy
                </button>

                <!-- Form hủy -->
                <form id="reject-form" action="{{ route('manager.expenses.reject', $expense->id) }}" method="POST" class="hidden flex flex-col md:flex-row gap-2">
                    @csrf
                    <input type="text" name="reject_reason" class="border border-gray-300 rounded px-3 py-2" placeholder="Lý do hủy" required>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded inline-flex items-center">
                        <i class="fas fa-times mr-2"></i> Gửi hủy
                    </button>
                </form>
            </div>
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Bản ghi này đã được {{ $expense->status === 'manager_approved' ? 'phê duyệt' : 'hủy' }}.
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const rejectBtn = document.getElementById('show-reject-form');
            const rejectForm = document.getElementById('reject-form');
            const approveForm = document.getElementById('approve-form');

            if (rejectBtn && rejectForm) {
                rejectBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    console.log('Reject button clicked, showing reject form');
                    rejectForm.classList.remove('hidden');
                    rejectBtn.classList.add('hidden');
                });
            }

            if (approveForm) {
                approveForm.addEventListener('submit', function (e) {
                    console.log('Approve form submitted to: ' + approveForm.action);
                });
            }

            if (rejectForm) {
                rejectForm.addEventListener('submit', function (e) {
                    console.log('Reject form submitted to: ' + rejectForm.action);
                });
            }
        });
    </script>
@endsection