<div id="sideBar"
    class="relative flex flex-col flex-wrap bg-white border-r border-gray-300 p-6 flex-none w-64 md:-ml-64 md:fixed md:top-0 md:z-30 md:h-screen md:shadow-xl animated faster">


    <!-- sidebar content -->
    <div class="flex flex-col">

        <!-- sidebar toggle -->
        <div class="text-right hidden md:block mb-4">
            <button id="sideBarHideBtn">
                <i class="fad fa-times-circle"></i>
            </button>
        </div>
        <!-- end sidebar toggle -->

        <p class="uppercase text-xs text-gray-600 mb-4 tracking-wider">homes</p>

        <!-- link -->
        <a href="{{ route('admin.dashboard') }}"
            class="mb-3 capitalize font-medium text-sm hover:text-teal-600 transition ease-in-out duration-500">
            <i class="fad fa-chart-pie text-xs mr-2"></i>
            Tổng quan phân tích
        </a>

        <p class="uppercase text-xs text-gray-600 mb-4 mt-4 tracking-wider">apps</p>

        <!-- link -->
        <a href="{{ route('admin.users.index') }}"
            class="mb-3 capitalize font-medium text-sm hover:text-teal-600 transition ease-in-out duration-500">
            <i class="fad fa-user text-xs mr-2"></i>
            Người dùng
        </a>

        <!-- link -->
        <a href="{{ route('admin.cards.index') }}"
            class="mb-3 capitalize font-medium text-sm hover:text-teal-600 transition ease-in-out duration-500">
            <i class="fad fa-credit-card text-xs mr-2"></i>
            Thanh toán
        </a>

        <p class="uppercase text-xs text-gray-600 mb-4 mt-4 tracking-wider">tìm kiếm</p>

        <!-- link -->
        <a href="{{ route('admin.search.history') }}"
            class="mb-3 capitalize font-medium text-sm hover:text-teal-600 transition ease-in-out duration-500">
            <i class="fad fa-search text-xs mr-2"></i>
            Lịch sử tìm kiếm
        </a>
        
        <!-- link -->
        <a href="{{ route('admin.search.statistics') }}"
            class="mb-3 capitalize font-medium text-sm hover:text-teal-600 transition ease-in-out duration-500">
            <i class="fad fa-chart-bar text-xs mr-2"></i>
            Thống kê tìm kiếm
        </a>
        
        <!-- link -->
        <a href="{{ route('admin.search.evaluate.form') }}"
            class="mb-3 capitalize font-medium text-sm hover:text-teal-600 transition ease-in-out duration-500">
            <i class="fad fa-handshake text-xs mr-2"></i>
            Đánh giá tìm kiếm
        </a>
        
    </div>
</div>
