@extends('layouts.navbar')

@section('title', 'Lịch Sử')

@section('content')
@php
$isMarketing = auth()->user()->department->name === 'Marketing';
$isAccountant = auth()->user()->department->name === 'Kế toán';
$isBusiness = auth()->user()->department->name === 'Kinh doanh';
@endphp
<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">{{ $isMarketing ? 'Lịch Sử Chi Phí' : 'Lịch Sử Doanh Thu' }}</h5>
    </div>
    <div class="card-body">
        <div class="mb-3 row">
            @if ($isAccountant)
            <div class="col-md-3"><strong>Tổng tiền mặt:</strong> {{ number_format($totalRevenue, 2) }} VNĐ</div>
            <div class="col-md-3"><strong>Tổng chuyển khoản:</strong> {{ number_format($totalTransfer, 2) }} VNĐ</div>
            <div class="col-md-3"><strong>Tổng chi phí:</strong> {{ number_format($totalExpenseTotal, 2) }} VNĐ</div>
            @elseif ($isBusiness)
            <div class="col-md-3"><strong>Tổng doanh thu:</strong> {{ number_format($totalRevenue, 2) }} VNĐ</div>
            <div class="col-md-3"><strong>Tổng hoa hồng:</strong> {{ number_format($totalCommission, 2) }} VNĐ</div>
            @elseif ($isMarketing)
            <div class="col-md-3"><strong>Tổng chi phí:</strong> {{ number_format($totalExpense, 2) }} VNĐ</div>
            @endif
            <div class="col-md-3"><strong>Số bản ghi:</strong> {{ $recordCount }}</div>
            @if (count($revenueBySource) > 0)
            <div class="col-12 mt-3">
                <h6 class="mb-3">Chi tiết theo nguồn:</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Nguồn</th>
                                @if ($isAccountant)
                                <th>Tiền mặt</th>
                                <th>Chuyển khoản</th>
                                <th>Chi phí</th>
                                @else
                                <th>Doanh thu</th>
                                @if ($isBusiness)
                                <th>Hoa hồng</th>
                                @endif
                                @if ($isMarketing)
                                <th>Chi phí</th>
                                @endif
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($revenueBySource as $source => $amount)
                            <tr>
                                <td>{{ $source }}</td>
                                @if ($isAccountant)
                                <td>{{ number_format($revenueBySource[$source], 2) }} VNĐ</td>
                                <td>{{ number_format($transferBySource[$source] ?? 0, 2) }} VNĐ</td>
                                <td>{{ number_format($expenseBySource[$source] ?? 0, 2) }} VNĐ</td>
                                @else
                                <td>{{ number_format($amount, 2) }} VNĐ</td>
                                @if ($isBusiness)
                                <td>{{ number_format($commissionBySource[$source] ?? 0, 2) }} VNĐ</td>
                                @endif
                                @if ($isMarketing)
                                <td>{{ number_format($expenseBySource[$source] ?? 0, 2) }} VNĐ</td>
                                @endif
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <form method="GET" action="{{ route('employee.financial.index') }}" class="row g-3 mb-4 align-items-end">
            <div class="col-md-3">
                <label for="filter_start_date" class="form-label">Từ ngày</label>
                <input type="date" class="form-control" id="filter_start_date" name="start_date" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <label for="filter_end_date" class="form-label">Đến ngày</label>
                <input type="date" class="form-control" id="filter_end_date" name="end_date" value="{{ request('end_date') }}">
            </div>
            @if (!$isAccountant)
            <div class="col-md-3">
                <label for="platform_id" class="form-label">Nền tảng</label>
                <select name="platform_id" id="platform_id" class="form-select">
                    <option value="">-- Tất cả --</option>
                    @foreach ($platforms as $platform)
                    <option value="{{ $platform->id }}" {{ request('platform_id') == $platform->id ? 'selected' : '' }}>{{ $platform->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select name="status" id="status" class="form-select">
                    <option value="">-- Tất cả --</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                    <option value="manager_approved" {{ request('status') == 'manager_approved' ? 'selected' : '' }}>Quản lý duyệt</option>
                    <option value="admin_approved" {{ request('status') == 'admin_approved' ? 'selected' : '' }}>Admin duyệt</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                </select>
            </div>
            @endif
            <div class="col-md-3">
                <div class="d-flex gap-2">
                    @if (request('start_date') || request('end_date') || request('platform_id') || request('status'))
                    <a href="{{ route('employee.financial.index') }}" class="btn btn-danger"><i class="fa-solid fa-repeat"></i></a>
                    @endif
                    <button type="submit" class="btn btn-primary">Lọc</button>
                </div>
            </div>
        </form>

        @if ($records->isEmpty())
        <p class="text-muted">Bạn chưa có bản ghi nào.</p>
        @else
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Phòng ban</th>
                        <th scope="col">Ngày</th>
                        @if ($isMarketing || $isBusiness)
                        <th scope="col">Tuyến</th>
                        @endif
                        @if ($isAccountant)
                        <th scope="col">Văn phòng</th>
                        <th scope="col">Tiền mặt</th>
                        <th scope="col">Chuyển khoản</th>
                        <th scope="col">Chi phí</th>
                        @elseif ($isBusiness)
                        <th scope="col">Doanh thu</th>
                        <th scope="col">Hoa hồng</th>
                        <th scope="col">Nguồn doanh thu</th>
                        @elseif ($isMarketing)
                        <th scope="col">Nền tảng</th>
                        <th scope="col">Chi phí</th>
                        @endif
                        <th scope="col">Trạng thái</th>
                        <th scope="col">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                    <tr>
                        <td>{{ $record->id }}</td>
                        <td>{{ $isAccountant ? auth()->user()->department->name : $record->department->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($record->record_date)->format('d/m/Y') }}</td>
                        @if ($isMarketing || $isBusiness)
                        <td>{{ $record->route->name }}</td>
                        @endif
                        @if ($isAccountant)
                        <td>{{ $record->office->name }}</td>
                        <td>{{ number_format($record->cash, 2) }} VNĐ</td>
                        <td>{{ number_format($record->bank_transfer, 2) }} VNĐ</td>
                        <td>{{ number_format($record->expense, 2) }} VNĐ</td>
                        @elseif ($isBusiness)
                        <td>{{ number_format($record->revenue, 2) }} VNĐ</td>
                        <td>{{ number_format($record->commission, 2) }} VNĐ</td>
                        <td>
                            @php
                            $noteData = json_decode($record->note);
                            $sources = $noteData->revenue_sources ?? [];
                            @endphp
                            @foreach ($sources as $source)
                            {{ $source->source_name }}: {{ number_format($source->amount, 2) }} VNĐ<br>
                            @endforeach
                        </td>
                        @elseif ($isMarketing)
                        <td>{{ $record->platform->name }}</td>
                        <td>{{ number_format($record->expenses->sum('amount'), 2) }} VNĐ</td>
                        @endif
                        <td>
                            @if ($isAccountant)
                            Đã ghi nhận
                            @else
                            @switch($record->status)
                            @case('pending')
                            <span class="badge bg-warning">Chờ duyệt</span>
                            @break
                            @case('manager_approved')
                            <span class="badge bg-info">Quản lý duyệt</span>
                            @break
                            @case('admin_approved')
                            <span class="badge bg-success">Admin duyệt</span>
                            @break
                            @case('rejected')
                            <span class="badge bg-danger">Từ chối</span>
                            @break
                            @endswitch
                            @endif
                        </td>
                        <td>
                            @if ($isAccountant)
                            <a href="{{ route('employee.financial.edit.accounting', $record->id) }}" class="btn btn-sm btn-primary">Sửa</a>
                            <form action="{{ route('employee.financial.destroy', $record->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa bản ghi này?')">Xóa</button>
                            </form>
                            @else
                            @if ($record->status === 'pending')
                            <a href="{{ route('employee.financial.edit.' . ($isMarketing ? 'marketing' : 'business'), $record->id) }}" class="btn btn-sm btn-primary">Sửa</a>
                            <form action="{{ route('employee.financial.destroy', $record->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa bản ghi này?')">Xóa</button>
                            </form>
                            @endif
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $records->links() }}
        </div>
        @endif

        <div class="mt-4">
            <a href="{{ route('employee.financial.create.' . ($isMarketing ? 'marketing' : ($isAccountant ? 'accounting' : 'business'))) }}" class="btn btn-success">Thêm bản ghi mới</a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Thành công!',
        text: '{{ session('
        success ') }}',
        confirmButtonText: 'OK',
        confirmButtonColor: '#0d6efd'
    });
    @endif

    @if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Lỗi!',
        text: '{{ session('
        error ') }}',
        confirmButtonText: 'OK',
        confirmButtonColor: '#dc3545'
    });
    @endif
</script>
@endsection