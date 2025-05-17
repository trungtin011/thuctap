@extends('layouts.admin')

@section('content')
    <div style="max-width:900px;margin:0 auto;">
        <h1 style="margin-bottom: 24px; text-align:center;">Thống kê Doanh Thu & Chi Phí</h1>

        <form method="GET" action="{{ route('admin.financial.total_revenue') }}" style="display: flex; flex-wrap:wrap; gap: 16px; align-items: center; justify-content:center; margin-bottom: 24px; background: #f7f7f7; padding: 16px 12px; border-radius: 8px;">
            <div>
                <label for="start_date">Từ ngày:</label>
                <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}">
            </div>
            <div>
                <label for="end_date">Đến ngày:</label>
                <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}">
            </div>
            <button type="submit" style="padding: 8px 20px; background: #2d8f2d; color: #fff; border: none; border-radius: 4px;">Lọc</button>
        </form>

        <div style="margin-bottom: 24px; text-align:center;">
            <span style="font-size: 1.2em; font-weight: bold; color: #2d8f2d;">
                Tổng doanh thu: {{ number_format($totalRevenue ?? 0) }} VNĐ
            </span>
            <span style="font-size: 1.2em; font-weight: bold; color: #d9534f; margin-left: 32px;">
                Tổng chi phí: {{ number_format($totalExpense ?? 0) }} VNĐ
            </span>
        </div>

        @if(!empty($labels) && !empty($data) && array_sum($data) > 0)
            <div style="background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #eee; padding: 24px; margin-bottom: 32px;">
                <canvas id="revenueExpenseChart" height="320"></canvas>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                const ctx = document.getElementById('revenueExpenseChart').getContext('2d');
                const chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($labels) !!},
                        datasets: [
                            {
                                label: 'Doanh thu',
                                data: {!! json_encode($data) !!},
                                backgroundColor: 'rgba(75, 192, 192, 0.3)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 2,
                                borderRadius: 6,
                                maxBarThickness: 36,
                            },
                            {
                                label: 'Chi phí',
                                data: {!! json_encode($expenseData ?? array_fill(0, count($labels), 0)) !!},
                                backgroundColor: 'rgba(220, 53, 69, 0.2)',
                                borderColor: 'rgba(220, 53, 69, 1)',
                                borderWidth: 2,
                                borderRadius: 6,
                                maxBarThickness: 36,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: true }
                        },
                        scales: {
                            x: { grid: { display: false } },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return value.toLocaleString('vi-VN');
                                    }
                                }
                            }
                        }
                    }
                });
            </script>

            <div style="overflow-x:auto;">
                <table style="width:100%;margin-top:24px;border-collapse:collapse;background:#fff;">
                    <thead>
                        <tr style="background:#f0f0f0;">
                            <th style="padding:8px 12px;border-bottom:1px solid #eee;">Ngày</th>
                            <th style="padding:8px 12px;border-bottom:1px solid #eee;">Doanh thu (VNĐ)</th>
                            <th style="padding:8px 12px;border-bottom:1px solid #eee;">Chi phí (VNĐ)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($labels as $i => $date)
                            <tr>
                                <td style="padding:8px 12px;border-bottom:1px solid #f7f7f7;">{{ $date }}</td>
                                <td style="padding:8px 12px;border-bottom:1px solid #f7f7f7;">{{ number_format($data[$i]) }}</td>
                                <td style="padding:8px 12px;border-bottom:1px solid #f7f7f7;">{{ number_format($expenseData[$i] ?? 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="color: #888; margin-top: 32px; text-align: center;">
                Không có dữ liệu doanh thu cho khoảng thời gian này.
            </div>
        @endif
    </div>
@endsection
