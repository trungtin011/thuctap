@extends('layouts.navigation')

@section('content')
    <!-- Thông báo -->
    <div class="bg-green-100 text-dark border-l-4 border-green-700 my-4 text-sm flex justify-between p-4">
        Chào mừng đến với 365pay.vn - Mua thẻ cào, thẻ game, thẻ online. Đổi thẻ cào thành tiền mặt với chiết khấu hợp lý. Liên hệ
        ngay để được hỗ trợ đổi thẻ!
    </div>
    <h2 class="py-4 text-2xl font-bold text-center">Đổi thẻ cào</h2>
    <div class="flex flex-col space-y-2">
        <span class="text-sm text-red-600 font-bold">
            - không nhận api từ game bài, thẻ ăn cắp, lừa đảo, không rõ nguồn gốc, thẻ rút từ visa,credit card...phát hiện
            khóa vv không hoàn số dư
        </span>
        <span class="text-sm">
            - Vui lòng đọc <span class="text-blue-600 font-bold">Điều Khoản</span> <span class="font-bold">tại đây</span>, Đổi
            thẻ sang thẻ game rẻ hơn tại 365pay.vn
        </span>
        <span class="text-sm">
            -<span class="text-red-600 font-bold">Tạo web con đổi thẻ, bán thẻ miễn phí</span> <span class="font-bold">tại
                đây</span>. Hướng dẫn tích hợp API gạch thẻ tự động cho Shop: <span class="font-bold">tại đây</span>
        </span>
        <span class="text-sm">
            Lịch sử nạp thẻ <span class="font-bold">tại đây</span>, Thông kê <span class="font-bold">tại đây</span>, Nhận
            thông báo Telegram <span class="font-bold">tại đây</span>
        </span>
    </div>

    <!-- Header với nút và thông tin người dùng -->
    <div class="flex justify-between items-center py-4">
        <div class="flex items-center space-x-2 w-full">
            <form action="" method="POST" class="flex items-center space-x-2 w-full">
                <select class="border border-gray-300 rounded px-2 py-1 text-sm w-1/4">
                    <option>Viettel</option>
                    <option>Vinaphone</option>
                    <option>Mobifone</option>
                </select>

                <input type="text" class="border border-gray-300 rounded px-2 py-1 text-sm w-1/4" placeholder="Mã nạp">

                <input type="text" class="border border-gray-300 rounded px-2 py-1 text-sm w-1/4" placeholder="Số Serial">

                <select id="price" name="amount[]" class="border border-gray-300 rounded px-2 py-1 text-sm w-1/4">
                    <option name="">--- Mệnh giá ---</option>
                    <option value="10000">10,000
                        đ - Thực nhận 8,600
                        đ </option>
                    <option value="20000">20,000
                        đ - Thực nhận 17,400
                        đ </option>
                    <option value="30000">30,000
                        đ - Thực nhận 25,650
                        đ </option>
                    <option value="50000">50,000
                        đ - Thực nhận 44,250
                        đ </option>
                    <option value="100000">100,000
                        đ - Thực nhận 88,500
                        đ </option>
                    <option value="200000">200,000
                        đ - Thực nhận 177,000
                        đ </option>
                    <option value="300000">300,000
                        đ - Thực nhận 262,500
                        đ </option>
                    <option value="500000">500,000
                        đ - Thực nhận 432,500
                        đ </option>
                    <option value="1000000">1,000,000
                        đ - Thực nhận 860,000
                        đ </option>
                </select>

                <button class="bg-yellow-500 text-white px-4 py-2 rounded text-sm">
                    Gửi
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

    <!-- Phần hướng dẫn -->
    <div class="mb-6">
        <h2 class="text-lg font-bold mb-2">Mua mã thẻ điện thoại, game, thẻ online</h2>
        <p class="text-sm">
            - Nap.top: Dịch vụ nạp tiền, nạp thẻ game, thẻ điện thoại, thẻ online. Link nạp ngay: <a href="#"
                class="text-blue-500">CardOnline</a><br>
            - Mua mã thẻ Viettel, Vinaphone, Mobifone, Gate, Vcoin, Garena, Zing, Scoin, VegaID, Appota, Bit từ hệ thống<br>
            - API nạp thẻ nhanh, tự động, tích hợp API, liên kết thẻ, giao dịch nhanh gọn.
        </p>
    </div>

    <!-- Phần nhà mạng -->
    <div class="mb-6">
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-4">
            <div class="border p-2 text-center">
                <img src="" alt="Viettel" class="mx-auto">
                <p class="text-sm mt-2">Thẻ Viettel</p>
            </div>
            <div class="border p-2 text-center">
                <img src="" alt="Vinaphone" class="mx-auto">
                <p class="text-sm mt-2">Thẻ Vinaphone</p>
            </div>
            <div class="border p-2 text-center">
                <img src="" alt="Mobifone" class="mx-auto">
                <p class="text-sm mt-2">Thẻ Mobifone</p>
            </div>
            <div class="border p-2 text-center">
                <img src="" alt="Vietnamobile" class="mx-auto">
                <p class="text-sm mt-2">Thẻ Vietnamobile</p>
            </div>
            <div class="border p-2 text-center">
                <img src="" alt="Mobile" class="mx-auto">
                <p class="text-sm mt-2">Thẻ Mobile</p>
            </div>
            <div class="border p-2 text-center">
                <img src="" alt="Garena" class="mx-auto">
                <p class="text-sm mt-2">Thẻ Garena</p>
            </div>
            <div class="border p-2 text-center">
                <img src="" alt="Zing" class="mx-auto">
                <p class="text-sm mt-2">Thẻ Zing</p>
            </div>
            <div class="border p-2 text-center">
                <img src="" alt="Vcoin" class="mx-auto">
                <p class="text-sm mt-2">Thẻ Vcoin</p>
            </div>
            <div class="border p-2 text-center">
                <img src="" alt="Gate" class="mx-auto">
                <p class="text-sm mt-2">Thẻ Gate</p>
            </div>
            <div class="border p-2 text-center">
                <img src="" alt="FunCard" class="mx-auto">
                <p class="text-sm mt-2">Thẻ FunCard</p>
            </div>
            <div class="border p-2 text-center">
                <img src="" alt="Scoin" class="mx-auto">
                <p class="text-sm mt-2">Thẻ Scoin</p>
            </div>
            <div class="border p-2 text-center">
                <img src="" alt="Gosu" class="mx-auto">
                <p class="text-sm mt-2">Thẻ Gosu</p>
            </div>
            <div class="border p-2 text-center">
                <img src="" alt="Scoin" class="mx-auto">
                <p class="text-sm mt-2">Thẻ Scoin</p>
            </div>
            <div class="border p-2 text-center">
                <img src="" alt="VegaID" class="mx-auto">
                <p class="text-sm mt-2">Thẻ Vega ID</p>
            </div>
            <div class="border p-2 text-center">
                <img src="" alt="Appota" class="mx-auto">
                <p class="text-sm mt-2">Thẻ Appota</p>
            </div>
            <div class="border p-2 text-center">
                <img src="" alt="Bit" class="mx-auto">
                <p class="text-sm mt-2">Thẻ Bit</p>
            </div>
        </div>
    </div>

    <!-- Phần ứng dụng/game -->
    <div class="mb-6">
        <h2 class="text-lg font-bold mb-2">Nạp tiền điện thoại, tiền game, mua vé data</h2>
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-4">
            <div class="border p-2 text-center">
                <img src="" alt="Free Fire" class="mx-auto">
                <p class="text-sm mt-2">Free Fire (Kim cương)</p>
            </div>
            <div class="border p-2 text-center">
                <img src="" alt="Viettel" class="mx-auto">
                <p class="text-sm mt-2">Viettel (Nạp tiền)</p>
            </div>
            <div class="border p-2 text-center">
                <img src="" alt="PUBG" class="mx-auto">
                <p class="text-sm mt-2">PUBG Mobile VN</p>
            </div>
            <div class="border p-2 text-center">
                <img src="" alt="Vinaphone" class="mx-auto">
                <p class="text-sm mt-2">Vinaphone (Nạp tiền)</p>
            </div>
            <div class="border p-2 text-center">
                <img src="" alt="Ninja" class="mx-auto">
                <p class="text-sm mt-2">Ninja King</p>
            </div>
        </div>
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
