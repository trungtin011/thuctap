<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Company Revenue</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
    <div class="min-h-screen flex flex-col">
        <header class="bg-blue-600 text-white p-4">
            <div class="container mx-auto flex justify-between items-center">
                <h1 class="text-2xl font-bold">Company Revenue</h1>
                @auth
                    <div class="flex items-center space-x-4">
                        <span>{{ Auth::user()->name }}</span>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-white hover:underline">Đăng xuất</button>
                        </form>
                    </div>
                @else
                    <div>
                        <a href="{{ route('login') }}" class="text-white hover:underline">Đăng nhập</a>
                        <a href="{{ route('register') }}" class="text-white hover:underline ml-4">Đăng ký</a>
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
            <p>&copy; 2025 Company Revenue. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>