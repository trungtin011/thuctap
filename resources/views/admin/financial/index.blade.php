@extends('layouts.admin')

@section('title', 'Danh sách đơn đã được quản lý phê duyệt')

@section('content')
<div class="max-w-4xl mx-auto">
    <h2 class="text-2xl font-bold mb-6">Danh sách đơn đã được quản lý phê duyệt</h2>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded shadow p-6">
        @if($financialRecords->isEmpty())
            <p class="text-gray-600">Không có đơn nào chờ phê duyệt.</p>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 rounded">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border-b text-left">ID</th>
                        <th class="px-4 py-2 border-b text-left">Phòng ban</th>
                        <th class="px-4 py-2 border-b text-left">Nền tảng</th>
                        <th class="px-4 py-2 border-b text-left">Doanh thu</th>
                        <th class="px-4 py-2 border-b text-left">Trạng thái</th>
                        <th class="px-4 py-2 border-b text-left">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($financialRecords as $record)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border-b">{{ $record->id }}</td>
                            <td class="px-4 py-2 border-b">{{ $record->department->name ?? 'N/A' }}</td>
                            <td class="px-4 py-2 border-b">{{ $record->platform->name ?? 'N/A' }}</td>
                            <td class="px-4 py-2 border-b">{{ number_format($record->revenue) }} VND</td>
                            <td class="px-4 py-2 border-b">
                                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">Quản lý duyệt</span>
                            </td>
                            <td class="px-4 py-2 border-b">
                                <form action="{{ route('admin.financial.approve', $record->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn phê duyệt đơn này không?')">
                                    @csrf
                                    <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 text-sm">Phê duyệt</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        <div class="mt-6">
            <a href="{{ route('admin.financial.history') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">Xem lịch sử đã phê duyệt</a>
        </div>
    </div>
</div>
@endsection
