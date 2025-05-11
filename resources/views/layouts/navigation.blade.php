<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- Icon 365 -->
    <link rel="shortcut icon" href="{{ asset('logo/Logo.png') }}" type="image/x-icon">
    <title>365Pay</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <!-- Ensure Font Awesome is included for icons -->
</head>

<body>
    <header class="bg-white shadow">
        <div class="container mx-auto flex justify-between items-center py-3" style="max-width: 1150px">
            <div class="flex items-center">
                <img src="{{ asset('logo/Logo.png') }}" class="w-10 flex-none">
                <strong class="capitalize ml-1 flex-1">365Pay</strong>
                <!-- Nút hamburger cho mobile -->
                <button class="sm:hidden text-gray-700 focus:outline-none focus:shadow-outline mx-2"
                    onclick="toggleNav()">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
            @if (Auth::guard('web')->check() || Auth::guard('admin')->check())
                <div class="relative flex items-center space-x-2">
                    <!-- Hiển thị số dư quỹ trên desktop -->
                    <div class="hidden lg:flex items-center space-x-2">
                        <i class="fas fa-wallet"></i>
                        <span>
                            <strong>Số dư:
                                <span
                                    class="text-red-700">{{ Auth::guard('web')->check() ? Auth::guard('web')->user()->balance : Auth::guard('admin')->user()->balance }}<sup>đ</sup></span></strong></span>
                    </div>
                    <!-- Nút hiển thị tên người dùng với dropdown -->
                    <button class="bg-red-700 text-white px-3 py-2 rounded flex items-center text-sm focus:outline-none"
                        onclick="toggleUserDropdown()">
                        <i class="fas fa-user mr-2"></i>
                        @if (Auth::guard('web')->check())
                            {{ Auth::guard('web')->user()->name }}
                        @elseif (Auth::guard('admin')->check())
                            {{ Auth::guard('admin')->user()->username }}
                        @endif
                        <i class="fas fa-caret-down ml-2"></i>
                    </button>
                    <!-- Dropdown menu -->
                    <div id="userDropdown"
                        class="absolute right-0 top-full mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 hidden">
                        <!-- Số dư quỹ trong dropdown trên mobile -->
                        <div class="lg:hidden px-4 py-2 text-sm text-gray-700">
                            <i class="fas fa-wallet mr-2"></i>
                            <strong>Số dư:
                                <span
                                    class="text-red-700">{{ Auth::guard('web')->check() ? Auth::guard('web')->user()->balance : Auth::guard('admin')->user()->balance }}<sup>đ</sup></span></strong></span>
                        </div>
                        @if (Auth::guard('web')->check())
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                href="{{ route('profile') }}">
                                <i class="fas fa-user mr-2"></i>
                                Hồ sơ
                            </a>
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                href="{{ route('cards.history') }}">
                                <i class="fas fa-history mr-2"></i>
                                Lịch sử nạp thẻ
                            </a>
                        @elseif (Auth::guard('admin')->check())
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt mr-2"></i>
                                Quản lý
                            </a>
                        @endif
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-sign-out-alt mr-2"></i>
                                Đăng xuất
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="flex space-x-2">
                    <a class="bg-gray-500 text-white px-3 rounded flex items-center text-sm"
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
        <nav id="navMenu" class="bg-gray-100 sm:block hidden">
            <div class="container mx-auto flex flex-col sm:flex-row sm:space-x-4 py-2" style="max-width: 1150px">
                <a class="text-gray-700 hover:text-gray-900 px-2 py-2 sm:py-0" href="{{ route('home') }}">Nạp thẻ</a>
                {{-- <div class="relative">
                    <button class="text-gray-700 hover:text-gray-900 px-2 py-2 sm:py-0 flex items-center"
                        onclick="toggleServiceDropdown()">
                        Dịch vụ
                        <i class="fas fa-caret-down ml-1"></i>
                    </button>
                    <!-- Dropdown menu -->
                    <div id="serviceDropdown"
                        class="absolute left-0 sm:left-auto sm:right-0 bg-white shadow mt-2 w-48 rounded-md z-10 hidden">
                        <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-200" href="#">Option 1</a>
                        <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-200" href="#">Option 2</a>
                        <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-200" href="#">Option 3</a>
                    </div>
                </div> --}}
                <a class="text-gray-700 hover:text-gray-900 px-2 py-2 sm:py-0" href="{{ url('search') }}">Tin tức</a>
            </div>
        </nav>
    </header>

    <div class="container mx-auto px-2" style="max-width: 1150px">
        @yield('content')
    </div>

    <script>
        function toggleUserDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('hidden');
        }

        function toggleServiceDropdown() {
            const dropdown = document.getElementById('serviceDropdown');
            dropdown.classList.toggle('hidden');
        }

        function toggleNav() {
            const nav = document.getElementById('navMenu');
            nav.classList.toggle('hidden');
        }

        // Đóng dropdown khi nhấp ra ngoài
        document.addEventListener('click', function(event) {
            const userDropdown = document.getElementById('userDropdown');
            const userButton = userDropdown ? userDropdown.previousElementSibling : null;
            const serviceDropdown = document.getElementById('serviceDropdown');
            const serviceButton = serviceDropdown ? serviceDropdown.previousElementSibling : null;

            if (userDropdown && userButton && !userDropdown.contains(event.target) && !userButton.contains(event
                    .target)) {
                userDropdown.classList.add('hidden');
            }

            if (serviceDropdown && serviceButton && !serviceDropdown.contains(event.target) && !serviceButton
                .contains(event.target)) {
                serviceDropdown.classList.add('hidden');
            }
        });
    </script>
</body>

</html>
