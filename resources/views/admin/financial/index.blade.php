@extends('layouts.admin')

@section('title', 'Quản lý tài chính')

@section('content')
<div class="flex items-center mt-4" style="background-color: #f1f1f1; padding: 20px 16px;">
    <h2 class="text-md underline">
        Danh sách bản ghi tài chính
    </h2>
</div>
<div class="p-4 mx-auto">
    <!-- Success/Error Messages -->
    @if (session('success'))
    <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger mb-4">{{ session('error') }}</div>
    @endif

    <!-- Filter Form -->
    <div class="flex justify-between mb-4">
        <div class="flex items-center">
            <form method="GET" action="{{ route('admin.financial.index') }}" class="flex items-center">
                <div class="flex flex-wrap gap-4">
                    <div>
                        <select name="department_id" id="department_id" class="border border-gray-300 px-4 py-2">
                            <option value="">Tất cả phòng ban</option>
                            @foreach ($departments as $department)
                            <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="platform_id" id="platform_id" class="border border-gray-300 px-4 py-2">
                            <option value="">Tất cả nền tảng</option>
                            @foreach ($platforms as $platform)
                            <option value="{{ $platform->id }}" {{ request('platform_id') == $platform->id ? 'selected' : '' }}>
                                {{ $platform->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <input type="date" name="start_date" id="start_date" class="border border-gray-300 px-4 py-2" value="{{ request('start_date') }}">
                    </div>
                    <div>
                        <input type="date" name="end_date" id="end_date" class="border border-gray-300 px-4 py-2" value="{{ request('end_date') }}">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 hover:bg-blue-700 transition duration-300 ease-in-out">
                            <i class="fas fa-search mr-2"></i> Lọc
                        </button>
                        <a href="{{ route('admin.financial.index') }}" class="bg-gray-500 text-white px-4 py-2 hover:bg-gray-600 transition duration-300 ease-in-out">
                            Xóa bộ lọc
                        </a>
                    </div>
                </div>
            </form>
        </div>
        <div>
            <a href="{{ route('admin.financial.history') }}" class="bg-green-600 text-white px-4 py-2 hover:bg-green-700 transition duration-300 ease-in-out">Lịch sử</a>
        </div>
    </div>

    <!-- Records Table -->
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-900">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 bg-white">ID</th>
                    <th scope="col" class="px-6 py-3 bg-white">Loại</th>
                    <th scope="col" class="px-6 py-3 bg-white">Phòng ban</th>
                    <th scope="col" class="px-6 py-3 bg-white">Nền tảng</th>
                    <th scope="col" class="px-6 py-3 bg-white">Ngày</th>
                    <th scope="col" class="px-6 py-3 bg-white">Người gửi</th>
                    <th scope="col" class="px-6 py-3 bg-white">Số tiền</th>
                    <th scope="col" class="px-6 py-3 bg-white">Hoa hồng</th>
                    <th scope="col" class="px-6 py-3 bg-white">Mô tả</th>
                    <th scope="col" class="px-6 py-3 bg-white">Trạng thái</th>
                    <th scope="col" class="px-6 py-3 bg-white">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($records as $record)
                <tr class="border-b border-gray-200 bg-white">
                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $record['id'] ?? 'N/A' }}</td>
                    <td class="px-6 py-4">{{ $record['type'] ?? 'N/A' }}</td>
                    <td class="px-6 py-4">{{ $record['department'] ?? 'N/A' }}</td>
                    <td class="px-6 py-4">{{ $record['platform'] ?? 'N/A' }}</td>
                    <td class="px-6 py-4">{{ isset($record['record_date']) ? \Carbon\Carbon::parse($record['record_date'])->format('d/m/Y') : 'N/A' }}</td>
                    <td class="px-6 py-4">{{ $record['submitted_by'] ?? 'N/A' }}</td>
                    <td class="px-6 py-4">{{ isset($record['amount']) ? number_format($record['amount'], 2) : '0.00' }}</td>
                    <td class="px-6 py-4">{{ isset($record['commission']) ? number_format($record['commission'], 2) : '0.00' }}</td>
                    <td class="px-6 py-4">{{ $record['description'] ?? 'N/A' }}</td>
                    <td class="px-6 py-4">{{ $record['status'] ?? 'N/A' }}</td>
                    <td class="px-6 py-4">
                        @if ($record['status'] ?? '' === 'manager_approved')
                        <div class="flex flex-col md:flex-row md:items-center gap-2">
                            <!-- Approve Form -->
                            <form action="{{ route('admin.financial.approve', ['id' => $record['id'], 'type' => $record['type']]) }}" method="POST" id="approve-form-{{ $record['id'] ?? 'default' }}">
                                @csrf
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded inline-flex items-center">
                                    <i class="fas fa-check-circle mr-2"></i> Phê duyệt
                                </button>
                            </form>

                            <!-- Reject Form Toggle -->
                            <button id="show-reject-form-{{ $record['id'] ?? 'default' }}" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded inline-flex items-center">
                                <i class="fas fa-times mr-2"></i> Từ chối
                            </button>

                            <!-- Reject Form -->
                            <form id="reject-form-{{ $record['id'] ?? 'default' }}" action="{{ route('admin.financial.reject', ['id' => $record['id'], 'type' => $record['type']]) }}" method="POST" class="hidden flex flex-col md:flex-row gap-2">
                                @csrf
                                <input type="text" name="reject_reason" class="border border-gray-300 rounded px-3 py-2" placeholder="Lý do từ chối" required>
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded inline-flex items-center">
                                    <i class="fas fa-times mr-2"></i> Gửi từ chối
                                </button>
                            </form>
                        </div>
                        @else
                        <span class="text-gray-500">Không có hành động</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="px-6 py-4 text-center">Không tìm thấy bản ghi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $records->links() }}
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        @forelse ($records as $record)
            const rejectBtn = document.getElementById('show-reject-form-{{ $record['id'] ?? 'default' }}');
            const rejectForm = document.getElementById('reject-form-{{ $record['id'] ?? 'default' }}');
            const approveForm = document.getElementById('approve-form-{{ $record['id'] ?? 'default' }}');

            if (rejectBtn && rejectForm) {
                rejectBtn.addEventListener('click', function(e) {
                    e.preventDefault();
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
        @empty
        @endforelse
    });
</script>

<!-- Font Awesome for Icons -->
<script src="https://kit.fontawesome.com/your-font-awesome-kit.js" crossorigin="anonymous"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection