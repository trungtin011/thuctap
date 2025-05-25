@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">

            {{-- Form Thêm Đại Lý --}}
            <div class="card shadow rounded mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Thêm Đại Lý</h4>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.dai_ly.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Tên đại lý:</label>
                            <input type="text" name="ten_dai_ly" class="form-control" value="{{ old('ten_dai_ly') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email:</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Số điện thoại:</label>
                            <input type="text" name="so_dien_thoai" class="form-control" value="{{ old('so_dien_thoai') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Địa chỉ:</label>
                            <input type="text" name="dia_chi" class="form-control" value="{{ old('dia_chi') }}" required>
                        </div>

                        <button type="submit" class="btn btn-success">Thêm Đại Lý</button>
                    </form>
                </div>
            </div>

            {{-- Danh sách Đại Lý --}}
            <div class="card shadow rounded">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Danh Sách Đại Lý</h5>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Tên đại lý</th>
                                    <th>Email</th>
                                    <th>SĐT</th>
                                    <th>Địa chỉ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($daiLys as $index => $dl)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $dl->ten_dai_ly }}</td>
                                        <td>{{ $dl->email }}</td>
                                        <td>{{ $dl->so_dien_thoai }}</td>
                                        <td>{{ $dl->dia_chi }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Chưa có đại lý nào.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
