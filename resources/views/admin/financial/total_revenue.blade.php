@extends('layouts.admin')

@section('content')
    <h1>Tổng Doanh Thu</h1>

    <form method="GET" action="{{ route('admin.financial.total_revenue') }}">
        <label for="start_date">Từ ngày:</label>
        <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}">
        
        <label for="end_date">Đến ngày:</label>
        <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}">
        
        <button type="submit">Lọc</button>
    </form>

<h3>Tổng doanh thu: {{ number_format($totalRevenue ?? 0) }} VNĐ</h3>



    @if(!empty($labels) && !empty($data))
        <canvas id="revenueChart" width="400" height="200"></canvas>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById('revenueChart').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($labels) !!},
                    datasets: [{
                        label: 'Doanh thu',
                        data: {!! json_encode($data) !!},
                        borderColor: 'rgba(75, 192, 192, 1)',
                        fill: false,
                    }]
                },
            });
        </script>
    @endif
@endsection
