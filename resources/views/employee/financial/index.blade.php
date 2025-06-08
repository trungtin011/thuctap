@extends('layouts.navbar')

@section('title', 'Lịch Sử Doanh Thu')

@section('content')
    @php
        $isMarketing = auth()->user()->department->name === 'Marketing';
        
        // Calculate totals for current display
        $totalRevenue = $financialRecords->sum('revenue');
        $totalCommission = $financialRecords->sum('commission');
        $totalExpense = $financialRecords->sum(function($record) {
            return $record->expenses->sum('amount');
        });
        
        // Tính tổng theo nguồn doanh thu
        $revenueBySource = [];
        $commissionBySource = [];
        foreach ($financialRecords as $record) {
            $noteData = json_decode($record->note);
            if (isset($noteData->revenue_sources)) {
                foreach ($noteData->revenue_sources as $source) {
                    $sourceName = $source->source_name;
                    if (!isset($revenueBySource[$sourceName])) {
                        $revenueBySource[$sourceName] = 0;
                        $commissionBySource[$sourceName] = 0;
                    }
                    $revenueBySource[$sourceName] += $source->amount;
                    $commissionBySource[$sourceName] += $source->commission ?? 0;
                }
            }
        }
        arsort($revenueBySource); // Sắp xếp theo doanh thu giảm dần
        
        $recordCount = $financialRecords->count();
    @endphp
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">{{ $isMarketing ? 'Lịch Sử Chi Phí' : 'Lịch Sử Doanh Thu' }}</h5>
        </div>
        <div class="card-body">
            <div class="mb-3 row">
                @if(!$isMarketing)
                <div class="col-md-4"><strong>Tổng doanh thu:</strong> {{ number_format($totalRevenue, 2) }} VNĐ</div>
                <div class="col-md-4"><strong>Tổng hoa hồng:</strong> {{ number_format($totalCommission, 2) }} VNĐ</div>
                @if(count($revenueBySource) > 0)
                <div class="col-12 mt-3">
                    <h6 class="mb-3">Chi tiết theo nguồn doanh thu:</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Nguồn doanh thu</th>
                                    <th>Tổng doanh thu</th>
                                    <th>Tổng hoa hồng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($revenueBySource as $source => $amount)
                                <tr>
                                    <td>{{ $source }}</td>
                                    <td>{{ number_format($amount, 2) }} VNĐ</td>
                                    <td>{{ number_format($commissionBySource[$source], 2) }} VNĐ</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
                @endif
                @if($isMarketing)
                <div class="col-md-6"><strong>Tổng chi phí:</strong> {{ number_format($totalExpense, 2) }} VNĐ</div>
                @endif
                <div class="col-md-4"><strong>Số bản ghi:</strong> {{ $recordCount }}</div>
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
                                @if(!$isMarketing)
                                <th scope="col">Tuyến</th>
                                <th scope="col">Doanh thu</th>
                                <th scope="col">Hoa hồng</th>
                                <th scope="col">Nguồn doanh thu</th>
                                @endif
                                @if($isMarketing)
                                <th scope="col">Chi phí</th>
                                <th scope="col">Chi tiết chi phí</th>
                                @endif
                                <th scope="col">Trạng thái</th>
                                <th scope="col">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($financialRecords as $record)
                                <tr>
                                    <td>{{ $record->id }}</td>
                                    <td>{{ $record->department->name }}</td>
                                    <td>
                                        <div>Tháng: {{ \Carbon\Carbon::parse($record->record_date)->format('m/Y') }}</div>
                                        <div>Ngày: {{ \Carbon\Carbon::parse($record->record_date)->format('d/m/Y') }}</div>
                                        <div>{{ \Carbon\Carbon::parse($record->record_time)->format('H:i') }}</div>
                                    </td>
                                    @if(!$isMarketing)
                                    <td>{{ $record->route->name ?? 'N/A' }}</td>
                                    <td>{{ number_format($record->revenue, 2) }}</td>
                                    <td>{{ number_format($record->commission, 2) }}</td>
                                    <td>
                                        @php
                                            $noteData = json_decode($record->note);
                                            $revenueSources = $noteData->revenue_sources ?? [];
                                        @endphp
                                        @foreach($revenueSources as $source)
                                            <div>{{ $source->source_name }}: {{ number_format($source->amount, 2) }} (HH: {{ number_format($source->commission ?? 0, 2) }})</div>
                                        @endforeach
                                    </td>
                                    @endif
                                    @if($isMarketing)
                                    <td>{{ number_format($record->expenses->sum('amount'), 2) }}</td>
                                    <td>
                                        @foreach($record->expenses as $expense)
                                            <div>{{ $expense->expenseType->name }}: {{ number_format($expense->amount, 2) }}</div>
                                            @if($expense->description)
                                                <small class="text-muted">{{ $expense->description }}</small>
                                            @endif
                                        @endforeach
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
