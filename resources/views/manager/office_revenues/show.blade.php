@extends('layouts.admin')

@section('title', 'Chi tiết Doanh thu Văn phòng')

@section('content')
    <div class="container-fluid px-4 py-6">
        <div class="mb-4">
            <a href="{{ route('manager.office_revenues.index') }}" class="px-4 py-2 italic text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left mr-2"></i> Quay lại
            </a>
        </div>

        <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
            <i class="fas fa-file-alt text-blue-500"></i> Chi tiết Doanh thu Văn phòng #{{ $officeRevenue->id }}
        </h2>

        <div class="bg-white p-6 border border-gray-300 rounded-lg shadow-sm mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
                <div>
                    <strong><i class="fas fa-id-card text-blue-500 mr-2"></i>ID:</strong>
                    {{ $officeRevenue->id }}
                </div>
                <div>
                    <strong><i class="fas fa-building text-blue-500 mr-2"></i>Phòng ban:</strong>
                    {{ $officeRevenue->department->name ?? 'N/A' }}
                </div>
                <div>
                    <strong><i class="fas fa-calendar-alt text-blue-500 mr-2"></i>Ngày ghi nhận:</strong>
                    {{ \Carbon\Carbon::parse($officeRevenue->record_date)->format('d/m/Y') }}
                </div>
                <div>
                    <strong><i class="fas fa-user text-blue-500 mr-2"></i>Người nhập:</strong>
                    {{ $officeRevenue->submittedBy->name ?? 'N/A' }}
                </div>
                <div>
                    <strong><i class="fas fa-coins text-blue-500 mr-2"></i>Tiền mặt:</strong>
                    {{ number_format($officeRevenue->cash, 0, ',', '.') }} VND
                </div>
                <div>
                    <strong><i class="fas fa-money-check-alt text-blue-500 mr-2"></i>Chuyển khoản:</strong>
                    {{ number_format($officeRevenue->bank_transfer, 0, ',', '.') }} VND
                </div>
                <div>
                    <strong><i class="fas fa-hand-holding-usd text-blue-500 mr-2"></i>Chi phí:</strong>
                    {{ number_format($officeRevenue->expense, 0, ',', '.') }} VND
                </div>
                <div>
                    <strong><i class="fas fa-wallet text-blue-500 mr-2"></i>Tổng:</strong>
                    {{ number_format($officeRevenue->total, 0, ',', '.') }} VND
                </div>
                <div>
                    <strong><i class="fas fa-info-circle text-blue-500 mr-2"></i>Trạng thái:</strong>
                    {{ $officeRevenue->status === 'rejected' ? 'Đã từ chối' : ($officeRevenue->status === 'approved' ? 'Đã phê duyệt' : 'Chờ duyệt') }}
                </div>
                @if ($officeRevenue->status === 'rejected' && $officeRevenue->reject_reason)
                    <div>
                        <strong><i class="fas fa-comment-slash text-blue-500 mr-2"></i>Lý do từ chối:</strong>
                        {{ $officeRevenue->reject_reason }}
                    </div>
                @endif
                <div class="col-span-2">
                    <strong><i class="fas fa-building-columns text-blue-500 mr-2"></i>Văn phòng:</strong>
                    <ul class="list-disc pl-5">
                        @foreach ($officeRevenue->offices as $office)
                            <li>{{ $office->name }}: {{ number_format($office->pivot->value, 0, ',', '.') }} VND</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        @if ($officeRevenue->status === 'pending')
            <div class="flex flex-col md:flex-row md:items-center gap-4">
                <!-- Nút phê duyệt -->
                <form action="{{ route('manager.office_revenues.approve', $officeRevenue->id) }}" method="POST"
                    id="approve-form">
                    @csrf
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded inline-flex items-center">
                        <i class="fas fa-check-circle mr-2"></i> Phê duyệt
                    </button>
                </form>

                <!-- Nút từ chối -->
                <button id="show-reject-form"
                    class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded inline-flex items-center">
                    <i class="fas fa-times-circle mr-2"></i> Từ chối
                </button>

                <!-- Form từ chối -->
                <form id="reject-form" action="{{ route('manager.office_revenues.reject', $officeRevenue->id) }}"
                    method="POST" class="hidden flex flex-col md:flex-row gap-2">
                    @csrf
                    <input type="text" name="reject_reason" class="border border-gray-300 rounded px-3 py-2"
                        placeholder="Lý do từ chối" required>
                    <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded inline-flex items-center">
                        <i class="fas fa-times mr-2"></i> Gửi từ chối
                    </button>
                </form>
            </div>
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Bản ghi này đã được
                {{ $officeRevenue->status === 'approved' ? 'phê duyệt' : 'từ chối' }}.
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rejectBtn = document.getElementById('show-reject-form');
            const rejectForm = document.getElementById('reject-form');
            const approveForm = document.getElementById('approve-form');

            if (rejectBtn && rejectForm) {
                rejectBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Reject button clicked, showing reject form');
                    rejectForm.classList.remove('hidden');
                    rejectBtn.classList.add('hidden');
                });
            }

            if (approveForm) {
                approveForm.addEventListener('submit', function(e) {
                    console.log('Approve form submitted to: ' + approveForm.action);
                });
            }

            if (rejectForm) {
                rejectForm.addEventListener('submit', function(e) {
                    console.log('Reject form submitted to: ' + rejectForm.action);
                });
            }
        });
    </script>
@endsection
