@extends('layouts.admin')

@section('title', 'Bản ghi đang chờ duyệt')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Bản ghi đang chờ duyệt</h1>
        <div class="mb-4 text-gray-600 italic">Dưới đây là danh sách các bản ghi chưa được phê duyệt. Vui lòng xem xét và
            duyệt từng bản
            ghi.
        </div>

        @if ($pendingRecords->count() > 0)
            <div class="row">
                @foreach ($pendingRecords as $record)
                    <div class="col-md-6 col-xl-4 mb-4">
                        <div class="card shadow border-left-warning">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="far fa-calendar-alt text-primary"></i>
                                    {{ \Carbon\Carbon::parse($record->record_date)->format('d/m/Y') }}
                                </h5>
                                <div class="mb-1">
                                    <i class="fas fa-building text-secondary"></i>
                                    <strong>Phòng ban:</strong> {{ $record->department->name ?? 'N/A' }}
                                </div>
                                <div class="mb-1">
                                    <i class="fas fa-coins text-success"></i>
                                    <strong>Doanh thu:</strong> {{ number_format($record->revenue) }} VND
                                </div>

                                <div class="mt-3 text-end">
                                    <a href="{{ route('manager.financial.show', $record->id) }}"
                                        class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye" title="Xem bản ghi"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Không có bản ghi nào đang chờ duyệt.
            </div>
        @endif
    </div>
@endsection
