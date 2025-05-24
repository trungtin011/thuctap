@extends('layouts.navbar')

@section('title', 'Lịch Sử Doanh Thu')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Lịch Sử Doanh Thu</h5>
        </div>
        <div class="card-body">
            {{-- Form lọc nâng cao --}}
            <form method="GET" action="" class="row g-3 mb-4 align-items-end">
                <div class="col-md-3">
                    <label for="filter_platform" class="form-label">Nền tảng</label>
                    <select id="filter_platform" name="platform_id" class="form-select">
                        <option value="">Tất cả</option>
                        @foreach ($platforms as $platform)
                            <option value="{{ $platform->id }}"
                                {{ request('platform_id') == $platform->id ? 'selected' : '' }}>
                                {{ $platform->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filter_status" class="form-label">Trạng thái</label>
                    <select id="filter_status" name="status" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Đang chờ</option>
                        <option value="manager_approved" {{ request('status') == 'manager_approved' ? 'selected' : '' }}>
                            Quản lý duyệt</option>
                        <option value="admin_approved" {{ request('status') == 'admin_approved' ? 'selected' : '' }}>Admin
                            duyệt</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Bị từ chối</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="filter_start_date" class="form-label">Từ ngày</label>
                    <input type="date" class="form-control" id="filter_start_date" name="start_date"
                        value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2">
                    <label for="filter_end_date" class="form-label">Đến ngày</label>
                    <input type="date" class="form-control" id="filter_end_date" name="end_date"
                        value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2">
                    <div class="flex flex-row gap-2">
                        @if (request('platform_id') || request('status') || request('start_date') || request('end_date'))
                            <a href="{{ route('employee.financial.index') }}"
                                class="bg-danger text-white p-2 w-10 text-center"><i class="fa-solid fa-repeat"></i></a>
                        @endif
                        <button type="submit" class="bg-primary text-white p-2 w-20">Lọc</button>
                    </div>
                </div>
            </form>
            {{-- End form lọc --}}
            @if ($financialRecords->isEmpty())
                <p class="text-muted">Bạn chưa có bản ghi doanh thu nào.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Phòng ban</th>
                                <th scope="col">Nền tảng</th>
                                <th scope="col">Doanh thu</th>
                                <th scope="col">Nguồn doanh thu</th>
                                <th scope="col">Tổng chi phí</th>
                                <th scope="col">Nguồn chi phí</th>
                                <th scope="col">ROAS</th>
                                <th scope="col">Trạng thái</th>
                                <th scope="col">Ngày ghi nhận</th>
                                <th scope="col">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($financialRecords as $record)
                                <tr data-id="{{ $record->id }}" data-platform-id="{{ $record->platform_id }}"
                                    data-revenue="{{ $record->revenue }}" data-record-date="{{ $record->record_date }}"
                                    data-record-time="{{ $record->record_time }}"
                                    data-note="{{ $record->note ? json_encode(json_decode($record->note)->note) : '' }}"
                                    data-expenses="{{ json_encode($record->expenses) }}"
                                    data-revenue-sources="{{ json_encode($record->revenue_sources ?? []) }}"
                                    data-metric-values="{{ json_encode($record->metric_values ?? []) }}">

                                    <td>{{ $record->id }}</td>
                                    <td>{{ $record->department->name }}</td>
                                    <td>{{ $record->platform->name }}</td>
                                    <td>{{ number_format($record->revenue, 2) }}</td>

                                    {{-- Nguồn doanh thu --}}
                                    <td>
                                        @if (!empty($record->revenue_sources))
                                            @foreach ($record->revenue_sources as $src)
                                                <div>{{ $src->source_name }}: {{ number_format($src->amount, 2) }}</div>
                                            @endforeach
                                        @else
                                            <div>N/A</div>
                                        @endif
                                    </td>

                                    {{-- Tổng chi phí --}}
                                    <td>{{ number_format(collect($record->expenses)->sum('amount'), 2) }}</td>

                                    {{-- Nguồn chi phí --}}
                                    <td>
                                        @foreach ($record->expenses as $exp)
                                            <div>{{ $exp->description ?? 'N/A' }}: {{ number_format($exp->amount, 2) }}
                                            </div>
                                        @endforeach
                                    </td>

                                    <td>{{ $record->roas ? number_format($record->roas, 2) : 'N/A' }}</td>

                                    {{-- Trạng thái --}}
                                    <td>
                                        @switch($record->status)
                                            @case('pending')
                                                <span class="badge bg-warning text-dark">Đang chờ</span>
                                            @break

                                            @case('manager_approved')
                                                <span class="badge bg-info">Quản lý duyệt</span>
                                            @break

                                            @case('admin_approved')
                                                <span class="badge bg-success">Admin duyệt</span>
                                            @break

                                            @case('rejected')
                                                <span class="badge bg-danger">Bị từ chối</span>
                                            @break

                                            @default
                                                <span class="badge bg-secondary">Không rõ</span>
                                        @endswitch
                                    </td>

                                    <td>{{ date('Y-m-d', strtotime($record->record_date)) }}</td>

                                    {{-- Hành động --}}
                                    <td class="text-center">
                                        @if ($record->status == 'pending')
                                            <button type="button" class="btn btn-sm btn-primary me-1 edit-btn"
                                                title="Sửa" data-bs-toggle="modal" data-bs-target="#editFinancialModal">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('employee.financial.destroy', $record->id) }}"
                                                method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Xóa"
                                                    onclick="return confirm('Bạn có chắc chắn muốn xóa bản ghi này?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="mt-4">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#financialModal">
                    <i class="bi bi-plus-circle me-1"></i> Nhập bản ghi mới
                </button>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="financialModal" tabindex="-1" aria-labelledby="financialModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="financialModalLabel">Nhập Doanh Thu và Chi Phí</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="financialForm" action="{{ route('employee.financial.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Phòng ban</label>
                            <input type="hidden" name="department_id" value="{{ auth()->user()->department_id }}">
                            <input type="text" class="form-control" value="{{ auth()->user()->department->name }}"
                                readonly>
                            <div class="invalid-feedback" id="department_id_error"></div>
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
                                        <input type="text" name="revenue_sources[0][source_name]" class="form-control"
                                            required>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">Số tiền</label>
                                        <input type="number" name="revenue_sources[0][amount]" class="form-control"
                                            step="0.01" min="0" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-secondary mb-3"
                            onclick="addRevenueSourceRow()">Thêm nguồn doanh thu</button>

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
                                        <input type="number" name="expenses[0][amount]" class="form-control"
                                            step="0.01" min="0" required>
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

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-primary">Lưu</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editFinancialModal" tabindex="-1" aria-labelledby="editFinancialModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editFinancialModalLabel">Sửa Doanh Thu và Chi Phí</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editFinancialForm" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="record_id">
                        <div class="mb-3">
                            <label class="form-label">Phòng ban</label>
                            <input type="hidden" name="department_id" value="{{ auth()->user()->department_id }}">
                            <input type="text" class="form-control" value="{{ auth()->user()->department->name }}"
                                readonly>
                            <div class="invalid-feedback" id="edit_department_id_error"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nền tảng</label>
                            <select name="platform_id" class="form-select" required onchange="loadMetrics(this, true)">
                                <option value="">Chọn nền tảng</option>
                                @foreach ($platforms as $platform)
                                    <option value="{{ $platform->id }}">{{ $platform->name }}</option>
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
                            <input type="date" name="record_date" class="form-control" required>
                            <div class="invalid-feedback" id="edit_record_date_error"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Thời gian ghi nhận</label>
                            <input type="time" name="record_time" class="form-control" required>
                            <div class="invalid-feedback" id="edit_record_time_error"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ghi chú</label>
                            <textarea name="note" class="form-control"></textarea>
                            <div class="invalid-feedback" id="edit_note_error"></div>
                        </div>

                        <h6 class="mb-3">Nguồn Doanh Thu</h6>
                        <div id="edit-revenue-source-container">
                            <!-- Revenue sources will be populated dynamically -->
                        </div>
                        <button type="button" class="btn btn-outline-secondary mb-3"
                            onclick="addRevenueSourceRow('edit-revenue-source-container')">Thêm nguồn doanh thu</button>

                        <h6 class="mb-3">Chi phí</h6>
                        <div id="edit-expense-container">
                            <!-- Expenses will be populated dynamically -->
                        </div>
                        <button type="button" class="btn btn-outline-secondary mb-3"
                            onclick="addExpenseRow('edit-expense-container')">Thêm chi phí</button>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-primary">Lưu</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let expenseIndex = 1;
        let revenueSourceIndex = 1;
        let editExpenseIndex = 0;
        let metricValueIndex = 0;
        let metricValues = []; // Lưu trữ metric_values đã nhập

        function addRevenueSourceRow(containerId = 'revenue-source-container') {
            const container = document.getElementById(containerId);
            const index = containerId === 'revenue-source-container' ? revenueSourceIndex++ : revenueSourceIndex++;
            const newRow = document.createElement('div');
            newRow.className = 'revenue-source-row mb-3 p-3 border rounded';
            newRow.innerHTML = `
            <div class="row">
                <div class="col-md-5 mb-2">
                    <label class="form-label">Tên nguồn</label>
                    <input type="text" name="revenue_sources[${index}][source_name]" class="form-control" required>
                </div>
                <div class="col-md-5 mb-2">
                    <label class="form-label">Số tiền</label>
                    <input type="number" name="revenue_sources[${index}][amount]" class="form-control" step="0.01" min="0" required>
                </div>
                <div class="col-md-2 mb-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.revenue-source-row').remove()">Xóa</button>
                </div>
            </div>
        `;
            container.appendChild(newRow);
        }

        function addExpenseRow(containerId) {
            const container = document.getElementById(containerId);
            const index = containerId === 'expense-container' ? expenseIndex++ : editExpenseIndex++;
            const newRow = document.createElement('div');
            newRow.className = 'expense-row mb-3 p-3 border rounded';
            newRow.innerHTML = `
            <div class="row">
                <div class="col-md-3 mb-2">
                    <label class="form-label">Loại chi phí</label>
                    <select name="expenses[${index}][expense_type_id]" class="form-select" required>
                        <option value="">Chọn loại chi phí</option>
                        @foreach ($expenseTypes as $expenseType)
                            <option value="{{ $expenseType->id }}">{{ $expenseType->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label">Số tiền</label>
                    <input type="number" name="expenses[${index}][amount]" class="form-control" step="0.01" min="0" required>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label">Mô tả</label>
                    <input type="text" name="expenses[${index}][description]" class="form-control">
                </div>
                <div class="col-md-3 mb-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.expense-row').remove()">Xóa</button>
                </div>
            </div>
        `;
            container.appendChild(newRow);
        }

        function loadMetrics(selectElement, isEdit = false) {
            const platformId = selectElement.value;
            const metricsContainerId = isEdit ? 'edit-metrics-container' : 'metrics-container';
            const valuesContainerId = isEdit ? 'edit-metric-values-container' : 'metric-values-container';
            const metricsContainer = document.getElementById(metricsContainerId);
            const valuesContainer = document.getElementById(valuesContainerId);

            if (!metricsContainer || !valuesContainer) {
                console.error(`Không tìm thấy container: ${metricsContainerId} hoặc ${valuesContainerId}`);
                return;
            }

            if (!platformId) {
                metricsContainer.innerHTML = '<p class="text-muted">Vui lòng chọn nền tảng để hiển thị các chỉ số.</p>';
                valuesContainer.innerHTML = '<p class="text-muted">Vui lòng chọn một chỉ số để xem và nhập giá trị.</p>';
                metricValues = [];
                updateMetricValuesContainer(valuesContainerId, isEdit);
                return;
            }

            fetch(`/employee/financial/get-metrics/${platformId}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Lỗi HTTP! Trạng thái: ${response.status} - ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    metricsContainer.innerHTML = '';
                    if (!data || !data.metrics || !Array.isArray(data.metrics)) {
                        metricsContainer.innerHTML =
                            '<p class="text-muted">Không có chỉ số nào hoặc dữ liệu không hợp lệ.</p>';
                        valuesContainer.innerHTML =
                            '<p class="text-muted">Vui lòng chọn một chỉ số để xem và nhập giá trị.</p>';
                        metricValues = [];
                        updateMetricValuesContainer(valuesContainerId, isEdit);
                        return;
                    }

                    if (data.metrics.length === 0) {
                        metricsContainer.innerHTML = '<p class="text-muted">Nền tảng này chưa có chỉ số nào.</p>';
                        valuesContainer.innerHTML =
                            '<p class="text-muted">Vui lòng chọn một chỉ số để xem và nhập giá trị.</p>';
                        metricValues = [];
                        updateMetricValuesContainer(valuesContainerId, isEdit);
                        return;
                    }

                    const metricList = document.createElement('div');
                    metricList.className = 'd-flex flex-wrap gap-2';
                    data.metrics.forEach(metric => {
                        const button = document.createElement('button');
                        button.type = 'button';
                        button.className = 'btn btn-outline-primary';
                        button.textContent = `${metric.name} (${metric.unit || 'Không có đơn vị'})`;
                        button.onclick = () => loadMetricValues(metric.id, isEdit, valuesContainerId, metric
                            .data_type, metric.name, metric.unit);
                        metricList.appendChild(button);
                    });
                    metricsContainer.appendChild(metricList);

                    if (isEdit) {
                        metricValues = [];
                        const recordId = document.querySelector('#editFinancialForm input[name="record_id"]').value;
                        Promise.all(data.metrics.map(metric =>
                                fetch(`/employee/financial/get-metric-values/${metric.id}?record_id=${recordId}`, {
                                    method: 'GET',
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    }
                                })
                                .then(response => response.json())
                                .then(data => ({
                                    metric_id: metric.id,
                                    name: metric.name,
                                    unit: metric.unit,
                                    data_type: metric.data_type,
                                    values: data.values || []
                                }))
                            ))
                            .then(results => {
                                metricValues = results;
                                updateMetricValuesContainer(valuesContainerId, isEdit);
                            })
                            .catch(error => {
                                console.error('Lỗi khi tải giá trị chỉ số:', error);
                                valuesContainer.innerHTML =
                                    `<p class="text-danger">Lỗi khi tải giá trị chỉ số: ${error.message}. Vui lòng thử lại.</p>`;
                            });
                    } else {
                        updateMetricValuesContainer(valuesContainerId, isEdit);
                    }
                })
                .catch(error => {
                    console.error('Lỗi khi tải chỉ số:', error);
                    metricsContainer.innerHTML =
                        `<p class="text-danger">Lỗi khi tải chỉ số: ${error.message}. Vui lòng thử lại.</p>`;
                    valuesContainer.innerHTML =
                        '<p class="text-muted">Vui lòng chọn một chỉ số để xem và nhập giá trị.</p>';
                    metricValues = [];
                    updateMetricValuesContainer(valuesContainerId, isEdit);
                });
        }

        function loadMetricValues(metricId, isEdit, valuesContainerId, dataType, metricName, metricUnit) {
            const valuesContainer = document.getElementById(valuesContainerId);
            const recordId = isEdit ? document.querySelector('#editFinancialForm input[name="record_id"]')?.value : null;

            if (!valuesContainer) {
                console.error(`Không tìm thấy container: ${valuesContainerId}`);
                return;
            }

            fetch(`/employee/financial/get-metric-values/${metricId}${isEdit && recordId ? '?record_id=' + recordId : ''}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Lỗi HTTP! Trạng thái: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log(`Metric values for metric_id ${metricId}:`, data.values);
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
                    console.error('Lỗi khi tải giá trị chỉ số:', error);
                    valuesContainer.innerHTML =
                        `<p class="text-danger">Lỗi khi tải giá trị chỉ số: ${error.message}. Vui lòng thử lại.</p>`;
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
                    console.log(`Displaying values for metric ${metric.name}:`, metric.values);
                    if (metric.values.length === 0) {
                        metricSection.innerHTML +=
                            '<p class="text-muted">Chưa có giá trị nào. Vui lòng nhập giá trị mới.</p>';
                    } else {
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
                                ${metric.values.map(value => `
                                        <tr>
                                            <td>${value.value}</td>
                                            <td>${value.recorded_at}</td>
                                        </tr>
                                    `).join('')}
                            </tbody>
                        `;
                        metricSection.appendChild(table);
                    }
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

            updateMetricRecordedAt(isEdit);
        }

        function updateMetricRecordedAt(isEdit) {
            const form = isEdit ? document.getElementById('editFinancialForm') : document.getElementById('financialForm');
            const recordDate = form.querySelector('input[name="record_date"]')?.value;
            const recordTime = form.querySelector('input[name="record_time"]')?.value;

            if (recordDate && recordTime) {
                const recordedAt = `${recordDate} ${recordTime}:00`;
                document.querySelectorAll('.metric-recorded-at').forEach(input => {
                    input.value = recordedAt;
                });
            }
        }

        document.getElementById('financialForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const formData = new FormData(form);

            document.querySelectorAll('.invalid-feedback').forEach(el => {
                el.textContent = '';
                el.closest('.form-control, .form-select')?.classList.remove('is-invalid');
            });

            fetch('{{ route('employee.financial.store') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('financialModal'));
                        modal.hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công!',
                            text: 'Bản ghi doanh thu và chi phí đã được thêm thành công.',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#0d6efd'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        if (data.errors && typeof data.errors === 'object') {
                            for (const [field, errors] of Object.entries(data.errors)) {
                                const errorElement = document.getElementById(`${field}_error`);
                                if (errorElement) {
                                    errorElement.textContent = errors[0];
                                    errorElement.closest('.form-control, .form-select')?.classList.add(
                                        'is-invalid');
                                }
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi!',
                                text: data.message || 'Dữ liệu trả về không hợp lệ.',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: 'Đã xảy ra lỗi khi gửi yêu cầu. Vui lòng thử lại.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc3545'
                    });
                });
        });

        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                const recordId = row.getAttribute('data-id');
                const form = document.getElementById('editFinancialForm');

                form.action = `/employee/financial/${recordId}`;
                form.querySelector('input[name="record_id"]').value = recordId;

                const platformId = row.getAttribute('data-platform-id');
                const platformSelect = form.querySelector('select[name="platform_id"]');
                platformSelect.value = platformId || '';

                form.querySelector('input[name="record_date"]').value = row.getAttribute(
                    'data-record-date') || '';
                form.querySelector('input[name="record_time"]').value = row.getAttribute(
                    'data-record-time') || '';

                form.querySelector('textarea[name="note"]').value = JSON.parse(row.getAttribute(
                    'data-note') || '""');

                const revenueSources = JSON.parse(row.getAttribute('data-revenue-sources') || '[]');
                const revenueSourceContainer = document.getElementById('edit-revenue-source-container');
                revenueSourceContainer.innerHTML = '';
                revenueSourceIndex = 0;
                revenueSources.forEach((source, index) => {
                    const newRow = document.createElement('div');
                    newRow.className = 'revenue-source-row mb-3 p-3 border rounded';
                    newRow.innerHTML = `
                <div class="row">
                    <div class="col-md-5 mb-2">
                        <label class="form-label">Tên nguồn</label>
                        <input type="text" name="revenue_sources[${index}][source_name]" class="form-control" value="${source.source_name || ''}" required>
                    </div>
                    <div class="col-md-5 mb-2">
                        <label class="form-label">Số tiền</label>
                        <input type="number" name="revenue_sources[${index}][amount]" class="form-control" step="0.01" min="0" value="${source.amount || 0}" required>
                    </div>
                    <div class="col-md-2 mb-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.revenue-source-row').remove()">Xóa</button>
                    </div>
                </div>
            `;
                    revenueSourceContainer.appendChild(newRow);
                    revenueSourceIndex = index + 1;
                });

                const expenseContainer = document.getElementById('edit-expense-container');
                expenseContainer.innerHTML = '';
                editExpenseIndex = 0;
                const expenses = JSON.parse(row.getAttribute('data-expenses') || '[]');
                expenses.forEach((expense, index) => {
                    const newRow = document.createElement('div');
                    newRow.className = 'expense-row mb-3 p-3 border rounded';
                    newRow.innerHTML = `
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Loại chi phí</label>
                        <select name="expenses[${index}][expense_type_id]" class="form-select" required>
                            <option value="">Chọn loại chi phí</option>
                            @foreach ($expenseTypes as $expenseType)
                                <option value="{{ $expenseType->id }}" ${expense.expense_type_id == {{ $expenseType->id }} ? 'selected' : ''}>{{ $expenseType->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Số tiền</label>
                        <input type="number" name="expenses[${index}][amount]" class="form-control" step="0.01" min="0" value="${expense.amount || 0}" required>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Mô tả</label>
                        <input type="text" name="expenses[${index}][description]" class="form-control" value="${expense.description || ''}">
                    </div>
                    <div class="col-md-3 mb-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.expense-row').remove()">Xóa</button>
                    </div>
                </div>
            `;
                    expenseContainer.appendChild(newRow);
                    editExpenseIndex = index + 1;
                });

                const modal = document.getElementById('editFinancialModal');
                modal.addEventListener('shown.bs.modal', function() {
                    if (platformId) {
                        loadMetrics(platformSelect, true);
                    } else {
                        document.getElementById('edit-metrics-container').innerHTML =
                            '<p class="text-muted">Vui lòng chọn nền tảng để hiển thị các chỉ số.</p>';
                        document.getElementById('edit-metric-values-container').innerHTML =
                            '<p class="text-muted">Vui lòng chọn một chỉ số để xem và nhập giá trị.</p>';
                    }
                });
            });
        });

        document.getElementById('editFinancialForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const formData = new FormData(form);

            document.querySelectorAll('.invalid-feedback').forEach(el => {
                el.textContent = '';
                el.closest('.form-control, .form-select')?.classList.remove('is-invalid');
            });

            fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById(
                            'editFinancialModal'));
                        modal.hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công!',
                            text: 'Bản ghi đã được cập nhật thành công.',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#0d6efd'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        for (const [field, errors] of Object.entries(data.errors)) {
                            const errorElement = document.getElementById(`edit_${field}_error`);
                            if (errorElement) {
                                errorElement.textContent = errors[0];
                                errorElement.closest('.form-control, .form-select')?.classList.add(
                                    'is-invalid');
                            }
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: 'Đã xảy ra lỗi. Vui lòng thử lại.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc3545'
                    });
                });
        });

        document.getElementById('financialModal').addEventListener('show.bs.modal', function() {
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
            metricValues = [];
            updateMetricValuesContainer('metric-values-container', false);
        });

        document.getElementById('editFinancialModal').addEventListener('show.bs.modal', function() {
            updateMetricRecordedAt(true);
        });

        document.getElementById('financialForm').querySelector('input[name="record_date"]')?.addEventListener('change',
            () => updateMetricRecordedAt(false));
        document.getElementById('financialForm').querySelector('input[name="record_time"]')?.addEventListener('change',
            () => updateMetricRecordedAt(false));
        document.getElementById('editFinancialForm').querySelector('input[name="record_date"]')?.addEventListener('change',
            () => updateMetricRecordedAt(true));
        document.getElementById('editFinancialForm').querySelector('input[name="record_time"]')?.addEventListener('change',
            () => updateMetricRecordedAt(true));
    </script>
@endsection
