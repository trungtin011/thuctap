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
            <a href="{{ route('login.admin') }}">
                <button class="dark:bg-gray-800 dark:hover:bg-gray-700 text-white px-4 py-2 focus:outline-none">
                    Đăng nhập Admin
                </button>
            </a>
        </div>
        <div class="flex">
            <div class="w-1/2">
                <form action="{{ route('user.login') }}" method="POST">
                    @csrf
                    <div class="mb-4 flex items-center gap-5">
                        <label class="w-1/2 block text-gray-700 font-bold mb-2" for="email">Tên đăng nhập:</label>
                        <input
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            name="email" id="email" type="text" placeholder="Email hoặc số điện thoại">
                    </div>
                    <div class="mb-4 flex items-center gap-5">
                        <label class="w-1/2 block text-gray-700 font-bold mb-2" for="password">Mật khẩu</label>
                        <input
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            name="password" id="password" type="password" placeholder="Mật khẩu">
                    </div>
                    <div class="mb-4">
                        <div class="g-recaptcha" data-sitekey="your_site_key"></div>
                    </div>
                    <div class="mb-4">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember" class="text-gray-700">Ghi nhớ?</label>
                    </div>
                    <div class="flex items-center gap-2">
                        <button
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                            type="submit">
                            Đăng nhập
                        </button>
                        <a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800"
                            href="">
                            Quên mật khẩu?
                        </a>
                    </div>
                </form>
            </div>
            <div class="w-1/2 pl-10">
                <div class="max-w-lg mx-auto p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700">
                    <p>Chức năng mở khóa tài khoản bằng SMS đang được phát triển và sẽ có trong phiên bản chính thức</p>
                </div>
            </div>
        </div>
    </div>
@endsection
