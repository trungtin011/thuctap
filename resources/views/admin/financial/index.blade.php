@extends('layouts.admin')

@section('title', 'Quản lý phê duyệt')

@section('content')
    <div class="flex items-center mt-4" style="background-color: #f1f1f1; padding: 20px 16px;">
        <h2 class="text-md underline">
            Danh sách doanh thu đã được quản lý phê duyệt
        </h2>
    </div>
    <div class="mx-auto">
        <form method="GET" action="{{ route('admin.financial.index') }}"
            class="flex items-center flex-row md:flex-col gap-5 p-4">
            {{-- Lọc phòng ban --}}
            <div class="border border-dashed border-gray-400 p-2">
                <label for="department_id" class="block text-sm font-medium text-gray-700">Phòng ban</label>
                <select name="department_id" id="department_id" class="w-full border-gray-300 rounded shadow-sm">
                    <option value="">-- Tất cả --</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Lọc nền tảng --}}
            <div class="border border-dashed border-gray-400 p-2">
                <label for="platform_id" class="block text-sm font-medium text-gray-700">Nền tảng</label>
                <select name="platform_id" id="platform_id" class="w-full border-gray-300 rounded shadow-sm">
                    <option value="">-- Tất cả --</option>
                    @foreach ($platforms as $plat)
                        <option value="{{ $plat->id }}" {{ request('platform_id') == $plat->id ? 'selected' : '' }}>
                            {{ $plat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Lọc theo ngày bắt đầu --}}
            <div class="border border-dashed border-gray-400 p-2">
                <label for="start_date" class="block text-sm font-medium text-gray-700">Từ ngày</label>
                <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                    class="w-full border-gray-300 rounded shadow-sm">
            </div>

            {{-- Lọc theo ngày kết thúc --}}
            <div class="border border-dashed border-gray-400 p-2">
                <label for="end_date" class="block text-sm font-medium text-gray-700">Đến ngày</label>
                <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                    class="w-full border-gray-300 rounded shadow-sm">
            </div>

            {{-- Trạng thái (ẩn hoặc cố định nếu luôn là 'manager_approved') --}}
            <div class="border border-dashed border-gray-400 p-2">
                <label for="status" class="block text-sm font-medium text-gray-700">Trạng thái</label>
                <select name="status" id="status">
                    <option value="manager_approved" {{ request('status') == 'manager_approved' ? 'selected' : '' }}>Quản lý
                        duyệt</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                    <option value="admin_approved" {{ request('status') == 'admin_approved' ? 'selected' : '' }}>Admin
                        duyệt</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                </select>
            </div>

            {{-- Nút lọc và đặt lại --}}
            <div class="flex items-end gap-2">
                @if (request()->hasAny(['department_id', 'platform_id', 'start_date', 'end_date', 'status']))
                    <a href="{{ route('admin.financial.index') }}"
                        class="bg-red-600 text-white px-6 py-4 hover:bg-red-400"><i class="fa-solid fa-rotate"></i></a>
                @endif
                <button type="submit" class="bg-blue-600 text-white px-6 py-4 hover:bg-blue-700"><i
                        class="fa-solid fa-filter"></i></button>
            </div>
        </form>

        @if ($financialRecords->isEmpty())
            <p class="text-gray-600 text-center my-10">Không có đơn nào chờ phê duyệt.</p>
        @else
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4">
                <table class="w-full text-sm text-left text-gray-900">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 bg-white">ID</th>
                            <th scope="col" class="px-6 py-3 bg-white">Phòng ban</th>
                            <th scope="col" class="px-6 py-3 bg-white">Nền tảng</th>
                            <th scope="col" class="px-6 py-3 bg-white">Doanh thu</th>
                            <th scope="col" class="px-6 py-3 bg-white">Trạng thái</th>
                            <th scope="col" class="px-6 py-3 bg-white">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($financialRecords as $record)
                            <tr class="border-b border-gray-200 bg-white hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $record->id }}
                                </td>
                                <td class="px-6 py-4">{{ $record->department->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4">{{ $record->platform->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4">{{ number_format($record->revenue) }} VND</td>
                                <td class="px-6 py-4">
                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">Quản
                                        lý đã duyệt</span>
                                </td>
                                <td class="px-6 py-4">
                                    <form action="{{ route('admin.financial.approve', $record->id) }}" method="POST"
                                        class="inline-block"
                                        onsubmit="return confirm('Bạn có chắc chắn muốn phê duyệt đơn này không?')">
                                        @csrf
                                        <button type="submit"
                                            class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 text-sm">Phê
                                            duyệt</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $financialRecords->links() }}
            </div>
        @endif
        <div class="mt-6 text-end">
            <a href="{{ route('admin.financial.history') }}"
                class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">Xem lịch sử đã phê duyệt</a>
        </div>
    </div>
@endsection
