@extends('layouts.admin')

@section('title', 'Chi tiết bản ghi tài chính')
@section('content')
    <div class="" style="background-color: #d8d8d8; padding: 20px 16px;">
        <a href="{{ route('manager.financial.index') }}" class="px-4 py-2 italic text-dark-600 hover:text-gray-600">
            <i class="fas fa-arrow-left mr-2"></i>Quay lại
        </a>
    </div>
    <div class="mx-auto px-4 py-6">

        <h2 class="text-3xl font-bold mb-6 flex items-center gap-2">
            Chi tiết bản ghi tài chính
        </h2>

        <div class="space-y-3 text-gray-700 mb-6 p-4 border border-gray-300 rounded-lg bg-gray-50 flex flex-col">
            <div class="m-0">
                <strong><i class="fas fa-calendar-alt text-blue-500 mr-2"></i>Ngày:</strong>
                {{ $financialRecord->record_date }}
            </div>
            <div class="">
                <strong><i class="fas fa-clock text-blue-500 mr-2"></i>Giờ:</strong>
                {{ \Carbon\Carbon::parse($financialRecord->record_time)->format('h:i a') }}
            </div>
            <div class="">
                <strong><i class="fas fa-building text-blue-500 mr-2"></i>Phòng ban:</strong>
                {{ $financialRecord->department->name }}
            </div>
            <div class="">
                <strong><i class="fas fa-desktop text-blue-500 mr-2"></i>Nền tảng:</strong>
                {{ $financialRecord->platform->name }}
            </div>
            <div class="">
                <strong><i class="fas fa-coins text-blue-500 mr-2"></i>Doanh thu tổng:</strong>
                {{ number_format($financialRecord->revenue) }} VND
            </div>
            <div class="">
                <strong><i class="fas fa-user text-blue-500 mr-2"></i>Người gửi:</strong>
                {{ $financialRecord->submittedBy->name ?? 'N/A' }}
            </div>
            <div class="">
                <strong><i class="fas fa-pen text-blue-500 mr-2"></i>Ghi chú:</strong> {{ $financialRecord->note }}
            </div>
        </div>

        @if ($financialRecord->revenueDetails && count($financialRecord->revenueDetails))
            <h4 class="text-xl font-semibold mb-2 flex items-center gap-2">
                <i class="fas fa-list-ul text-green-600"></i> Chi tiết doanh thu
            </h4>
            <ul class="list-disc pl-6 text-gray-800 mb-6">
                @foreach ($financialRecord->revenueDetails as $item)
                    <li>
                        {{ $item->description }}: {{ number_format($item->amount) }} VND
                    </li>
                @endforeach
            </ul>
        @endif

        <h4 class="text-xl font-semibold mb-2 flex items-center gap-2">
            Tổng quan bản ghi tài chính
        </h4>
        <div class="overflow-x-auto mb-6">
            <table class="w-full border border-gray-300 text-sm text-left text-gray-700">
                <thead class="bg-gray-100 text-gray-800">
                    <tr>
                        <th class="border px-4 py-2"><i class="fas fa-building mr-1"></i>Phòng ban</th>
                        <th class="border px-4 py-2"><i class="fas fa-desktop mr-1"></i>Nền tảng</th>
                        <th class="border px-4 py-2"><i class="fas fa-money-bill-wave mr-1"></i>Doanh thu</th>
                        <th class="border px-4 py-2"><i class="fas fa-hand-holding-usd mr-1"></i>Tổng chi phí</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalExpense = $financialRecord->expenses->sum('amount');
                        $roas = $totalExpense > 0 ? round(($financialRecord->revenue / $totalExpense) * 100, 2) : 'N/A';
                    @endphp
                    <tr>
                        <td class="border px-4 py-2">{{ $financialRecord->department->name ?? 'N/A' }}</td>
                        <td class="border px-4 py-2">{{ $financialRecord->platform->name ?? 'N/A' }}</td>
                        <td class="border px-4 py-2">{{ number_format($financialRecord->revenue) }}</td>
                        <td class="border px-4 py-2">{{ number_format($totalExpense) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex md:items-center gap-4 mb-6">
            {{-- Nút phê duyệt --}}
            <form action="{{ route('manager.financial.approve', $financialRecord->id) }}" method="POST"
                class="flex items-center gap-2">
                @csrf
                <button
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded inline-flex items-center w-fit">
                    <i class="fas fa-check-circle mr-2"></i>Phê duyệt
                </button>
            </form>

            {{-- Nút từ chối + ô nhập lý do (ẩn/hiện bằng JS) --}}
            <div id="reject-section" class="flex gap-2 md:w-auto">
                <button id="show-reject-form"
                    class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded inline-flex items-center w-fit">
                    <i class="fas fa-times-circle mr-2"></i>Từ chối
                </button>

                <form id="reject-form" action="{{ route('manager.financial.reject', $financialRecord->id) }}"
                    method="POST" class="hidden md:flex md:flex-row gap-2 items-start md:items-center">
                    @csrf
                    <input type="text" name="note" class="border border-gray-300 rounded px-3 py-2 m-0 md:w-64"
                        placeholder="Lý do từ chối" required>
                    <button
                        class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded inline-flex items-center">
                        <i class="fas fa-times mr-2"></i>Gửi từ chối
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rejectBtn = document.getElementById('show-reject-form');
            const rejectForm = document.getElementById('reject-form');

            rejectBtn.addEventListener('click', function() {
                rejectForm.classList.remove('hidden');
                rejectBtn.classList.add('hidden');
            });
        });
    </script>
@endsection
