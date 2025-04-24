@extends('layouts.navigation')

<style>
    .dotted-border-bottom {
        border-bottom: 1px dotted #c6d9d7;
    }
</style>
@section('content')
    <div class="max-w-6xl mx-auto py-4">
        <nav class="mb-4 text-[13px] text-[#666]">
            <a href="{{ route('home') }}" class="hover:underline">Trang chủ</a>
            <span class="mx-1">/</span>
            <span class="font-semibold">Sửa thông tin</span>
        </nav>
        <div class="space-y-4">
            <!-- Success or Error Messages -->
            @if (session('success'))
                <div
                    class="alert border-l-4 border-green-700 bg-green-100 text-black-700 my-4 text-sm p-4 mt-5 alert-dismissible alert-custom flex justify-between">
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
        </div>
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Left main box -->
            @if ($editMode)
                @include('user.profile.edit_profile')
            @else
                <div class="flex-1 border border-[#ddd]">
                    <div class="border-b border-[#ddd] px-4 py-2">
                        <h2 class="font-bold text-[13px] text-[#333]">THÔNG TIN TÀI KHOẢN</h2>
                    </div>
                    <table class="w-full text-[15px] text-[#333]">
                        <tbody>
                            @if (Auth::guard('web')->check())
                                <tr class="dotted-border-bottom">
                                    <td class="px-4 py-2 w-[150px] text-[#333]">Tên đăng nhập</td>
                                    <td class="px-4 py-2 font-semibold">
                                        {{ Auth::guard('web')->check() ? Auth::guard('web')->user()->name : Auth::guard('admin')->user()->username }}
                                    </td>
                                </tr>
                                <tr class="dotted-border-bottom">
                                    <td class="px-4 py-2">Email</td>
                                    <td class="px-4 py-2">
                                        {{ Auth::guard('web')->check() ? Auth::guard('web')->user()->email : Auth::guard('admin')->user()->email }}
                                    </td>
                                </tr>
                            @else
                                <tr class="dotted-border-bottom">
                                    <td class="px-4 py-2">Họ và tên:</td>
                                    <td class="px-4 py-2 font-semibold">{{ Auth::guard('admin')->user()->username }}</td>
                                </tr>
                            @endif
                            {{-- <tr class="dotted-border-bottom">
                            <td class="px-4 py-2">Nhóm</td>
                            <td class="px-4 py-2 font-semibold">Thành viên</td>
                        </tr> --}}
                            {{-- <tr class="dotted-border-bottom align-top">
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
                        </tr> --}}
                            <tr>
                                <td class="px-4 py-2">Ngày đăng ký:</td>
                                <td class="px-4 py-2 font-semibold">
                                    {{ Auth::guard('web')->check() ? Auth::guard('web')->user()->created_at : Auth::guard('admin')->user()->created_at }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="flex gap-2 p-4 border-t border-[#ddd]">
                        <a href="{{ route('profile', ['edit' => true]) }}"
                            class="bg-[#3c763d] text-white text-[12px] font-semibold px-3 py-1 rounded hover:bg-[#2b542c]">
                            Sửa thông tin
                        </a>
                        <button
                            class="bg-[#337ab7] text-white text-[12px] font-semibold px-3 py-1 rounded hover:bg-[#286090]"
                            type="button">
                            Đổi mật khẩu
                        </button>
                        <button
                            class="bg-[#f0ad4e] text-white text-[12px] font-semibold px-3 py-1 rounded hover:bg-[#ec971f]"
                            type="button">
                            Đổi mật khẩu cấp 2
                        </button>
                    </div>
                </div>
            @endif
            <!-- Right side box -->
            <div class="w-full max-w-xs space-y-4">
                <div class="border border-[#ddd] p-3 text-[15px] text-[#333]">
                    <div class="flex justify-between items-center mb-2">
                        <span>Số dư quỹ</span>
                        <button
                            class="bg-[#003366] text-white text-[11px] font-semibold px-3 py-1 rounded hover:bg-[#002244]"
                            type="button">
                            Lịch sử quỹ
                        </button>
                    </div>
                    <div class="font-semibold">
                        {{ Auth::guard('web')->check() ? Auth::guard('web')->user()->balance : Auth::guard('admin')->user()->balance }}<sup>đ</sup>
                    </div>
                </div>
                {{-- <div class="border border-[#ddd] p-3 text-[11px] text-[#333]">
                    <div class="flex justify-between items-center mb-2">
                        Bảo mật bằng mật khẩu cấp 2
                        <button
                            class="bg-[#003366] text-white text-[11px] font-semibold px-3 py-1 rounded hover:bg-[#002244]"
                            type="button">
                            Bảo mật
                        </button>
                    </div>
                    <div class="mb-2">Đang bật</div>
                </div> --}}
            </div>

        </div>

        <div id="ajax-edit-container"></div>

    </div>
@endsection
