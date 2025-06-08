@extends('layouts.admin')
@section('title', 'Báo cáo Hoa hồng')
@section('content')
<div class="container mx-auto mt-6">
    <h2 class="text-xl font-bold mb-4">Báo cáo Hoa hồng theo Đại lý / Tuyến</h2>
    <form method="GET" class="flex gap-4 mb-4">
        <div>
            <label>Đại lý:</label>
            <select name="dai_ly_id" class="form-control">
                <option value="">-- Tất cả --</option>
                @foreach($dailies as $daily)
                    <option value="{{ $daily->id }}" {{ request('dai_ly_id') == $daily->id ? 'selected' : '' }}>
                        {{ $daily->ten_dai_ly }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label>Tuyến:</label>
            <select name="route_id" class="form-control">
                <option value="">-- Tất cả --</option>
                @foreach($routes as $route)
                    <option value="{{ $route->id }}" {{ request('route_id') == $route->id ? 'selected' : '' }}>
                        {{ $route->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label>Từ ngày:</label>
            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
        </div>
        <div>
            <label>Đến ngày:</label>
            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
        </div>
        <div class="flex items-end">
            <button type="submit" class="btn btn-primary">Lọc</button>
        </div>
    </form>
    <div class="bg-white rounded shadow">
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Đại lý</th>
                    <th>Tuyến</th>
                    <th>Nền tảng</th>
                    <th>Doanh thu</th>
                    <th>Hoa hồng</th>
                    <th>Ngày ghi nhận</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $record)
                    <tr>
                        <td>{{ $record->id }}</td>
                        <td>{{ $record->daily->ten_dai_ly ?? 'N/A' }}</td>
                        <td>{{ $record->route->name ?? 'N/A' }}</td>
                        <td>{{ $record->platform->name ?? 'N/A' }}</td>
                        <td>{{ number_format($record->revenue, 2) }}</td>
                        <td class="text-blue-600 font-bold">{{ number_format($record->commission ?? 0, 2) }}</td>
                        <td>{{ $record->record_date }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted">Không có dữ liệu.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-3">
            {{ $records->links() }}
        </div>
    </div>
</div>
@endsection
