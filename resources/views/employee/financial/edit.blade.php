@extends('layouts.navbar')

@section('title', 'Sửa Bản Ghi Doanh Thu')

@section('content')
    @php
        $isMarketing = $department->name === 'Marketing';
        $isAccountant = $department->name === 'Kế toán';
        $isBusiness = $department->name === 'Kinh doanh';
    @endphp
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">{{ $isMarketing ? 'Sửa Chi Phí' : 'Sửa Doanh Thu' }}</h5>
        </div>
        <div class="card-body">
            <form id="financialForm" action="{{ route('employee.financial.update', $financialRecord->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Phòng ban</label>
                    <input type="hidden" name="department_id" value="{{ $department->id }}">
                    <input type="text" class="form-control" value="{{ $department->name }}" readonly>
                    <div class="invalid-feedback" id="department_id_error"></div>
                </div>

                @if (!$isMarketing && !$isAccountant)
                    <div class="mb-3">
                        <label for="route_id" class="form-label">Chọn Tuyến</label>
                        <select name="route_id" id="route_id" class="form-select" required>
                            <option value="">-- Chọn tuyến --</option>
                            @foreach ($routes as $route)
                                <option value="{{ $route->id }}"
                                    {{ $financialRecord->route_id == $route->id ? 'selected' : '' }}>{{ $route->name }}
                                </option>
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
                                value="{{ $noteData->transfer_total ?? 0 }}">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Chi</label>
                            <input type="number" name="expense_total" class="form-control" step="0.01" min="0"
                                value="{{ $noteData->expense_total ?? 0 }}">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                @endif

                @if (!$isMarketing)
                    <h6 class="mb-3">Nguồn Doanh Thu</h6>
                    <div id="revenue-source-container">
                        @foreach ($financialRecord->revenue_sources as $index => $source)
                            <div class="revenue-source-row mb-3 p-3 border rounded">
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label">Tên nguồn</label>
                                        <select name="revenue_sources[{{ $index }}][source_name]" class="form-select"
                                            required>
                                            <option value="">-- Chọn nguồn doanh thu --</option>
                                            @if ($isAccountant)
                                                @foreach ($offices as $office)
                                                    <option value="{{ $office->name }}"
                                                        {{ $source->source_name == $office->name ? 'selected' : '' }}>
                                                        {{ $office->name }}</option>
                                                @endforeach
                                            @else
                                                @foreach ($fields as $field)
                                                    <option value="{{ $field->name }}"
                                                        {{ $source->source_name == $field->name ? 'selected' : '' }}>
                                                        {{ $field->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label class="form-label">Số tiền</label>
                                        <input type="number" name="revenue_sources[{{ $index }}][amount]"
                                            class="form-control" step="0.01" min="0"
                                            value="{{ $source->amount }}" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    @if (!$isAccountant)
                                        <div class="col-md-3 mb-2">
                                            <label class="form-label">Hoa hồng</label>
                                            <input type="number" name="revenue_sources[{{ $index }}][commission]"
                                                class="form-control" step="0.01" min="0"
                                                value="{{ $source->commission ?? 0 }}" required>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    @endif
                                    <div class="col-md-2 mb-2 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger btn-sm"
                                            onclick="this.closest('.revenue-source-row').remove()">Xóa</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-outline-secondary mb-3" onclick="addRevenueSourceRow()">Thêm nguồn
                        doanh thu</button>
                @endif

                @if ($isMarketing)
                    <h6 class="mb-3">Chi Phí</h6>
                    <div id="expense-container">
                        @foreach ($financialRecord->expenses as $index => $expense)
                            <div class="expense-row mb-3 p-3 border rounded">
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label">Loại chi phí</label>
                                        <select name="expenses[{{ $index }}][expense_type_id]" class="form-select"
                                            required>
                                            <option value="">-- Chọn loại chi phí --</option>
                                            @foreach ($expenseTypes as $type)
                                                <option value="{{ $type->id }}"
                                                    {{ $expense->expense_type_id == $type->id ? 'selected' : '' }}>
                                                    {{ $type->name }}</option>
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
@endsection

@section('scripts')
    <script>
        let revenueSourceIndex = {{ count($financialRecord->revenue_sources) }};
        let expenseIndex = {{ count($financialRecord->expenses) }};

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
                        <div class="text-end">
                            <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.expense-row').remove()">Xóa</button>
                        </div>
                    </div>
                `;
                container.appendChild(newRow);
                expenseIndex++;
            }
        @endif

        document.getElementById('financialForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch(this.action, {
                    method: 'POST', // Use POST with _method=PUT for Laravel
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
