@extends('layouts.navbar')

@section('title', 'Nhập Bản Ghi Doanh Thu')

@section('content')
    @php
        $isMarketing = auth()->user()->department->name === 'Marketing';
        $isAccountant = auth()->user()->department->name === 'Kế toán';
    @endphp
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">{{ $isMarketing ? 'Nhập Chi Phí' : 'Nhập Doanh Thu' }}</h5>
        </div>
        <div class="card-body">
            <form id="financialForm" action="{{ route('employee.financial.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Phòng ban</label>
                    <input type="hidden" name="department_id" value="{{ auth()->user()->department_id }}">
                    <input type="text" class="form-control" value="{{ auth()->user()->department->name }}" readonly>
                    <div class="invalid-feedback" id="department_id_error"></div>
                </div>

                @if (!$isMarketing && !$isAccountant)
                    <div class="mb-3">
                        <label for="route_id" class="form-label">Chọn Tuyến</label>
                        <select name="route_id" id="route_id" class="form-select" required>
                            <option value="">-- Chọn tuyến --</option>
                            @foreach ($routes as $route)
                                <option value="{{ $route->id }}">{{ $route->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback" id="route_id_error"></div>
                    </div>
                @endif

                @if ($isAccountant)
                    <h6 class="mb-3">Tổng quan</h6>
                    <div class="row mb-3">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Chuyển khoản</label>
                            <input type="number" name="transfer_total" class="form-control" step="0.01" min="0"
                                value="0">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Chi</label>
                            <input type="number" name="expense_total" class="form-control" step="0.01" min="0"
                                value="0">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                @endif

                @if (!$isMarketing)
                    <h6 class="mb-3">Nguồn Doanh Thu</h6>
                    <div id="revenue-source-container">
                        <div class="revenue-source-row mb-3 p-3 border rounded">
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Tên nguồn</label>
                                    <select name="revenue_sources[0][source_name]" class="form-select" required>
                                        <option value="">-- Chọn nguồn doanh thu --</option>
                                        @if ($isAccountant)
                                            @foreach ($offices as $office)
                                                <option value="{{ $office->name }}">{{ $office->name }}</option>
                                            @endforeach
                                        @else
                                            @foreach ($fields as $field)
                                                <option value="{{ $field->name }}">{{ $field->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="form-label">Số tiền</label>
                                    <input type="number" name="revenue_sources[0][amount]" class="form-control"
                                        step="0.01" min="0">
                                    <div class="invalid-feedback"></div>
                                </div>
                                @if (!$isAccountant)
                                    <div class="col-md-3 mb-2">
                                        <label class="form-label">Hoa hồng</label>
                                        <input type="number" name="revenue_sources[0][commission]" class="form-control"
                                            step="0.01" min="0">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                @endif
                                <div class="col-md-2 mb-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger btn-sm"
                                        onclick="this.closest('.revenue-source-row').remove()">Xóa</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-secondary mb-3" onclick="addRevenueSourceRow()">Thêm nguồn
                        doanh thu</button>
                @endif

                @if ($isMarketing)
                    <div class="mb-3">
                        <label for="route_id" class="form-label">Chọn Tuyến</label>
                        <select name="route_id" id="route_id" class="form-select" required>
                            <option value="">-- Chọn tuyến --</option>
                            @foreach ($routes as $route)
                                <option value="{{ $route->id }}">{{ $route->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback" id="route_id_error"></div>
                    </div>
                @endif

                @if ($isMarketing)
                    <div class="mb-3">
                        <label for="platform_id" class="form-label">Chọn Nền tảng</label>
                        <select name="platform_id" id="platform_id" class="form-select" required onchange="showPlatformMetrics()">
                            <option value="">-- Chọn nền tảng --</option>
                            @foreach ($platforms as $platform)
                                <option value="{{ $platform->id }}" data-metrics='@json($platform->metrics)'>{{ $platform->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback" id="platform_id_error"></div>
                    </div>
                    <div id="platform-metrics-list" class="mb-3"></div>
                @endif

                @if ($isMarketing)
                    <h6 class="mb-3">Chi Phí</h6>
                    <div id="expense-container">
                        <div class="expense-row mb-3 p-3 border rounded">
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Loại chi phí</label>
                                    <select name="expenses[0][expense_type_id]" class="form-select" required>
                                        <option value="">-- Chọn loại chi phí --</option>
                                        @foreach ($expenseTypes as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Số tiền</label>
                                    <input type="number" name="expenses[0][amount]" class="form-control" step="0.01"
                                        min="0" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Mô tả</label>
                                    <input type="text" name="expenses[0][description]" class="form-control">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-secondary mb-3" onclick="addExpenseRow()">Thêm chi
                        phí</button>
                @endif

                <div class="mt-3">
                    <a href="{{ route('employee.financial.index') }}" class="btn btn-secondary">Quay lại</a>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let revenueSourceIndex = 1;
        let expenseIndex = 1;

        @if (!$isMarketing)
            function addRevenueSourceRow() {
                const container = document.getElementById('revenue-source-container');
                const newRow = document.createElement('div');
                newRow.className = 'revenue-source-row mb-3 p-3 border rounded';
                newRow.innerHTML = `
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Tên nguồn</label>
                            <select name="revenue_sources[${revenueSourceIndex}][source_name]" class="form-select" required>
                                <option value="">-- Chọn nguồn doanh thu --</option>
                                @if ($isAccountant)
                                    @foreach ($offices as $office)
                                        <option value="{{ $office->name }}">{{ $office->name }}</option>
                                    @endforeach
                                @else
                                    @foreach ($fields as $field)
                                        <option value="{{ $field->name }}">{{ $field->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Số tiền</label>
                            <input type="number" name="revenue_sources[${revenueSourceIndex}][amount]" class="form-control" step="0.01" min="0" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        @if (!$isAccountant)
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Hoa hồng</label>
                            <input type="number" name="revenue_sources[${revenueSourceIndex}][commission]" class="form-control" step="0.01" min="0" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        @else
                        <div class="col-md-3 mb-2"></div>
                        @endif
                        <div class="col-md-2 mb-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.revenue-source-row').remove()">Xóa</button>
                        </div>
                    </div>
                `;
                container.appendChild(newRow);
                revenueSourceIndex++;
            }
        @endif

        @if ($isMarketing)
            function addExpenseRow() {
                const container = document.getElementById('expense-container');
                const newRow = document.createElement('div');
                newRow.className = 'expense-row mb-3 p-3 border rounded';
                newRow.innerHTML = `
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Loại chi phí</label>
                        <select name="expenses[${expenseIndex}][expense_type_id]" class="form-select" required>
                            <option value="">-- Chọn loại chi phí --</option>
                            @foreach ($expenseTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Số tiền</label>
                        <input type="number" name="expenses[${expenseIndex}][amount]" class="form-control" step="0.01" min="0" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Mô tả</label>
                        <input type="text" name="expenses[${expenseIndex}][description]" class="form-control">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="text-end">
                    <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.expense-row').remove()">Xóa</button>
                </div>
            `;
                container.appendChild(newRow);
                expenseIndex++;
            }
        @endif

        function showPlatformMetrics() {
            const select = document.getElementById('platform_id');
            const metricsDiv = document.getElementById('platform-metrics-list');
            const selected = select.options[select.selectedIndex];
            let metrics = [];
            try {
                metrics = JSON.parse(selected.getAttribute('data-metrics')) || [];
            } catch (e) {}
            if (metrics.length > 0) {
                let html = '<label class="form-label">Nhập dữ liệu các trường của nền tảng:</label>';
                metrics.forEach((m, idx) => {
                    html += `<div class=\"mb-2\">` +
                        `<label class=\"form-label\">${m.name} (${m.unit || ''}, ${m.data_type || ''})</label>` +
                        `<input type=\"text\" name=\"metrics[${m.id}]\" class=\"form-control\" placeholder=\"Nhập giá trị\">` +
                        `</div>`;
                });
                metricsDiv.innerHTML = html;
            } else {
                metricsDiv.innerHTML = '';
            }
        }

        document.getElementById('financialForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công!',
                            text: 'Bản ghi đã được thêm thành công.',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#0d6efd'
                        }).then(() => {
                            window.location.href = '{{ route('employee.financial.index') }}';
                        });
                    } else {
                        for (const [field, errors] of Object.entries(data.errors)) {
                            const errorElement = document.getElementById(`${field}_error`);
                            if (errorElement) {
                                errorElement.textContent = errors[0];
                                errorElement.closest('.form-control, .form-select')?.classList.add(
                                    'is-invalid');
                            }
                        }
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: 'Đã xảy ra lỗi khi gửi yêu cầu. Vui lòng thử lại.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc3545'
                    });
                });
        });
    </script>
@endsection
