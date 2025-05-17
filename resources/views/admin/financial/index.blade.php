    @extends('layouts.admin')

@section('title', 'Danh sách đơn đã được quản lý phê duyệt')

@section('content')
<div class="container mt-4">
    <h2>Danh sách đơn đã được quản lý phê duyệt</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($financialRecords->isEmpty())
        <p>Không có đơn nào chờ phê duyệt.</p>
    @else
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Phòng ban</th>
                <th>Nền tảng</th>
                <th>Doanh thu</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($financialRecords as $record)
                <tr>
                    <td>{{ $record->id }}</td>
                    <td>{{ $record->department->name ?? 'N/A' }}</td>
                    <td>{{ $record->platform->name ?? 'N/A' }}</td>
                    <td>{{ number_format($record->revenue) }} VND</td>
                    <td><span class="badge bg-info">Quản lý duyệt</span></td>
                   
                    <td>
                        <form action="{{ route('admin.financial.approve', $record->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn phê duyệt đơn này không?')">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">Phê duyệt</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
   <a href="{{ route('admin.financial.history') }}" class="btn btn-secondary mb-3">Xem lịch sử đã phê duyệt</a>
@endsection
