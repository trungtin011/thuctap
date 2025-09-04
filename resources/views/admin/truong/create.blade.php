@extends('layouts.admin')

@section('title', 'Thêm Trường Dữ Liệu')

@section('content')
    <div class="flex items-center mt-4" style="background-color: #f1f1f1; padding: 20px 16px;">
        <h2 class="text-md underline">Thêm Trường Dữ Liệu Mới</h2>
    </div>

    <div class="p-4 mx-auto max-w-6xl">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.truong.fields.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow-md" id="fields-form">
            @csrf
            <div id="fields-container">
                <div class="flex gap-4 mb-4">
                    <div class="w-1/3">
                        <label class="block text-gray-700 font-medium mb-1">Tên trường</label>
                        <input type="text" name="fields[0][name]"
                            class="w-full border border-gray-300 px-4 py-2 rounded">
                    </div>
                    <div class="w-1/3">
                        <label class="block text-gray-700 font-medium mb-1">Loại dữ liệu</label>
                        <select name="fields[0][type]"
                            class="w-full border border-gray-300 px-4 py-2 rounded">
                            <option value="text">Văn bản</option>
                            <option value="number">Số</option>
                            <option value="email">Email</option>
                            <option value="date">Ngày</option>
                        </select>
                    </div>
                    <div class="w-1/3">
                        <label class="block text-gray-700 font-medium mb-1">Phòng ban</label>
                        <select name="fields[0][department_id]"
                            class="w-full border border-gray-300 px-4 py-2 rounded">
                            <option value="">Chọn phòng ban</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="button" onclick="addField()"
                    class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition mb-4">
                    + Thêm trường nữa
                </button>
            </div>

            <div class="flex justify-between mt-6">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-1"></i> Lưu tất cả
                </button>
                <a href="{{ route('admin.truong.fields.index') }}"
                   class="bg-gray-400 text-white px-6 py-2 rounded hover:bg-gray-500 transition">
                    Hủy
                </a>
            </div>
        </form>
    </div>

    <script>
        let fieldIndex = 1;

        function addField() {
            const container = document.getElementById('fields-container');
            const html = `
                <div class="flex gap-4 mb-4">
                    <div class="w-1/3">
                        <input type="text" name="fields[${fieldIndex}][name]"
                            class="w-full border border-gray-300 px-4 py-2 rounded"
                            placeholder="Tên trường">
                    </div>
                    <div class="w-1/3">
                        <select name="fields[${fieldIndex}][type]"
                            class="w-full border border-gray-300 px-4 py-2 rounded">
                            <option value="text">Văn bản</option>
                            <option value="number">Số</option>
                            <option value="email">Email</option>
                            <option value="date">Ngày</option>
                        </select>
                    </div>
                    <div class="w-1/3">
                        <select name="fields[${fieldIndex}][department_id]"
                            class="w-full border border-gray-300 px-4 py-2 rounded">
                            <option value="">Chọn phòng ban</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            fieldIndex++;
        }
    </script>
@endsection
