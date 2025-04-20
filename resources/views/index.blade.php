@extends('layouts.navigation')

@section('content')
    <!-- Success or Error Messages -->
    @if (session('success'))
        <div
            class="alert border-l-4 border-green-700 bg-green-100 text-red-700 my-4 text-sm p-4 mt-5 alert-dismissible alert-custom flex justify-between">
            <ul class="mb-0 pl-3">
                <li>{{ session('success') }}</li>
            </ul>
            <a href="{{ url()->current() }}" class="close" data-dismiss="alert" aria-label="close">×</a>
        </div>
    @endif
    @if (session('error'))
        <div
            class="alert border-l-4 border-red-700 bg-red-100 text-red-700 my-4 text-sm p-4 mt-5 alert-dismissible alert-custom flex justify-between">
            <ul class="mb-0 pl-3">
                <li>{{ session('error') }}</li>
            </ul>
            <a href="{{ url()->current() }}" class="close" data-dismiss="alert" aria-label="close">×</a>
        </div>
    @endif
    @if ($errors->any())
        <div
            class="alert bg-red-100 border-l-4 border-red-700 text-red-700 px-4 py-3 mt-5 alert-dismissible alert-custom flex justify-between">
            <ul class="mb-0 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <a href="{{ url()->current() }}" class="close" data-dismiss="alert" aria-label="close">×</a>
        </div>
    @endif
    <!-- Thông báo -->
    <div class="bg-green-100 text-dark border-l-4 border-green-700 my-4 text-sm flex justify-between p-4">
        Chào mừng đến với 365pay.vn - Mua thẻ cào, thẻ game, thẻ online. Đổi thẻ cào thành tiền mặt với chiết khấu hợp lý.
        Liên hệ
        ngay để được hỗ trợ đổi thẻ!
    </div>

    <h2 class="py-4 text-2xl font-bold text-center">Đổi thẻ cào</h2>

    <div class="flex justify-between items-center py-4">
        <div class="flex items-center space-x-2 w-full">
            <form action="{{ route('cards.submit') }}" method="POST" class="flex items-center space-x-2 w-full">
                @csrf
                <select name="telco" class="border border-gray-300 rounded px-2 py-1 text-sm w-1/4">
                    <option value="Viettel" {{ old('telco') == 'Viettel' ? 'selected' : '' }}>Viettel</option>
                    <option value="Vinaphone" {{ old('telco') == 'Vinaphone' ? 'selected' : '' }}>Vinaphone</option>
                    <option value="Mobifone" {{ old('telco') == 'Mobifone' ? 'selected' : '' }}>Mobifone</option>
                </select>

                <input type="text" name="card_code" value="{{ old('card_code') }}"
                    class="border border-gray-300 rounded px-2 py-1 text-sm w-1/4" placeholder="Mã nạp">

                <input type="text" name="card_serial" value="{{ old('card_serial') }}"
                    class="border border-gray-300 rounded px-2 py-1 text-sm w-1/4" placeholder="Số Serial">

                <select id="price" name="amount" class="border border-gray-300 rounded px-2 py-1 text-sm w-1/4">
                    <option value="">--- Mệnh giá ---</option>
                    <option value="10000" {{ old('amount') == '10000' ? 'selected' : '' }}>10,000 đ - Thực nhận 8,600 đ
                    </option>
                    <option value="20000" {{ old('amount') == '20000' ? 'selected' : '' }}>20,000 đ - Thực nhận 17,400 đ
                    </option>
                    <option value="30000" {{ old('amount') == '30000' ? 'selected' : '' }}>30,000 đ - Thực nhận 25,650 đ
                    </option>
                    <option value="50000" {{ old('amount') == '50000' ? 'selected' : '' }}>50,000 đ - Thực nhận 44,250 đ
                    </option>
                    <option value="100000" {{ old('amount') == '100000' ? 'selected' : '' }}>100,000 đ - Thực nhận 88,500 đ
                    </option>
                    <option value="200000" {{ old('amount') == '200000' ? 'selected' : '' }}>200,000 đ - Thực nhận 177,000
                        đ</option>
                    <option value="300000" {{ old('amount') == '300000' ? 'selected' : '' }}>300,000 đ - Thực nhận 262,500
                        đ</option>
                    <option value="500000" {{ old('amount') == '500000' ? 'selected' : '' }}>500,000 đ - Thực nhận 432,500
                        đ</option>
                    <option value="1000000" {{ old('amount') == '1000000' ? 'selected' : '' }}>1,000,000 đ - Thực nhận
                        860,000 đ</option>
                </select>

                <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded text-sm w-full sm:w-auto">
                    Nạp
                </button>
            </form>
        </div>
    </div>

    <!-- Bảng giá cước -->
    <div class="overflow-x-auto mb-6">
        <table class="min-w-full border border-gray-300 text-sm">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border px-4 py-2">Nhà mạng</th>
                    <th class="border px-4 py-2">10.000</th>
                    <th class="border px-4 py-2">20.000</th>
                    <th class="border px-4 py-2">50.000</th>
                    <th class="border px-4 py-2">100.000</th>
                    <th class="border px-4 py-2">200.000</th>
                    <th class="border px-4 py-2">500.000</th>
                    <th class="border px-4 py-2">1.000.000</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="border px-4 py-2">HD Viettel</td>
                    <td class="border px-4 py-2">14%</td>
                    <td class="border px-4 py-2">15%</td>
                    <td class="border px-4 py-2">14.5%</td>
                    <td class="border px-4 py-2">11.5%</td>
                    <td class="border px-4 py-2">11.5%</td>
                    <td class="border px-4 py-2">12.5%</td>
                    <td class="border px-4 py-2">11.5%</td>
                </tr>
                <tr>
                    <td class="border px-4 py-2">HD Vinaphone</td>
                    <td class="border px-4 py-2">12%</td>
                    <td class="border px-4 py-2">12.5%</td>
                    <td class="border px-4 py-2">11.5%</td>
                    <td class="border px-4 py-2">11%</td>
                    <td class="border px-4 py-2">11%</td>
                    <td class="border px-4 py-2">11%</td>
                    <td class="border px-4 py-2">12%</td>
                </tr>
                <tr>
                    <td class="border px-4 py-2">HD Mobifone</td>
                    <td class="border px-4 py-2">11.5%</td>
                    <td class="border px-4 py-2">11.5%</td>
                    <td class="border px-4 py-2">12%</td>
                    <td class="border px-4 py-2">11%</td>
                    <td class="border px-4 py-2">10%</td>
                    <td class="border px-4 py-2">12%</td>
                    <td class="border px-4 py-2">11.5%</td>
                </tr>
                <tr>
                    <td class="border px-4 py-2">Đổi thẻ Web cam</td>
                    <td class="border px-4 py-2">11.5%</td>
                    <td class="border px-4 py-2">11.5%</td>
                    <td class="border px-4 py-2">12%</td>
                    <td class="border px-4 py-2">11%</td>
                    <td class="border px-4 py-2">10%</td>
                    <td class="border px-4 py-2">12%</td>
                    <td class="border px-4 py-2">11.5%</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Phần tin tức -->
    <div>
        <h2 class="text-lg font-bold mb-2">Tin tức</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <div class="border p-2">
                <img src="" alt="Tin 1" class="w-full h-32 object-cover">
                <h3 class="text-sm font-bold mt-2">Công tác xử lý khách hàng</h3>
                <p class="text-xs text-gray-600">Cập nhật thông tin xử lý khách hàng nhanh chóng...</p>
            </div>
        </div>
    </div>
@endsection
