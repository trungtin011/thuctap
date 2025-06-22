@extends('layouts.navbar')

@section('title', 'Sửa Bản Ghi')

@section('content')
    @php
        $isMarketing = auth()->user()->department->name === 'Marketing';
        $isAccountant = auth()->user()->department->name === 'Kế Toán';
        $isBusiness = auth()->user()->department->name === 'Kinh Doanh';
    @endphp
    @if (!auth()->user()->department)
        <div class="alert alert-danger">
            Không thể xác định phòng ban của bạn. Vui lòng liên hệ quản trị viên.
        </div>
    @else
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">{{ $isMarketing ? 'Sửa Chi Phí' : 'Sửa Doanh Thu' }}</h5>
            </div>
            <div class="card-body">
                <form id="financialForm"
                    action="{{ route('employee.financial.update.' . ($isMarketing ? 'marketing' : ($isAccountant ? 'accounting' : 'business')), $isAccountant ? $officeRevenue->id : $financialRecord->id) }}"
                    method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Phòng ban</label>
                        <input type="hidden" name="department_id" value="{{ auth()->user()->department_id }}">
                        <input type="text" class="form-control" value="{{ auth()->user()->department->name }}" readonly>
                        <div class="invalid-feedback" id="department_id_error"></div>
                    </div>

                    @if ($isMarketing || $isBusiness)
                        <div class="mb-3">
                            <label for="route_id" class="form-label">Chọn Tuyến</label>
                            <select name="route_id" id="route_id" class="form-select" required>
                                <option value="">-- Chọn tuyến --</option>
                                @foreach ($routes as $route)
                                    <option value="{{ $route->id }}"
                                        {{ $financialRecord->route_id == $route->id ? 'selected' : '' }}>
                                        {{ $route->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="route_id_error"></div>
                        </div>
                    @endif

                    @if ($isMarketing)
                        <div class="mb-3">
                            <label for="platform_id" class="form-label">Chọn Nền tảng</label>
                            <select name="platform_id" id="platform_id" class="form-select" required
                                onchange="showPlatformMetrics()">
                                <option value="">-- Chọn nền tảng --</option>
                                @foreach ($platforms as $platform)
                                    <option value="{{ $platform->id }}" data-metrics='@json($platform->metrics)'
                                        {{ $financialRecord->platform_id == $platform->id ? 'selected' : '' }}>
                                        {{ $platform->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="platform_id_error"></div>
                        </div>
                        <div id="platform-metrics-list" class="mb-3">
                            <!-- Metrics sẽ được hiển thị bằng JavaScript -->
                        </div>
                    @endif

                    @if ($isAccountant)
                        <div class="mb-3">
                            <label class="form-label">Chi phí</label>
                            <input type="number" name="expense" class="form-control" step="0.01" min="0"
                                value="{{ $officeRevenue->expense }}" required>
                            <div class="invalid-feedback" id="expense_error"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Chuyển khoản</label>
                            <input type="number" name="bank_transfer" class="form-control" step="0.01" min="0"
                                value="{{ $officeRevenue->bank_transfer }}" required>
                            <div class="invalid-feedback" id="bank_transfer_error"></div>
                        </div>
                        <h6 class="mb-3">Nguồn Doanh Thu</h6>
                        <div id="revenue-source-container">
                            @foreach ($officeRevenue->offices as $index => $office)
                                <div class="revenue-source-row mb-3 p-3 border rounded">
                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Văn phòng</label>
                                            <select name="revenue_sources[{{ $index }}][office_id]"
                                                class="form-select" required>
                                                <option value="">-- Chọn văn phòng --</option>
                                                @foreach ($offices as $o)
                                                    <option value="{{ $o->id }}"
                                                        {{ $office->id == $o->id ? 'selected' : '' }}>{{ $o->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Giá trị (VNĐ)</label>
                                            <input type="number" name="revenue_sources[{{ $index }}][value]"
                                                class="form-control" step="0.01" min="0"
                                                value="{{ $office->pivot->value ?? 0 }}" required>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-4 mb-2 d-flex align-items-end">
                                            <button type="button" class="btn btn-danger btn-sm"
                                                onclick="this.closest('.revenue-source-row').remove()">Xóa</button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-outline-secondary mb-3" onclick="addRevenueSourceRow()">Thêm
                            văn phòng</button>
                    @elseif ($isBusiness)
                        <h6 class="mb-3">Nguồn Doanh Thu</h6>
                        <div id="revenue-source-container">
                            @php
                                $revenueSources = is_array($financialRecord->revenue_sources)
                                    ? $financialRecord->revenue_sources
                                    : [];
                                if (
                                    !is_array($financialRecord->revenue_sources) &&
                                    is_object($financialRecord->revenue_sources)
                                ) {
                                    $revenueSources = get_object_vars($financialRecord->revenue_sources);
                                }
                            @endphp
                            @foreach ($revenueSources as $index => $source)
                                <div class="revenue-source-row mb-3 p-3 border rounded">
                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Tên nguồn</label>
                                            <select name="revenue_sources[{{ $index }}][source_name]"
                                                class="form-select" required>
                                                <option value="">-- Chọn nguồn doanh thu --</option>
                                                @foreach ($fields as $field)
                                                    <option value="{{ $field->name }}"
                                                        @php
$selected = false;
                                                                if (is_array($source)) {
                                                                    $selected = isset($source['source_name']) && $source['source_name'] == $field->name;
                                                                } elseif (is_object($source)) {
                                                                    $selected = isset($source->source_name) && $source->source_name == $field->name;
                                                                } @endphp
                                                        {{ $selected ? 'selected' : '' }}>
                                                        {{ $field->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="form-label">Số tiền</label>
                                            <input type="number" name="revenue_sources[{{ $index }}][amount]"
                                                class="form-control" step="0.01" min="0"
                                                value="@php echo is_array($source) ? ($source['amount'] ?? 0) : ($source->amount ?? 0); @endphp"
                                                required>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="form-label">Hoa hồng</label>
                                            <input type="number" name="revenue_sources[{{ $index }}][commission]"
                                                class="form-control" step="0.01" min="0"
                                                value="@php echo is_array($source) ? ($source['commission'] ?? 0) : ($source->commission ?? 0); @endphp"
                                                required>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-2 mb-2 d-flex align-items-end">
                                            <button type="button" class="btn btn-danger btn-sm"
                                                onclick="this.closest('.revenue-source-row').remove()">Xóa</button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-outline-secondary mb-3"
                            onclick="addRevenueSourceRow()">Thêm nguồn doanh thu</button>
                    @endif

                    @if ($isMarketing)
                        <h6 class="mb-3">Chi Phí</h6>
                        <div id="expense-container">
                            @foreach ($financialRecord->expenses as $index => $expense)
                                <div class="expense-row mb-3 p-3 border rounded">
                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Loại chi phí</label>
                                            <select name="expenses[{{ $index }}][expense_type_id]"
                                                class="form-select" required>
                                                <option value="">-- Chọn loại chi phí --</option>
                                                @foreach ($expenseTypes as $type)
                                                    <option value="{{ $type->id }}"
                                                        {{ $expense->expense_type_id == $type->id ? 'selected' : '' }}>
                                                        {{ $type->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Số tiền</label>
                                            <input type="number" name="expenses[{{ $index }}][amount]"
                                                class="form-control" step="0.01" min="0"
                                                value="{{ $expense->amount }}" required>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Mô tả</label>
                                            <input type="text" name="expenses[{{ $index }}][description]"
                                                class="form-control" value="{{ $expense->description ?? '' }}">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="text-end">
                                            <button type="button" class="btn btn-danger btn-sm"
                                                onclick="this.closest('.expense-row').remove()">Xóa</button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-outline-secondary mb-3" onclick="addExpenseRow()">Thêm chi
                            phí</button>
                    @endif

                    <div class="mt-3">
                        <a href="{{ route('employee.financial.index') }}" class="btn btn-secondary">Quay lại</a>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        @php
            $revenueSourcesCount = $isAccountant ? (is_array($officeRevenue->revenue_sources ?? []) ? count($officeRevenue->revenue_sources) : 0) : (is_array($financialRecord->revenue_sources ?? []) ? count($financialRecord->revenue_sources) : 0);
            $expenseCount = $isMarketing ? (is_array($financialRecord->expenses ?? []) ? count($financialRecord->expenses) : 0) : 0;
        @endphp
        let revenueSourceIndex = {{ $revenueSourcesCount }};
        let expenseIndex = {{ $expenseCount }};

        @if ($isAccountant || $isBusiness)
            function addRevenueSourceRow() {
                const container = document.getElementById('revenue-source-container');
                const newRow = document.createElement('div');
                newRow.className = 'revenue-source-row mb-3 p-3 border rounded';
                newRow.innerHTML = `
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">${@if ($isAccountant) 'Văn phòng' @else 'Tên nguồn' @endif}</label>
                            <select name="revenue_sources[${revenueSourceIndex}][${@if ($isAccountant) 'office_id' @else 'source_name' @endif}]" class="form-select" required>
                                <option value="">-- Chọn ${@if ($isAccountant) 'văn phòng' @else 'nguồn doanh thu' @endif} --</option>
                                @if ($isAccountant)
                                    @foreach ($offices as $office)
                                        <option value="{{ $office->id }}">{{ $office->name }}</option>
                                    @endforeach
                                @else
                                    @foreach ($fields as $field)
                                        <option value="{{ $field->name }}">{{ $field->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        @if ($isAccountant)
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Giá trị (VNĐ)</label>
                                <input type="number" name="revenue_sources[${revenueSourceIndex}][value]" class="form-control" step="0.01" min="0" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        @else
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Số tiền</label>
                                <input type="number" name="revenue_sources[${revenueSourceIndex}][amount]" class="form-control" step="0.01" min="0" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Hoa hồng</label>
                                <input type="number" name="revenue_sources[${revenueSourceIndex}][commission]" class="form-control" step="0.01" min="0" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        @endif
                        <div class="col-md-${@if ($isAccountant) '4' @else '2' @endif} mb-2 d-flex align-items-end">
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
                        <div class="text-end">
                            <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.expense-row').remove()">Xóa</button>
                        </div>
                    </div>
                `;
                container.appendChild(newRow);
                expenseIndex++;
            }

            function showPlatformMetrics() {
                const select = document.getElementById('platform_id');
                const metricsDiv = document.getElementById('platform-metrics-list');
                const selected = select.options[select.selectedIndex];
                let metrics = [];
                try {
                    metrics = JSON.parse(selected.getAttribute('data-metrics')) || [];
                } catch (e) {}
                if (metrics.length > 0) {
                    // Truyền metricsData từ Blade sang JavaScript
                    const metricsData = @json($metricsData ?? []);
                    let html = '<label class="form-label">Nhập dữ liệu các trường của nền tảng:</label>';
                    metrics.forEach((m, idx) => {
                        const oldValue = metricsData[m.id] || ''; // Truy cập m.id trong JavaScript
                        html += `<div class="mb-2">` +
                            `<label class="form-label">${m.name} (${(m.unit || '') + (m.data_type ? ' ' + m.data_type : '')})</label>` +
                            `<input type="text" name="metrics[${m.id}]" class="form-control" placeholder="Nhập giá trị" value="${oldValue}">` +
                            `</div><div class="invalid-feedback"></div>`;
                    });
                    metricsDiv.innerHTML = html;
                } else {
                    metricsDiv.innerHTML = '';
                }
            }

            // Gọi hàm để hiển thị metrics ngay khi tải trang
            showPlatformMetrics();
        @endif

        document.getElementById('financialForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch(this.action, {
                    method: 'POST', // Laravel handles PUT via _method=PUT
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
                            text: 'Bản ghi đã được cập nhật thành công.',
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
