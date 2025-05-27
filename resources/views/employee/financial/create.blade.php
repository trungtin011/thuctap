@extends('layouts.navbar')

@section('title', 'Nhập Bản Ghi Doanh Thu')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Nhập Doanh Thu và Chi Phí</h5>
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
                <div class="mb-3">
                    <label for="office_id" class="form-label">Chọn Văn phòng</label>
                    <select name="office_id" id="office_id" class="form-select" required>
                        <option value="">-- Chọn văn phòng --</option>
                        @foreach ($offices as $office)
                            <option value="{{ $office->id }}">{{ $office->name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" id="office_id_error"></div>
                </div>
                <div class="mb-3">
                    <label for="commission" class="form-label">Hoa hồng (VNĐ)</label>
                    <input type="number" name="commission" id="commission" class="form-control" min="0" value="0">
                    <div class="invalid-feedback" id="commission_error"></div>
                </div>
                <div class="mb-3">
                    <label for="dai_ly_id" class="form-label">Chọn Đại lý</label>
                    <select name="dai_ly_id" id="dai_ly_id" class="form-select" required>
                        <option value="">-- Chọn đại lý --</option>
                        @foreach ($dailies as $daily)
                            <option value="{{ $daily->id }}">{{ $daily->ten_dai_ly }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" id="dai_ly_id_error"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nền tảng</label>
                    <select name="platform_id" class="form-select" required onchange="loadMetrics(this)">
                        <option value="">Chọn nền tảng</option>
                        @foreach ($platforms as $platform)
                            <option value="{{ $platform->id }}">{{ $platform->name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" id="platform_id_error"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Chỉ số của nền tảng</label>
                    <div id="metrics-container" class="border p-3 rounded">
                        <p class="text-muted">Vui lòng chọn nền tảng để hiển thị các chỉ số.</p>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Giá trị chỉ số</label>
                    <div id="metric-values-container" class="border p-3 rounded">
                        <p class="text-muted">Vui lòng chọn một chỉ số để xem và nhập giá trị.</p>
                    </div>
                </div>
                <input type="hidden" name="record_date" id="auto_record_date">
                <input type="hidden" name="record_time" id="auto_record_time">
                <div class="mb-3">
                    <label class="form-label">Ghi chú</label>
                    <textarea name="note" class="form-control"></textarea>
                    <div class="invalid-feedback" id="note_error"></div>
                </div>
                <h6 class="mb-3">Nguồn Doanh Thu</h6>
                <div id="revenue-source-container">
                    <div class="revenue-source-row mb-3 p-3 border rounded">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Tên nguồn</label>
                                <input type="text" name="revenue_sources[0][source_name]" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Số tiền</label>
                                <input type="number" name="revenue_sources[0][amount]" class="form-control" step="0.01"
                                    min="0" required>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-secondary mb-3" onclick="addRevenueSourceRow()">Thêm nguồn
                    doanh thu</button>
                <h6 class="mb-3">Chi phí</h6>
                <div id="expense-container">
                    <div class="expense-row mb-3 p-3 border rounded">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Loại chi phí</label>
                                <select name="expenses[0][expense_type_id]" class="form-select" required>
                                    <option value="">Chọn loại chi phí</option>
                                    @foreach ($expenseTypes as $expenseType)
                                        <option value="{{ $expenseType->id }}">{{ $expenseType->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Số tiền</label>
                                <input type="number" name="expenses[0][amount]" class="form-control" step="0.01"
                                    min="0" required>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Mô tả</label>
                                <input type="text" name="expenses[0][description]" class="form-control">
                            </div>
                            <div class="col-md-3 mb-2 d-flex align-items-end">
                                <button type="button" class="btn btn-danger btn-sm"
                                    onclick="this.closest('.expense-row').remove()">Xóa</button>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-secondary mb-3"
                    onclick="addExpenseRow('expense-container')">Thêm chi phí</button>
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
        let expenseIndex = 1;
        let revenueSourceIndex = 1;
        let metricValueIndex = 0;
        let metricValues = [];

        function addRevenueSourceRow() {
            const container = document.getElementById('revenue-source-container');
            const newRow = document.createElement('div');
            newRow.className = 'revenue-source-row mb-3 p-3 border rounded';
            newRow.innerHTML = `
                <div class="row">
                    <div class="col-md-5 mb-2">
                        <label class="form-label">Tên nguồn</label>
                        <input type="text" name="revenue_sources[${revenueSourceIndex}][source_name]" class="form-control" required>
                    </div>
                    <div class="col-md-5 mb-2">
                        <label class="form-label">Số tiền</label>
                        <input type="number" name="revenue_sources[${revenueSourceIndex}][amount]" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="col-md-2 mb-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.revenue-source-row').remove()">Xóa</button>
                    </div>
                </div>
            `;
            container.appendChild(newRow);
            revenueSourceIndex++;
        }

        function addExpenseRow(containerId) {
            const container = document.getElementById(containerId);
            const newRow = document.createElement('div');
            newRow.className = 'expense-row mb-3 p-3 border rounded';
            newRow.innerHTML = `
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Loại chi phí</label>
                        <select name="expenses[${expenseIndex}][expense_type_id]" class="form-select" required>
                            <option value="">Chọn loại chi phí</option>
                            @foreach ($expenseTypes as $expenseType)
                                <option value="{{ $expenseType->id }}">{{ $expenseType->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Số tiền</label>
                        <input type="number" name="expenses[${expenseIndex}][amount]" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Mô tả</label>
                        <input type="text" name="expenses[${expenseIndex}][description]" class="form-control">
                    </div>
                    <div class="col-md-3 mb-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.expense-row').remove()">Xóa</button>
                    </div>
                </div>
            `;
            container.appendChild(newRow);
            expenseIndex++;
        }

        function loadMetrics(selectElement) {
            const platformId = selectElement.value;
            const metricsContainer = document.getElementById('metrics-container');
            const valuesContainer = document.getElementById('metric-values-container');

            if (!platformId) {
                metricsContainer.innerHTML = '<p class="text-muted">Vui lòng chọn nền tảng để hiển thị các chỉ số.</p>';
                valuesContainer.innerHTML = '<p class="text-muted">Vui lòng chọn một chỉ số để xem và nhập giá trị.</p>';
                metricValues = [];
                updateMetricValuesContainer('metric-values-container', false);
                return;
            }

            fetch(`/employee/financial/get-metrics/${platformId}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    metricsContainer.innerHTML = '';
                    if (data.metrics.length === 0) {
                        metricsContainer.innerHTML = '<p class="text-muted">Nền tảng này chưa có chỉ số nào.</p>';
                        valuesContainer.innerHTML =
                            '<p class="text-muted">Vui lòng chọn một chỉ số để xem và nhập giá trị.</p>';
                        metricValues = [];
                        updateMetricValuesContainer('metric-values-container', false);
                        return;
                    }

                    const metricList = document.createElement('div');
                    metricList.className = 'd-flex flex-wrap gap-2';
                    data.metrics.forEach(metric => {
                        const button = document.createElement('button');
                        button.type = 'button';
                        button.className = 'btn btn-outline-primary';
                        button.textContent = `${metric.name} (${metric.unit || 'Không có đơn vị'})`;
                        button.onclick = () => loadMetricValues(metric.id, false, 'metric-values-container',
                            metric.data_type, metric.name, metric.unit);
                        metricList.appendChild(button);
                    });
                    metricsContainer.appendChild(metricList);
                    updateMetricValuesContainer('metric-values-container', false);
                })
                .catch(error => {
                    metricsContainer.innerHTML =
                        `<p class="text-danger">Lỗi khi tải chỉ số: ${error.message}. Vui lòng thử lại.</p>`;
                    valuesContainer.innerHTML =
                        '<p class="text-muted">Vui lòng chọn một chỉ số để xem và nhập giá trị.</p>';
                    metricValues = [];
                    updateMetricValuesContainer('metric-values-container', false);
                });
        }

        function loadMetricValues(metricId, isEdit, valuesContainerId, dataType, metricName, metricUnit) {
            const valuesContainer = document.getElementById(valuesContainerId);
            const existingMetric = metricValues.find(m => m.metric_id === metricId);
            if (!existingMetric) {
                metricValues.push({
                    metric_id: metricId,
                    name: metricName,
                    unit: metricUnit,
                    data_type: dataType,
                    values: []
                });
            }
            updateMetricValuesContainer(valuesContainerId, isEdit);
        }

        function updateMetricValuesContainer(valuesContainerId, isEdit) {
            const valuesContainer = document.getElementById(valuesContainerId);
            valuesContainer.innerHTML = '';

            if (metricValues.length === 0) {
                valuesContainer.innerHTML = '<p class="text-muted">Vui lòng chọn một chỉ số để xem và nhập giá trị.</p>';
                return;
            }

            metricValues.forEach(metric => {
                const metricSection = document.createElement('div');
                metricSection.className = 'mb-3';
                metricSection.innerHTML = `<h6>${metric.name} (${metric.unit || 'Không có đơn vị'})</h6>`;
                const inputGroup = document.createElement('div');
                inputGroup.className = 'mt-2';
                inputGroup.innerHTML = `
                    <label class="form-label">Thêm giá trị mới</label>
                    <div class="input-group">
                        <input type="${metric.data_type === 'string' ? 'text' : 'number'}" 
                               name="metric_values[${metricValueIndex}][value]" 
                               class="form-control" 
                               placeholder="Nhập giá trị" 
                               ${metric.data_type === 'float' ? 'step="0.01"' : ''} 
                               required>
                        <input type="hidden" name="metric_values[${metricValueIndex}][metric_id]" value="${metric.metric_id}">
                        <input type="hidden" name="metric_values[${metricValueIndex}][recorded_at]" class="metric-recorded-at">
                    </div>
                `;
                metricSection.appendChild(inputGroup);
                metricValueIndex++;
                valuesContainer.appendChild(metricSection);
            });

            updateMetricRecordedAt();
        }

        function updateMetricRecordedAt() {
            const recordDate = document.getElementById('auto_record_date').value;
            const recordTime = document.getElementById('auto_record_time').value;
            if (recordDate && recordTime) {
                const recordedAt = `${recordDate} ${recordTime}:00`;
                document.querySelectorAll('.metric-recorded-at').forEach(input => {
                    input.value = recordedAt;
                });
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
                            text: 'Bản ghi doanh thu và chi phí đã được thêm thành công.',
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

        document.addEventListener('DOMContentLoaded', function() {
            const now = new Date();
            const utc = now.getTime() + (now.getTimezoneOffset() * 60000);
            const vnDate = new Date(utc + 7 * 60 * 60000);
            const yyyy = vnDate.getFullYear();
            const mm = String(vnDate.getMonth() + 1).padStart(2, '0');
            const dd = String(vnDate.getDate()).padStart(2, '0');
            const hh = String(vnDate.getHours()).padStart(2, '0');
            const min = String(vnDate.getMinutes()).padStart(2, '0');
            document.getElementById('auto_record_date').value = `${yyyy}-${mm}-${dd}`;
            document.getElementById('auto_record_time').value = `${hh}:${min}`;
            updateMetricRecordedAt();
        });
    </script>
@endsection
