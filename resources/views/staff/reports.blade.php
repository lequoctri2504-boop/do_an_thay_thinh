@extends('layouts.staff')

@section('title', 'Báo cáo - Thống kê')

@section('content')
<section class="dashboard-section active">
    <div class="section-header">
        <h1>Báo cáo & Thống kê</h1>
        <div class="header-actions">
            {{-- Form Lọc và Xuất file --}}
            <form action="{{ route('staff.reports') }}" method="GET" class="d-flex align-items-center gap-2">
                {{-- QUICK SELECT: Lọc theo Ngày, Tháng, Năm --}}
                <select name="quick_select" id="quick_select" class="form-select" onchange="submitQuickSelect(this.value)" style="width: 150px;">
                    <option value="this_month" {{ ($selectedQuick ?? '') == 'this_month' ? 'selected' : '' }}>Tháng này</option>
                    <option value="today" {{ ($selectedQuick ?? '') == 'today' ? 'selected' : '' }}>Hôm nay</option>
                    <option value="7_days" {{ ($selectedQuick ?? '') == '7_days' ? 'selected' : '' }}>7 ngày qua</option>
                    <option value="30_days" {{ ($selectedQuick ?? '') == '30_days' ? 'selected' : '' }}>30 ngày qua</option>
                    <option value="this_year" {{ ($selectedQuick ?? '') == 'this_year' ? 'selected' : '' }}>Năm nay</option>
                    <option value="custom" {{ ($selectedQuick ?? '') == 'custom' ? 'selected' : '' }}>Tùy chỉnh</option>
                </select>
                
                {{-- CUSTOM DATE PICKER --}}
                <div id="custom_date_range" class="d-flex align-items-center gap-2" style="display: none;">
                    <label for="start_date" class="small mb-0">Từ:</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" 
                           value="{{ $queryStartFormatted ?? '' }}" style="width: 150px;">
                    
                    <label for="end_date" class="small mb-0">Đến:</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" 
                           value="{{ $queryEndFormatted ?? '' }}" style="width: 150px;">
                    
                    <button type="submit" class="btn btn-sm btn-primary">Lọc</button>
                </div>
                
                <button type="button" onclick="alert('Chức năng Xuất file đang được phát triển, vui lòng cài đặt thư viện.');" class="btn btn-primary">
                    <i class="fas fa-download"></i> Xuất báo cáo
                </button>
            </form>
        </div>
    </div>

    {{-- Báo cáo Thống kê chính (Stats Cards) --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-money-bill-wave"></i></div>
            <div class="stat-content">
                <h3>DOANH THU (ĐH HT)</h3>
                <div class="stat-value">{{ number_format($tongDoanhThu ?? 0, 0, ',', '.') }}₫</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon yellow"><i class="fas fa-shopping-cart"></i></div>
            <div class="stat-content">
                <h3>TỔNG ĐƠN HÀNG</h3>
                <div class="stat-value">{{ $tongDonHang ?? 0 }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-user-plus"></i></div>
            <div class="stat-content">
                <h3>KHÁCH HÀNG MỚI</h3>
                <div class="stat-value">{{ $khachHangMoi ?? 0 }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon red"><i class="fas fa-clock"></i></div>
            <div class="stat-content">
                <h3>ĐANG XỬ LÝ</h3>
                <div class="stat-value">{{ $donDangXuLy ?? 0 }}</div>
            </div>
        </div>
    </div>

    {{-- Dữ liệu chi tiết --}}
    <div class="dashboard-row">
        
        {{-- Top sản phẩm bán chạy --}}
        <div class="dashboard-card col-6">
            <div class="card-header">
                <h3><i class="fas fa-trophy"></i> Top 5 Sản phẩm bán chạy nhất</h3>
            </div>
            <div class="low-stock-list">
                @forelse($topSellingProducts ?? [] as $key => $product)
                    <div class="stock-item" style="padding: 15px; border-radius: 8px; background: #f9f9f9; margin-bottom: 10px;">
                        <div class="stock-info" style="display: flex; justify-content: space-between;">
                            <strong>{{ $key + 1 }}. {{ $product->ten }}</strong>
                            <span class="stock-qty" style="font-weight: 600; color: var(--primary-color);">
                                Bán: {{ $product->tong_so_luong_ban }} SP
                            </span>
                        </div>
                        <small class="text-muted">Doanh thu: {{ number_format($product->tong_doanh_thu) }}₫</small>
                    </div>
                @empty
                    <p class="text-center" style="padding: 20px;">Chưa có dữ liệu bán hàng trong giai đoạn này.</p>
                @endforelse
            </div>
        </div>

        {{-- Hoạt động gần đây (Đơn hàng) --}}
        <div class="dashboard-card col-6">
            <div class="card-header">
                <h3><i class="fas fa-history"></i> Đơn hàng gần đây</h3>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($recentOrders ?? [] as $o)
                        @php
                            $status = strtolower($o->trang_thai);
                            if ($status == 'dang_xu_ly') $statusClass = 'processing';
                            elseif ($status == 'hoan_thanh') $statusClass = 'delivered';
                            else $statusClass = 'cancelled';
                        @endphp
                        <tr>
                            <td><strong>#{{ $o->ma }}</strong></td>
                            <td>{{ $o->nguoiDung->ho_ten ?? 'Khách lẻ' }}</td>
                            <td>{{ number_format($o->thanh_tien) }}₫</td>
                            <td><span class="status-badge status-{{ $statusClass }}">{{ $o->trang_thai }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center">Chưa có đơn hàng nào.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectElement = document.getElementById('quick_select');
        const customDateRange = document.getElementById('custom_date_range');
        const form = selectElement.closest('form');
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        
        const initialSelectedQuick = '{{ $selectedQuick ?? "this_month" }}';
        
        function toggleCustomDate(selectedValue) {
            if (selectedValue === 'custom') {
                customDateRange.style.display = 'flex';
                startDateInput.required = true;
                endDateInput.required = true;
            } else {
                customDateRange.style.display = 'none';
                startDateInput.required = false;
                endDateInput.required = false;
            }
        }

        window.submitQuickSelect = function(selectedValue) {
            if (selectedValue !== 'custom') {
                // Clear custom dates before submitting quick select
                startDateInput.value = ''; 
                endDateInput.value = '';
                form.submit();
            } else {
                toggleCustomDate(selectedValue);
            }
        }
        
        // Khởi tạo trạng thái ban đầu và đảm bảo Custom Date được hiển thị nếu đang lọc theo đó
        toggleCustomDate(initialSelectedQuick);
        if (initialSelectedQuick === 'custom') {
            document.getElementById('custom_date_range').style.display = 'flex';
        }

        // Gắn sự kiện change cho select
        selectElement.addEventListener('change', function() {
            window.submitQuickSelect(this.value);
        });
    });
</script>
@endpush