@extends('layouts.admin')

@section('title', 'Báo Cáo Tổng Quan')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <!-- Form lọc ngày -->
        <div class="filter-form rounded-lg mb-6 flex items-center">
            <form id="filterForm" class="flex items-center gap-4">
                <div class="flex flex-col">
                    <input type="date" name="date1" id="date1" value="2025-04-01" min="2024-01-01" max="2025-12-31"
                        class="border border-dashed border-gray-600 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex flex-col">
                    <input type="date" name="date2" id="date2" value="2025-05-01" min="2024-01-01" max="2025-12-31"
                        class="border border-dashed border-gray-600 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200">
                    Lọc
                </button>
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
        function fetchRevenueData() {
            const date1 = document.getElementById('date1').value;
            const date2 = document.getElementById('date2').value;
            fetch(`/api/revenue?date1=${date1}&date2=${date2}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    if (window.barChart) window.barChart.destroy();
                    if (window.lineChart) window.lineChart.destroy();

                    const barCtx = document.getElementById('revenueBarChart').getContext('2d');
                    window.barChart = new Chart(barCtx, {
                        type: 'bar',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                    label: 'Doanh thu (VND)',
                                    data: data.revenues,
                                    backgroundColor: 'rgba(34, 197, 94, 0.3)', // Màu xanh lá nhạt
                                    borderColor: 'rgba(34, 197, 94, 1)', // Màu xanh lá đậm
                                    borderWidth: 1,
                                    borderRadius: 5, // Bo góc cột
                                    barThickness: 30 // Độ dày cột
                                },
                                {
                                    label: 'Chi phí (VND)',
                                    data: data.expenses,
                                    backgroundColor: 'rgba(239, 68, 68, 0.3)', // Màu đỏ nhạt
                                    borderColor: 'rgba(239, 68, 68, 1)', // Màu đỏ đậm
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
                                        color: 'rgba(0, 0, 0, 0.05)' // Lưới nhạt hơn
                                    },
                                    ticks: {
                                        font: {
                                            size: 12
                                        },
                                        callback: value => value.toLocaleString('vi-VN') +
                                            ' VND' // Định dạng số
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
                                    }, // Ẩn lưới trục x
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
                                    backgroundColor: 'rgba(34, 197, 94, 0.5)', // Màu điểm
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
