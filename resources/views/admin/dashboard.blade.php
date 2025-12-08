@extends('layouts.admin')

@section('title', 'Dashboard Tổng quan - PhoneShop')

@section('content')
<section class="dashboard-section active" id="overview">
    <div class="section-header">
        <h1>Dashboard Tổng quan</h1>
        <div class="header-actions">
            <form action="{{ route('admin.dashboard') }}" method="GET" class="d-flex align-items-center gap-2">
                {{-- QUICK SELECT: Lọc theo Ngày, Tháng, Năm --}}
                <select name="quick_select" class="form-select" onchange="this.form.submit()" style="width: 150px;">
                    <option value="this_month" {{ $selectedQuick == 'this_month' ? 'selected' : '' }}>Tháng này</option>
                    <option value="today" {{ $selectedQuick == 'today' ? 'selected' : '' }}>Hôm nay</option>
                    <option value="this_year" {{ $selectedQuick == 'this_year' ? 'selected' : '' }}>Năm nay</option>
                    <option value="custom" {{ $selectedQuick == 'custom' ? 'selected' : '' }}>Tùy chỉnh (Ngày/Tháng/Năm)</option>
                </select>
                
                {{-- CUSTOM DATE PICKER: Chỉ hiện khi chọn Tùy chỉnh (Để cho phép lọc theo ngày/tháng/năm tùy ý) --}}
                <div id="custom_date_range" class="d-flex align-items-center gap-2" style="display: {{ $selectedQuick == 'custom' ? 'flex' : 'none' }};">
                    <label for="start_date" class="small mb-0">Từ:</label>
                    {{-- Input date vẫn được gửi đi để Controller sử dụng nếu quick_select là 'custom' --}}
                    <input type="date" name="start_date" id="start_date" class="form-control" 
                           value="{{ request('start_date') ?? \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}" style="width: 150px;">
                    
                    <label for="end_date" class="small mb-0">Đến:</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" 
                           value="{{ request('end_date') ?? \Carbon\Carbon::now()->format('Y-m-d') }}" style="width: 150px;">
                    
                    <button type="submit" class="btn btn-primary btn-sm">Lọc</button>
                </div>
                
                <a href="{{ route('admin.reports') }}" class="btn btn-secondary btn-sm" title="Xuất báo cáo chi tiết">
                    <i class="fas fa-download"></i> Xuất File
                </a>
            </form>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <i class="fas fa-money-bill-wave stat-icon blue"></i>
            <div class="stat-content">
                <h3>Doanh thu (ĐH hoàn thành)</h3>
                <div class="stat-value">
                    {{ number_format($tongDoanhThu, 0, ',', '.') }}₫
                </div>
            </div>
        </div>

        <div class="stat-card">
            <i class="fas fa-shopping-bag stat-icon yellow"></i>
            <div class="stat-content">
                <h3>Tổng đơn hàng</h3>
                <div class="stat-value">{{ $tongDonHang }}</div>
            </div>
        </div>

        <div class="stat-card">
            <i class="fas fa-users stat-icon green"></i>
            <div class="stat-content">
                <h3>Khách hàng</h3>
                <div class="stat-value">{{ $tongKhachHang }}</div>
            </div>
        </div>

        <div class="stat-card">
            <i class="fas fa-box stat-icon red"></i>
            <div class="stat-content">
                <h3>Sản phẩm</h3>
                <div class="stat-value">{{ $tongSanPham }}</div>
            </div>
        </div>
    </div>
    
    {{-- THÊM MỘT HÀNG THỐNG KÊ CHI TIẾT ĐƠN HÀNG TRONG KHOẢNG LỌC --}}
    <div class="stats-grid" style="grid-template-columns: repeat(2, 1fr);">
        <div class="stat-card">
            <i class="fas fa-plus-circle stat-icon blue" style="background: linear-gradient(135deg, #1abc9c 0%, #16a085 100%);"></i>
            <div class="stat-content">
                <h3>Đơn hàng mới</h3>
                <div class="stat-value">{{ $donMoi }}</div>
            </div>
        </div>
        <div class="stat-card">
            <i class="fas fa-check-circle stat-icon green" style="background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);"></i>
            <div class="stat-content">
                <h3>Đã hoàn thành</h3>
                <div class="stat-value">{{ $donHoanThanh }}</div>
            </div>
        </div>
    </div>
    
    <div class="dashboard-card col-12" style="margin-top: 20px;">
        <div class="chart-placeholder" style="height: 350px;">
            <i class="fas fa-chart-area"></i>
            <p>Biểu đồ Doanh thu/Đơn hàng (Cần tích hợp thư viện Chart.js)</p>
        </div>
    </div>

</section>
@endsection
@push('scripts')
<script>
    // Xử lý hiển thị/ẩn Custom Date Range khi thay đổi Quick Select
    document.addEventListener('DOMContentLoaded', function() {
        const selectElement = document.querySelector('select[name="quick_select"]');
        const customDateRange = document.getElementById('custom_date_range');
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');

        function toggleCustomDate() {
            if (selectElement.value === 'custom') {
                customDateRange.style.display = 'flex';
                // Đặt required cho input ngày khi ở chế độ tùy chỉnh
                startDateInput.required = true;
                endDateInput.required = true;
            } else {
                customDateRange.style.display = 'none';
                startDateInput.required = false;
                endDateInput.required = false;
                
                // Reset giá trị ngày khi chuyển sang chế độ nhanh
                startDateInput.value = '';
                endDateInput.value = '';
            }
        }
        
        // Khởi tạo trạng thái ban đầu
        toggleCustomDate();

        // Gắn sự kiện change
        selectElement.addEventListener('change', toggleCustomDate);
        
        // Ngăn form submit khi nhấn Enter trên input ngày tháng mà không nhấn nút Lọc (nếu cần)
        customDateRange.querySelectorAll('input').forEach(input => {
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    document.querySelector('.header-actions .btn-primary').click();
                }
            });
        });
    });
</script>
@endpush