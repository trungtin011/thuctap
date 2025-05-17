<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Admin - Company Revenue</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 font-sans">
    <div class="min-h-screen flex flex-col">
        <div class="flex flex-1 min-h-0">
            <!-- Sidebar -->
            <aside class="w-64 bg-indigo-700 text-white flex-shrink-0 flex flex-col">
                <div class="p-4 border-b border-indigo-800">
                    <h1 class="text-2xl font-bold">Admin Panel</h1>
                </div>
                <nav class="flex-1 p-4 space-y-2">
                    <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded hover:bg-indigo-800">Dashboard</a>
                    <a href="{{ route('users.index') }}" class="block px-3 py-2 rounded hover:bg-indigo-800">Quản lý người dùng</a>
                    <a href="{{ route('roles.index') }}" class="block px-3 py-2 rounded hover:bg-indigo-800">Quản lý vai trò</a>
                    <a href="{{ route('departments.index') }}" class="block px-3 py-2 rounded hover:bg-indigo-800">Quản lý phòng ban</a>
                    {{-- Thêm các menu admin khác nếu cần --}}
                </nav>
                <div class="p-4 border-t border-indigo-800 mt-auto">
                    <div class="mb-2">{{ Auth::user()->name }}</div>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-white hover:underline">Đăng xuất</button>
                    </form>
                </div>
            </aside>
            <!-- Main content -->
            <main class="flex-1 flex flex-col min-h-screen">
                <header class="bg-white shadow p-4">
                    <span class="font-semibold text-lg">@yield('title')</span>
                </header>
                <div class="flex-grow container mx-auto p-4">
                    @if (session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    @yield('content')
                </div>
                <footer class="bg-gray-800 text-white text-center p-4">
                    <p>&copy; 2025 Company Revenue. All rights reserved.</p>
                </footer>
            </main>
        </div>
    </div>
</body>
</html>
