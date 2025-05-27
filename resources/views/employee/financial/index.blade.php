@extends('layouts.navbar')

@section('title', 'Lịch Sử Doanh Thu')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Lịch Sử Doanh Thu</h5>
        </div>
        <div class="card-body">
            @php
                $totalRevenue = $financialRecords->sum('revenue');
                $totalExpense = $financialRecords
                    ->flatMap(function ($r) {
                        return $r->expenses;
                    })
                    ->sum('amount');
                $avgRoas =
                    $financialRecords->count() > 0
                        ? round($financialRecords->where('roas', '!=', null)->avg('roas'), 2)
                        : 0;
                $recordCount = $financialRecords->count();
                $roasWarnings = $financialRecords->filter(function ($r) {
                    return $r->roas !== null && ($r->roas < 1 || $r->roas > 10);
                });
            @endphp
            <div class="mb-3 row">
                <div class="col-md-3"><strong>Tổng doanh thu:</strong> {{ number_format($totalRevenue, 2) }} VNĐ</div>
                <div class="col-md-3"><strong>Tổng chi phí:</strong> {{ number_format($totalExpense, 2) }} VNĐ</div>
                <div class="col-md-3"><strong>ROAS TB:</strong> {{ $avgRoas }}</div>
                <div class="col-md-3"><strong>Số bản ghi:</strong> {{ $recordCount }}</div>
            </div>
            @if ($roasWarnings->count() > 0)
                <div class="alert alert-warning">
                    <strong>Cảnh báo:</strong> Có {{ $roasWarnings->count() }} bản ghi có ROAS bất thường (ROAS < 1 hoặc>
                        10).
                </div>
            @endif
            <form method="GET" action="" class="row g-3 mb-4 align-items-end">
                <div class="col-md-3">
                    <label for="filter_platform" class="form-label">Nền tảng</label>
                    <select id="filter_platform" name="platform_id" class="form-select">
                        <option value="">Tất cả</option>
                        @foreach ($platforms as $platform)
                            <option value="{{ $platform->id }}"
                                {{ request('platform_id') == $platform->id ? 'selected' : '' }}>
                                {{ $platform->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filter_status" class="form-label">Trạng thái</label>
                    <select id="filter_status" name="status" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Đang chờ</option>
                        <option value="manager_approved" {{ request('status') == 'manager_approved' ? 'selected' : '' }}>
                            Quản lý duyệt</option>
                        <option value="admin_approved" {{ request('status') == 'admin_approved' ? 'selected' : '' }}>Admin
                            duyệt</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Bị từ chối
                        </option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="filter_start_date" class="form-label">Từ ngày</label>
                    <input type="date" class="form-control" id="filter_start_date" name="start_date"
                        value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2">
                    <label for="filter_end_date" class="form-label">Đến ngày</label>
                    <input type="date" class="form-control" id="filter_end_date" name="end_date"
                        value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2">
                    <div class="flex flex-row gap-2">
                        @if (request('platform_id') || request('status') || request('start_date') || request('end_date'))
                            <a href="{{ route('employee.financial.index') }}"
                                class="bg-danger text-white p-2 w-10 text-center"><i class="fa-solid fa-repeat"></i></a>
                        @endif
                        <button type="submit" class="bg-primary text-white p-2 w-20">Lọc</button>
                    </div>
                </div>
            </form>
            @if ($financialRecords->isEmpty())
                <p class="text-muted">Bạn chưa có bản ghi doanh thu nào.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Phòng ban</th>
                                <th scope="col">Đại lý</th>
                                <th scope="col">Nền tảng</th>
                                <th scope="col">Doanh thu</th>
                                <th scope="col">Nguồn doanh thu</th>
                                <th scope="col">Tổng chi phí</th>
                                <th scope="col">Nguồn chi phí</th>
                                <th scope="col">ROAS</th>
                                <th scope="col">Trạng thái</th>
                                <th scope="col">Ngày ghi nhận</th>
                                <th scope="col">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($financialRecords as $record)
                                <tr data-id="{{ $record->id }}" data-platform-id="{{ $record->platform_id }}"
                                    data-revenue="{{ $record->revenue }}" data-record-date="{{ $record->record_date }}"
                                    data-record-time="{{ $record->record_time }}"
                                    data-note="{{ $record->note && json_decode($record->note) ? e(optional(json_decode($record->note))->note) : '' }}"
                                    data-expenses="{{ json_encode($record->expenses) }}"
                                    data-revenue-sources="{{ json_encode($record->revenue_sources ?? []) }}"
                                    data-metric-values="{{ json_encode($record->metric_values ?? []) }}"
                                    @if ($record->roas !== null && ($record->roas < 1 || $record->roas > 10)) style="background:#fff3cd;" @endif>
                                    <td>{{ $record->id }}</td>
                                    <td>{{ $record->department->name }}</td>
                                    <td>{{ $record->daily->ten_dai_ly ?? 'Chưa có đại lý' }}</td>
                                    <td>{{ $record->platform->name }}</td>
                                    <td>{{ number_format($record->revenue, 2) }}</td>
                                    <td>
                                        @if (!empty($record->revenue_sources))
                                            @foreach ($record->revenue_sources as $src)
                                                <div>{{ $src->source_name }}: {{ number_format($src->amount, 2) }}</div>
                                            @endforeach
                                        @else
                                            <div>N/A</div>
                                        @endif
                                    </td>
                                    <td>{{ number_format(collect($record->expenses)->sum('amount'), 2) }}</td>
                                    <td>
                                        @foreach ($record->expenses as $exp)
                                            <div>{{ $exp->description ?? 'N/A' }}: {{ number_format($exp->amount, 2) }}
                                            </div>
                                        @endforeach
                                    </td>
                                    <td>{{ $record->roas ? number_format($record->roas, 2) : 'N/A' }}
                                        @if ($record->roas !== null && ($record->roas < 1 || $record->roas > 10))
                                            <span class="badge bg-warning text-dark">!</span>
                                        @endif
                                    </td>
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
                                    <td>{{ \Illuminate\Support\Carbon::parse($record->record_date)->format('Y-m-d') }}
                                        {{ $record->record_time }}</td>
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
                    <i class="bi bi-plus-circle me-1"></i> Nhập bản ghi mới
                </a>
            </div>
        </div>
    </div>
@endsection
