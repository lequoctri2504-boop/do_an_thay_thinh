@extends('layouts.staff')

@section('title', 'Dashboard Nhân viên - PhoneShop')

@section('content')
<section class="dashboard-section active" id="overview">
    <div class="section-header">
        <h1>Tổng quan</h1>
        <div class="header-actions">
            <select class="form-select">
                <option>Hôm nay</option>
                <option>7 ngày qua</option>
                <option>30 ngày qua</option>
                <option>Tháng này</option>
            </select>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div><i class="fas fa-shopping-cart"></i></div>
            <div class="stat-content">
                <h3>Đơn hàng mới</h3>
                <div class="stat-value">{{ $donHangMoi }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div><i class="fas fa-clock"></i></div>
            <div class="stat-content">
                <h3>Đang xử lý</h3>
                <div class="stat-value">{{ $donDangXuLy }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div><i class="fas fa-check-circle"></i></div>
            <div class="stat-content">
                <h3>Hoàn thành</h3>
                <div class="stat-value">{{ $donHoanThanh }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div><i class="fas fa-times-circle"></i></div>
            <div class="stat-content">
                <h3>Đã hủy</h3>
                <div class="stat-value">{{ $donDaHuy }}</div>
            </div>
        </div>
    </div>

    <!-- Charts & Tables Row -->
    <div class="dashboard-row">
        <!-- Recent Orders -->
        <div class="dashboard-card col-8">
            <div class="card-header">
                <h3><i class="fas fa-shopping-bag"></i> Đơn hàng gần đây</h3>
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
                    @foreach($donHangGanDay as $dh)
                        <tr>
                            <td><strong>#{{ $dh->id }}</strong></td>
                            <td>{{ $dh->nguoiDung->ho_ten ?? 'Khách lẻ' }}</td>
                            <td>{{ $dh->ngay_dat }}</td>
                            <td>{{ number_format($dh->thanh_tien) }}₫</td>
                            <td><span class="status-badge">{{ $dh->trang_thai }}</span></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Actions + Low stock -->
        <div class="dashboard-card col-4">
            <div class="card-header">
                <h3><i class="fas fa-bolt"></i> Thao tác nhanh</h3>
            </div>
            <div class="quick-actions">
                <button>
                    <i class="fas fa-plus-circle"></i>
                    <span>Tạo đơn hàng mới</span>
                </button>
                <button>
                    <i class="fas fa-box"></i>
                    <span>Cập nhật tồn kho</span>
                </button>
                <button>
                    <i class="fas fa-user-plus"></i>
                    <span>Thêm khách hàng</span>
                </button>
                <button>
                    <i class="fas fa-file-alt"></i>
                    <span>Xuất báo cáo</span>
                </button>
            </div>

            <div class="card-header mt-4">
                <h3><i class="fas fa-exclamation-triangle"></i> Sản phẩm sắp hết</h3>
            </div>
            <div class="low-stock-list">
                @foreach($sanPhamSapHet as $sp)
                    <div class="stock-item">
                        <div class="stock-info">
                            <strong>{{ $sp->ten }}</strong>
                            <span class="stock-qty {{ $sp->so_luong <= 3 ? 'danger' : 'warning' }}">
                                Còn {{ $sp->so_luong }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endsection
