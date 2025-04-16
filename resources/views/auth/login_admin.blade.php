@extends('layouts.navigation')

@section('content')
    <div class="container mx-auto mt-2">
        <div class="flex justify-start mb-10">
            <a href="{{ route('home') }}" class="text-gray-500">Trang chủ</a>
            <span class="mx-2 text-gray-500">/</span>
            <a href="#" class="text-gray-500">Đăng nhập</a>
        </div>
        @if ($errors->any())
            <div class="alert bg-red-100 border border-red-400 text-red-700 px-4 py-3 alert-dismissible alert-custom">
                <a href="{{ url()->current() }}" class="close" data-dismiss="alert" aria-label="close">×</a>
                <ul class="mb-0 pl-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <h2 class="text-2xl font-bold mb-6">Đăng nhập tài khoản</h2>
        <!-- Nút chọn quyền đăng nhập -->
        <div class="flex gap-4 mb-6">
            <a href="{{ route('login') }}">
                <button class="dark:bg-gray-800 dark:hover:bg-gray-700 text-white px-4 py-2 focus:outline-none">
                    Đăng nhập Người dùng
                </button>
            </a>
        </div>
        <div class="flex">
            <div class="w-1/2">
                <!-- Form đăng nhập -->
                <form id="login-form" action="{{ route('admin.login') }}" method="POST">
                    @csrf
                    <input type="hidden" id="login-type" name="login_type" value="user">

                    <div class="mb-4">
                        <label for="username" class="block text-gray-700 font-bold mb-2">Tên đăng nhập:</label>
                        <input type="text" name="username" id="username"
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Tên đăng nhập">
                    </div>

                    <div class="mb-4">
                        <label for="password" class="block text-gray-700 font-bold mb-2">Mật khẩu:</label>
                        <input type="password" name="password" id="password"
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Nhập mật khẩu">
                    </div>

                    <div class="mb-4">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember" class="text-gray-700">Ghi nhớ đăng nhập?</label>
                    </div>

                    <button type="submit"
                        class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none">
                        Đăng nhập
                    </button>

                    <div class="text-center mt-4">
                        <a href="#" class="text-blue-500 hover:text-blue-700 text-sm">Quên mật khẩu?</a>
                    </div>
                </form>
            </div>
            <div class="w-1/2 pl-10">
                <div class="max-w-lg mx-auto p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700">
                    <p>Tài khoản bị tạm khóa do đăng nhập sai nhiều lần, vui lòng SMS mở khóa theo cú pháp:</p>
                    <p><strong>NS TSR MK gửi 8066</strong> (lấy lại mk) hoặc <strong>NS TSR MKC2 gửi 8066</strong> (lấy lại
                        mkc2)
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
