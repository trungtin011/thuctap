@extends('layouts.navigation_admin')

@section('content')
    <div class="mx-auto mt-10" style="width: 1540px">
        <h1 class="text-2xl font-bold mb-5">Quản lý nạp thẻ</h1>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">Nhà mạng</th>
                        <th scope="col" class="px-6 py-3">Số serial</th>
                        <th scope="col" class="px-6 py-3">Mã thẻ</th>
                        <th scope="col" class="px-6 py-3">Mệnh giá</th>
                        <th scope="col" class="px-6 py-3">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cards as $card)
                        <tr class="odd:bg-white even:bg-gray-50 border-b dark:border-gray-700">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $card->telco }}</td>
                            <td class="px-6 py-4">{{ $card->card_serial }}</td>
                            <td class="px-6 py-4">{{ $card->card_code }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                {{ number_format($card->amount) }} đ</td>
                            <td class="px-6 py-4">
                                <form action="{{ route('admin.updateCardStatus', $card->id) }}" method="POST"
                                    class="flex space-x-2" id="status-form-{{ $card->id }}">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="bg-transparent py-1 text-sm"
                                        onchange="updateSelectColor({{ $card->id }})"
                                        id="status-select-{{ $card->id }}">
                                        <option value="pending" {{ $card->status == 'pending' ? 'selected' : '' }}>Đang xử
                                            lý</option>
                                        <option value="success" {{ $card->status == 'success' ? 'selected' : '' }}>Thành
                                            công</option>
                                        <option value="failed" {{ $card->status == 'failed' ? 'selected' : '' }}>Thất bại
                                        </option>
                                    </select>
                                </form>
                            </td>

                            <script>
                                function updateSelectColor(cardId) {
                                    const selectElement = document.getElementById('status-select-' + cardId);
                                    const selectedOption = selectElement.options[selectElement.selectedIndex];

                                    // Cập nhật màu sắc của chữ trong select
                                    if (selectedOption.value === 'pending') {
                                        selectElement.style.color = '#8b6f1d'; // Màu vàng cho "Đang xử lý"
                                    } else if (selectedOption.value === 'success') {
                                        selectElement.style.color = '#006400'; // Màu xanh lá cho "Thành công"
                                    } else if (selectedOption.value === 'failed') {
                                        selectElement.style.color = '#b80000'; // Màu đỏ cho "Thất bại"
                                    }

                                    // Gửi form khi chọn trạng thái
                                    selectElement.form.submit();
                                }

                                // Set màu sắc mặc định cho select khi trang được load
                                window.addEventListener('DOMContentLoaded', function() {
                                    const selects = document.querySelectorAll('select[name="status"]');
                                    selects.forEach(select => {
                                        const selectedOption = select.options[select.selectedIndex];
                                        if (selectedOption.value === 'pending') {
                                            select.style.color = '#8b6f1d';
                                        } else if (selectedOption.value === 'success') {
                                            select.style.color = '#006400';
                                        } else if (selectedOption.value === 'failed') {
                                            select.style.color = '#b80000';
                                        }
                                    });
                                });
                            </script>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $cards->links() }}
        </div>
    </div>
@endsection

<style>
    form {
        margin: 0 !important;
    }

    select {
        margin: 0 !important;
    }
</style>
