@extends('layouts.navigation')

@section('content')
    <!-- Success or Error Messages -->
    @if (session('success'))
        <div class="bg-green-100 text-green-700 my-4 text-sm p-4">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 text-red-700 my-4 text-sm p-4">
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="bg-red-100 text-red-700 my-4 text-sm p-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Thông báo -->
    <div class="bg-red-100 text-red-700 my-4 text-sm flex justify-between p-4">
        Chào mừng đến với hệ thống đổi thẻ cào thành tiền mặt với chiết khấu hợp lý. Liên hệ hỗ trợ qua Telegram!
    </div>
    <h2 class="py-4 text-2xl font-bold text-center">Đổi Thẻ Cào</h2>
    <div class="flex flex-col space-y-2">
        <span class="text-sm text-red-600 font-bold">
            - Không nhận thẻ từ game bài, thẻ ăn cắp, lừa đảo, không rõ nguồn gốc, thẻ rút từ visa/credit card. Phát hiện sẽ khóa tài khoản vĩnh viễn và không hoàn số dư.
        </span>
        <span class="text-sm">
            - Vui lòng đọc <a href="/terms" class="text-blue-600 font-bold">Điều Khoản</a>. Đổi thẻ sang thẻ game rẻ hơn tại <a href="https://365pay.vn" class="text-blue-600 font-bold">365pay.vn</a>.
        </span>
        <span class="text-sm">
            - <span class="text-red-600 font-bold">Tạo web con đổi thẻ, bán thẻ miễn phí</span> <a href="/create-subsite" class="font-bold text-blue-600">tại đây</a>. Hướng dẫn tích hợp API gạch thẻ tự động: <a href="/api-docs" class="font-bold text-blue-600">tại đây</a>.
        </span>
        <span class="text-sm">
            Lịch sử nạp thẻ <a href="{{ route('cards.history') }}" class="font-bold text-blue-600">tại đây</a>. Nhận thông báo qua Telegram <a href="https://t.me/your_channel" class="font-bold text-blue-600">tại đây</a>.
        </span>
    </div>

    <!-- Form Đổi Thẻ Cào -->
    <div class="flex justify-between items-center py-4">
        <form action="{{ route('cards.submit') }}" method="POST" class="flex flex-col sm:flex-row items-center space-y-2 sm:space-y-0 sm:space-x-2 w-full">
            @csrf
            <select name="telco" class="border border-gray-300 rounded px-2 py-1 text-sm w-full sm:w-auto">
                <option value="Viettel" {{ old('telco') == 'Viettel' ? 'selected' : '' }}>Viettel</option>
                <option value="Vinaphone" {{ old('telco') == 'Vinaphone' ? 'selected' : '' }}>Vinaphone</option>
                <option value="Mobifone" {{ old('telco') == 'Mobifone' ? 'selected' : '' }}>Mobifone</option>
            </select>

            <input type="text" name="card_code" value="{{ old('card_code') }}" class="border border-gray-300 rounded px-2 py-1 text-sm w-full sm:w-auto" placeholder="Mã nạp" required>

            <input type="text" name="card_serial" value="{{ old('card_serial') }}" class="border border-gray-300 rounded px-2 py-1 text-sm w-full sm:w-auto" placeholder="Số Serial" required>

            <select name="amount" id="price" class="border border-gray-300 rounded px-2 py-1 text-sm w-full sm:w-auto" required>
                <option value="">--- Mệnh giá ---</option>
                <option value="10000" {{ old('amount') == '10000' ? 'selected' : '' }}>10,000 đ - Thực nhận 8,600 đ</option>
                <option value="20000" {{ old('amount') == '20000' ? 'selected' : '' }}>20,000 đ - Thực nhận 17,400 đ</option>
                <option value="30000" {{ old('amount') == '30000' ? 'selected' : '' }}>30,000 đ - Thực nhận 25,650 đ</option>
                <option value="50000" {{ old('amount') == '50000' ? 'selected' : '' }}>50,000 đ - Thực nhận 44,250 đ</option>
                <option value="100000" {{ old('amount') == '100000' ? 'selected' : '' }}>100,000 đ - Thực nhận 88,500 đ</option>
                <option value="200000" {{ old('amount') == '200000' ? 'selected' : '' }}>200,000 đ - Thực nhận 177,000 đ</option>
                <option value="300000" {{ old('amount') == '300000' ? 'selected' : '' }}>300,000 đ - Thực nhận 262,500 đ</option>
                <option value="500000" {{ old('amount') == '500000' ? 'selected' : '' }}>500,000 đ - Thực nhận 432,500 đ</option>
                <option value="1000000" {{ old('amount') == '1000000' ? 'selected' : '' }}>1,000,000 đ - Thực nhận 860,000 đ</option>
            </select>

            <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded text-sm w-full sm:w-auto">
                Gửi
            </button>
        </form>
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

    <!-- Phần hướng dẫn -->
    <div class="mb-6">
        <h2 class="text-lg font-bold mb-2">Mua mã thẻ điện thoại, game, thẻ online</h2>
        <p class="text-sm">
            - Nap.top: Dịch vụ nạp tiền, nạp thẻ game, thẻ điện thoại, thẻ online. Link nạp ngay: <a href="https://cardonline.vn" class="text-blue-500">CardOnline</a><br>
            - Mua mã thẻ Viettel, Vinaphone, Mobifone, Gate, Vcoin, Garena, Zing, Scoin, VegaID, Appota, Bit từ hệ thống.<br>
            - API nạp thẻ nhanh, tự động, tích hợp API, liên kết thẻ, giao dịch nhanh gọn. Xem chi tiết <a href="/api-docs" class="text-blue-500">tại đây</a>.
        </p>
    </div>

    <!-- Phần nhà mạng -->
    <div class="mb-6">
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-4">
            @foreach (['Viettel', 'Vinaphone', 'Mobifone', 'Vietnamobile', 'Mobile', 'Garena', 'Zing', 'Vcoin', 'Gate', 'FunCard', 'Scoin', 'Gosu', 'VegaID', 'Appota', 'Bit'] as $card)
                <div class="border p-2 text-center">
                    <img src="https://via.placeholder.com/100x50?text={{ $card }}" alt="{{ $card }}" class="mx-auto">
                    <p class="text-sm mt-2">Thẻ {{ $card }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Phần ứng dụng/game -->
    <div class="mb-6">
        <h2 class="text-lg font-bold mb-2">Nạp tiền điện thoại, tiền game, mua vé data</h2>
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-4">
            @foreach ([
                ['name' => 'Free Fire (Kim cương)', 'image' => 'FreeFire'],
                ['name' => 'Viettel (Nạp tiền)', 'image' => 'Viettel'],
                ['name' => 'PUBG Mobile VN', 'image' => 'PUBG'],
                ['name' => 'Vinaphone (Nạp tiền)', 'image' => 'Vinaphone'],
                ['name' => 'Ninja King', 'image' => 'Ninja']
            ] as $game)
                <div class="border p-2 text-center">
                    <img src="https://via.placeholder.com/50?text={{ $game['image'] }}" alt="{{ $game['name'] }}" class="mx-auto">
                    <p class="text-sm mt-2">{{ $game['name'] }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Phần tin tức -->
    <div>
        <h2 class="text-lg font-bold mb-2">Tin tức</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <div class="border p-2">
                <img src="https://via.placeholder.com/150?text=Tin1" alt="Tin 1" class="w-full h-32 object-cover">
                <h3 class="text-sm font-bold mt-2">Công tác xử lý khách hàng</h3>
                <p class="text-xs text-gray-600">Cập nhật thông tin xử lý khách hàng nhanh chóng...</p>
            </div>
        </div>
    </div>
@endsection