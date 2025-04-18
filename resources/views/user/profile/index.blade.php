@extends('layouts.navigation')

<style>
    .dotted-border-bottom {
        border-bottom: 1px dotted #c6d9d7;
    }
</style>
@section('content')
    <div class="max-w-6xl mx-auto py-4">
        <nav class="mb-4 text-[13px] text-[#666]">
            <a href="#" class="hover:underline">Trang chủ</a>
            <span class="mx-1">/</span>
            <span class="font-semibold">Sửa thông tin</span>
        </nav>
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Left main box -->
            <div class="flex-1 border border-[#ddd]">
                <div class="border-b border-[#ddd] px-4 py-2">
                    <h2 class="font-bold text-[13px] text-[#333]">THÔNG TIN TÀI KHOẢN</h2>
                </div>
                <table class="w-full text-[11px] text-[#333]">
                    <tbody>
                        <tr class="dotted-border-bottom">
                            <td class="px-4 py-2 w-[120px] text-[#333]">Tên đăng nhập</td>
                            <td class="px-4 py-2 font-semibold">ykhoaban</td>
                        </tr>
                        <tr class="dotted-border-bottom">
                            <td class="px-4 py-2">Họ và tên:</td>
                            <td class="px-4 py-2 font-semibold">Y Khoa Eban</td>
                        </tr>
                        <tr class="dotted-border-bottom">
                            <td class="px-4 py-2">Email</td>
                            <td class="px-4 py-2"></td>
                        </tr>
                        <tr class="dotted-border-bottom">
                            <td class="px-4 py-2">Nhóm</td>
                            <td class="px-4 py-2 font-semibold">Thành viên</td>
                        </tr>
                        <tr class="dotted-border-bottom align-top">
                            <td class="px-4 py-2">Xác thực email:</td>
                            <td class="px-4 py-2 flex items-center gap-2">
                                <span
                                    class="inline-block bg-[#d9534f] text-white text-[10px] font-semibold px-2 py-[2px] rounded">
                                    Chưa xác thực
                                </span>
                                <span class="text-[10px] text-[#999]">Xác minh ngay</span>
                            </td>
                        </tr>
                        <tr class="dotted-border-bottom align-top">
                            <td class="px-4 py-2">Xác thực giấy tờ:</td>
                            <td class="px-4 py-2 flex items-center gap-2">
                                <span
                                    class="inline-block bg-[#d9534f] text-white text-[10px] font-semibold px-2 py-[2px] rounded">
                                    Chưa xác thực
                                </span>
                                <span class="text-[10px] text-[#999]">Xác minh ngay</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2">Ngày đăng ký:</td>
                            <td class="px-4 py-2 font-semibold">2025-04-14 13:43:00</td>
                        </tr>
                    </tbody>
                </table>
                <div class="flex gap-2 p-4 border-t border-[#ddd]">
                    <button class="bg-[#3c763d] text-white text-[12px] font-semibold px-3 py-1 rounded hover:bg-[#2b542c]"
                        type="button">
                        Sửa thông tin
                    </button>
                    <button class="bg-[#337ab7] text-white text-[12px] font-semibold px-3 py-1 rounded hover:bg-[#286090]"
                        type="button">
                        Đổi mật khẩu
                    </button>
                    <button class="bg-[#f0ad4e] text-white text-[12px] font-semibold px-3 py-1 rounded hover:bg-[#ec971f]"
                        type="button">
                        Đổi mật khẩu cấp 2
                    </button>
                </div>
            </div>
            <!-- Right side box -->
            <div class="w-full max-w-xs space-y-4">
                <div class="border border-[#ddd] p-3 text-[11px] text-[#333]">
                    <div class="flex justify-between items-center mb-2">
                        <span>Số dư quỹ</span>
                        <button
                            class="bg-[#003366] text-white text-[11px] font-semibold px-3 py-1 rounded hover:bg-[#002244]"
                            type="button">
                            Lịch sử quỹ
                        </button>
                    </div>
                    <div class="font-semibold">0đ</div>
                </div>
                <div class="border border-[#ddd] p-3 text-[11px] text-[#333]">
                    <div class="flex justify-between items-center mb-2">
                        Bảo mật bằng mật khẩu cấp 2
                        <button
                            class="bg-[#003366] text-white text-[11px] font-semibold px-3 py-1 rounded hover:bg-[#002244]"
                            type="button">
                            Bảo mật
                        </button>
                    </div>
                    <div class="mb-2">Đang bật</div>
                </div>
            </div>
        </div>
    </div>
@endsection
