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
    <!-- TailwindCSS (optional, consider removing if using Bootstrap) -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
    <div class="min-h-screen flex flex-col">
        <header class="bg-blue-600 text-white p-4">
            <div class="container mx-auto flex justify-between items-center">
                <h1 class="text-2xl font-bold">Company Revenue</h1>
                @auth
                    <div class="flex items-center space-x-4">
                        @php
                            $role = Auth::user()->role;
                        @endphp
                        @if ($role && $role->level === 'admin')
                            <a href="{{ route('dashboard') }}" class="text-white hover:underline">Quản trị hệ thống</a>
                        @elseif ($role && $role->level === 'manager')
                            <a href="{{ route('manager.financial.index') }}" class="text-white hover:underline">Quản lý doanh thu</a>
                        @elseif ($role && $role->level === 'employee')
                            <a href="{{ route('welcome') }}" class="text-white hover:underline">Trang chủ</a>
                            <a href="{{ route('employee.financial.index') }}" class="text-white hover:underline">Nhập doanh thu</a>
                        @endif
                        
                        <span>{{ Auth::user()->name }}</span>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-white hover:underline">Đăng xuất</button>
                        </form>
                    </div>
                @else
                    <div>
                        <a href="{{ route('login') }}" class="text-white hover:underline">Đăng nhập</a>
                    </div>
                @endauth
            </div>
        </header>

        <main class="flex-grow container mx-auto p-4">
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            @yield('content')
        </main>

        <footer class="bg-gray-800 text-white text-center p-4">
            <p>© 2025 Company Revenue. All rights reserved.</p>
        </footer>
    </div>

    <!-- JavaScript Includes -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @yield('scripts')
</body>
</html>