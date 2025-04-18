
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
</head>

<body>
    <header class="bg-white shadow">
        <div class="container mx-auto flex justify-between items-center py-3" style="max-width: 1150px">
            <div class="flex items-center">
                <img alt="Company logo" class="h-10" height="50" src="{{ asset('logo/logo.png') }}" />
            </div>
            @if (Auth::guard('web')->check() || Auth::guard('admin')->check())
                <div class="flex space-x-2 items-center">
                    <!-- User/Admin Name -->
                    <a class="bg-gray-500 text-white px-3 py-2 rounded flex items-center text-sm" href="#">
                        <i class="fas fa-user mr-2"></i>
                        @if (Auth::guard('web')->check())
                            {{ Auth::guard('web')->user()->name }}
                        @elseif (Auth::guard('admin')->check())
                            {{ Auth::guard('admin')->user()->username }}
                        @endif
                    </a>
                    <!-- Balance for Web Users -->
                    @if (Auth::guard('web')->check())
                        <span class="bg-blue-600 text-white px-3 py-2 rounded text-sm flex items-center">
                            <i class="fas fa-wallet mr-2"></i>
                            Số dư: {{ number_format(Auth::guard('web')->user()->balance, 0, ',', '.') }} đ
                        </span>
                    @endif
                    <!-- Logout Button -->
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="bg-red-600 text-white px-3 py-2 rounded flex items-center text-sm">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Đăng xuất
                        </button>
                    </form>
                </div>
            @else
                <div class="flex space-x-2">
                    <a class="bg-gray-500 text-white px-3 py-2 rounded flex items-center text-sm"
                        href="{{ route('register') }}">
                        <i class="fas fa-user mr-2"></i>
                        Đăng ký
                    </a>
                    <a class="bg-blue-700 text-white px-3 py-2 rounded flex items-center text-sm"
                        href="{{ route('login') }}">
                        <i class="fas fa-lock mr-2"></i>
                        Đăng nhập
                    </a>
                </div>
            @endif
        </div>
        <nav class="bg-gray-100">
            <div class="container mx-auto flex space-x-4 py-2" style="max-width: 1150px">
                <a class="text-dark-700 hover:text-dark-700" href="#">MUA THẺ</a>
                <a class="text-dark-700 hover:text-dark-700" href="#">ĐỔI THẺ</a>
                <a class="text-dark-700 hover:text-dark-700" href="#">NẠP ĐIỆN THOẠI</a>
                <a class="text-dark-700 hover:text-dark-700" href="#">TOPUP CAROT</a>
                <a class="text-dark-700 hover:text-dark-700" href="#">TOPUP GAME</a>
                <a class="text-dark-700 hover:text-dark-700" href="#">CHUYỂN QUỸ</a>
                <a class="text-dark-700 hover:text-dark-700" href="#">RÚT QUỸ</a>
                <a class="text-dark-700 hover:text-dark-700" href="#">LỊCH SỬ</a>
                <a class="text-gray-700 hover:text-dark-700" href="#">NẠP QUỸ</a>
                <div class="relative">
                    <button class="text-dark-700 hover:text-dark-700">
                        DỊCH VỤ
                        <i class="fas fa-caret-down"></i>
                    </button>
                    <!-- Dropdown menu placeholder -->
                    <div class="absolute bg-white shadow mt-2 hidden">
                        <a class="block px-4 py-2 hover:bg-gray-200" href="#">Option 1</a>
                        <a class="block px-4 py-2 hover:bg-gray-200" href="#">Option 2</a>
                        <a class="block px-4 py-2 hover:bg-gray-200" href="#">Option 3</a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <div class="container mx-auto" style="max-width: 1150px">
        @yield('content')
    </div>
</body>

</html>
