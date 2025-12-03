@extends('layouts.admin')

@section('title', 'Quản lý Khuyến mãi')

@section('content')
<section class="dashboard-section active">
    <div class="section-header">
        <h1>Quản lý Khuyến mãi</h1>
        <div class="header-actions">
            {{-- Đã sửa link trỏ đến form tạo mới --}}
            <a href="{{ route('admin.promotions.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tạo khuyến mãi</a>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tên chương trình</th>
                        <th>Mã giảm giá</th>
                        <th>Giảm (%) / Tiền</th>
                        <th>Ngày bắt đầu</th>
                        <th>Ngày kết thúc</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($promotions as $promo)
                        @php
                            // Mapping trạng thái sang class CSS
                            $statusClass = strtolower($promo->status);
                            if (strpos($statusClass, 'diễn') !== false) $statusClass = 'processing';
                            elseif (strpos($statusClass, 'kết') !== false) $statusClass = 'delivered';
                            else $statusClass = 'cancelled';
                        @endphp
                    <tr>
                        <td>{{ $promo->name }}</td>
                        <td><strong>{{ $promo->code }}</strong></td>
                        <td>{{ $promo->discount }}</td>
                        <td>{{ \Carbon\Carbon::parse($promo->start_date)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($promo->end_date)->format('d/m/Y') }}</td>
                        <td><span class="status-badge status-{{ $statusClass }}">{{ $promo->status }}</span></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                            <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection