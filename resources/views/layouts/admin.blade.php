<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Admin - Company Revenue</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://kit-pro.fontawesome.com/releases/v5.12.1/css/pro.min.css">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/d70c32c211.js" crossorigin="anonymous"></script>
    <!-- Tailwind CSS -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <!-- Thêm CSS cho dropdown -->
    <style>
        .dropdown-submenu {
            display: none;
        }

        .dropdown-submenu.active {
            display: block;
        }

        .dropdown-toggle::after {
            content: '\f078';
            /* Font Awesome chevron-down */
            font-family: "Font Awesome 5 Pro";
            margin-left: 8px;
            transition: transform 0.3s ease;
        }

        .dropdown-toggle.active::after {
            transform: rotate(180deg);
            /* Xoay mũi tên khi mở dropdown */
        }
    </style>
</head>

<body class="bg-gray-50 font-sans">
    <div class="flex flex-col min-h-screen">
        <div class="flex flex-1 min-h-0">
            <!-- Sidebar -->
            <aside class="w-64 bg-indigo-700 text-white flex-shrink-0 flex flex-col">
                <div class="p-4 border-b border-indigo-800">
                    <h1 class="text-2xl font-bold">Admin Panel</h1>
                </div>
                <nav class="flex-1 p-4 space-y-2">
                    <!-- Liên kết Tổng doanh thu -->
                    <a href="{{ route('admin.financial.total_revenue') }}"
                        class="block px-3 py-2 rounded hover:bg-indigo-800 font-semibold text-yellow-300">
                        <i class="mr-2">
                            <span class="fas fa-chart-line"></span>
                        </i>
                        Tổng doanh thu
                    </a>
                    <!-- Mục tiêu doanh thu -->
                    <a href="{{ route('admin.targets.create') }}"
                        class="block px-3 py-2 rounded hover:bg-indigo-800 font-semibold text-green-300">
                        <i class="mr-2">
                            <span class="fas fa-bullseye"></span>
                        </i>
                        Mục tiêu doanh thu
                    </a>
                    <!-- Dropdown submenu cho Quản lý -->
                    <div>
                        <button
                            class="dropdown-toggle block px-3 py-2 rounded hover:bg-indigo-800 font-semibold w-full text-left"
                            onclick="toggleDropdown(this)">
                            <i class="mr-2">
                                <span class="fas fa-cogs"></span>
                            </i>
                            Quản lý
                        </button>
                        <ul class="dropdown-submenu pl-4 space-y-1">
                            <li>
                                <a href="{{ route('users.index') }}"
                                    class="block px-3 py-2 rounded hover:bg-indigo-800">
                                    <i class="mr-2">
                                        <span class="fas fa-user"></span>
                                    </i>
                                    Người dùng
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('roles.index') }}"
                                    class="block px-3 py-2 rounded hover:bg-indigo-800">
                                    <i class="mr-2">
                                        <span class="fas fa-user-tag"></span>
                                    </i>
                                    Vai trò
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('departments.index') }}"
                                    class="block px-3 py-2 rounded hover:bg-indigo-800">
                                    <i class="mr-2">
                                        <span class="fas fa-building"></span>
                                    </i>
                                    Phòng ban
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('platforms.index') }}"
                                    class="block px-3 py-2 rounded hover:bg-indigo-800">
                                    <i class="mr-2">
                                        <span class="fas fa-desktop"></span>
                                    </i>
                                    Nền tảng
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('expense-types.index') }}"
                                    class="block px-3 py-2 rounded hover:bg-indigo-800">
                                    <i class="mr-2">
                                        <span class="fas fa-money-bill-wave"></span>
                                    </i>
                                    Chi phí
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.financial.index') }}"
                                    class="block px-3 py-2 rounded hover:bg-indigo-800">
                                    <i class="mr-2">
                                        <span class="fas fa-money-check-alt"></span>
                                    </i>
                                    Duyệt
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>

                <div class="p-4 border-t border-indigo-800 mt-auto">
                    <div class="mb-2">{{ Auth::user()->name }}</div>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-white hover:underline">
                            <i class="mr-2">
                                <span class="fas fa-sign-out-alt"></span>
                            </i>
                            Đăng xuất
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Main content -->
            <main class="flex-1 flex flex-col min-h-screen">
                <header class="bg-white shadow p-4">
                    <span class="font-semibold text-lg">@yield('title')</span>
                </header>
                <div class="">
                    @if (session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif
                    @yield('content')
                    @yield('scripts')
                </div>
                <footer class="bg-gray-800 text-white text-center p-4 mt-auto">
                    <p>© 2025 Company Revenue. All rights reserved.</p>
                </footer>
            </main>
        </div>
    </div>

    <!-- Script cho dropdown -->
    <script>
        function toggleDropdown(element) {
            const submenu = element.nextElementSibling;
            const isActive = submenu.classList.contains('active');

            // Đóng tất cả các submenu khác (nếu cần)
            document.querySelectorAll('.dropdown-submenu').forEach(menu => {
                menu.classList.remove('active');
                menu.previousElementSibling.classList.remove('active');
            });

            // Mở/đóng submenu hiện tại
            if (!isActive) {
                submenu.classList.add('active');
                element.classList.add('active');
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</body>

</html>
