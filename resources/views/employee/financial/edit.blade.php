@extends('layouts.navbar')

@section('title', 'Sửa Bản Ghi Doanh Thu')

@section('content')
    @php
        $isMarketing = auth()->user()->department->name === 'Marketing';
    @endphp
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">{{ $isMarketing ? 'Sửa Chi Phí' : 'Sửa Doanh Thu' }}</h5>
        </div>
        <div class="card-body">
            <form id="editFinancialForm" action="{{ route('employee.financial.update', $financialRecord->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="record_id" value="{{ $financialRecord->id }}">
                <div class="mb-3">
                    <label class="form-label">Phòng ban</label>
                    <input type="hidden" name="department_id" value="{{ auth()->user()->department_id }}">
                    <input type="text" class="form-control" value="{{ auth()->user()->department->name }}" readonly>
                    <div class="invalid-feedback" id="edit_department_id_error"></div>
                </div>
                @if($showRoutes)
                <div class="mb-3">
                    <label for="route_id" class="form-label">Chọn Tuyến</label>
                    <select name="route_id" id="route_id" class="form-select" required>
                        <option value="">-- Chọn tuyến --</option>
                        @foreach($routes as $route)
                            <option value="{{ $route->id }}" {{ $financialRecord->route_id == $route->id ? 'selected' : '' }}>
                                {{ $route->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" id="edit_route_id_error"></div>
                </div>
                @endif
                @if(!$isMarketing)
                <h6 class="mb-3">Nguồn Doanh Thu</h6>
                <div id="edit-revenue-source-container">
                    @foreach (json_decode($financialRecord->note)->revenue_sources ?? [] as $index => $source)
                        <div class="revenue-source-row mb-3 p-3 border rounded">
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Tên nguồn</label>
                                    <select name="revenue_sources[{{ $index }}][source_name]" class="form-select" >
                                        <option value="">Chọn nguồn doanh thu</option>
                                        @foreach($fields as $field)
                                            <option value="{{ $field->name }}" {{ $source->source_name == $field->name ? 'selected' : '' }}>
                                                {{ $field->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="form-label">Số tiền</label>
                                    <input type="number" name="revenue_sources[{{ $index }}][amount]" class="form-control" step="0.01" min="0" value="{{ $source->amount }}" >
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="form-label">Hoa hồng</label>
                                    <input type="number" name="revenue_sources[{{ $index }}][commission]" class="form-control" step="0.01" min="0" value="{{ $source->commission ?? 0 }}" >
                                </div>
                                <div class="col-md-2 mb-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.revenue-source-row').remove()">Xóa</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-outline-secondary mb-3" onclick="addRevenueSourceRow('edit-revenue-source-container')">Thêm nguồn doanh thu</button>
                @endif

                @if($isMarketing)
                <h6 class="mb-3">Chi Phí</h6>
                <div id="edit-expense-container">
                    @foreach ($financialRecord->expenses as $index => $expense)
                        <div class="expense-row mb-3 p-3 border rounded">
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Loại chi phí</label>
                                    <select name="expenses[{{ $index }}][expense_type_id]" class="form-select" required>
                                        <option value="">-- Chọn loại chi phí --</option>
                                        @foreach ($expenseTypes as $type)
                                            <option value="{{ $type->id }}" {{ $expense->expense_type_id == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Số tiền</label>
                                    <input type="number" name="expenses[{{ $index }}][amount]" class="form-control" step="0.01" min="0" value="{{ $expense->amount }}" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Mô tả</label>
                                    <input type="text" name="expenses[{{ $index }}][description]" class="form-control" value="{{ $expense->description }}">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.expense-row').remove()">Xóa</button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-outline-secondary mb-3" onclick="addExpenseRow()">Thêm chi phí</button>
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
        let revenueSourceIndex = {{ count(json_decode($financialRecord->note)->revenue_sources ?? []) }};
        let expenseIndex = {{ $financialRecord->expenses->count() }};

        @if(!$isMarketing)
        function addRevenueSourceRow(containerId = 'edit-revenue-source-container') {
            const container = document.getElementById(containerId);
            const newRow = document.createElement('div');
            newRow.className = 'revenue-source-row mb-3 p-3 border rounded';
            newRow.innerHTML = `
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Tên nguồn</label>
                        <select name="revenue_sources[${revenueSourceIndex}][source_name]" class="form-select" required>
                            <option value="">Chọn nguồn doanh thu</option>
                            @foreach($fields as $field)
                                <option value="{{ $field->name }}">{{ $field->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Số tiền</label>
                        <input type="number" name="revenue_sources[${revenueSourceIndex}][amount]" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Hoa hồng</label>
                        <input type="number" name="revenue_sources[${revenueSourceIndex}][commission]" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="col-md-2 mb-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.revenue-source-row').remove()">Xóa</button>
                    </div>
                </div>
            `;
            container.appendChild(newRow);
            revenueSourceIndex++;
        }
        @endif

        @if($isMarketing)
        function addExpenseRow() {
            const container = document.getElementById('edit-expense-container');
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
                        text: '{{ $isMarketing ? "Chi phí đã được cập nhật thành công." : "Doanh thu đã được cập nhật thành công." }}',
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
    </script>
@endsection