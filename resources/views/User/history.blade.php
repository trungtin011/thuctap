
@extends('layouts.navigation')

@section('content')
    <h2 class="py-4 text-2xl font-bold text-center">Lịch sử nạp thẻ</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-300 text-sm">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border px-4 py-2">Nhà mạng</th>
                    <th class="border px-4 py-2">Số serial</th>
                    <th class="border px-4 py-2">Mã thẻ</th>
                    <th class="border px-4 py-2">Mệnh giá</th>
                    <th class="border px-4 py-2">Thực nhận</th>
                    <th class="border px-4 py-2">Trạng thái</th>
                    <th class="border px-4 py-2">Thời gian</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cards as $card)
                    <tr>
                        <td class="border px-4 py-2">{{ $card->telco }}</td>
                        <td class="border px-4 py-2">{{ $card->card_serial }}</td>
                        <td class="border px-4 py-2">{{ $card->card_code }}</td>
                        <td class="border px-4 py-2">{{ number_format($card->amount) }} đ</td>
                        <td class="border px-4 py-2">{{ $card->response ? number_format($card->response) . ' đ' : '-' }}</td>
                        <td class="border px-4 py-2">
                            @if ($card->status == 'pending')
                                <span class="text-yellow-600">Đang xử lý</span>
                            @elseif ($card->status == 'success')
                                <span class="text-green-600">Thành công</span>
                            @else
                                <span class="text-red-600">Thất bại</span>
                            @endif
                        </td>
                        <td class="border px-4 py-2">{{ $card->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endforeach
            </tbody>
        </table>
    </div>
    {{ $cards->links() }}
@endsection