@extends('layouts.admin')
@section('title', 'Báo Cáo Tổng Quan')
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
                                {{ number_format($avg_roas, 2) }}%
                                <i class="fas fa-chevron-up ml-1"></i>
                            </span>
                        </div>
                        <div class="mt-8">
                            <h1 class="h5">{{ number_format($avg_roas, 2) }}</h1>
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
                    <select id="platform" name="platform">
                        <option value="">Tất cả</option>
                        @foreach ($platforms as $platform)
                            <option value="{{ $platform->id }}"
                                {{ request('platform') == $platform->id ? 'selected' : '' }}>
                                {{ $platform->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="border border-dashed border-gray-400 p-2">
                    <label for="yearFilter">Chọn năm:</label>
                    <select id="yearFilter" name="yearFilter">
                        <option value="all" {{ request('yearFilter', 'all') == 'all' ? 'selected' : '' }}>Tất cả năm
                        </option>
                        @foreach ($years as $year)
                            <option value="{{ $year }}" {{ request('yearFilter') == $year ? 'selected' : '' }}>
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

            {{-- Biểu đồ so sánh và biểu đồ tròn --}}
            <div class="grid grid-cols-3 gap-6 mt-6 xl:grid-cols-1">
                <!-- Cột 1: Biểu đồ đường (revenueExpenseChart) -->
                <div class="bg-white rounded-lg shadow-md p-6 col-span-2 relative">
                    @if (!empty($labels) && !empty($data) && array_sum($data) > 0)
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Xu Hướng Doanh Thu và Chi Phí Theo Thời Gian</h3>
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
                        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                        <script>
                            // Khởi tạo biểu đồ ban đầu (mặc định là line)
                            const ctx = document.getElementById('revenueExpenseChart').getContext('2d');
                            let chart = new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: {!! json_encode($labels) !!},
                                    datasets: [{
                                            label: 'Doanh thu',
                                            data: {!! json_encode($data) !!},
                                            backgroundColor: 'rgba(75, 192, 192, 0.1)',
                                            borderColor: 'rgba(75, 192, 192, 1)',
                                            borderWidth: 2,
                                            fill: true,
                                            tension: 0.4,
                                        },
                                        {
                                            label: 'Chi phí',
                                            data: {!! json_encode($expenseData ?? array_fill(0, count($labels), 0)) !!},
                                            backgroundColor: 'rgba(220, 53, 69, 0.1)',
                                            borderColor: 'rgba(220, 53, 69, 1)',
                                            borderWidth: 2,
                                            fill: true,
                                            tension: 0.4,
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
                                            text: 'Xu Hướng Doanh Thu và Chi Phí Theo Thời Gian'
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
                                    chartOptions.classList.add('hidden'); // Ẩn dropdown sau khi chọn
                                });
                            });

                            // Hàm cập nhật biểu đồ
                            function updateChart(type) {
                                chart.destroy(); // Xóa biểu đồ cũ
                                let newChart;

                                if (type === 'mixed') {
                                    newChart = new Chart(ctx, {
                                        type: 'bar',
                                        data: {
                                            labels: {!! json_encode($labels) !!},
                                            datasets: [{
                                                    type: 'bar', // Cột cho doanh thu
                                                    label: 'Doanh thu',
                                                    data: {!! json_encode($data) !!},
                                                    backgroundColor: 'rgba(75, 192, 192, 0.3)',
                                                    borderColor: 'rgba(75, 192, 192, 1)',
                                                    borderWidth: 2,
                                                    borderRadius: 6,
                                                    maxBarThickness: 36,
                                                },
                                                {
                                                    type: 'line', // Đường cho chi phí
                                                    label: 'Chi phí',
                                                    data: {!! json_encode($expenseData ?? array_fill(0, count($labels), 0)) !!},
                                                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                                                    borderColor: 'rgba(220, 53, 69, 1)',
                                                    borderWidth: 2,
                                                    fill: true,
                                                    tension: 0.4,
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
                                                    text: 'Kết Hợp Doanh Thu và Chi Phí Theo Thời Gian'
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
                                                    label: 'Doanh thu',
                                                    data: {!! json_encode($data) !!},
                                                    backgroundColor: type === 'pie' ? 'rgba(75, 192, 192, 0.6)' :
                                                        'rgba(75, 192, 192, 0.1)',
                                                    borderColor: type === 'pie' ? 'rgba(75, 192, 192, 1)' : 'rgba(75, 192, 192, 1)',
                                                    borderWidth: 2,
                                                    fill: type !== 'pie',
                                                    tension: type === 'line' ? 0.4 : 0,
                                                },
                                                {
                                                    label: 'Chi phí',
                                                    data: {!! json_encode($expenseData ?? array_fill(0, count($labels), 0)) !!},
                                                    backgroundColor: type === 'pie' ? 'rgba(220, 53, 69, 0.6)' :
                                                        'rgba(220, 53, 69, 0.1)',
                                                    borderColor: type === 'pie' ? 'rgba(220, 53, 69, 1)' : 'rgba(220, 53, 69, 1)',
                                                    borderWidth: 2,
                                                    fill: type !== 'pie',
                                                    tension: type === 'line' ? 0.4 : 0,
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
                                                    text: type === 'pie' ? 'Phân Bổ Doanh Thu và Chi Phí' :
                                                        'Xu Hướng Doanh Thu và Chi Phí Theo Thời Gian'
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
                                                }
                                            }
                                        }
                                    });
                                }

                                chart = newChart; // Cập nhật tham chiếu biểu đồ
                            }

                            // Ẩn dropdown khi nhấp ra ngoài
                            document.addEventListener('click', function(e) {
                                if (!chartOptionsBtn.contains(e.target) && !chartOptions.contains(e.target)) {
                                    chartOptions.classList.add('hidden');
                                }
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
                                        style="color: #D69E2E">{{ number_format($filteredAvgRoas, 2) }}%</span>
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
                        <p class="text-gray-700"><span class="text-green-500">{{ number_format($avg_roas, 2) }}%</span>
                            ROAS trung bình trong tháng này.</p>
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
                                    {{ $record->record_date ? \Carbon\Carbon::parse($record->record_date)->locale('vi')->diffForHumans(['parts' => 1, 'short' => true]) : 'Không có ngày' }}
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

    

    <!-- Thêm Chart.js từ CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dữ liệu từ PHP được truyền vào JavaScript
            const platformData = @json($recent_records->groupBy('platform.name')->map->sum('revenue')->toArray());

            // Khởi tạo biểu đồ tròn cho phân bổ doanh thu theo nền tảng
            const pieCtx = document.getElementById('platformPieChart').getContext('2d');
            const pieChart = new Chart(pieCtx, {
                type: 'polarArea', // Đã thay đổi từ 'pie' thành 'polarArea' trong phiên bản trước
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

            // Khởi tạo biểu đồ tròn cho trạng thái bản ghi
            const ctx = document.getElementById('recordStatusPieChart').getContext('2d');
            const recordStatusPieChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Đã duyệt', 'Chưa duyệt'],
                    datasets: [{
                        label: 'Trạng thái bản ghi',
                        data: [{{ $approved_count ?? 0 }}, {{ $not_approved_count ?? 0 }}],
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.6)', // Màu xanh lam cho "Đã duyệt"
                            'rgba(255, 99, 132, 0.6)' // Màu đỏ cho "Chưa duyệt"
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
        });
    </script>
@endsection
