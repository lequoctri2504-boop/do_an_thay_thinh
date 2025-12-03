@extends('layouts.admin')

@section('title', 'Quản lý Đơn hàng')

@section('content')
<section class="dashboard-section active">
    <div class="section-header">
        <h1>Quản lý Đơn hàng</h1>
        <div class="header-actions">
            <input type="text" class="form-control" placeholder="Tìm kiếm đơn hàng...">
            <button class="btn btn-primary"><i class="fas fa-filter"></i> Lọc</button>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($orders as $order)
                    @php
                        // Chuyển trạng thái sang lowercase để mapping CSS
                        $status = strtolower($order->trang_thai);
                        if ($status == 'dang_xu_ly') $statusClass = 'processing';
                        elseif ($status == 'dang_giao') $statusClass = 'shipping';
                        elseif ($status == 'hoan_thanh') $statusClass = 'delivered';
                        else $statusClass = 'cancelled';
                        
                        // Lấy tên khách hàng nếu có, hoặc Khách lẻ
                        $customerName = $order->nguoiDung->ho_ten ?? $order->ten_nguoi_nhan ?? 'Khách lẻ';
                    @endphp
                    <tr>
                        <td><strong>#{{ $order->ma }}</strong></td>
                        <td>{{ $customerName }}</td>
                        <td>{{ \Carbon\Carbon::parse($order->ngay_dat)->format('d/m/Y H:i') }}</td>
                        <td><strong>{{ number_format($order->thanh_tien, 0, ',', '.') }}₫</strong></td>
                        <td>
                            <span class="status-badge status-{{ $statusClass }}">
                                {{ $order->trang_thai }}
                            </span>
                            <br>
                            <small class="text-muted">TT: {{ $order->trang_thai_tt }}</small>
                        </td>
                        <td>
                            <a href="#" class="btn btn-sm btn-secondary" title="Chi tiết">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($order->trang_thai == 'DANG_XU_LY')
                                <button class="btn btn-sm btn-primary" title="Xử lý">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Chưa có đơn hàng nào.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrapper">
            {{ $orders->links() }}
        </div>
    </div>
</section>
@endsection