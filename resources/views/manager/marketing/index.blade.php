@extends('layouts.admin')

@section('title', 'Danh sách bản ghi tài chính - Kế toán')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Danh sách bản ghi tài chính - Phòng Kế toán</h1>
        <div class="mb-4 text-gray-600 italic">
            Dưới đây là danh sách các bản ghi từ phòng Kế toán. Vui lòng xem xét và xử lý từng bản ghi.
            <span class="text-red-500">Lưu ý: Dữ liệu tài chính có thể chưa được cập nhật đầy đủ.</span>
        </div>

        @if (session('success'))
            <div class="alert alert-success bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @if ($records->count() > 0)
            <div class="w-full border-collapse overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300 text-sm text-left text-gray-700">
                    <thead class="bg-gray-100 text-gray-800">
                        <tr>
                            <th class="border px-4 py-2">ID</th>
                            <th class="border px-4 py-2">Phòng ban</th>
                            <th class="border px-4 py-2">Ngày</th>
                            <th class="border px-4 py-2">Tổng số tiền</th>
                            <th class="border px-4 py-2">Chuyển khoản</th>
                            <th class="border px-4 py-2">Phí</th>
                        
                            <th class="border px-4 py-2 text-end">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($records as $record)
                            <tr class="hover:bg-gray-50">
                                <td class="border px-4 py-2">{{ $record['id'] }}</td>
                                <td class="border px-4 py-2">{{ $record['department']['name'] ?? 'N/A' }}</td>
                                <td class="border px-4 py-2">{{ \Carbon\Carbon::parse($record['record_date'])->format('d/m/Y') }}</td>
                                <td class="border px-4 py-2">{{ number_format($record['total_amount'], 2) }} VND</td>
                                <td class="border px-4 py-2">{{ number_format($record['transferred_amount'], 2) }} VND</td>
                                <td class="border px-4 py-2">{{ number_format($record['fee'], 2) }} VND</td>
                               
                                <td class="border px-4 py-2 text-end space-x-1">
                                    <a href="{{ route('manager.marketing.show', $record['id']) }}"
                                       class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <i class="fas fa-eye me-1" title="Xem"></i> Xem
                                    </a>
                                    @if (!$record['is_processed'])
                                        <form action="{{ route('manager.marketing.approve', $record['id']) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('POST')
                                            <button type="submit"
                                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                                    onclick="return confirm('Bạn có chắc muốn phê duyệt bản ghi này?')">
                                                <i class="fas fa-check-circle me-1" title="Phê duyệt"></i> Phê duyệt
                                            </button>
                                        </form>
                                        <button type="button"
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                                onclick="document.getElementById('reject-form-{{ $record['id'] }}').showModal()">
                                            <i class="fas fa-times-circle me-1" title="Từ chối"></i> Từ chối
                                        </button>
                                        <dialog id="reject-form-{{ $record['id'] }}" class="modal">
                                            <div class="modal-box">
                                                <h3 class="font-bold text-lg">Từ chối bản ghi #{{ $record['id'] }}</h3>
                                                <form action="{{ route('manager.marketing.reject', $record['id']) }}" method="POST">
                                                    @csrf
                                                    @method('POST')
                                                    <div class="py-4">
                                                        <label for="note" class="block text-sm font-medium text-gray-700">Lý do từ chối</label>
                                                        <textarea id="note" name="note" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required></textarea>
                                                    </div>
                                                    <div class="modal-action">
                                                        <button type="submit" class="btn btn-error">Xác nhận từ chối</button>
                                                        <button type="button" class="btn" onclick="document.getElementById('reject-form-{{ $record['id'] }}').close()">Hủy</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </dialog>
                                    @else
                                        <span class="text-gray-600">
                                            {{ $record['action'] === 'manager_approved' ? 'Đã phê duyệt' : 'Đã từ chối' }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
                <i class="fas fa-info-circle"></i> Không có bản ghi nào từ phòng Kế toán.
            </div>
        @endif
    </div>
@endsection