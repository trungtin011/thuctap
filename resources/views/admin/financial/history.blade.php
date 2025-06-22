<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử tài chính</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Lịch sử tài chính</h1>

        <!-- Records Table -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Loại</th>
                        <th>Phòng ban</th>
                        <th>Nền tảng</th>
                        <th>Ngày</th>
                        <th>Người gửi</th>
                        <th>Số tiền</th>
                        <th>Hoa hồng</th>
                        <th>Mô tả</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($records as $record)
                        <tr>
                            <td>{{ $record['id'] }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $record['type'])) }}</td>
                            <td>{{ $record['department'] }}</td>
                            <td>{{ $record['platform'] }}</td>
                            <td>{{ \Carbon\Carbon::parse($record['record_date'])->format('d/m/Y') }}</td>
                            <td>{{ $record['submitted_by'] }}</td>
                            <td>{{ number_format($record['amount'], 2) }}</td>
                            <td>{{ number_format($record['commission'], 2) }}</td>
                            <td>{{ $record['description'] }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $record['status'])) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-center">Không tìm thấy bản ghi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Back Button -->
        <a href="{{ route('admin.financial.index') }}" class="btn btn-primary mt-3">Quay lại danh sách</a>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>