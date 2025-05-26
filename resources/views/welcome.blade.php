@extends('layouts.navbar')

@section('title', 'Dashboard')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-10">

        <!-- Slider Container -->
        <div class="relative w-full overflow-hidden rounded-lg shadow-lg mb-16">
            <div class="relative h-64 md:h-96">
                <!-- Slides -->
                <div class="absolute inset-0 transition-transform duration-700 ease-in-out" style="transform: translateX(0%)"
                    data-slide="0">
                    <img src="https://images.unsplash.com/photo-1515377905703-c4788e51af15?auto=format&fit=crop&w=1470&q=80"
                        alt="Advertising Campaign" class="w-full h-full object-cover rounded-lg">
                    <div
                        class="absolute inset-0 bg-indigo-900 bg-opacity-50 rounded-lg flex flex-col justify-center items-center text-center text-white p-6">
                        <h2 class="text-4xl font-extrabold mb-4 drop-shadow-lg">Quản lý Hiệu suất Quảng cáo Toàn Diện</h2>
                        <p class="max-w-xl text-lg drop-shadow-md">Theo dõi doanh thu, chi phí và hiệu quả các chiến dịch
                            trên Facebook, Google, TikTok dễ dàng và chính xác.</p>
                    </div>
                </div>
                <div class="absolute inset-0 transition-transform duration-700 ease-in-out"
                    style="transform: translateX(100%)" data-slide="1">
                    <img src="https://images.unsplash.com/photo-1515377905703-c4788e51af15?auto=format&fit=crop&w=1470&q=80"
                        alt="Team Work" class="w-full h-full object-cover rounded-lg">
                    <div
                        class="absolute inset-0 bg-indigo-900 bg-opacity-50 rounded-lg flex flex-col justify-center items-center text-center text-white p-6">
                        <h2 class="text-4xl font-extrabold mb-4 drop-shadow-lg">Phê duyệt & Quản lý Quyền Riêng Biệt</h2>
                        <p class="max-w-xl text-lg drop-shadow-md">Hệ thống phân quyền rõ ràng với trình duyệt phê duyệt
                            giúp bảo mật và quản lý linh hoạt.</p>
                    </div>
                </div>
                <div class="absolute inset-0 transition-transform duration-700 ease-in-out"
                    style="transform: translateX(200%)" data-slide="2">
                    <img src="https://images.unsplash.com/photo-1497493292307-31c376b6e479?auto=format&fit=crop&w=1470&q=80"
                        alt="Analytics Dashboard" class="w-full h-full object-cover rounded-lg">
                    <div
                        class="absolute inset-0 bg-indigo-900 bg-opacity-50 rounded-lg flex flex-col justify-center items-center text-center text-white p-6">
                        <h2 class="text-4xl font-extrabold mb-4 drop-shadow-lg">Báo cáo Chi tiết & Phân tích Dữ liệu</h2>
                        <p class="max-w-xl text-lg drop-shadow-md">Cung cấp khả năng đánh giá hiệu quả chiến dịch với các
                            báo cáo và biểu đồ thông minh.</p>
                    </div>
                </div>
            </div>

            <!-- Slider Controls -->
            <div class="absolute bottom-5 left-1/2 transform -translate-x-1/2 flex space-x-3 z-30">
                <button
                    class="w-4 h-4 rounded-full bg-white bg-opacity-50 hover:bg-opacity-75 focus:outline-none slider-dot"
                    data-slide="0" aria-label="Slide 1"></button>
                <button
                    class="w-4 h-4 rounded-full bg-white bg-opacity-50 hover:bg-opacity-75 focus:outline-none slider-dot"
                    data-slide="1" aria-label="Slide 2"></button>
                <button
                    class="w-4 h-4 rounded-full bg-white bg-opacity-50 hover:bg-opacity-75 focus:outline-none slider-dot"
                    data-slide="2" aria-label="Slide 3"></button>
            </div>
        </div>

        <!-- Intro and Services -->
        <section class="mb-16 text-center max-w-4xl mx-auto">
            <h2 class="text-3xl font-bold mb-4 text-gray-900 dark:text-white">Giải pháp Quản lý Doanh thu & Quảng cáo Hiệu
                quả</h2>
            <p class="text-gray-700 dark:text-gray-300 mb-8">Hệ thống giúp bạn theo dõi mọi khía cạnh tài chính và hiệu suất
                quảng cáo trên đa nền tảng, hỗ trợ đưa ra quyết định nhanh chóng và chính xác.</p>
        </section>

        <section class="grid gap-10 md:grid-cols-3">
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 flex flex-col items-center hover:shadow-2xl transition-shadow duration-300">
                <img src="https://images.unsplash.com/photo-1556740738-b6a63e27c4df?auto=format&fit=crop&w=400&q=80"
                    alt="Revenue Tracking" class="w-24 h-24 rounded-full mb-4 object-cover">
                <h3 class="text-xl font-semibold mb-2 text-gray-800 dark:text-gray-100">Theo dõi Doanh thu</h3>
                <p class="text-gray-600 dark:text-gray-300 text-center">Ghi nhận & quản lý doanh thu từ các nền tảng quảng
                    cáo và đại lý một cách chi tiết và minh bạch.</p>
            </div>
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 flex flex-col items-center hover:shadow-2xl transition-shadow duration-300">
                <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=400&q=80"
                    alt="Cost Management" class="w-24 h-24 rounded-full mb-4 object-cover">
                <h3 class="text-xl font-semibold mb-2 text-gray-800 dark:text-gray-100">Quản lý Chi phí</h3>
                <p class="text-gray-600 dark:text-gray-300 text-center">Theo dõi chi phí quảng cáo chính xác, hỗ trợ cân đối
                    ngân sách và tối ưu hóa chi tiêu.</p>
            </div>
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 flex flex-col items-center hover:shadow-2xl transition-shadow duration-300">
                <img src="https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&w=400&q=80"
                    alt="Campaign Performance" class="w-24 h-24 rounded-full mb-4 object-cover">
                <h3 class="text-xl font-semibold mb-2 text-gray-800 dark:text-gray-100">Hiệu suất Chiến dịch</h3>
                <p class="text-gray-600 dark:text-gray-300 text-center">Đánh giá và tối ưu chiến dịch với các chỉ số hiệu
                    quả và báo cáo trực quan.</p>
            </div>
        </section>

        <!-- Call to Action -->
        <section class="mt-20 bg-indigo-600 rounded-lg text-white p-10 text-center">
            <h2 class="text-3xl font-bold mb-4">Bắt đầu Quản lý Hiệu quả Ngay Hôm Nay</h2>
            <p class="max-w-xl mx-auto mb-6">Tham gia hệ thống của chúng tôi để tối ưu hóa chiến dịch quảng cáo và quản lý
                tài chính doanh nghiệp hiệu quả hơn.</p>

            @auth
                @php
                    $role = Auth::user()->role;
                @endphp
                @if ($role && ($role->level === 'admin' || $role->level === 'manager'))
                    <a href="{{ route('dashboard') }}"
                        class="inline-block bg-white text-indigo-600 font-semibold py-3 px-8 rounded shadow hover:bg-gray-100 transition">Đi
                        tới Bảng điều khiển</a>
                @elseif ($role && $role->level === 'employee')
                    <a href="{{ route('employee.financial.index') }}"
                        class="inline-block bg-white text-indigo-600 font-semibold py-3 px-8 rounded shadow hover:bg-gray-100 transition">Tạo
                        bản ghi</a>
                @endif
            @else
                <a href="{{ route('login') }}"
                    class="inline-block bg-white text-indigo-600 font-semibold py-3 px-8 rounded shadow hover:bg-gray-100 transition">Đăng
                    nhập ngay</a>
                <a href="{{ route('register') }}"
                    class="inline-block ml-4 bg-indigo-800 hover:bg-indigo-900 py-3 px-8 rounded font-semibold transition">Đăng
                    ký tài khoản</a>
            @endauth
        </section>
    </div>

    <!-- Slider Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const slides = document.querySelectorAll('[data-slide]');
            const dots = document.querySelectorAll('.slider-dot');
            let currentIndex = 0;

            function updateSlides(newIndex) {
                slides.forEach((slide, i) => {
                    slide.style.transform = `translateX(${100 * (i - newIndex)}%)`;
                });
                dots.forEach(dot => dot.classList.remove('bg-indigo-600', 'bg-opacity-100'));
                dots[newIndex].classList.add('bg-indigo-600', 'bg-opacity-100');
                currentIndex = newIndex;
            }

            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    updateSlides(index);
                });
            });

            updateSlides(0);
        });
    </script>

@endsection
