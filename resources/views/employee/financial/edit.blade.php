@extends('layouts.navbar')

@section('title', 'Sửa Bản Ghi Doanh Thu')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Sửa Doanh Thu và Chi Phí</h5>
        </div>
        <div class="card-body">
            <form id="editFinancialForm" action="{{ route('employee.financial.update', $financialRecord->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="record_id" value="{{ $financialRecord->id }}">
                <div class="mb-3">
                    <label class="form-label">Phòng ban</label>
                    <input547 type="hidden" name="department_id" value="{{ auth()->user()->department_id }}">
                    <input type="text" class="form-control" value="{{ auth()->user()->department->name }}" readonly>
                    <div class="invalid-feedback" id="edit_department_id_error"></div>
                </div>
                <div class="mb-3">
                    <label for="edit_office_id" class="form-label">Chọn Văn phòng</label>
                    <select name="office_id" id="edit_office_id" class="form-select" required>
                        <option value="">-- Chọn văn phòng --</option>
                        @foreach($offices as $office)
                            <option value="{{ $office->id }}" {{ $financialRecord->office_id == $office->id ? 'selected' : '' }}>
                                {{ $office->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" id="edit_office_id_error"></div>
                </div>
                <div class="mb-3">
                    <label for="commission" class="form-label">Hoa hồng (VNĐ)</label>
                    <input type="number" name="commission" id="commission" class="form-control" min="0" step="100000000" value="{{ $financialRecord->commission }}">
                    <div class="invalid-feedback" id="commission_error"></div>
                </div>
                <div class="mb-3">
                    <label for="dai_ly_id" class="form-label">Chọn Đại lý</label>
                    <select name="dai_ly_id" id="dai_ly_id" class="form-select" required>
                        <option value="">-- Chọn đại lý --</option>
                        @foreach($dailies as $daily)
                            <option value="{{ $daily->id }}" {{ $financialRecord->dai_ly_id == $daily->id ? 'selected' : '' }}>
                                {{ $daily->ten_dai_ly }}
                            </option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" id="edit_dai_ly_id_error"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nền tảng</label>
                    <select name="platform_id" class="form-select" required onchange="loadMetrics(this, true)">
                        <option value="">Chọn nền tảng</option>
                        @foreach ($platforms as $platform)
                            <option value="{{ $platform->id }}" {{ $financialRecord->platform_id == $platform->id ? 'selected' : '' }}>
                                {{ $platform->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" id="edit_platform_id_error"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Chỉ số của nền tảng</label>
                    <div id="edit-metrics-container" class="border p-3 rounded">
                        <p class="text-muted">Vui lòng chọn nền tảng để hiển thị các chỉ số.</p>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Giá trị chỉ số</label>
                    <div id="edit-metric-values-container" class="border p-3 rounded">
                        <p class="text-muted">Vui lòng chọn một chỉ số để xem và nhập giá trị.</p>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ngày ghi nhận</label>
                    <input type="date" name="record_date" class="form-control" value="{{ $financialRecord->record_date }}" required>
                    <div class="invalid-feedback" id="edit_record_date_error"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Thời gian ghi nhận</label>
                    <input type="time" name="record_time" class="form-control" value="{{ $financialRecord->record_time }}" required>
                    <div class="invalid-feedback" id="edit_record_time_error"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ghi chú</label>
                    <textarea name="note" class="form-control">{{ optional(json_decode($financialRecord->note))->note }}</textarea>
                    <div class="invalid-feedback" id="edit_note_error"></div>
                </div>
                <h6 class="mb-3">Nguồn Doanh Thu</h6>
                <div id="edit-revenue-source-container">
                    @foreach (json_decode($financialRecord->note)->revenue_sources ?? [] as $index => $source)
                        <div class="revenue-source-row mb-3 p-3 border rounded">
                            <div class="row">
                                <div class="col-md-5 mb-2">
                                    <label class="form-label">Tên nguồn</label>
                                    <input type="text" name="revenue_sources[{{ $index }}][source_name]" class="form-control" value="{{ $source->source_name }}" required>
                                </div>
                                <div class="col-md-5 mb-2">
                                    <label class="form-label">Số tiền</label>
                                    <input type="number" name="revenue_sources[{{ $index }}][amount]" class="form-control" step="0.01" min="0" value="{{ $source->amount }}" required>
                                </div>
                                <div class="col-md-2 mb-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.revenue-source-row').remove()">Xóa</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-outline-secondary mb-3" onclick="addRevenueSourceRow('edit-revenue-source-container')">Thêm nguồn doanh thu</button>
                <h6 class="mb-3">Chi phí</h6>
                <div id="edit-expense-container">
                    @foreach ($financialRecord->expenses as $index => $expense)
                        <div class="expense-row mb-3 p-3 border rounded">
                            <div class="row">
                                <div class="col-md-3 mb-2">
                                    <label class="form-label">Loại chi phí</label>
                                    <select name="expenses[{{ $index }}][expense_type_id]" class="form-select" required>
                                        <option value="">Chọn loại chi phí</option>
                                        @foreach ($expenseTypes as $expenseType)
                                            <option value="{{ $expenseType->id }}" {{ $expense->expense_type_id == $expenseType->id ? 'selected' : '' }}>
                                                {{ $expenseType->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="form-label">Số tiền</label>
                                    <input type="number" name="expenses[{{ $index }}][amount]" class="form-control" step="0.01" min="0" value="{{ $expense->amount }}" required>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="form-label">Mô tả</label>
                                    <input type="text" name="expenses[{{ $index }}][description]" class="form-control" value="{{ $expense->description }}">
                                </div>
                                <div class="col-md-3 mb-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.expense-row').remove()">Xóa</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-outline-secondary mb-3" onclick="addExpenseRow('edit-expense-container')">Thêm chi phí</button>
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
        let expenseIndex = {{ count($financialRecord->expenses) }};
        let revenueSourceIndex = {{ count(json_decode($financialRecord->note)->revenue_sources ?? []) }};
        let metricValueIndex = 0;
        let metricValues = [];

        function addRevenueSourceRow(containerId = 'edit-revenue-source-container') {
            const container = document.getElementById(containerId);
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

        function loadMetrics(selectElement, isEdit = true) {
            const platformId = selectElement.value;
            const metricsContainer = document.getElementById('edit-metrics-container');
            const valuesContainer = document.getElementById('edit-metric-values-container');
            const recordId = '{{ $financialRecord->id }}';

            if (!platformId) {
                metricsContainer.innerHTML = '<p class="text-muted">Vui lòng chọn nền tảng để hiển thị các chỉ số.</p>';
                valuesContainer.innerHTML = '<p class="text-muted">Vui lòng chọn một chỉ số để xem và nhập giá trị.</p>';
                metricValues = [];
                updateMetricValuesContainer('edit-metric-values-container', true);
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
                    valuesContainer.innerHTML = '<p class="text-muted">Vui lòng chọn một chỉ số để xem và nhập giá trị.</p>';
                    metricValues = [];
                    updateMetricValuesContainer('edit-metric-values-container', true);
                    return;
                }

                const metricList = document.createElement('div');
                metricList.className = 'd-flex flex-wrap gap-2';
                data.metrics.forEach(metric => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'btn btn-outline-primary';
                    button.textContent = `${metric.name} (${metric.unit || 'Không có đơn vị'})`;
                    button.onclick = () => loadMetricValues(metric.id, true, 'edit-metric-values-container', metric.data_type, metric.name, metric.unit);
                    metricList.appendChild(button);
                });
                metricsContainer.appendChild(metricList);

                data.metrics.forEach(metric => {
                    loadMetricValues(metric.id, true, 'edit-metric-values-container', metric.data_type, metric.name, metric.unit);
                });
            })
            .catch(error => {
                metricsContainer.innerHTML = `<p class="text-danger">Lỗi khi tải chỉ số: ${error.message}. Vui lòng thử lại.</p>`;
                valuesContainer.innerHTML = '<p class="text-muted">Vui lòng chọn một chỉ số để xem và nhập giá trị.</p>';
                metricValues = [];
                updateMetricValuesContainer('edit-metric-values-container', true);
            });
        }

        function loadMetricValues(metricId, isEdit, valuesContainerId, dataType, metricName, metricUnit) {
            const valuesContainer = document.getElementById(valuesContainerId);
            const recordId = '{{ $financialRecord->id }}';

            fetch(`/employee/financial/get-metric-values/${metricId}?record_id=${recordId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                const existingMetric = metricValues.find(m => m.metric_id === metricId);
                if (!existingMetric) {
                    metricValues.push({
                        metric_id: metricId,
                        name: metricName,
                        unit: metricUnit,
                        data_type: dataType,
                        values: data.values || []
                    });
                } else {
                    existingMetric.values = data.values || [];
                }
                updateMetricValuesContainer(valuesContainerId, isEdit);
            })
            .catch(error => {
                valuesContainer.innerHTML = `<p class="text-danger">Lỗi khi tải giá trị chỉ số: ${error.message}. Vui lòng thử lại.</p>`;
            });
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

                if (isEdit) {
                    const table = document.createElement('table');
                    table.className = 'table table-bordered table-sm';
                    table.innerHTML = `
                        <thead>
                            <tr>
                                <th>Giá trị</th>
                                <th>Ngày ghi nhận</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${metric.values.length === 0 ? '<tr><td colspan="2" class="text-muted">Chưa có giá trị nào.</td></tr>' : 
                                metric.values.map(value => `
                                    <tr>
                                        <td>${value.value}</td>
                                        <td>${value.recorded_at}</td>
                                    </tr>
                                `).join('')}
                        </tbody>
                    `;
                    metricSection.appendChild(table);
                }

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

            updateMetricRecordedAt(true);
        }

        function updateMetricRecordedAt(isEdit) {
            const form = document.getElementById('editFinancialForm');
            const recordDate = form.querySelector('input[name="record_date"]').value;
            const recordTime = form.querySelector('input[name="record_time"]').value;
            if (recordDate && recordTime) {
                const recordedAt = `${recordDate} ${recordTime}:00`;
                document.querySelectorAll('.metric-recorded-at').forEach(input => {
                    input.value = recordedAt;
                });
            }
        }

        document.getElementById('editFinancialForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
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
                        const errorElement = document.getElementById(`edit_${field}_error`);
                        if (errorElement) {
                            errorElement.textContent = errors[0];
                            errorElement.closest('.form-control, .form-select').classList.add('is-invalid');
                        }
                    }
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi!',
                    text: 'Đã xảy ra lỗi. Vui lòng thử lại.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc3545'
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const platformSelect = document.querySelector('select[name="platform_id"]');
            loadMetrics(platformSelect, true);
            updateMetricRecordedAt(true);
        });
    </script>
@endsection