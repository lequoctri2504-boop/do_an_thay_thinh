@extends('layouts.staff')

@section('title', 'Dashboard Nhân viên - PhoneShop')

@section('content')
<div class="dashboard-wrapper">
    {{-- TIÊU ĐỀ & BỘ LỌC NHANH --}}
    <div class="dashboard-header-flex">
        <div class="header-content">
            <h1 class="main-title"><i class="fas fa-tachometer-alt"></i> Bảng Điều Khiển</h1>
            <p class="subtitle">Chào mừng trở lại! Theo dõi hoạt động kinh doanh của bạn hôm nay.</p>
        </div>
        <div class="header-filter">
            <form action="{{ route('staff.dashboard') }}" method="GET" id="quickFilterForm">
                <div class="filter-box">
                    <i class="fas fa-calendar-day"></i>
                    <select name="quick_select" class="modern-select" onchange="this.form.submit()">
                        <option value="today" {{ $selectedQuick == 'today' ? 'selected' : '' }}>Hôm nay</option>
                        <option value="7_days" {{ $selectedQuick == '7_days' ? 'selected' : '' }}>7 ngày qua</option>
                        <option value="30_days" {{ $selectedQuick == '30_days' ? 'selected' : '' }}>30 ngày qua</option>
                        <option value="this_month" {{ $selectedQuick == 'this_month' ? 'selected' : '' }}>Tháng này</option>
                        <option value="this_year" {{ $selectedQuick == 'this_year' ? 'selected' : '' }}>Năm nay</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    {{-- THỐNG KÊ TỔNG QUAN --}}
    <div class="stats-grid-modern">
        <div class="stat-card-modern border-blue">
            <div class="icon-box bg-blue"><i class="fas fa-shopping-cart"></i></div>
            <div class="stat-info">
                <span class="stat-label">Đơn hàng mới</span>
                <h2 class="stat-number">{{ $donMoi }}</h2>
            </div>
        </div>

        <div class="stat-card-modern border-yellow">
            <div class="icon-box bg-yellow"><i class="fas fa-truck-loading"></i></div>
            <div class="stat-info">
                <span class="stat-label">Đang giao hàng</span>
                <h2 class="stat-number">{{ $donDangXuLy }}</h2>
            </div>
        </div>

        <div class="stat-card-modern border-green">
            <div class="icon-box bg-green"><i class="fas fa-check-double"></i></div>
            <div class="stat-info">
                <span class="stat-label">Đã hoàn thành</span>
                <h2 class="stat-number">{{ $donHoanThanh }}</h2>
            </div>
        </div>

        <div class="stat-card-modern border-red">
            <div class="icon-box bg-red"><i class="fas fa-ban"></i></div>
            <div class="stat-info">
                <span class="stat-label">Đã hủy bỏ</span>
                <h2 class="stat-number">{{ $donDaHuy }}</h2>
            </div>
        </div>
    </div>

    {{-- NỘI DUNG CHÍNH --}}
    <div class="dashboard-content-grid">
        {{-- BÊN TRÁI: DANH SÁCH ĐƠN HÀNG --}}
        <div class="content-main-card shadow-soft">
            <div class="card-top-header">
                <h3><i class="fas fa-history"></i> Đơn hàng gần đây</h3>
                <a href="{{ route('staff.orders') }}" class="btn-text-link">Xem tất cả <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="table-container">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Ngày đặt</th>
                            <th class="text-right">Tổng tiền</th>
                            <th class="text-center">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($donHangGanDay as $dh)
                            @php
                                $status = strtolower($dh->trang_thai);
                                if ($status == 'dang_xu_ly') $statusClass = 'badge-processing';
                                elseif ($status == 'dang_giao') $statusClass = 'badge-shipping';
                                elseif ($status == 'hoan_thanh') $statusClass = 'badge-delivered';
                                else $statusClass = 'badge-cancelled';
                            @endphp
                            <tr>
                                <td class="id-text">#{{ $dh->ma }}</td>
                                <td class="user-cell">
                                    <div class="avatar-sm">{{ substr($dh->nguoiDung->ho_ten ?? 'K', 0, 1) }}</div>
                                    {{ $dh->nguoiDung->ho_ten ?? 'Khách lẻ' }}
                                </td>
                                <td>{{ \Carbon\Carbon::parse($dh->ngay_dat)->format('d/m/Y') }}</td>
                                <td class="text-right amount-text">{{ number_format($dh->thanh_tien) }}₫</td>
                                <td class="text-center">
                                    <span class="badge-status {{ $statusClass }}">{{ $dh->trang_thai }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="empty-msg">Hiện chưa có dữ liệu giao dịch.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- BÊN PHẢI: THAO TÁC & CẢNH BÁO --}}
        <div class="content-side-cards">
            {{-- THAO TÁC NHANH --}}
            <div class="side-card shadow-soft border-red-top">
                <h4 class="side-title"><i class="fas fa-magic"></i> Lối tắt nhanh</h4>
                <div class="action-buttons-stack">
                    <a href="{{ route('admin.orders') }}" class="quick-btn-item">
                        <span class="icon-circle bg-red-soft"><i class="fas fa-plus"></i></span>
                        <span>Tạo đơn hàng (Admin)</span>
                    </a>
                    <a href="{{ route('staff.products') }}" class="quick-btn-item">
                        <span class="icon-circle bg-green-soft"><i class="fas fa-sync"></i></span>
                        <span>Cập nhật tồn kho</span>
                    </a>
                    <a href="{{ route('staff.customers') }}" class="quick-btn-item">
                        <span class="icon-circle bg-yellow-soft"><i class="fas fa-user-plus"></i></span>
                        <span>Thêm khách hàng</span>
                    </a>
                </div>
            </div>

            {{-- CẢNH BÁO HẾT HÀNG --}}
            <div class="side-card shadow-soft">
                <h4 class="side-title text-red"><i class="fas fa-bullhorn"></i> Sắp hết hàng</h4>
                <div class="stock-alert-list">
                    @forelse($sanPhamSapHet as $sp)
                        <div class="stock-alert-item">
                            <div class="alert-info">
                                <span class="product-title">{{ $sp->ten }}</span>
                                <span class="product-sku">SKU: {{ $sp->sku }}</span>
                            </div>
                            <div class="alert-badge {{ $sp->so_luong <= 3 ? 'urgent' : 'warning' }}">
                                {{ $sp->so_luong }}
                            </div>
                        </div>
                    @empty
                        <p class="empty-small">Tồn kho hiện đang ổn định.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* CSS Tông Đỏ phối hợp màu sắc chuyên nghiệp */
    .dashboard-wrapper { padding: 25px; background: #fcfcfc; }

    /* Header */
    .dashboard-header-flex { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px; border-bottom: 2px solid #f8d7da; padding-bottom: 15px; }
    .main-title { margin: 0; color: #721c24; font-size: 1.8rem; font-weight: 800; }
    .subtitle { margin: 5px 0 0; color: #842029; opacity: 0.7; }
    .filter-box { background: white; border: 1px solid #f5c2c7; border-radius: 8px; padding: 5px 15px; display: flex; align-items: center; gap: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
    .modern-select { border: none; outline: none; font-weight: 600; color: #dc3545; cursor: pointer; padding: 5px; }

    /* Thống kê - Stats Cards */
    .stats-grid-modern { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 30px; }
    .stat-card-modern { background: white; padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 15px; transition: transform 0.3s ease; border: 1px solid #eee; }
    .stat-card-modern:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
    .icon-box { width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: white; }
    .stat-number { margin: 0; font-size: 1.6rem; font-weight: 800; color: #2c3e50; }
    .stat-label { font-size: 0.85rem; color: #7f8c8d; font-weight: 600; }
    
    .bg-blue { background: #3498db; } .border-blue { border-left: 4px solid #3498db; }
    .bg-yellow { background: #f39c12; } .border-yellow { border-left: 4px solid #f39c12; }
    .bg-green { background: #27ae60; } .border-green { border-left: 4px solid #27ae60; }
    .bg-red { background: #dc3545; } .border-red { border-left: 4px solid #dc3545; }

    /* Layout Content */
    .dashboard-content-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 25px; }
    .shadow-soft { box-shadow: 0 4px 15px rgba(0,0,0,0.04); border-radius: 12px; }
    .content-main-card { background: white; padding: 20px; border: 1px solid #f1f1f1; }
    
    /* Table */
    .card-top-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .card-top-header h3 { font-size: 1.1rem; color: #dc3545; margin: 0; }
    .btn-text-link { color: #842029; text-decoration: none; font-size: 0.9rem; font-weight: 600; }
    .btn-text-link:hover { text-decoration: underline; }
    
    .modern-table { width: 100%; border-collapse: collapse; }
    .modern-table th { padding: 12px; text-align: left; font-size: 0.8rem; color: #adb5bd; text-transform: uppercase; border-bottom: 1px solid #eee; }
    .modern-table td { padding: 15px 12px; border-bottom: 1px solid #f9f9f9; font-size: 0.9rem; }
    .id-text { font-weight: 700; color: #dc3545; }
    .amount-text { font-weight: 700; color: #2c3e50; }
    .user-cell { display: flex; align-items: center; gap: 10px; font-weight: 600; }
    .avatar-sm { width: 28px; height: 28px; background: #dc3545; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; }

    /* Status Badges */
    .badge-status { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; }
    .badge-processing { background: #e1f5fe; color: #0288d1; }
    .badge-shipping { background: #fff3e0; color: #f57c00; }
    .badge-delivered { background: #e8f5e9; color: #2e7d32; }
    .badge-cancelled { background: #ffebee; color: #c62828; }

    /* Side Cards */
    .side-card { background: white; padding: 20px; margin-bottom: 20px; border: 1px solid #f1f1f1; }
    .border-red-top { border-top: 4px solid #dc3545; }
    .side-title { margin: 0 0 15px; font-size: 1rem; color: #2c3e50; }
    .text-red { color: #dc3545; }

    /* Quick Actions */
    .action-buttons-stack { display: flex; flex-direction: column; gap: 10px; }
    .quick-btn-item { display: flex; align-items: center; gap: 12px; text-decoration: none; color: #495057; font-weight: 600; font-size: 0.9rem; padding: 10px; border-radius: 8px; transition: 0.2s; }
    .quick-btn-item:hover { background: #fff5f5; color: #dc3545; }
    .icon-circle { width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; }
    .bg-red-soft { background: #ffebeb; color: #dc3545; }
    .bg-green-soft { background: #e8f5e9; color: #2e7d32; }
    .bg-yellow-soft { background: #fff3e0; color: #f57c00; }

    /* Stock List */
    .stock-alert-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px dashed #eee; }
    .product-title { display: block; font-weight: 600; font-size: 0.9rem; color: #2c3e50; }
    .product-sku { font-size: 0.75rem; color: #adb5bd; }
    .alert-badge { padding: 2px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 700; }
    .alert-badge.urgent { background: #dc3545; color: white; }
    .alert-badge.warning { background: #ffc107; color: #212529; }
</style>
@endsection