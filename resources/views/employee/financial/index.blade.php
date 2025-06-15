@extends('layouts.navbar')

@section('title', 'Lịch Sử Doanh Thu')

@section('content')
    @php
        $isMarketing = auth()->user()->department->name === 'Marketing';
        $isAccountant = auth()->user()->department->name === 'Kế toán';
    @endphp
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">{{ $isMarketing ? 'Lịch Sử Chi Phí' : 'Lịch Sử Doanh Thu' }}</h5>
        </div>
        <div class="card-body">
            <div class="mb-3 row">
                @if (!$isMarketing)
                    @if ($isAccountant)
                        <div class="col-md-3"><strong>Tổng tiền mặt:</strong> {{ number_format($totalRevenue) }} VNĐ</div>
                    @endif
                    @if (!$isAccountant)
                        <div class="col-md-3"><strong>Tổng doanh thu:</strong> {{ number_format($totalRevenue) }} VNĐ
                        </div>
                        <div class="col-md-3"><strong>Tổng hoa hồng:</strong> {{ number_format($totalCommission) }} VNĐ
                        </div>
                    @endif
                    @if ($isAccountant)
                        <div class="col-md-3"><strong>Tổng chuyển khoản:</strong> {{ number_format($totalTransfer) }} VNĐ
                        </div>
                        <div class="col-md-3"><strong>Tổng chi:</strong> {{ number_format($totalExpenseTotal) }} VNĐ</div>
                        <div class="col-md-3"><strong>Tổng dt phòng hàng:</strong>
                            {{ number_format($totalExpenseTotal + $totalTransfer + $totalRevenue) }} VNĐ</div>
                    @endif
                    @if (count($revenueBySource) > 0)
                        <div class="col-12 mt-3">
                            <h6 class="mb-3">Chi tiết theo nguồn doanh thu:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nguồn doanh thu</th>
                                            <th>Tổng doanh thu</th>
                                            @if (!$isAccountant)
                                                <th>Tổng hoa hồng</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($revenueBySource as $source => $amount)
                                            <tr>
                                                <td>{{ $source }}</td>
                                                <td>{{ number_format($amount) }} VNĐ</td>
                                                @if (!$isAccountant)
                                                    <td>{{ number_format($commissionBySource[$source]) }} VNĐ</td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                @endif
                @if ($isMarketing)
                    <div class="col-md-6"><strong>Tổng chi phí:</strong> {{ number_format($totalExpense) }} VNĐ</div>
                @endif
                <div class="col-md-3"><strong>Số bản ghi:</strong> {{ $recordCount }}</div>
            </div>

            <form method="GET" action="" class="row g-3 mb-4 align-items-end">
                <div class="col-md-4">
                    <label for="filter_start_date" class="form-label">Từ ngày</label>
                    <input type="date" class="form-control" id="filter_start_date" name="start_date"
                        value="{{ request('start_date') }}">
                </div>
                <div class="col-md-4">
                    <label for="filter_end_date" class="form-label">Đến ngày</label>
                    <input type="date" class="form-control" id="filter_end_date" name="end_date"
                        value="{{ request('end_date') }}">
                </div>
                <div class="col-md-4">
                    <div class="flex flex-row gap-2">
                        @if (request('start_date') || request('end_date'))
                            <a href="{{ route('employee.financial.index') }}"
                                class="bg-danger text-white p-2 w-10 text-center"><i class="fa-solid fa-repeat"></i></a>
                        @endif
                        <button type="submit" class="bg-primary text-white p-2 w-20">Lọc</button>
                    </div>
                </div>
            </form>

            @if ($financialRecords->isEmpty())
                <p class="text-muted">Bạn chưa có bản ghi nào.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Phòng ban</th>
                                <th scope="col">Ngày</th>
                                @if (!$isMarketing && !$isAccountant)
                                    <th scope="col">Tuyến</th>
                                @endif
                                @if (!$isMarketing)
                                    <th scope="col">Doanh thu</th>
                                    @if (!$isAccountant)
                                        <th scope="col">Hoa hồng</th>
                                    @endif
                                    @if ($isAccountant)
                                        <th scope="col">Chuyển khoản</th>
                                        <th scope="col">Chi</th>
                                    @endif
                                    <th scope="col">Nguồn doanh thu</th>
                                @endif
                                @if ($isMarketing)
                                    <th scope="col">Chi phí</th>
                                    <th scope="col">Chi tiết chi phí</th>
                                @endif
                                <th scope="col">Trạng thái</th>
                                <th scope="col">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($financialRecords as $record)
                                @php
                                    $noteData = json_decode($record->note);
                                    $transferTotal = $isAccountant ? $noteData->transfer_total ?? 0 : 0;
                                    $expenseTotal = $isAccountant ? $noteData->expense_total ?? 0 : 0;
                                @endphp
                                <tr>
                                    <td>{{ $record->id }}</td>
                                    <td>{{ $record->department->name }}</td>
                                    <td>
                                        <div>Tháng: {{ \Carbon\Carbon::parse($record->record_date)->format('m/Y') }}</div>
                                        <div>Ngày: {{ \Carbon\Carbon::parse($record->record_date)->format('d/m/Y') }}</div>
                                        <div>{{ \Carbon\Carbon::parse($record->record_time)->format('H:i') }}</div>
                                    </td>
                                    @if (!$isMarketing && !$isAccountant)
                                        <td>{{ $record->route->name ?? 'N/A' }}</td>
                                    @endif
                                    @if (!$isMarketing)
                                        <td>{{ number_format($record->revenue) }}</td>
                                        @if (!$isAccountant)
                                            <td>{{ number_format($record->commission) }}</td>
                                        @endif
                                        @if ($isAccountant)
                                            <td>{{ number_format($transferTotal) }}</td>
                                            <td>{{ number_format($expenseTotal) }}</td>
                                        @endif
                                        <td>
                                            @php
                                                $revenueSources = $noteData->revenue_sources ?? [];
                                            @endphp
                                            @foreach ($revenueSources as $source)
                                                <div>{{ $source->source_name }}: {{ number_format($source->amount) }}
                                                    @if (!$isAccountant && isset($source->commission))
                                                        (HH: {{ number_format($source->commission) }})
                                                    @endif
                                                </div>
                                            @endforeach
                                        </td>
                                    @endif
                                    @if ($isMarketing)
                                        <td>{{ number_format($record->expenses->sum('amount')) }}</td>
                                        <td>
                                            @foreach ($record->expenses as $expense)
                                                <div>{{ $expense->expenseType->name }}:
                                                    {{ number_format($expense->amount) }}</div>
                                                @if ($expense->description)
                                                    <small class="text-muted">{{ $expense->description }}</small>
                                                @endif
                                            @endforeach
                                        </td>
                                        <td>{{ $record->platform->name ?? '' }}</td>
                                        <td>{{ $record->route->name ?? '' }}</td>
                                        <td>
                                            @php
                                                $metrics = $record->platform ? $record->platform->metrics : collect();
                                                $metricValues = $record->metric_values;
                                            @endphp
                                            @if ($metrics->count())
                                                <ul class="list-group">
                                                    @foreach ($metrics as $metric)
                                                        <li class="list-group-item">
                                                            <strong>{{ $metric->name }}</strong>
                                                            @php
                                                                $value = $metricValues->where('metric_id', $metric->id)->first();
                                                            @endphp
                                                            : {{ $value ? $value->value : '-' }} {{ $metric->unit }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <span class="text-muted">Không có trường nào</span>
                                            @endif
                                        </td>
                                    @endif
                                    <td>
                                        @switch($record->status)
                                            @case('pending')
                                                <span class="badge bg-warning text-dark">Đang chờ</span>
                                            @break

                                            @case('manager_approved')
                                                <span class="badge bg-info">Quản lý duyệt</span>
                                            @break

                                            @case('admin_approved')
                                                <span class="badge bg-success">Admin duyệt</span>
                                            @break

                                            @case('rejected')
                                                <span class="badge bg-danger">Bị từ chối</span>
                                            @break

                                            @default
                                                <span class="badge bg-secondary">Không rõ</span>
                                        @endswitch
                                    </td>
                                    <td class="text-center">
                                        @if ($record->status == 'pending')
                                            <a href="{{ route('employee.financial.edit', $record->id) }}"
                                                class="btn btn-sm btn-primary me-1" title="Sửa">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('employee.financial.destroy', $record->id) }}"
                                                method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Xóa"
                                                    onclick="return confirm('Bạn có chắc chắn muốn xóa bản ghi này?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if (method_exists($financialRecords, 'links'))
                    <div class="mt-3">
                        {{ $financialRecords->links() }}
                    </div>
                @endif
            @endif
            <div class="mt-4">
                <a href="{{ route('employee.financial.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> {{ $isMarketing ? 'Nhập chi phí mới' : 'Nhập doanh thu mới' }}
                </a>
            </div>
        </div>
    </div>
@endsection
