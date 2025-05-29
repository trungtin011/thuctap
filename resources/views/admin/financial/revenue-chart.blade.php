@extends('layouts.admin')

@section('title', 'Báo Cáo Tổng Quan')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <!-- Form lọc -->
        <div class="filter-form rounded-lg mb-6 flex items-center">
            <form id="filterForm" class="flex items-end gap-4 flex-wrap">
                <div class="flex flex-col">
                    <label for="date1" class="text-sm font-medium text-gray-700">Từ ngày</label>
                    <input type="date" name="date1" id="date1"
                        class="border border-dashed border-gray-600 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex flex-col">
                    <label for="date2" class="text-sm font-medium text-gray-700">Đến ngày</label>
                    <input type="date" name="date2" id="date2"
                        class="border border-dashed border-gray-600 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex flex-col">
                    <label for="platform_id" class="text-sm font-medium text-gray-700">Nền tảng</label>
                    <select name="platform_id" id="platform_id"
                        class="border border-dashed border-gray-600 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Tất cả</option>
                    </select>
                </div>
                <div class="flex flex-col">
                    <label for="dai_ly_id" class="text-sm font-medium text-gray-700">Đại lý</label>
                    <select name="dai_ly_id" id="dai_ly_id"
                        class="border border-dashed border-gray-600 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Tất cả</option>
                    </select>
                </div>
                <div class="flex flex-col">
                    <label for="office_id" class="text-sm font-medium text-gray-700">Văn phòng</label>
                    <select name="office_id" id="office_id"
                        class="border border-dashed border-gray-600 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Tất cả</option>
                    </select>
                </div>
                <div class="flex flex-col">
                    <label for="department_id" class="text-sm font-medium text-gray-700">Phòng ban</label>
                    <select name="department_id" id="department_id"
                        class="border border-dashed border-gray-600 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Tất cả</option>
                    </select>
                </div>
                <div class="flex items-center">
                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Biểu đồ -->
        <div class="flex flex-row md:flex-col gap-6 mb-6">
            <!-- Biểu đồ cột -->
            <div class="bg-white shadow-md rounded-lg p-6 w-1/2">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 text-center">Biểu Đồ Cột</h3>
                <div class="relative h-96">
                    <canvas id="revenueBarChart"></canvas>
                </div>
            </div>
            <!-- Biểu đồ đường -->
            <div class="bg-white shadow-md rounded-lg p-6 w-1/2">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 text-center">Biểu Đồ Đường</h3>
                <div class="relative h-96">
                    <canvas id="revenueLineChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Phân tích -->
        <div class="analysis bg-white shadow-md rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 text-center">Phân Tích Lý Do Doanh Thu Thay Đổi</h3>
            <div id="analysisText" class="text-gray-700 leading-relaxed">Đang tải phân tích...</div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function populateSelectOptions(selectId, data, valueKey, textKey) {
            const select = document.getElementById(selectId);
            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item[valueKey];
                option.textContent = item[textKey];
                select.appendChild(option);
            });
        }

        function fetchRevenueData() {
            const date1 = document.getElementById('date1').value;
            const date2 = document.getElementById('date2').value;
            const platform_id = document.getElementById('platform_id').value;
            const dai_ly_id = document.getElementById('dai_ly_id').value;
            const office_id = document.getElementById('office_id').value;
            const department_id = document.getElementById('department_id').value;

            const queryParams = new URLSearchParams({
                date1,
                date2,
                ...(platform_id && {
                    platform_id
                }),
                ...(dai_ly_id && {
                    dai_ly_id
                }),
                ...(office_id && {
                    office_id
                }),
                ...(department_id && {
                    department_id
                }),
            });

            fetch(`/api/revenue?${queryParams}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    // Cập nhật danh sách tùy chọn cho các select
                    document.getElementById('platform_id').innerHTML = '<option value="">Tất cả</option>';
                    document.getElementById('dai_ly_id').innerHTML = '<option value="">Tất cả</option>';
                    document.getElementById('office_id').innerHTML = '<option value="">Tất cả</option>';
                    document.getElementById('department_id').innerHTML = '<option value="">Tất cả</option>';

                    populateSelectOptions('platform_id', data.platforms, 'id', 'name');
                    populateSelectOptions('dai_ly_id', data.dai_lies, 'id', 'ten_dai_ly');
                    populateSelectOptions('office_id', data.offices, 'id', 'name');
                    populateSelectOptions('department_id', data.departments, 'id', 'name');

                    // Hủy biểu đồ cũ nếu tồn tại
                    if (window.barChart) window.barChart.destroy();
                    if (window.lineChart) window.lineChart.destroy();

                    // Biểu đồ cột
                    const barCtx = document.getElementById('revenueBarChart').getContext('2d');
                    window.barChart = new Chart(barCtx, {
                        type: 'bar',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                    label: 'Doanh thu (VND)',
                                    data: data.revenues,
                                    backgroundColor: 'rgba(34, 197, 94, 0.3)',
                                    borderColor: 'rgba(34, 197, 94, 1)',
                                    borderWidth: 1,
                                    borderRadius: 5,
                                    barThickness: 30
                                },
                                {
                                    label: 'Chi phí (VND)',
                                    data: data.expenses,
                                    backgroundColor: 'rgba(239, 68, 68, 0.3)',
                                    borderColor: 'rgba(239, 68, 68, 1)',
                                    borderWidth: 1,
                                    borderRadius: 5,
                                    barThickness: 30
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Số tiền (VND)',
                                        font: {
                                            size: 14,
                                            weight: 'bold'
                                        }
                                    },
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.05)'
                                    },
                                    ticks: {
                                        font: {
                                            size: 12
                                        },
                                        callback: value => value.toLocaleString('vi-VN') + ' VND'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Ngày',
                                        font: {
                                            size: 14,
                                            weight: 'bold'
                                        }
                                    },
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        font: {
                                            size: 12
                                        }
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'top',
                                    labels: {
                                        font: {
                                            size: 12
                                        },
                                        boxWidth: 20,
                                        padding: 15
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'So Sánh Doanh Thu và Chi Phí (Cột)',
                                    font: {
                                        size: 16,
                                        weight: 'bold'
                                    },
                                    padding: 20
                                },
                                tooltip: {
                                    callbacks: {
                                        label: context => {
                                            let label = context.dataset.label || '';
                                            if (label) label += ': ';
                                            label += context.parsed.y.toLocaleString('vi-VN') + ' VND';
                                            return label;
                                        }
                                    }
                                }
                            }
                        }
                    });

                    // Biểu đồ đường
                    const lineCtx = document.getElementById('revenueLineChart').getContext('2d');
                    window.lineChart = new Chart(lineCtx, {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                    label: 'Doanh thu (VND)',
                                    data: data.revenues,
                                    fill: false,
                                    borderColor: 'rgba(34, 197, 94, 1)',
                                    backgroundColor: 'rgba(34, 197, 94, 0.5)',
                                    tension: 0.3,
                                    pointRadius: 5,
                                    pointHoverRadius: 7
                                },
                                {
                                    label: 'Chi phí (VND)',
                                    data: data.expenses,
                                    fill: false,
                                    borderColor: 'rgba(239, 68, 68, 1)',
                                    backgroundColor: 'rgba(239, 68, 68, 0.5)',
                                    tension: 0.3,
                                    pointRadius: 5,
                                    pointHoverRadius: 7
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Số tiền (VND)',
                                        font: {
                                            size: 14,
                                            weight: 'bold'
                                        }
                                    },
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.05)'
                                    },
                                    ticks: {
                                        font: {
                                            size: 12
                                        },
                                        callback: value => value.toLocaleString('vi-VN') + ' VND'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Ngày',
                                        font: {
                                            size: 14,
                                            weight: 'bold'
                                        }
                                    },
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        font: {
                                            size: 12
                                        }
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'top',
                                    labels: {
                                        font: {
                                            size: 12
                                        },
                                        boxWidth: 20,
                                        padding: 15
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'So Sánh Doanh Thu và Chi Phí (Đường)',
                                    font: {
                                        size: 16,
                                        weight: 'bold'
                                    },
                                    padding: 20
                                },
                                tooltip: {
                                    callbacks: {
                                        label: context => {
                                            let label = context.dataset.label || '';
                                            if (label) label += ': ';
                                            label += context.parsed.y.toLocaleString('vi-VN') + ' VND';
                                            return label;
                                        }
                                    }
                                }
                            }
                        }
                    });

                    document.getElementById('analysisText').innerText = data.analysis;
                })
                .catch(error => console.error('Error:', error));
        }

        fetchRevenueData();

        document.getElementById('filterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            fetchRevenueData();
        });
    </script>
@endsection
