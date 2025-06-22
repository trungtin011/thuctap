
@extends('layouts.admin')

@section('title', 'Chi tiết bản ghi tài chính')

@section('content')
    <div class="container-fluid px-4 py-6">
        <div class="mb-4">
            <a href="{{ route('manager.financial.index') }}" class="px-4 py-2 italic text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left mr-2"></i>Quay lại
            </a>
        </div>

        <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
            <i class="fas fa-file-alt text-blue-500"></i> Chi tiết bản ghi tài chính #{{ $financialRecord->id }}
        </h2>

        <div class="bg-white p-6 border border-gray-300 rounded-lg shadow-sm mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
                <div>
                    <strong><i class="fas fa-id-card text-blue-500 mr-2"></i>ID:</strong>
                    {{ $financialRecord->id }}
                </div>
                <div>
                    <strong><i class="fas fa-building text-blue-500 mr-2"></i>Phòng ban:</strong>
                    {{ $financialRecord->department->name ?? 'N/A' }}
                </div>
                <div>
                    <strong><i class="fas fa-calendar-alt text-blue-500 mr-2"></i>Ngày:</strong>
                    {{ \Carbon\Carbon::parse($financialRecord->record_date)->format('d/m/Y') }} {{ $financialRecord->record_time }}
                </div>
                <div>
                    <strong><i class="fas fa-route text-blue-500 mr-2"></i>Tuyến:</strong>
                    {{ $financialRecord->route->name ?? 'N/A' }}
                </div>
                <div>
                    <strong><i class="fas fa-user-tie text-blue-500 mr-2"></i>Đại lý:</strong>
                    {{ $financialRecord->daiLy->ten_dai_ly ?? 'N/A' }}
                </div>
                <div>
                    <strong><i class="fas fa-coins text-blue-500 mr-2"></i>Doanh thu:</strong>
                    {{ number_format($financialRecord->revenue, 0, ',', '.') }} VND
                </div>
                <div>
                    <strong><i class="fas fa-hand-holding-usd text-blue-500 mr-2"></i>Hoa hồng:</strong>
                    {{ number_format($financialRecord->commission, 0, ',', '.') }} VND
                </div>
                <div>
                    <strong><i class="fas fa-user text-blue-500 mr-2"></i>Người gửi:</strong>
                    {{ $financialRecord->submittedBy->name ?? 'N/A' }}
                </div>
                <div class="col-span-2">
                    <strong><i class="fas fa-sticky-note text-blue-500 mr-2"></i>Ghi chú:</strong>
                    {{ json_decode($financialRecord->note, true)['note'] ?? 'Không có ghi chú' }}
                </div>
            </div>
        </div>

        @php
            $revenueSources = json_decode($financialRecord->note, true)['revenue_sources'] ?? [];
        @endphp
        @if (!empty($revenueSources))
            <h4 class="text-xl font-semibold mb-4 flex items-center gap-2">
                <i class="fas fa-list-ul text-green-600"></i> Chi tiết nguồn doanh thu
            </h4>
            <div class="overflow-x-auto mb-6">
                <table class="w-full border border-gray-300 text-sm text-left text-gray-700">
                    <thead class="bg-gray-100 text-gray-800">
                        <tr>
                            <th class="border px-4 py-2">Nguồn</th>
                            <th class="border px-4 py-2">Doanh thu</th>
                            <th class="border px-4 py-2">Hoa hồng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($revenueSources as $source)
                            <tr>
                                <td class="border px-4 py-2">{{ $source['source_name'] ?? 'N/A' }}</td>
                                <td class="border px-4 py-2">{{ number_format($source['amount'], 0, ',', '.') }} VND</td>
                                <td class="border px-4 py-2">{{ number_format($source['commission'], 0, ',', '.') }} VND</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if ($financialRecord->status === 'pending')
            <div class="flex flex-col md:flex-row md:items-center gap-4">
                <!-- Nút phê duyệt -->
                <form action="{{ route('manager.financial.approve', $financialRecord->id) }}" method="POST">
                    @csrf
                    <button class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded inline-flex items-center">
                        <i class="fas fa-check-circle mr-2"></i> Phê duyệt
                    </button>
                </form>

                <!-- Nút từ chối -->
                <button id="show-reject-form" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded inline-flex items-center">
                    <i class="fas fa-times-circle mr-2"></i> Từ chối
                </button>

                <!-- Form từ chối -->
                <form id="reject-form" action="{{ route('manager.financial.reject', $financialRecord->id) }}" method="POST" class="hidden flex flex-col md:flex-row gap-2">
                    @csrf
                    <input type="text" name="note" class="border border-gray-300 rounded px-3 py-2" placeholder="Lý do từ chối" required>
                    <button class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded inline-flex items-center">
                        <i class="fas fa-times mr-2"></i> Gửi từ chối
                    </button>
                </form>
            </div>
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Bản ghi này đã được {{ $financialRecord->status === 'manager_approved' ? 'phê duyệt' : 'từ chối' }}.
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const rejectBtn = document.getElementById('show-reject-form');
            const rejectForm = document.getElementById('reject-form');

            if (rejectBtn && rejectForm) {
                rejectBtn.addEventListener('click', function () {
                    rejectForm.classList.remove('hidden');
                    rejectBtn.classList.add('hidden');
                });
            }
        });
    </script>
@endsection
