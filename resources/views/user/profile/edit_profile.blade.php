<div class="flex-1 border border-[#ddd]">
    <section class="flex-1 border border-gray-200 rounded-md px-4 py-2">
        <div class="border-b border-[#ddd]">
            <h2 class="font-bold text-[13px] text-[#333]">SỬA THÔNG TIN TÀI KHOẢN</h2>
        </div>
        <!-- Success or Error Messages -->
        @if (session('success'))
            <div
                class="alert border-l-4 border-green-700 bg-green-100 text-red-700 my-4 text-sm p-4 mt-5 alert-dismissible alert-custom flex justify-between">
                <ul class="mb-0 pl-3">
                    <li>{{ session('success') }}</li>
                </ul>
                <a href="{{ url()->current() }}" class="close" data-dismiss="alert" aria-label="close">×</a>
            </div>
        @endif
        @if (session('error'))
            <div
                class="alert border-l-4 border-red-700 bg-red-100 text-red-700 my-4 text-sm p-4 mt-5 alert-dismissible alert-custom flex justify-between">
                <ul class="mb-0 pl-3">
                    <li>{{ session('error') }}</li>
                </ul>
                <a href="{{ url()->current() }}" class="close" data-dismiss="alert" aria-label="close">×</a>
            </div>
        @endif
        @if ($errors->any())
            <div
                class="alert bg-red-100 border-l-4 border-red-700 text-red-700 px-4 py-3 mt-5 alert-dismissible alert-custom flex justify-between">
                <ul class="mb-0 pl-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <a href="{{ url()->current() }}" class="close" data-dismiss="alert" aria-label="close">×</a>
            </div>
        @endif
        <form action="{{ route('profile.update') }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <div class="flex flex-col sm:flex-row sm:items-center sm:gap-4">
                <label for="name" class="w-32 font-semibold text-sm mb-1 sm:mb-0">Họ và tên</label>
                <input id="name" name="name" type="text" placeholder=""
                    value="{{ old('name', $users->name) }}"
                    class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm" />
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center sm:gap-4">
                <label for="email" class="w-32 font-semibold text-sm mb-1 sm:mb-0">Email</label>
                <input id="email" name="email" type="email" placeholder=""
                    value="{{ old('email', $users->email) }}"
                    class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm" />
            </div>
            <hr class="my-6 border-gray-200" />
            <div class="flex justify-center">
                <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded px-4 py-2">
                    Cập nhật thông tin
                </button>
            </div>
        </form>
    </section>
</div>
