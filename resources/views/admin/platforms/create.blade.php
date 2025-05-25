@extends('layouts.admin')

@section('title', 'Tạo nền tảng')

@section('content')
    <div class="max-w-4xl py-4 mx-auto">
        <h2 class="text-2xl font-bold mb-6">Tạo nền tảng</h2>
        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('platforms.store') }}" method="POST" class="bg-white rounded shadow p-6">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Tên nền tảng</label>
                <input type="text"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    id="name" name="name" value="{{ old('name') }}" required maxlength="50">
            </div>

            <!-- Chỉ số -->
            <div class="mb-4">
                <h3 class="text-lg font-semibold mb-2">Chỉ số</h3>
                <div id="metrics-container">
                    @if (old('metrics'))
                        @foreach (old('metrics') as $index => $metric)
                            <div class="flex flex-col mb-2">
                                <input type="text" name="metrics[{{ $index }}][name]"
                                    value="{{ $metric['name'] }}" placeholder="Tên chỉ số"
                                    class="mb-3 border-gray-300 rounded-md shadow-sm p-2" required>

                                <input type="text" name="metrics[{{ $index }}][unit]"
                                    value="{{ $metric['unit'] }}" placeholder="Đơn vị"
                                    class="mb-3 border-gray-300 rounded-md shadow-sm p-2">
                                    
                                <select name="metrics[{{ $index }}][data_type]"
                                    class="mb-3 border-gray-300 rounded-md shadow-sm p-2" required>
                                    <option value="int" {{ $metric['data_type'] == 'int' ? 'selected' : '' }}>Số nguyên
                                    </option>
                                    <option value="float" {{ $metric['data_type'] == 'float' ? 'selected' : '' }}>Số thực
                                    </option>
                                    <option value="string" {{ $metric['data_type'] == 'string' ? 'selected' : '' }}>Chuỗi
                                    </option>
                                </select>
                                <button type="button"
                                    class="remove-metric bg-red-500 text-white px-2 py-1 rounded">Xóa</button>
                            </div>
                        @endforeach
                    @endif
                </div>
                <button type="button" id="add-metric" class="bg-green-500 text-white px-4 py-2 rounded mt-2">Thêm chỉ
                    số</button>
            </div>

            <div class="flex space-x-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Tạo</button>
                <a href="{{ route('platforms.index') }}"
                    class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">Hủy</a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('add-metric').addEventListener('click', function() {
            const container = document.getElementById('metrics-container');
            const index = container.children.length;
            const row = document.createElement('div');
            row.className = 'flex flex-col mb-2';
            row.innerHTML = `
                <div class="mb-3">
                    <input type="text" name="metrics[${index}][name]" placeholder="Tên chỉ số" class="w-full border-gray-300 rounded-md shadow-sm p-2" required>
                    <div class="text-sm mt-2">Ví dụ: Lượt click, Tỷ lệ chuyển đổi</div>
                </div>
                <div class="mb-3">
                    <input type="text" name="metrics[${index}][unit]" placeholder="Đơn vị" class="w-full border-gray-300 rounded-md shadow-sm p-2">
                    <div class="text-sm mt-2">Ví dụ: lượt, % (có thể để trống)</div>
                </div>
                <div class="mb-3">
                    <select name="metrics[${index}][data_type]" class="w-full border-gray-300 rounded-md shadow-sm p-2" required>
                        <option value="int">Số nguyên</option>
                        <option value="float">Số thực</option>
                        <option value="string">Chuỗi</option>
                    </select>
                    <div class="text-sm mt-2">Loại giá trị: số nguyên (1, 2), số thực (1.5), chuỗi (text)</div>
                </div>
                <button type="button" class="remove-metric bg-red-500 text-white px-2 py-1 rounded self-start">Xóa</button>
            `;
            container.appendChild(row);
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-metric')) {
                e.target.parentElement.remove();
            }
        });
    </script>
@endsection
