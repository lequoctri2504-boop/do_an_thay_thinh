@extends('layouts.staff')

@section('title', 'Dashboard Nhân viên - PhoneShop')

@section('content')
<section class="dashboard-section active" id="overview">
    <div class="section-header">
        <h1>Tổng quan</h1>
        <div class="header-actions">
            {{-- Form Lọc theo thời gian --}}
            <form action="{{ route('staff.dashboard') }}" method="GET">
                <select name="quick_select" class="form-select" onchange="this.form.submit()">
                    <option value="today" {{ $selectedQuick == 'today' ? 'selected' : '' }}>Hôm nay</option>
                    <option value="7_days" {{ $selectedQuick == '7_days' ? 'selected' : '' }}>7 ngày qua</option>
                    <option value="30_days" {{ $selectedQuick == '30_days' ? 'selected' : '' }}>30 ngày qua</option>
                    <option value="this_month" {{ $selectedQuick == 'this_month' ? 'selected' : '' }}>Tháng này</option>
                    <option value="this_year" {{ $selectedQuick == 'this_year' ? 'selected' : '' }}>Năm nay</option>
                </select>
            </form>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div><i class="fas fa-shopping-cart stat-icon blue"></i></div>
            <div class="stat-content">
                <h3>Đơn hàng mới (Đang xử lý)</h3>
                <div class="stat-value">{{ $donMoi }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div><i class="fas fa-clock stat-icon yellow"></i></div>
            <div class="stat-content">
                <h3>Đang xử lý (Đang giao)</h3>
                <div class="stat-value">{{ $donDangXuLy }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div><i class="fas fa-check-circle stat-icon green"></i></div>
            <div class="stat-content">
                <h3>Hoàn thành</h3>
                <div class="stat-value">{{ $donHoanThanh }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div><i class="fas fa-times-circle stat-icon red"></i></div>
            <div class="stat-content">
                <h3>Đã hủy</h3>
                <div class="stat-value">{{ $donDaHuy }}</div>
            </div>
        </div>
    </div>

    <div class="dashboard-row">
        <div class="dashboard-card col-8">
            <div class="card-header">
                <h3><i class="fas fa-shopping-bag"></i> Đơn hàng gần đây (Trong thời gian lọc)</h3>
                <a href="{{ route('staff.orders') }}" class="view-all">Xem tất cả</a>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($donHangGanDay as $dh)
                        @php
                            $status = strtolower($dh->trang_thai);
                            if ($status == 'dang_xu_ly') $statusClass = 'processing';
                            elseif ($status == 'dang_giao') $statusClass = 'shipping';
                            elseif ($status == 'hoan_thanh') $statusClass = 'delivered';
                            else $statusClass = 'cancelled';
                        @endphp
                        <tr>
                            <td><strong>#{{ $dh->ma }}</strong></td>
                            <td>{{ $dh->nguoiDung->ho_ten ?? 'Khách lẻ' }}</td>
                            <td>{{ \Carbon\Carbon::parse($dh->ngay_dat)->format('d/m/Y') }}</td>
                            <td>{{ number_format($dh->thanh_tien) }}₫</td>
                            <td><span class="status-badge status-{{ $statusClass }}">{{ $dh->trang_thai }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">Không có đơn hàng nào trong thời gian này.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="dashboard-card col-4">
            <div class="card-header">
                <h3><i class="fas fa-bolt"></i> Thao tác nhanh</h3>
            </div>
            <div class="quick-actions" style="display: flex; flex-direction: column; gap: 15px;">
                <a href="{{ route('admin.orders') }}" class="btn action-btn blue">
                    <i class="fas fa-plus-circle"></i>
                    <span>Tạo đơn hàng mới (Chuyển Admin)</span>
                </a>
                <a href="{{ route('staff.products') }}" class="btn action-btn green">
                    <i class="fas fa-box"></i>
                    <span>Cập nhật tồn kho</span>
                </a>
                <a href="{{ route('staff.customers') }}" class="btn action-btn yellow">
                    <i class="fas fa-user-plus"></i>
                    <span>Thêm khách hàng</span>
                </a>
                <a href="{{ route('staff.reports') }}" class="btn action-btn red">
                    <i class="fas fa-file-alt"></i>
                    <span>Xuất báo cáo</span>
                </a>
            </div>

            <div class="card-header mt-4">
                <h3><i class="fas fa-exclamation-triangle"></i> Sản phẩm sắp hết (Dưới 10)</h3>
            </div>
            <div class="low-stock-list">
                @forelse($sanPhamSapHet as $sp)
                    <div class="stock-item">
                        <div class="stock-info">
                            <strong>{{ $sp->ten }}</strong>
                            <span class="stock-qty {{ $sp->so_luong <= 3 ? 'danger' : 'warning' }}">
                                Còn {{ $sp->so_luong }}
                            </span>
                        </div>
                        <small class="text-muted">SKU: {{ $sp->sku }}</small>
                    </div>
                @empty
                    <p class="text-center text-muted">Không có sản phẩm nào sắp hết.</p>
                @endforelse
            </div>
        </div>
    </div>
</section>
@endsection