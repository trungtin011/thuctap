@extends('layouts.admin')
@section('title', 'Thiết lập mục tiêu doanh thu')
@section('content')
<div class="max-w-xl mx-auto mt-8">
    <h2 class="text-2xl font-bold mb-6">Thiết lập mục tiêu doanh thu năm</h2>
    @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif
    <form method="POST" action="{{ route('admin.targets.store') }}" class="bg-white p-6 rounded shadow">
        @csrf
        <div class="mb-4">
            <label for="year" class="block font-semibold mb-1">Năm</label>
            <input type="number" name="year" id="year" class="form-control" value="{{ old('year', date('Y')) }}" required min="2020" max="2100">
        </div>
        <div class="mb-4">
            <label for="target_amount" class="block font-semibold mb-1">Số tiền mục tiêu (VNĐ)</label>
            <input type="number" name="target_amount" id="target_amount" class="form-control" required min="0" step="1000" placeholder="VD: 1000000000" value="{{ old('target_amount') }}">
        </div>
        <div class="mb-4">
            <label for="department_id" class="block font-semibold mb-1">Phòng ban (tùy chọn)</label>
            <select name="department_id" id="department_id" class="form-control">
                <option value="">-- Toàn công ty --</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Lưu mục tiêu</button>
    </form>

    {{-- Danh sách mục tiêu đã tạo --}}
    @php
        $targets = \App\Models\FinancialTarget::with('department')->orderByDesc('year')->get();
    @endphp

    <div class="mt-8 bg-gray-50 border rounded p-4">
        <h3 class="font-bold mb-2 text-lg">Danh sách mục tiêu đã tạo</h3>
        @if($targets->isEmpty())
            <div class="text-gray-500">Chưa có mục tiêu nào.</div>
        @else
        <table class="w-full text-sm mb-2">
            <thead>
                <tr>
                    <th class="py-2 px-2 border-b">Năm</th>
                    <th class="py-2 px-2 border-b">Phòng ban</th>
                    <th class="py-2 px-2 border-b">Số tiền mục tiêu</th>
                    <th class="py-2 px-2 border-b">Đã đạt</th>
                    <th class="py-2 px-2 border-b">% hoàn thành</th>
                    <th class="py-2 px-2 border-b">Còn thiếu</th>
                    <th class="py-2 px-2 border-b">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($targets as $target)
                @php
                    // Đảm bảo doanh thu tính đúng theo năm và phòng ban
                    $totalRevenue = \App\Models\FinancialRecord::whereYear('record_date', $target->year)
                        ->when($target->department_id, function($q) use ($target) {
                            return $q->where('department_id', $target->department_id);
                        })
                        ->where('status', 'admin_approved') // CHỈ TÍNH DOANH THU ĐÃ ĐƯỢC ADMIN DUYỆT
                        ->sum('revenue');
                    $percent = $target->target_amount > 0 ? round(($totalRevenue / $target->target_amount) * 100, 2) : 0;
                    $remain = max(0, $target->target_amount - $totalRevenue);
                @endphp
                <tr>
                    <td class="py-2 px-2 border-b">{{ $target->year }}</td>
                    <td class="py-2 px-2 border-b">{{ $target->department ? $target->department->name : 'Toàn công ty' }}</td>
                    <td class="py-2 px-2 border-b">{{ number_format($target->target_amount, 0, ',', '.') }} VNĐ</td>
                    <td class="py-2 px-2 border-b">{{ number_format($totalRevenue, 0, ',', '.') }} VNĐ</td>
                    <td class="py-2 px-2 border-b">{{ $percent }}%</td>
                    <td class="py-2 px-2 border-b text-red-600">{{ number_format($remain, 0, ',', '.') }} VNĐ</td>
                    <td class="py-2 px-2 border-b">
                        <form action="{{ route('admin.targets.destroy', $target->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Bạn có chắc chắn muốn xóa mục tiêu này?')">
                                Xóa
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@endsection
