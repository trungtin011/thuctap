@extends('layouts.admin')

@section('title', 'Quản lý doanh thu')

@section('content')
    <div class="bg-gray-100 flex-1 p-6">
        <!-- Báo Cáo Tổng Quan -->
        <div class="grid grid-cols-4 gap-6 xl:grid-cols-1">
            <!-- Tổng doanh thu -->
            <div class="report-card">
                <div class="card">
                    <div class="card-body flex flex-col">
                        <div class="flex flex-row justify-between items-center">
                            <div class="h6 text-indigo-700 fas fa-money-bill-wave"></div>
                            <span class="rounded-full text-white badge bg-teal-400 text-xs">
                                {{ number_format($total_revenue / 1000000, 2) }}M
                                <i class="fas fa-chevron-up ml-1"></i>
                            </span>
                        </div>
                        <div class="mt-8">
                            <h1 class="h5">{{ number_format($total_revenue, 2) }} VNĐ</h1>
                            <p>Tổng Doanh Thu</p>
                        </div>
                    </div>
                </div>
                <div class="footer bg-white p-1 mx-4 border border-t-0 rounded rounded-t-none"></div>
            </div>

            <!-- Tổng chi phí -->
            <div class="report-card">
                <div class="card">
                    <div class="card-body flex flex-col">
                        <div class="flex flex-row justify-between items-center">
                            <div class="h6 text-red-700 fas fa-shopping-cart"></div>
                            <span class="rounded-full text-white badge bg-red-400 text-xs">
                                {{ number_format($total_expenses / 1000000, 2) }}M
                                <i class="fas fa-chevron-down ml-1"></i>
                            </span>
                        </div>
                        <div class="mt-8">
                            <h1 class="h5">{{ number_format($total_expenses, 2) }} VNĐ</h1>
                            <p>Tổng Chi Phí</p>
                        </div>
                    </div>
                </div>
                <div class="footer bg-white p-1 mx-4 border border-t-0 rounded rounded-t-none"></div>
            </div>

            <!-- ROAS trung bình -->
            <div class="report-card">
                <div class="card">
                    <div class="card-body flex flex-col">
                        <div class="flex flex-row justify-between items-center">
                            <div class="h6 text-yellow-600 fas fa-chart-line"></div>
                            <span class="rounded-full text-white badge bg-teal-400 text-xs">
                                {{ $avg_roas }}%
                                <i class="fas fa-chevron-up ml-1"></i>
                            </span>
                        </div>
                        <div class="mt-8">
                            <h1 class="h5">{{ $avg_roas }}</h1>
                            <p>ROAS Trung Bình</p>
                        </div>
                    </div>
                </div>
                <div class="footer bg-white p-1 mx-4 border border-t-0 rounded rounded-t-none"></div>
            </div>

            <!-- Số lượng bản ghi -->
            <div class="report-card">
                <div class="card">
                    <div class="card-body flex flex-col">
                        <div class="flex flex-row justify-between items-center">
                            <div class="h6 text-green-700 fas fa-file-alt"></div>
                            <span class="rounded-full text-white badge bg-teal-400 text-xs">
                                {{ $record_count }}
                                <i class="fas fa-chevron-up ml-1"></i>
                            </span>
                        </div>
                        <div class="mt-8">
                            <h1 class="h5">{{ $record_count }}</h1>
                            <p>Bản Ghi Đã Duyệt</p>
                        </div>
                    </div>
                </div>
                <div class="footer bg-white p-1 mx-4 border border-t-0 rounded rounded-t-none"></div>
            </div>
        </div>

        {{-- Biểu đồ so sánh và biểu đồ tròn --}}
        <div class="flex space-x-4 mt-6">
            <div class="w-2/2">
                <!-- Bộ lọc -->
                <div class="flex space-x-4">
                    <div>
                        <label for="yearFilter" class="mr-2 text-gray-700 font-medium">Chọn năm:</label>
                        <select id="yearFilter"
                            class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="all">Tất cả năm</option>
                            @foreach ($years as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="timeFilter" class="mr-2 text-gray-700 font-medium">Chọn khoảng thời gian:</label>
                        <select id="timeFilter"
                            class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="all">Tất cả</option>
                            <option value="month">Tháng hiện tại</option>
                            <option value="3months">3 tháng gần nhất</option>
                        </select>
                    </div>
                </div>
                <canvas id="sealsOverview" height="100"></canvas>
            </div>
            <div class="flex justify-center">
                <canvas id="platformPieChart" height="100"></canvas>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-2 gap-6 xl:grid-cols-1">
            <div class="card bg-teal-400 border-teal-400 shadow-md text-white">
                <div class="card-body flex flex-row">
                    <div class="img-wrapper w-40 h-40 flex justify-center items-center">
                        <img src="{{ asset('img/happy.svg') }}" alt="Hình ảnh chúc mừng">
                    </div>
                    <div class="py-2 ml-10">
                        <h1 class="h6">Tuyệt Vời, {{ auth()->user()->name }}!</h1>
                        <p class="text-white text-xs">Bạn đã duyệt {{ $record_count }} bản ghi tài chính trong tháng này.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col">
                <div class="alert alert-dark mb-6">
                    Xin chào! Hãy xem quản lý bản ghi tài chính tại
                    <a class="ml-2" href="{{ route('admin.financial.index') }}">Bản Ghi Tài Chính</a>
                </div>

                <div class="grid grid-cols-2 gap-6 h-full">
                    <div class="card">
                        <div class="py-3 px-4 flex flex-row justify-between">
                            <h1 class="h6">
                                {{ number_format($total_revenue / 1000000, 2) }}M VNĐ
                                <p>Tổng Doanh Thu</p>
                            </h1>
                            <div
                                class="bg-teal-200 text-teal-700 border-teal-300 border w-10 h-10 rounded-full flex justify-center items-center">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                        </div>
                        <div class="analytics_1"></div>
                    </div>

                    <div class="card">
                        <div class="py-3 px-4 flex flex-row justify-between">
                            <h1 class="h6">
                                {{ number_format($total_expenses / 1000000, 2) }}M VNĐ
                                <p>Tổng Chi Phí</p>
                            </h1>
                            <div
                                class="bg-indigo-200 text-indigo-700 border-indigo-300 border w-10 h-10 rounded-full flex justify-center items-center">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                        </div>
                        <div class="analytics_1"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-6">
            <div class="card-header flex flex-row justify-between">
                <h1 class="h6">Bản Ghi Đã Duyệt Gần Đây</h1>
                <div class="flex flex-row justify-center items-center">
                    <a href="{{ route('admin.financial.index') }}">
                        <i class="fas fa-chevron-double-down mr-6"></i>
                    </a>
                    <a href="{{ route('admin.financial.index') }}">
                        <i class="fas fa-ellipsis-v"></i>
                    </a>
                </div>
            </div>

            <div class="card-body grid grid-cols-2 gap-6 lg:grid-cols-1">
                <div class="p-8 bg-white shadow-md rounded-lg">
                    <h1 class="text-2xl font-bold">{{ number_format($total_revenue, 2) }} VNĐ</h1>
                    <p class="text-gray-700 font-medium">Tổng Doanh Thu Đã Duyệt</p>
                    <div class="mt-20 mb-2 flex items-center">
                        <div class="py-1 px-3 rounded bg-green-200 text-green-900 mr-3">
                            <i class="fa fa-caret-up"></i>
                        </div>
                        <p class="text-gray-700"><span class="text-green-500">{{ number_format($avg_roas, 2) }}%</span>
                            ROAS trung bình trong tháng này.</p>
                    </div>
                    <a href="{{ route('admin.financial.index') }}"
                        class="mt-6 inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Xem tất cả bản
                        ghi</a>
                </div>
                <div class="bg-white shadow-md rounded-lg p-4">
                    <canvas id="platformPieChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-6 mt-6 xl:grid-cols-1">
            <div class="card">
                <div class="card-header">Thống Kê Nền Tảng</div>
                @foreach ($recent_records->groupBy('platform.name') as $platform => $records)
                    <div
                        class="p-6 flex flex-row justify-between items-center text-gray-600 border-b {{ $loop->last ? 'border-b-0' : '' }}">
                        <div class="flex items-center">
                            <i class="fas fa-store mr-4"></i>
                            <h1>{{ $platform }}</h1>
                        </div>
                        <div>
                            {{ number_format($records->sum('revenue') / 1000000, 2) }}M VNĐ
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card col-span-2 xl:col-span-1">
                <div class="card-header">Bản Ghi Đã Duyệt Gần Đây</div>
                <table class="table-auto w-full text-left">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 border-r"></th>
                            <th class="px-4 py-2 border-r">Nền Tảng</th>
                            <th class="px-4 py-2 border-r">Doanh Thu</th>
                            <th class="px-4 py-2">Ngày</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600">
                        @foreach ($recent_records as $record)
                            <tr>
                                <td class="border border-l-0 px-4 py-2 text-center text-green-500"><i
                                        class="fas fa-circle"></i></td>
                                <td class="border border-l-0 px-4 py-2">{{ $record->platform->name }}</td>
                                <td class="border border-l-0 px-4 py-2">{{ number_format($record->revenue, 2) }} VNĐ</td>
                                <td class="border border-l-0 border-r-0 px-4 py-2">
                                    {{ $record->record_date ? \Carbon\Carbon::parse($record->record_date)->diffForHumans(['parts' => 1, 'short' => true]) : 'Không có ngày' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Thêm Chart.js từ CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dữ liệu từ PHP được truyền vào JavaScript
            const revenueData = @json($monthly_revenue);
            const totalRevenue = {{ $total_revenue ?? 0 }};
            const platformData = @json($recent_records->groupBy('platform.name')->map->sum('revenue')->toArray());

            // Khởi tạo biểu đồ cột
            let barChart = null;
            const barCtx = document.getElementById('sealsOverview').getContext('2d');

            function updateBarChart(yearFilter, timeFilter) {
                let filteredData = [];
                const currentDate = new Date();
                const currentYear = currentDate.getFullYear();
                const currentMonth = currentDate.getMonth() + 1;

                // Lọc theo năm trước
                if (yearFilter !== 'all') {
                    filteredData = revenueData.filter(item => {
                        const [year] = item.month.split('-');
                        return parseInt(year) === parseInt(yearFilter);
                    });
                } else {
                    filteredData = revenueData;
                }

                // Lọc theo khoảng thời gian (nếu có)
                if (timeFilter !== 'all') {
                    switch (timeFilter) {
                        case 'month':
                            filteredData = filteredData.filter(item => {
                                const [year, month] = item.month.split('-');
                                return parseInt(year) === currentYear && parseInt(month) === currentMonth;
                            });
                            break;
                        case '3months':
                            // Tính khoảng 3 tháng gần nhất (bao gồm tháng hiện tại và 1 tháng sau)
                            const startMonth = currentMonth - 1; // Bắt đầu từ tháng trước
                            const endMonth = currentMonth + 1; // Kết thúc ở tháng sau
                            const startYear = currentYear;
                            const endYear = currentYear;

                            filteredData = filteredData.filter(item => {
                                const [year, month] = item.month.split('-');
                                const itemYear = parseInt(year);
                                const itemMonth = parseInt(month);

                                if (itemYear === startYear) {
                                    return itemMonth >= startMonth && itemMonth <= endMonth;
                                }
                                return false;
                            });
                            break;
                    }
                }

                // Cập nhật hoặc khởi tạo biểu đồ cột
                if (barChart) {
                    barChart.destroy();
                }
                barChart = new Chart(barCtx, {
                    type: 'bar',
                    data: {
                        labels: filteredData.map(item => item.month),
                        datasets: [{
                            label: 'Tổng Doanh Thu (VNĐ)',
                            data: filteredData.map(item => item.total_revenue),
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Doanh Thu (VNĐ)'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Tháng'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            title: {
                                display: true,
                                text: 'Tổng Doanh Thu Đã Duyệt Theo Thời Gian'
                            }
                        }
                    }
                });
            }

            // Khởi tạo biểu đồ tròn
            const pieCtx = document.getElementById('platformPieChart').getContext('2d');
            const pieChart = new Chart(pieCtx, {
                type: 'pie',
                data: {
                    labels: Object.keys(platformData),
                    datasets: [{
                        label: 'Doanh Thu Theo Nền Tảng (VNĐ)',
                        data: Object.values(platformData),
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.6)',
                            'rgba(54, 162, 235, 0.6)',
                            'rgba(255, 206, 86, 0.6)',
                            'rgba(75, 192, 192, 0.6)',
                            'rgba(153, 102, 255, 0.6)',
                            'rgba(255, 159, 64, 0.6)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        title: {
                            display: true,
                            text: 'Phân Bổ Doanh Thu Theo Nền Tảng'
                        }
                    }
                }
            });

            // Khởi tạo biểu đồ cột với dữ liệu mặc định
            updateBarChart('all', 'all');

            // Lắng nghe sự kiện thay đổi bộ lọc
            document.getElementById('yearFilter').addEventListener('change', function(e) {
                const timeFilter = document.getElementById('timeFilter').value;
                updateBarChart(e.target.value, timeFilter);
            });

            document.getElementById('timeFilter').addEventListener('change', function(e) {
                const yearFilter = document.getElementById('yearFilter').value;
                updateBarChart(yearFilter, e.target.value);
            });
        });
    </script>
@endsection
