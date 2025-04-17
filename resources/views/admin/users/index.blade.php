@extends('layouts.navigation_admin')

@section('content')
    <div class="mx-auto mt-10" style="width: 1540px">
        <h1 class="text-2xl font-bold mb-5">Quản lý người dùng</h1>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Tên người dùng
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Email
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Số dư
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Ngày tạo
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Thao tác
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr
                            class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $user->name }}
                            </th>
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white normal-case">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                @if ($user->balance == 0)
                                    <span
                                        class="bg-red-100 text-red-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                                        Chưa nạp tiền
                                    </span>
                                @else
                                    {{ number_format($user->balance, 0, ',', '.') }} VNĐ
                                @endif
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $user->created_at }}
                            </td>
                            <td class="px-6 py-4 flex space-x-3">
                                <a href="{{ route('admin.users.show', $user->id) }}"
                                    class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Xem</a>

                                <a href="{{ route('admin.users.edit', $user->id) }}"
                                    class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Sửa</a>

                                <!-- Button mở modal -->
                                <button onclick="openModal({{ $user->id }})"
                                    class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                                    Cộng tiền
                                </button>

                                <a href="{{ route('admin.users.toggleStatus', $user->id) }}"
                                    class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                                    {{ $user->status === 'locked' ? 'Mở khóa' : 'Khóa' }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Cộng tiền -->
    <div id="addBalanceModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-md w-96">
            <h2 class="text-lg font-bold mb-4">Cộng tiền cho người dùng</h2>
            <form id="addBalanceForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="amount" class="block text-gray-700 font-semibold mb-1">Số tiền cần cộng (VNĐ):</label>
                    <input type="number" name="amount" id="amount" min="1000"
                        class="w-full p-2 border border-gray-300 rounded focus:ring focus:border-blue-400"
                        placeholder="Nhập số tiền...">
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="closeModal()"
                        class="bg-gray-500 text-white px-4 py-2 rounded mr-2 hover:bg-gray-600">
                        Hủy
                    </button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Xác nhận
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function openModal(userId) {
            let form = document.getElementById('addBalanceForm');
            form.action = '/admin/users/' + userId + '/add-balance';
            document.getElementById('addBalanceModal').classList.remove('hidden');
            document.getElementById('addBalanceModal').classList.add('flex');
        }

        function closeModal() {
            document.getElementById('addBalanceModal').classList.remove('flex');
            document.getElementById('addBalanceModal').classList.add('hidden');
        }
    </script>
@endsection
