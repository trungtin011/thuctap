@extends('layouts.navigation')

@section('content')
    <div class="container mx-auto mt-2">
        <div class="flex justify-start mb-10">
            <a href="{{ route('home') }}" class="text-gray-500">Trang chủ</a>
            <span class="mx-2 text-gray-500">/</span>
            <a href="#" class="text-gray-500">Đăng ký tài khoản</a>
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
        <h1 class="text-2xl font-bold mb-4">Đăng ký</h1>
        <div class="flex">
            <div class="w-1/2">
                <form action="{{ route('register') }}" method="POST">
                    @csrf
                    <div class="mb-4 flex items-center gap-5">
                        <label for="name" class="block text-gray-700 w-1/2">Tên đăng nhập:</label>
                        <input type="text" name="name" id="name"
                            class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="Tên đăng nhập">
                    </div>
                    <div class="mb-4 flex items-center gap-4">
                        <label for="email" class="block text-gray-700 w-1/2">Email</label>
                        <div class="flex flex-col w-full">
                            <input type="text" name="email" id="email"
                                class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="Số đt hoặc email" value="{{ old('email') }}">
                        </div>
                    </div>
                    <div class="mb-4 flex items-center gap-5">
                        <label for="password" class="block text-gray-700 w-1/2">Mật khẩu</label> <input type="password"
                            name="password" id="password" class="w-full px-3 py-2 border border-gray-300 rounded"
                            placeholder="Mật khẩu" value="{{ old('password') }}">
                    </div>
                    <div class="mb-4 flex items-center gap-4">
                        <label for="password2" class="block text-gray-700 w-1/2">Mật khẩu cấp 2</label>
                        <div class="flex flex-col w-full">
                            <input type="password" name="password_level2" id="password2"
                                class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="Mật khẩu cấp 2" value="{{ old('password_level2') }}">
                            <p class="text-red-500 text-sm mt-1">Mật khẩu cấp 2 không được giống với mật khẩu đăng nhập.</p>
                        </div>
                    </div>
                    <div class="mb-4 flex items-center gap-5">
                        <div class="g-recaptcha" data-sitekey="your_site_key"></div>
                    </div>
                    <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded">Đăng ký</button>
                </form>
            </div>
            <div class="w-1/2 pl-10">
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4">
                    <p>Hoặc bạn có thể đăng ký tài khoản bằng cách nhắn tin theo cú pháp sau (phí 1000đ)</p>
                    <p class="font-bold text-red-600">NS TSR DK [tên đăng nhập] gửi 8066</p>
                    <p>Ví dụ: NS TSR DK minhthu123 gửi 8066</p>
                </div>
            </div>
        </div>
    </div>
@endsection
