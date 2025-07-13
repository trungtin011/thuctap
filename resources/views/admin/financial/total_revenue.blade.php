@extends('layouts.admin')
@section('title', 'Báo Cáo Tổng Quan')
@section('content')
    <div class="bg-gray-100 flex-1 p-6">
        <div class="grid grid-cols-4 gap-6 xl:grid-cols-1">
            <!-- Tổng doanh thu -->
            <div class="report-card">
                <div class="card">
                    <div class="card-body flex flex-col">
                        <div class="flex flex-row justify-between items-center">
                            <div class="h6 text-indigo-700 fas fa-money-bill-wave"></div>
                            <span class="rounded-full text-white badge bg-teal-400 text-xs">
                                {{ number_format($totalRevenue / 1000000, 2) }}M
                                <i class="fas fa-chevron-up ml-1"></i>
                            </span>
                        </div>
                        <div class="mt-8">
                            <h1 class="h5">{{ number_format($totalRevenue, 2) }} VNĐ</h1>
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
                                @if ($totalExpenses > 0)
                                    {{ number_format($totalExpenses / 1000000, 2) }}M
                                    <i class="fas fa-chevron-down ml-1"></i>
                                @else
                                    0 M
                                    <i class="fas fa-minus ml-1"></i>
                                @endif
                            </span>
                        </div>
                        <div class="mt-8">
                            <h1 class="h5">
                                @if ($totalExpenses > 0)
                                    {{ number_format($totalExpenses) }} VNĐ
                                @else
                                    0 VNĐ
                                @endif
                            </h1>
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
                                {{ $avgRoas ? number_format($avgRoas, 2) : 'N/A' }}
                                <i class="fas fa-chevron-up ml-1"></i>
                            </span>
                        </div>
                        <div class="mt-8">
                            <h1 class="h5">{{ $avgRoas ? number_format($avgRoas, 2) : 'N/A' }}</h1>
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
                                {{ $recordCount }}
                                <i class="fas fa-chevron-up ml-1"></i>
                            </span>
                        </div>
                        <div class="mt-8">
                            <h1 class="h5">{{ $recordCount }}</h1>
                            <p>Bản Ghi Đã Duyệt</p>
                        </div>
                    </div>
                </div>
                <div class="footer bg-white p-1 mx-4 border border-t-0 rounded rounded-t-none"></div>
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
                        <p class="text-white text-xs">Bạn đã duyệt {{ $recordCount }} bản ghi tài chính trong tháng này.
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
                                {{ number_format($totalRevenue / 1000000, 2) }}M VNĐ
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
                                {{ number_format($totalExpenses / 1000000, 2) }}M VNĐ
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

        <!-- Form lọc và biểu đồ -->
        <div class="mt-6">
            <form method="GET" action="{{ route('admin.financial.total_revenue') }}"
                style="display: flex; flex-wrap:wrap; gap: 16px; align-items: center;">
                <div class="border border-dashed border-gray-400 p-2">
                    <label for="start_date">Từ ngày:</label>
                    <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}">
                </div>
                <div class="border border-dashed border-gray-400 p-2">
                    <label for="end_date">Đến ngày:</label>
                    <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}">
                </div>
                <div class="border border-dashed border-gray-400 p-2">
                    <label for="platform">Nền tảng:</label>
                    <select id="platform" name="platform_id">
                        <option value="">Tất cả</option>
                        @foreach ($platforms as $platform)
                            <option value="{{ $platform->id }}"
                                {{ request('platform_id') == $platform->id ? 'selected' : '' }}>
                                {{ $platform->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="border border-dashed border-gray-400 p-2">
                    <label for="yearFilter">Chọn năm:</label>
                    <select id="yearFilter" name="year">
                        <option value="all" {{ request('year', 'all') == 'all' ? 'selected' : '' }}>Tất cả năm</option>
                        @foreach ($years as $year)
                            <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                {{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="border border-dashed border-gray-400 p-2">
                    <label for="timeFilter">Chọn khoảng thời gian:</label>
                    <select id="timeFilter" name="timeFilter">
                        <option value="all" {{ request('timeFilter', 'all') == 'all' ? 'selected' : '' }}>Tất cả
                        </option>
                        <option value="month" {{ request('timeFilter') == 'month' ? 'selected' : '' }}>Tháng hiện tại
                        </option>
                        <option value="3months" {{ request('timeFilter') == '3months' ? 'selected' : '' }}>3 tháng gần
                            nhất</option>
                    </select>
                </div>
                <button type="submit"
                    style="padding: 8px 20px; background: #6d6c6c; color: #fff; border: none;">Lọc</button>
            </form>

            <!-- Biểu đồ so sánh và biểu đồ tròn -->
            <div class="grid grid-cols-3 gap-6 mt-6 xl:grid-cols-1">
                <!-- Cột 1: Biểu đồ đường (revenueExpenseChart) -->
                <div class="bg-white rounded-lg shadow-md p-6 col-span-2 relative">
                    @if (!empty($labels) && !empty($data) && array_sum($data) > 0)
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Xu Hướng Doanh Thu, Chi Phí và ROAS</h3>
                            <div class="relative">
                                <button id="chartOptionsBtn" class="text-gray-600 hover:text-gray-800 focus:outline-none">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div id="chartOptions"
                                    class="hidden absolute right-0 mt-2 w-40 bg-white border rounded shadow-lg z-10">
                                    <ul class="py-1">
                                        <li><a href="#" class="block px-4 py-2 text-sm hover:bg-gray-100"
                                                data-type="bar">Biểu đồ cột</a></li>
                                        <li><a href="#" class="block px-4 py-2 text-sm hover:bg-gray-100"
                                                data-type="line">Biểu đồ đường</a></li>
                                        <li><a href="#" class="block px-4 py-2 text-sm hover:bg-gray-100"
                                                data-type="pie">Biểu đồ tròn</a></li>
                                        <li><a href="#" class="block px-4 py-2 text-sm hover:bg-gray-100"
                                                data-type="mixed">Kết hợp (Cột + Đường)</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <canvas id="revenueExpenseChart" height="100"></canvas>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const ctx = document.getElementById('revenueExpenseChart').getContext('2d');
                                let chart = new Chart(ctx, {
                                    type: 'line',
                                    data: {
                                        labels: {!! json_encode($labels) !!},
                                        datasets: [{
                                                label: 'Doanh Thu',
                                                data: {!! json_encode($data) !!},
                                                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                                                borderColor: 'rgba(75, 192, 192, 1)',
                                                borderWidth: 2,
                                                fill: true,
                                                tension: 0.4,
                                            },
                                            {
                                                label: 'Chi Phí',
                                                data: {!! json_encode($expenseData ?? array_fill(0, count($labels), 0)) !!},
                                                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                                                borderColor: 'rgba(220, 53, 69, 1)',
                                                borderWidth: 2,
                                                fill: true,
                                                tension: 0.4,
                                            },
                                            {
                                                label: 'ROAS',
                                                data: {!! json_encode($roasData ?? array_fill(0, count($labels), 0)) !!},
                                                backgroundColor: 'rgba(255, 206, 86, 0.1)',
                                                borderColor: 'rgba(255, 206, 86, 1)',
                                                borderWidth: 2,
                                                fill: false,
                                                tension: 0.4,
                                                yAxisID: 'y1',
                                            }
                                        ]
                                    },
                                    options: {
                                        responsive: true,
                                        plugins: {
                                            legend: {
                                                display: true
                                            },
                                            title: {
                                                display: true,
                                                text: 'Xu Hướng Doanh Thu, Chi Phí và ROAS Theo Thời Gian'
                                            }
                                        },
                                        scales: {
                                            x: {
                                                grid: {
                                                    display: false
                                                },
                                                title: {
                                                    display: true,
                                                    text: 'Ngày'
                                                }
                                            },
                                            y: {
                                                beginAtZero: true,
                                                ticks: {
                                                    callback: function(value) {
                                                        return value.toLocaleString('vi-VN');
                                                    }
                                                },
                                                title: {
                                                    display: true,
                                                    text: 'Số Tiền (VNĐ)'
                                                }
                                            },
                                            y1: {
                                                position: 'right',
                                                beginAtZero: true,
                                                title: {
                                                    display: true,
                                                    text: 'ROAS'
                                                },
                                                grid: {
                                                    drawOnChartArea: false
                                                }
                                            }
                                        }
                                    }
                                });

                                // Xử lý nút 3 chấm và dropdown
                                const chartOptionsBtn = document.getElementById('chartOptionsBtn');
                                const chartOptions = document.getElementById('chartOptions');

                                chartOptionsBtn.addEventListener('click', function(e) {
                                    e.preventDefault();
                                    chartOptions.classList.toggle('hidden');
                                });

                                // Xử lý chọn kiểu biểu đồ
                                document.querySelectorAll('#chartOptions a').forEach(item => {
                                    item.addEventListener('click', function(e) {
                                        e.preventDefault();
                                        const chartType = this.getAttribute('data-type');
                                        updateChart(chartType);
                                        chartOptions.classList.add('hidden');
                                    });
                                });

                                function updateChart(type) {
                                    chart.destroy();
                                    let newChart;

                                    if (type === 'mixed') {
                                        newChart = new Chart(ctx, {
                                            type: 'bar',
                                            data: {
                                                labels: {!! json_encode($labels) !!},
                                                datasets: [{
                                                        type: 'bar',
                                                        label: 'Doanh Thu',
                                                        data: {!! json_encode($data) !!},
                                                        backgroundColor: 'rgba(75, 192, 192, 0.3)',
                                                        borderColor: 'rgba(75, 192, 192, 1)',
                                                        borderWidth: 2,
                                                        borderRadius: 6,
                                                        maxBarThickness: 36,
                                                    },
                                                    {
                                                        type: 'line',
                                                        label: 'Chi Phí',
                                                        data: {!! json_encode($expenseData ?? array_fill(0, count($labels), 0)) !!},
                                                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                                                        borderColor: 'rgba(220, 53, 69, 1)',
                                                        borderWidth: 2,
                                                        fill: true,
                                                        tension: 0.4,
                                                    },
                                                    {
                                                        type: 'line',
                                                        label: 'ROAS',
                                                        data: {!! json_encode($roasData ?? array_fill(0, count($labels), 0)) !!},
                                                        backgroundColor: 'rgba(255, 206, 86, 0.1)',
                                                        borderColor: 'rgba(255, 206, 86, 1)',
                                                        borderWidth: 2,
                                                        fill: false,
                                                        tension: 0.4,
                                                        yAxisID: 'y1',
                                                    }
                                                ]
                                            },
                                            options: {
                                                responsive: true,
                                                plugins: {
                                                    legend: {
                                                        display: true
                                                    },
                                                    title: {
                                                        display: true,
                                                        text: 'Kết Hợp Doanh Thu, Chi Phí và ROAS'
                                                    }
                                                },
                                                scales: {
                                                    x: {
                                                        grid: {
                                                            display: false
                                                        },
                                                        title: {
                                                            display: true,
                                                            text: 'Ngày'
                                                        }
                                                    },
                                                    y: {
                                                        beginAtZero: true,
                                                        ticks: {
                                                            callback: function(value) {
                                                                return value.toLocaleString('vi-VN');
                                                            }
                                                        },
                                                        title: {
                                                            display: true,
                                                            text: 'Số Tiền (VNĐ)'
                                                        }
                                                    },
                                                    y1: {
                                                        position: 'right',
                                                        beginAtZero: true,
                                                        title: {
                                                            display: true,
                                                            text: 'ROAS'
                                                        },
                                                        grid: {
                                                            drawOnChartArea: false
                                                        }
                                                    }
                                                }
                                            }
                                        });
                                    } else {
                                        newChart = new Chart(ctx, {
                                            type: type,
                                            data: {
                                                labels: {!! json_encode($labels) !!},
                                                datasets: [{
                                                        label: 'Doanh Thu',
                                                        data: {!! json_encode($data) !!},
                                                        backgroundColor: type === 'pie' ? 'rgba(75, 192, 192, 0.6)' :
                                                            'rgba(75, 192, 192, 0.1)',
                                                        borderColor: type === 'pie' ? 'rgba(75, 192, 192, 1)' :
                                                            'rgba(75, 192, 192, 1)',
                                                        borderWidth: 2,
                                                        fill: type !== 'pie',
                                                        tension: type === 'line' ? 0.4 : 0,
                                                    },
                                                    {
                                                        label: 'Chi Phí',
                                                        data: {!! json_encode($expenseData ?? array_fill(0, count($labels), 0)) !!},
                                                        backgroundColor: type === 'pie' ? 'rgba(220, 53, 69, 0.6)' :
                                                            'rgba(220, 53, 69, 0.1)',
                                                        borderColor: type === 'pie' ? 'rgba(220, 53, 69, 1)' :
                                                            'rgba(220, 53, 69, 1)',
                                                        borderWidth: 2,
                                                        fill: type !== 'pie',
                                                        tension: type === 'line' ? 0.4 : 0,
                                                    },
                                                    {
                                                        label: 'ROAS',
                                                        data: {!! json_encode($roasData ?? array_fill(0, count($labels), 0)) !!},
                                                        backgroundColor: type === 'pie' ? 'rgba(255, 206, 86, 0.6)' :
                                                            'rgba(255, 206, 86, 0.1)',
                                                        borderColor: type === 'pie' ? 'rgba(255, 206, 86, 1)' :
                                                            'rgba(255, 206, 86, 1)',
                                                        borderWidth: 2,
                                                        fill: type !== 'pie',
                                                        tension: type === 'line' ? 0.4 : 0,
                                                        yAxisID: type !== 'pie' ? 'y1' : undefined,
                                                    }
                                                ]
                                            },
                                            options: {
                                                responsive: true,
                                                plugins: {
                                                    legend: {
                                                        display: true
                                                    },
                                                    title: {
                                                        display: true,
                                                        text: type === 'pie' ? 'Phân Bổ Doanh Thu, Chi Phí và ROAS' :
                                                            'Xu Hướng Doanh Thu, Chi Phí và ROAS'
                                                    }
                                                },
                                                scales: type === 'pie' ? {} : {
                                                    x: {
                                                        grid: {
                                                            display: false
                                                        },
                                                        title: {
                                                            display: true,
                                                            text: 'Ngày'
                                                        }
                                                    },
                                                    y: {
                                                        beginAtZero: true,
                                                        ticks: {
                                                            callback: function(value) {
                                                                return value.toLocaleString('vi-VN');
                                                            }
                                                        },
                                                        title: {
                                                            display: true,
                                                            text: 'Số Tiền (VNĐ)'
                                                        }
                                                    },
                                                    y1: {
                                                        position: 'right',
                                                        beginAtZero: true,
                                                        title: {
                                                            display: true,
                                                            text: 'ROAS'
                                                        },
                                                        grid: {
                                                            drawOnChartArea: false
                                                        }
                                                    }
                                                }
                                            }
                                        });
                                    }
                                    chart = newChart;
                                }

                                document.addEventListener('click', function(e) {
                                    if (!chartOptionsBtn.contains(e.target) && !chartOptions.contains(e.target)) {
                                        chartOptions.classList.add('hidden');
                                    }
                                });
                            });
                        </script>
                    @else
                        <div style="color: #888; margin-top: 32px; text-align: center;">
                            Không có dữ liệu doanh thu cho khoảng thời gian này.
                        </div>
                    @endif

                    <!-- Thêm phần hiển thị dữ liệu theo bộ lọc -->
                    <div class="mt-6 grid grid-cols-4 gap-6 xl:grid-cols-1">
                        <!-- Tổng doanh thu theo bộ lọc -->
                        <div class="report-card">
                            <div class="card">
                                <div class="card-body flex flex-col">
                                    <span class="font-bold"
                                        style="color: #4C51BF">{{ number_format($filteredTotalRevenue, 2) }} VNĐ</span>
                                    <p>Tổng Doanh Thu (Theo Bộ Lọc)</p>
                                </div>
                            </div>
                            <div class="footer bg-white p-1 mx-4 border border-t-0 rounded rounded-t-none"></div>
                        </div>

                        <!-- Tổng chi phí theo bộ lọc -->
                        <div class="report-card">
                            <div class="card">
                                <div class="card-body flex flex-col">
                                    <span class="font-bold"
                                        style="color: #C53030">{{ number_format($filteredTotalExpenses, 2) }} VNĐ</span>
                                    <p>Tổng Chi Phí (Theo Bộ Lọc)</p>
                                </div>
                            </div>
                            <div class="footer bg-white p-1 mx-4 border border-t-0 rounded rounded-t-none"></div>
                        </div>

                        <!-- ROAS trung bình theo bộ lọc -->
                        <div class="report-card">
                            <div class="card">
                                <div class="card-body flex flex-col">
                                    <span class="font-bold"
                                        style="color: #D69E2E">{{ $filteredAvgRoas ? number_format($filteredAvgRoas, 2) : 'N/A' }}</span>
                                    <p>ROAS Trung Bình (Theo Bộ Lọc)</p>
                                </div>
                            </div>
                            <div class="footer bg-white p-1 mx-4 border border-t-0 rounded rounded-t-none"></div>
                        </div>

                        <!-- Số lượng bản ghi theo bộ lọc -->
                        <div class="report-card">
                            <div class="card">
                                <div class="card-body flex flex-col">
                                    <span class="font-bold" style="color: #2F855A">{{ $filteredRecordCount }}</span>
                                    <p>Bản Ghi Đã Duyệt (Theo Bộ Lọc)</p>
                                </div>
                            </div>
                            <div class="footer bg-white p-1 mx-4 border border-t-0 rounded rounded-t-none"></div>
                        </div>
                    </div>
                </div>

                <!-- Cột 2: Biểu đồ tròn (platformPieChart) -->
                <div class="bg-white rounded-lg shadow-md p-6 flex justify-center col-span-1">
                    <canvas id="platformPieChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- So sánh doanh thu theo tháng -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 mt-8 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="bg-white bg-opacity-20 rounded-lg p-2 mr-4">
                            <i class="fas fa-chart-line text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-white text-xl font-bold">So Sánh Doanh Thu Theo Tháng</h3>
                            <p class="text-blue-100 text-sm">Phân tích hiệu suất kinh doanh</p>
                        </div>
                    </div>
                    <div class="hidden md:block">
                        <div class="bg-white bg-opacity-20 rounded-lg px-3 py-1">
                            <span class="text-white text-sm font-medium">Báo cáo chi tiết</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 bg-gray-50 border-b border-gray-200">
                <form id="compareMonthsForm" method="GET" action="{{ route('admin.financial.total_revenue') }}">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">
                                <i class="fas fa-calendar-alt mr-2 text-blue-500"></i> Tháng thứ nhất
                            </label>
                            <select name="compare_month1"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white">
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}"
                                        {{ request('compare_month1', 1) == $i ? 'selected' : '' }}>Tháng
                                        {{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">
                                <i class="fas fa-calendar-alt mr-2 text-green-500"></i> Tháng thứ hai
                            </label>
                            <select name="compare_month2"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 bg-white">
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}"
                                        {{ request('compare_month2', 2) == $i ? 'selected' : '' }}>Tháng
                                        {{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">
                                <i class="fas fa-calendar mr-2 text-purple-500"></i> Năm
                            </label>
                            <select name="compare_year"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white">
                                @foreach ($years as $year)
                                    <option value="{{ $year }}"
                                        {{ request('compare_year', date('Y')) == $year ? 'selected' : '' }}>
                                        {{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <button type="submit"
                                class="bg-black text-white px-6 py-3 rounded-lg font-medium hover:from-blue-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                                So sánh
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Kết quả so sánh -->
            @if (isset($monthlyComparison))
                <div class="p-6">
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-2">Kết quả so sánh</h4>
                        <div class="flex items-center space-x-4 text-sm text-gray-600">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                                <span>{{ $monthlyComparison['month1']['name'] }} {{ $monthlyComparison['year'] }}</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                                <span>{{ $monthlyComparison['month2']['name'] }} {{ $monthlyComparison['year'] }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-row gap-3 mb-6 overflow-x-auto">
                        <!-- Doanh thu -->
                        <div
                            class="min-w-[220px] flex-1 bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-3 border border-green-200 flex-shrink-0 flex flex-col items-stretch">
                            <div class="flex items-center justify-between w-full mb-2">
                                <div class="bg-green-500 rounded p-1">
                                    <i class="fas fa-dollar-sign text-white text-base"></i>
                                </div>
                                <span class="text-xs font-medium text-green-600">Doanh thu</span>
                            </div>
                            <div class="w-full space-y-1">
                                <div class="flex justify-between items-center w-full text-xs">
                                    <span class="text-gray-600">{{ $monthlyComparison['month1']['name'] }}</span>
                                    <span class="font-semibold text-gray-800 text-right">
                                        @if ($monthlyComparison['month1']['revenue'] > 0)
                                            {{ number_format($monthlyComparison['month1']['revenue']) }} ₫
                                        @else
                                            0 ₫
                                        @endif
                                    </span>
                                </div>
                                <div class="flex justify-between items-center w-full text-xs">
                                    <span class="text-gray-600">{{ $monthlyComparison['month2']['name'] }}</span>
                                    <span class="font-semibold text-gray-800 text-right">
                                        @if ($monthlyComparison['month2']['revenue'] > 0)
                                            {{ number_format($monthlyComparison['month2']['revenue']) }} ₫
                                        @else
                                            0 ₫
                                        @endif
                                    </span>
                                </div>
                                <div class="pt-1 border-t border-green-200 mt-1 w-full">
                                    <div class="flex justify-between items-center w-full text-xs">
                                        <span class="font-medium">Chênh lệch</span>
                                        <span
                                            class="font-bold {{ $monthlyComparison['difference']['revenue'] >= 0 ? 'text-green-600' : 'text-red-600' }} text-right">
                                            {{ $monthlyComparison['difference']['revenue'] >= 0 ? '+' : '' }}{{ number_format($monthlyComparison['difference']['revenue']) }}
                                            ₫
                                        </span>
                                    </div>
                                    @if ($monthlyComparison['month1']['revenue'] > 0)
                                        <div class="text-[10px] text-gray-500 mt-0.5 text-right w-full">
                                            {{ number_format(($monthlyComparison['difference']['revenue'] / $monthlyComparison['month1']['revenue']) * 100, 2) }}%
                                            so với {{ $monthlyComparison['month1']['name'] }}
                                        </div>
                                    @else
                                        <div class="text-[10px] text-gray-500 mt-0.5 text-right w-full">N/A</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Chi phí -->
                        <div
                            class="min-w-[220px] flex-1 bg-gradient-to-br from-red-50 to-red-100 rounded-lg p-3 border border-red-200 flex-shrink-0 flex flex-col items-stretch">
                            <div class="flex items-center justify-between w-full mb-2">
                                <div class="bg-red-500 rounded p-1">
                                    <i class="fas fa-receipt text-white text-base"></i>
                                </div>
                                <span class="text-xs font-medium text-red-600">Chi phí</span>
                            </div>
                            <div class="w-full space-y-1">
                                <div class="flex justify-between items-center w-full text-xs">
                                    <span class="text-gray-600">{{ $monthlyComparison['month1']['name'] }}</span>
                                    <span class="font-semibold text-gray-800 text-right">
                                        @if ($monthlyComparison['month1']['expenses'] > 0)
                                            {{ number_format($monthlyComparison['month1']['expenses']) }} ₫
                                        @else
                                            0 ₫
                                        @endif
                                    </span>
                                </div>
                                <div class="flex justify-between items-center w-full text-xs">
                                    <span class="text-gray-600">{{ $monthlyComparison['month2']['name'] }}</span>
                                    <span class="font-semibold text-gray-800 text-right">
                                        @if ($monthlyComparison['month2']['expenses'] > 0)
                                            {{ number_format($monthlyComparison['month2']['expenses']) }} ₫
                                        @else
                                            0 ₫
                                        @endif
                                    </span>
                                </div>
                                <div class="pt-1 border-t border-red-200 mt-1 w-full">
                                    <div class="flex justify-between items-center w-full text-xs">
                                        <span class="font-medium">Chênh lệch</span>
                                        <span
                                            class="font-bold {{ $monthlyComparison['difference']['expenses'] >= 0 ? 'text-red-600' : 'text-green-600' }} text-right">
                                            {{ $monthlyComparison['difference']['expenses'] >= 0 ? '+' : '' }}{{ number_format($monthlyComparison['difference']['expenses']) }}
                                            ₫
                                        </span>
                                    </div>
                                    @if ($monthlyComparison['month1']['expenses'] > 0)
                                        <div class="text-[10px] text-gray-500 mt-0.5 text-right w-full">
                                            {{ number_format(($monthlyComparison['difference']['expenses'] / $monthlyComparison['month1']['expenses']) * 100, 2) }}%
                                            so với {{ $monthlyComparison['month1']['name'] }}
                                        </div>
                                    @else
                                        <div class="text-[10px] text-gray-500 mt-0.5 text-right w-full">N/A</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- ROAS -->
                        <div
                            class="min-w-[220px] flex-1 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-3 border border-blue-200 flex-shrink-0 flex flex-col items-stretch">
                            <div class="flex items-center justify-between w-full mb-2">
                                <div class="bg-blue-500 rounded p-1">
                                    <i class="fas fa-chart-line text-white text-base"></i>
                                </div>
                                <span class="text-xs font-medium text-blue-600">ROAS</span>
                            </div>
                            <div class="w-full space-y-1">
                                <div class="flex justify-between items-center w-full text-xs">
                                    <span class="text-gray-600">{{ $monthlyComparison['month1']['name'] }}</span>
                                    <span class="font-semibold text-gray-800 text-right">
                                        @if ($monthlyComparison['month1']['roas'] > 0)
                                            {{ number_format($monthlyComparison['month1']['roas'], 2) }}
                                        @else
                                            0
                                        @endif
                                    </span>
                                </div>
                                <div class="flex justify-between items-center w-full text-xs">
                                    <span class="text-gray-600">{{ $monthlyComparison['month2']['name'] }}</span>
                                    <span class="font-semibold text-gray-800 text-right">
                                        @if ($monthlyComparison['month2']['roas'] > 0)
                                            {{ number_format($monthlyComparison['month2']['roas'], 2) }}
                                        @else
                                            0
                                        @endif
                                    </span>
                                </div>
                                <div class="pt-1 border-t border-blue-200 mt-1 w-full">
                                    <div class="flex justify-between items-center w-full text-xs">
                                        <span class="font-medium">Chênh lệch</span>
                                        <span
                                            class="font-bold {{ $monthlyComparison['difference']['roas'] >= 0 ? 'text-blue-600' : 'text-red-600' }} text-right">
                                            {{ $monthlyComparison['difference']['roas'] >= 0 ? '+' : '' }}{{ number_format($monthlyComparison['difference']['roas'], 2) }}
                                        </span>
                                    </div>
                                    @if ($monthlyComparison['month1']['roas'] > 0)
                                        <div class="text-[10px] text-gray-500 mt-0.5 text-right w-full">
                                            {{ number_format(($monthlyComparison['difference']['roas'] / $monthlyComparison['month1']['roas']) * 100, 2) }}%
                                            so với {{ $monthlyComparison['month1']['name'] }}
                                        </div>
                                    @else
                                        <div class="text-[10px] text-gray-500 mt-0.5 text-right w-full">N/A</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Hoa hồng -->
                        <div
                            class="min-w-[220px] flex-1 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-3 border border-purple-200 flex-shrink-0 flex flex-col items-stretch">
                            <div class="flex items-center justify-between w-full mb-2">
                                <div class="bg-purple-500 rounded p-1">
                                    <i class="fas fa-hand-holding-usd text-white text-base"></i>
                                </div>
                                <span class="text-xs font-medium text-purple-600">Hoa hồng</span>
                            </div>
                            <div class="w-full space-y-1">
                                <div class="flex justify-between items-center w-full text-xs">
                                    <span class="text-gray-600">{{ $monthlyComparison['month1']['name'] }}</span>
                                    <span class="font-semibold text-gray-800 text-right">
                                        @if ($monthlyComparison['month1']['commission'] > 0)
                                            {{ number_format($monthlyComparison['month1']['commission']) }} ₫
                                        @else
                                            0 ₫
                                        @endif
                                    </span>
                                </div>
                                <div class="flex justify-between items-center w-full text-xs">
                                    <span class="text-gray-600">{{ $monthlyComparison['month2']['name'] }}</span>
                                    <span class="font-semibold text-gray-800 text-right">
                                        @if ($monthlyComparison['month2']['commission'] > 0)
                                            {{ number_format($monthlyComparison['month2']['commission']) }} ₫
                                        @else
                                            0 ₫
                                        @endif
                                    </span>
                                </div>
                                <div class="pt-1 border-t border-purple-200 mt-1 w-full">
                                    <div class="flex justify-between items-center w-full text-xs">
                                        <span class="font-medium">Chênh lệch</span>
                                        <span
                                            class="font-bold {{ $monthlyComparison['difference']['commission'] >= 0 ? 'text-purple-600' : 'text-red-600' }} text-right">
                                            {{ $monthlyComparison['difference']['commission'] >= 0 ? '+' : '' }}{{ number_format($monthlyComparison['difference']['commission']) }}
                                            ₫
                                        </span>
                                    </div>
                                    @if ($monthlyComparison['month1']['commission'] > 0)
                                        <div class="text-[10px] text-gray-500 mt-0.5 text-right w-full">
                                            {{ number_format(($monthlyComparison['difference']['commission'] / $monthlyComparison['month1']['commission']) * 100, 2) }}%
                                            so với {{ $monthlyComparison['month1']['name'] }}
                                        </div>
                                    @else
                                        <div class="text-[10px] text-gray-500 mt-0.5 text-right w-full">N/A</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Biểu đồ so sánh tháng -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <canvas id="monthlyComparisonChart" height="100"></canvas>
                    </div>

                    <!-- Bảng chi tiết -->
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                            <h5 class="font-semibold text-gray-800">Bảng so sánh chi tiết</h5>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Chỉ số</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $monthlyComparison['month1']['name'] }}</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $monthlyComparison['month2']['name'] }}</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Chênh lệch</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            % Thay đổi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                                                <span class="font-medium text-gray-900">Doanh thu</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if ($monthlyComparison['month1']['revenue'] > 0)
                                                {{ number_format($monthlyComparison['month1']['revenue']) }} ₫
                                            @else
                                                0 ₫
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if ($monthlyComparison['month2']['revenue'] > 0)
                                                {{ number_format($monthlyComparison['month2']['revenue']) }} ₫
                                            @else
                                                0 ₫
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="font-medium {{ $monthlyComparison['difference']['revenue'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $monthlyComparison['difference']['revenue'] >= 0 ? '+' : '' }}{{ number_format($monthlyComparison['difference']['revenue']) }}
                                                ₫
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($monthlyComparison['month1']['revenue'] > 0)
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($monthlyComparison['difference']['revenue'] / $monthlyComparison['month1']['revenue']) * 100 >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ number_format(($monthlyComparison['difference']['revenue'] / $monthlyComparison['month1']['revenue']) * 100, 2) }}%
                                                </span>
                                            @else
                                                <span class="text-gray-400">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-2 h-2 bg-red-500 rounded-full mr-3"></div>
                                                <span class="font-medium text-gray-900">Chi phí</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if ($monthlyComparison['month1']['expenses'] > 0)
                                                {{ number_format($monthlyComparison['month1']['expenses']) }} ₫
                                            @else
                                                0 ₫
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if ($monthlyComparison['month2']['expenses'] > 0)
                                                {{ number_format($monthlyComparison['month2']['expenses']) }} ₫
                                            @else
                                                0 ₫
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="font-medium {{ $monthlyComparison['difference']['expenses'] >= 0 ? 'text-red-600' : 'text-green-600' }}">
                                                {{ $monthlyComparison['difference']['expenses'] >= 0 ? '+' : '' }}{{ number_format($monthlyComparison['difference']['expenses']) }}
                                                ₫
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($monthlyComparison['month1']['expenses'] > 0)
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($monthlyComparison['difference']['expenses'] / $monthlyComparison['month1']['expenses']) * 100 >= 0 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                    {{ number_format(($monthlyComparison['difference']['expenses'] / $monthlyComparison['month1']['expenses']) * 100, 2) }}%
                                                </span>
                                            @else
                                                <span class="text-gray-400">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                                                <span class="font-medium text-gray-900">ROAS</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if ($monthlyComparison['month1']['roas'] > 0)
                                                {{ number_format($monthlyComparison['month1']['roas'], 2) }}
                                            @else
                                                0
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if ($monthlyComparison['month2']['roas'] > 0)
                                                {{ number_format($monthlyComparison['month2']['roas'], 2) }}
                                            @else
                                                0
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="font-medium {{ $monthlyComparison['difference']['roas'] >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                                                {{ $monthlyComparison['difference']['roas'] >= 0 ? '+' : '' }}{{ number_format($monthlyComparison['difference']['roas'], 2) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($monthlyComparison['month1']['roas'] > 0)
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($monthlyComparison['difference']['roas'] / $monthlyComparison['month1']['roas']) * 100 >= 0 ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ number_format(($monthlyComparison['difference']['roas'] / $monthlyComparison['month1']['roas']) * 100, 2) }}%
                                                </span>
                                            @else
                                                <span class="text-gray-400">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-2 h-2 bg-purple-500 rounded-full mr-3"></div>
                                                <span class="font-medium text-gray-900">Hoa hồng</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if ($monthlyComparison['month1']['commission'] > 0)
                                                {{ number_format($monthlyComparison['month1']['commission']) }} ₫
                                            @else
                                                0 ₫
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if ($monthlyComparison['month2']['commission'] > 0)
                                                {{ number_format($monthlyComparison['month2']['commission']) }} ₫
                                            @else
                                                0 ₫
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="font-medium {{ $monthlyComparison['difference']['commission'] >= 0 ? 'text-purple-600' : 'text-red-600' }}">
                                                {{ $monthlyComparison['difference']['commission'] >= 0 ? '+' : '' }}{{ number_format($monthlyComparison['difference']['commission']) }}
                                                ₫
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($monthlyComparison['month1']['commission'] > 0)
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($monthlyComparison['difference']['commission'] / $monthlyComparison['month1']['commission']) * 100 >= 0 ? 'bg-purple-100 text-purple-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ number_format(($monthlyComparison['difference']['commission'] / $monthlyComparison['month1']['commission']) * 100, 2) }}%
                                                </span>
                                            @else
                                                <span class="text-gray-400">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-chart-bar text-gray-400 text-3xl"></i>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900 mb-2">Chưa có dữ liệu so sánh</h4>
                    <p class="text-gray-500 mb-6">Chọn tháng và năm để xem báo cáo so sánh chi tiết</p>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 max-w-md mx-auto">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-blue-500 mr-3"></i>
                            <div class="text-sm text-blue-700">
                                <p class="font-medium">Mẹo:</p>
                                <p>Chọn hai tháng khác nhau để so sánh hiệu suất kinh doanh</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
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
                    <h1 class="text-2xl font-bold">{{ number_format($totalRevenue, 2) }} VNĐ</h1>
                    <p class="text-gray-700 font-medium">Tổng Doanh Thu Đã Duyệt</p>
                    <div class="mt-20 mb-2 flex items-center">
                        <div class="py-1 px-3 rounded bg-green-200 text-green-900 mr-3">
                            <i class="fa fa-caret-up"></i>
                        </div>
                        <p class="text-gray-700"><span
                                class="text-green-500">{{ $avgRoas ? number_format($avgRoas, 2) : 'N/A' }}</span> ROAS
                            trung bình trong tháng này.</p>
                    </div>
                    <a href="{{ route('admin.financial.index') }}"
                        class="mt-6 inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Xem tất cả bản
                        ghi</a>
                </div>
                <div class="bg-white shadow-md rounded-lg p-4" style="height: 300px;">
                    <canvas id="recordStatusPieChart" class="mx-auto"></canvas>
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
                            <th class="px-4 py-2 border-r">Trạng Thái</th>
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
                                    {{ $record->record_date? \Carbon\Carbon::parse($record->record_date)->locale('vi')->diffForHumans(['parts' => 1, 'short' => true]): 'Không có ngày' }}
                                </td>
                                <td class="border border-l-0 px-4 py-2">
                                    <span
                                        class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded">{{ $record->status ? 'Đã Duyệt' : 'Chưa duyệt' }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dữ liệu cho biểu đồ tròn theo nền tảng
            const platformData = @json($recent_records->groupBy('platform.name')->map->sum('revenue')->toArray());

            // Biểu đồ tròn cho phân bổ doanh thu theo nền tảng
            const pieCtx = document.getElementById('platformPieChart').getContext('2d');
            const pieChart = new Chart(pieCtx, {
                type: 'polarArea',
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

            // Biểu đồ tròn cho trạng thái bản ghi
            const ctx = document.getElementById('recordStatusPieChart').getContext('2d');
            const recordStatusPieChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Đã duyệt', 'Chưa duyệt'],
                    datasets: [{
                        label: 'Trạng thái bản ghi',
                        data: [{{ $approved_count ?? 0 }}, {{ $not_approved_count ?? 0 }}],
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.6)',
                            'rgba(255, 99, 132, 0.6)'
                        ],
                        borderColor: [
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 99, 132, 1)'
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
                            text: 'Thống Kê Trạng Thái Bản Ghi'
                        }
                    }
                }
            });

            // Biểu đồ so sánh tháng
            @if (isset($monthlyComparison))
                const monthlyCtx = document.getElementById('monthlyComparisonChart').getContext('2d');
                const monthlyComparisonChart = new Chart(monthlyCtx, {
                    type: 'bar',
                    data: {
                        labels: ['{{ $monthlyComparison['month1']['name'] }}',
                            '{{ $monthlyComparison['month2']['name'] }}'
                        ],
                        datasets: [{
                                label: 'Doanh Thu',
                                data: [{{ $monthlyComparison['month1']['revenue'] ?? 0 }},
                                    {{ $monthlyComparison['month2']['revenue'] ?? 0 }}
                                ],
                                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                                borderColor: 'rgb(59, 130, 246)',
                                borderWidth: 2,
                                borderRadius: 4,
                                barThickness: 40
                            },
                            {
                                label: 'Chi Phí',
                                data: [{{ $monthlyComparison['month1']['expenses'] ?? 0 }},
                                    {{ $monthlyComparison['month2']['expenses'] ?? 0 }}
                                ],
                                backgroundColor: 'rgba(239, 68, 68, 0.5)',
                                borderColor: 'rgb(239, 68, 68)',
                                borderWidth: 2,
                                borderRadius: 4,
                                barThickness: 40
                            },
                            {
                                label: 'ROAS',
                                data: [{{ $monthlyComparison['month1']['roas'] ?? 0 }},
                                    {{ $monthlyComparison['month2']['roas'] ?? 0 }}
                                ],
                                backgroundColor: 'rgba(255, 206, 86, 0.5)',
                                borderColor: 'rgb(255, 206, 86)',
                                borderWidth: 2,
                                borderRadius: 4,
                                barThickness: 40,
                                yAxisID: 'y1'
                            },
                            {
                                label: 'Hoa hồng',
                                data: [{{ $monthlyComparison['month1']['commission'] ?? 0 }},
                                    {{ $monthlyComparison['month2']['commission'] ?? 0 }}
                                ],
                                backgroundColor: 'rgba(153, 102, 255, 0.5)',
                                borderColor: 'rgb(153, 102, 255)',
                                borderWidth: 2,
                                borderRadius: 4,
                                barThickness: 40
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    font: {
                                        size: 12,
                                        family: "'Inter', sans-serif"
                                    },
                                    padding: 20,
                                    usePointStyle: true,
                                    pointStyle: 'circle'
                                }
                            },
                            title: {
                                display: true,
                                text: 'So Sánh Doanh Thu, Chi Phí, ROAS và Hoa hồng',
                                font: {
                                    size: 16,
                                    weight: 'bold',
                                    family: "'Inter', sans-serif"
                                },
                                padding: {
                                    top: 10,
                                    bottom: 30
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(255, 255, 255, 0.9)',
                                titleColor: '#1f2937',
                                titleFont: {
                                    size: 13,
                                    weight: 'bold',
                                    family: "'Inter', sans-serif"
                                },
                                bodyColor: '#4b5563',
                                bodyFont: {
                                    size: 12,
                                    family: "'Inter', sans-serif"
                                },
                                borderColor: '#e5e7eb',
                                borderWidth: 1,
                                padding: 12,
                                displayColors: true,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.dataset.label === 'ROAS') {
                                            label += context.parsed.y.toFixed(2);
                                        } else {
                                            label += context.parsed.y.toLocaleString('vi-VN') + ' ₫';
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    drawBorder: false,
                                    color: '#e5e7eb'
                                },
                                ticks: {
                                    font: {
                                        size: 12,
                                        family: "'Inter', sans-serif"
                                    },
                                    color: '#4b5563',
                                    padding: 10,
                                    callback: function(value) {
                                        return value.toLocaleString('vi-VN');
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Số Tiền (₫)'
                                }
                            },
                            y1: {
                                position: 'right',
                                beginAtZero: true,
                                grid: {
                                    drawOnChartArea: false
                                },
                                ticks: {
                                    font: {
                                        size: 12,
                                        family: "'Inter', sans-serif"
                                    },
                                    color: '#4b5563',
                                    padding: 10,
                                    callback: function(value) {
                                        return value.toFixed(2);
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'ROAS'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 12,
                                        family: "'Inter', sans-serif"
                                    },
                                    color: '#4b5563',
                                    padding: 10
                                }
                            }
                        },
                        layout: {
                            padding: {
                                left: 10,
                                right: 10,
                                top: 0,
                                bottom: 0
                            }
                        }
                    }
                });
            @endif
        });
    </script>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Hàm format số
            function formatNumber(number, decimals = 0) {
                return new Intl.NumberFormat('vi-VN', {
                    minimumFractionDigits: decimals,
                    maximumFractionDigits: decimals
                }).format(number);
            }

            // Xử lý form so sánh
            $('#compareMonthsForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const url = window.location.pathname + '?' + new URLSearchParams(formData).toString();

                $.get(url, function(response) {
                    if (response.monthlyComparison) {
                        const ctx = document.getElementById('monthlyComparisonChart').getContext(
                            '2d');
                        if (window.monthlyComparisonChart) {
                            window.monthlyComparisonChart.destroy();
                        }

                        window.monthlyComparisonChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: [response.monthlyComparison.month1.name, response
                                    .monthlyComparison.month2.name
                                ],
                                datasets: [{
                                        label: 'Doanh Thu',
                                        data: [response.monthlyComparison.month1
                                            .revenue || 0, response
                                            .monthlyComparison.month2.revenue || 0
                                        ],
                                        backgroundColor: 'rgba(59, 130, 246, 0.5)',
                                        borderColor: 'rgb(59, 130, 246)',
                                        borderWidth: 2,
                                        borderRadius: 4,
                                        barThickness: 40
                                    },
                                    {
                                        label: 'Chi Phí',
                                        data: [response.monthlyComparison.month1
                                            .expenses || 0, response
                                            .monthlyComparison.month2.expenses || 0
                                        ],
                                        backgroundColor: 'rgba(239, 68, 68, 0.5)',
                                        borderColor: 'rgb(239, 68, 68)',
                                        borderWidth: 2,
                                        borderRadius: 4,
                                        barThickness: 40
                                    },
                                    {
                                        label: 'ROAS',
                                        data: [response.monthlyComparison.month1.roas ||
                                            0, response.monthlyComparison.month2
                                            .roas || 0
                                        ],
                                        backgroundColor: 'rgba(255, 206, 86, 0.5)',
                                        borderColor: 'rgb(255, 206, 86)',
                                        borderWidth: 2,
                                        borderRadius: 4,
                                        barThickness: 40,
                                        yAxisID: 'y1'
                                    },
                                    {
                                        label: 'Hoa hồng',
                                        data: [response.monthlyComparison.month1
                                            .commission || 0, response
                                            .monthlyComparison.month2.commission ||
                                            0
                                        ],
                                        backgroundColor: 'rgba(153, 102, 255, 0.5)',
                                        borderColor: 'rgb(153, 102, 255)',
                                        borderWidth: 2,
                                        borderRadius: 4,
                                        barThickness: 40
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'top',
                                        labels: {
                                            font: {
                                                size: 12,
                                                family: "'Inter', sans-serif"
                                            },
                                            padding: 20,
                                            usePointStyle: true,
                                            pointStyle: 'circle'
                                        }
                                    },
                                    title: {
                                        display: true,
                                        text: 'So Sánh Doanh Thu, Chi Phí, ROAS và Hoa hồng',
                                        font: {
                                            size: 16,
                                            weight: 'bold',
                                            family: "'Inter', sans-serif"
                                        },
                                        padding: {
                                            top: 10,
                                            bottom: 30
                                        }
                                    },
                                    tooltip: {
                                        backgroundColor: 'rgba(255, 255, 255, 0.9)',
                                        titleColor: '#1f2937',
                                        titleFont: {
                                            size: 13,
                                            weight: 'bold',
                                            family: "'Inter', sans-serif"
                                        },
                                        bodyColor: '#4b5563',
                                        bodyFont: {
                                            size: 12,
                                            family: "'Inter', sans-serif"
                                        },
                                        borderColor: '#e5e7eb',
                                        borderWidth: 1,
                                        padding: 12,
                                        displayColors: true,
                                        callbacks: {
                                            label: function(context) {
                                                let label = context.dataset.label || '';
                                                if (label) {
                                                    label += ': ';
                                                }
                                                if (context.dataset.label === 'ROAS') {
                                                    label += context.parsed.y.toFixed(
                                                    2);
                                                } else {
                                                    label += formatNumber(context.parsed
                                                        .y) + ' ₫';
                                                }
                                                return label;
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        grid: {
                                            drawBorder: false,
                                            color: '#e5e7eb'
                                        },
                                        ticks: {
                                            font: {
                                                size: 12,
                                                family: "'Inter', sans-serif"
                                            },
                                            color: '#4b5563',
                                            padding: 10,
                                            callback: function(value) {
                                                return formatNumber(value);
                                            }
                                        },
                                        title: {
                                            display: true,
                                            text: 'Số Tiền (₫)'
                                        }
                                    },
                                    y1: {
                                        position: 'right',
                                        beginAtZero: true,
                                        grid: {
                                            drawOnChartArea: false
                                        },
                                        ticks: {
                                            font: {
                                                size: 12,
                                                family: "'Inter', sans-serif"
                                            },
                                            color: '#4b5563',
                                            padding: 10,
                                            callback: function(value) {
                                                return value.toFixed(2);
                                            }
                                        },
                                        title: {
                                            display: true,
                                            text: 'ROAS'
                                        }
                                    },
                                    x: {
                                        grid: {
                                            display: false
                                        },
                                        ticks: {
                                            font: {
                                                size: 12,
                                                family: "'Inter', sans-serif"
                                            },
                                            color: '#4b5563',
                                            padding: 10
                                        }
                                    }
                                },
                                layout: {
                                    padding: {
                                        left: 10,
                                        right: 10,
                                        top: 0,
                                        bottom: 0
                                    }
                                }
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
