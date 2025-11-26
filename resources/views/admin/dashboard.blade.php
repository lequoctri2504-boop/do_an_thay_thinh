@extends('layouts.admin')

@section('title', 'Dashboard Tổng quan - PhoneShop')

@section('content')
<section class="dashboard-section active" id="overview">
    <div class="section-header">
        <h1>Dashboard Tổng quan</h1>
        <div class="header-actions">
            <select class="form-select">
                <option>Tháng này</option>
                <option>Tháng trước</option>
                <option>Quý này</option>
                <option>Năm nay</option>
            </select>
            <button class="btn btn-primary"><i class="fas fa-download"></i> Xuất báo cáo</button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div><i class="fas fa-money-bill-wave"></i></div>
            <div class="stat-content">
                <h3>Doanh thu (ĐH hoàn thành)</h3>
                <div class="stat-value">
                    {{ number_format($tongDoanhThu, 0, ',', '.') }}₫
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div><i class="fas fa-shopping-bag"></i></div>
            <div class="stat-content">
                <h3>Tổng đơn hàng</h3>
                <div class="stat-value">{{ $tongDonHang }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div><i class="fas fa-users"></i></div>
            <div class="stat-content">
                <h3>Khách hàng</h3>
                <div class="stat-value">{{ $tongKhachHang }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div><i class="fas fa-box"></i></div>
            <div class="stat-content">
                <h3>Sản phẩm</h3>
                <div class="stat-value">{{ $tongSanPham }}</div>
            </div>
        </div>
    </div>

    {{-- Phần còn lại bạn có thể giữ nguyên như HTML tĩnh: chart placeholder, top sản phẩm, hoạt động gần đây --}}
</section>
@endsection
