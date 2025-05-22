<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Company Revenue</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- TailwindCSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 font-sans">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="text-white shadow-md" style="background: linear-gradient(to right, #4f46e5, #312e81);">
            <div class="container mx-auto px-4 py-3 flex justify-between items-center">
                <a href="{{ route('welcome') }}" class="text-lg font-bold tracking-tight flex items-center">
                    <i class="fas fa-chart-line mr-2"></i> Company Revenue
                </a>
                <!-- Hamburger Menu for Mobile -->
                <button class="md:hidden text-white" onclick="toggleMenu()">
                    <i class="fas fa-bars text-lg"></i>
                </button>
                <!-- Navigation -->
                <nav class="nav-menu hidden md:flex items-center space-x-4">
                    <a href="{{ route('welcome') }}"
                       class="nav-link text-white hover:text-yellow-100 text-sm font-medium {{ request()->routeIs('welcome') ? 'active' : '' }}">
                        Trang chủ
                    </a>
                 
                    @auth
                        @php
                            $role = Auth::user()->role;
                        @endphp
                        @if ($role && $role->level === 'admin')
                            <a href="{{ route('dashboard') }}"
                               class="nav-link text-white hover:text-yellow-100 text-sm font-medium {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                Quản trị
                            </a>
                        @elseif ($role && $role->level === 'manager')
                            <a href="{{ route('manager.financial.index') }}"
                               class="nav-link text-white hover:text-yellow-100 text-sm font-medium {{ request()->routeIs('manager.financial.index') ? 'active' : '' }}">
                                Quản lý doanh thu
                            </a>
                        @elseif ($role && $role->level === 'employee')
                            <a href="{{ route('employee.financial.index') }}"
                               class="nav-link text-white hover:text-yellow-100 text-sm font-medium {{ request()->routeIs('employee.financial.index') ? 'active' : '' }}">
                                Nhập doanh thu
                            </a>
                        @endif
                    @endauth
                    <!-- User Dropdown -->
                    @auth
                        <div class="relative">
                            <button class="flex items-center text-white hover:text-yellow-100 text-sm font-medium"
                                    onclick="toggleDropdown(this)">
                                <i class="fas fa-user-circle mr-1"></i> {{ Auth::user()->name }}
                                <i class="fas fa-chevron-down ml-1"></i>
                            </button>
                            <div class="dropdown-menu absolute right-0 mt-2 w-40 bg-white text-gray-800 shadow-lg rounded-lg overflow-hidden z-50">
                                <a href="#profile" class="block px-3 py-1.5 text-sm hover:bg-gray-100 flex items-center">
                                    <i class="fas fa-user mr-2"></i> Hồ sơ
                                </a>
                                <a href="#settings" class="block px-3 py-1.5 text-sm hover:bg-gray-100 flex items-center">
                                    <i class="fas fa-cog mr-2"></i> Cài đặt
                                </a>
                                <form action="{{ route('logout') }}" method="POST" class="w-full" onsubmit="handleLogout(event)">
                                    @csrf
                                    <button type="submit"
                                            class="w-full text-left px-3 py-1.5 text-sm hover:bg-gray-100 flex items-center">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Đăng xuất
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}"
                           class="nav-link text-white hover:text-yellow-100 text-sm font-medium {{ request()->routeIs('login') ? 'active' : '' }}">
                            Đăng nhập
                        </a>
                    @endauth
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow container mx-auto px-4 py-4">
            @if (session('success'))
                <div class="alert bg-green-100 border-l-4 border-green-500 text-green-700 p-3 mb-4 rounded-lg shadow-sm text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-4 rounded-lg shadow-sm text-sm">
                    {{ session('error') }}
                </div>
            @endif
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-gradient-to-r from-indigo-900 to-gray-900 text-white py-6">
            <div class="container mx-auto px-4 grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- About -->
                <div>
                    <h3 class="text-base font-semibold mb-3 flex items-center">
                        <i class="fas fa-chart-line mr-2"></i> Company Revenue
                    </h3>
                    <p class="text-xs">Giải pháp tài chính và phân tích dữ liệu tiên tiến cho doanh nghiệp hiện đại.</p>
                </div>
                <!-- Quick Links -->
                <div>
                    <h3 class="text-base font-semibold mb-3">Liên kết nhanh</h3>
                    <ul class="space-y-1">
                        <li><a href="{{ route('welcome') }}" class="footer-link text-xs hover:text-yellow-100">Trang chủ</a></li>
                        <li><a href="#about" class="footer-link text-xs hover:text-yellow-100">Giới thiệu</a></li>
                        <li><a href="#services" class="footer-link text-xs hover:text-yellow-100">Dịch vụ</a></li>
                        <li><a href="#contact" class="footer-link text-xs hover:text-yellow-100">Liên hệ</a></li>
                    </ul>
                </div>
                <!-- Contact Info -->
                <div>
                    <h3 class="text-base font-semibold mb-3">Liên hệ</h3>
                    <p class="text-xs"><i class="fas fa-map-marker-alt mr-2"></i> 123 Đường Doanh Thu, TP.HCM, VN</p>
                    <p class="text-xs"><i class="fas fa-phone-alt mr-2"></i> +84 123 456 789</p>
                    <p class="text-xs"><i class="fas fa-envelope mr-2"></i> contact@companyrevenue.vn</p>
                </div>
                <!-- Social Media & Newsletter -->
                <div>
                    <h3 class="text-base font-semibold mb-3">Kết nối</h3>
                    <div class="flex space-x-3 mb-3">
                        <a href="#facebook" class="text-white hover:text-yellow-100"><i class="fab fa-facebook-f text-base"></i></a>
                        <a href="#twitter" class="text-white hover:text-yellow-100"><i class="fab fa-twitter text-base"></i></a>
                        <a href="#linkedin" class="text-white hover:text-yellow-100"><i class="fab fa-linkedin-in text-base"></i></a>
                    </div>
                    <form class="flex">
                        <input type="email" placeholder="Nhập email" class="w-full p-1.5 rounded-l-md text-gray-800 text-xs focus:outline-none">
                        <button type="submit" class="bg-indigo-600 p-1.5 rounded-r-md text-white hover:bg-indigo-500"><i class="fas fa-paper-plane"></i></button>
                    </form>
                </div>
            </div>
            <div class="border-t border-indigo-800 mt-6 pt-3 text-center">
                <p class="text-xs">© 2025 Company Revenue. All rights reserved.</p>
            </div>
            <!-- Back to Top Button -->
            <button onclick="scrollToTop()" class="back-to-top fixed bottom-4 right-4 bg-indigo-600 text-white p-2 rounded-full shadow-lg hover:bg-indigo-500 opacity-70 hover:opacity-100">
                <i class="fas fa-arrow-up"></i>
            </button>
        </footer>
    </div>

    <!-- JavaScript Includes -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function toggleDropdown(button) {
            // Close other dropdowns and mega menus
            document.querySelectorAll('.dropdown-menu, .mega-menu').forEach(menu => {
                if (menu !== button.nextElementSibling) {
                    menu.classList.remove('show');
                }
            });
            // Toggle the clicked dropdown
            const dropdown = button.nextElementSibling;
            dropdown.classList.toggle('show');
        }

        function toggleMegaMenu(button) {
            // Close other dropdowns and mega menus
            document.querySelectorAll('.dropdown-menu, .mega-menu').forEach(menu => {
                if (menu !== button.nextElementSibling) {
                    menu.classList.remove('show');
                }
            });
            // Toggle the clicked mega menu
            const megaMenu = button.nextElementSibling;
            megaMenu.classList.toggle('show');
        }

        function toggleMenu() {
            const menu = document.querySelector('.nav-menu');
            // Close all dropdowns and mega menus when toggling mobile menu
            document.querySelectorAll('.dropdown-menu, .mega-menu').forEach(menu => {
                menu.classList.remove('show');
            });
            menu.classList.toggle('active');
        }

        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Close dropdowns and mega menus when clicking outside
        document.addEventListener('click', function(event) {
            const dropdowns = document.querySelectorAll('.dropdown-menu, .mega-menu');
            dropdowns.forEach(dropdown => {
                const parent = dropdown.parentElement;
                if (!parent.contains(event.target)) {
                    dropdown.classList.remove('show');
                }
            });
        });

        // SweetAlert2 for logout confirmation
        function handleLogout(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Bạn có chắc muốn đăng xuất?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Đăng xuất',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.closest('form').submit();
                }
            });
        }
    </script>
    @yield('scripts')
</body>
</html>