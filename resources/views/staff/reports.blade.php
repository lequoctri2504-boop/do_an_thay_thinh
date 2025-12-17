@extends('layouts.staff')

@section('title', 'Báo cáo - Thống kê')

@section('content')
<section class="dashboard-section active">
    <div class="section-header">
        <h1>Báo cáo & Thống kê Chuyên sâu</h1>
        <div class="header-actions">
            {{-- Form Lọc và Xuất file --}}
            <form action="{{ route('staff.reports') }}" method="GET" class="d-flex align-items-center gap-2" id="reportFilterForm">
                {{-- QUICK SELECT: Lọc theo Ngày, Tháng, Năm --}}
                <select name="quick_select" id="quick_select" class="form-select" style="width: 150px;">
                    <option value="this_month" {{ ($selectedQuick ?? '') == 'this_month' ? 'selected' : '' }}>Tháng này</option>
                    <option value="today" {{ ($selectedQuick ?? '') == 'today' ? 'selected' : '' }}>Hôm nay</option>
                    <option value="7_days" {{ ($selectedQuick ?? '') == '7_days' ? 'selected' : '' }}>7 ngày qua</option>
                    <option value="30_days" {{ ($selectedQuick ?? '') == '30_days' ? 'selected' : '' }}>30 ngày qua</option>
                    <option value="this_year" {{ ($selectedQuick ?? '') == 'this_year' ? 'selected' : '' }}>Năm nay</option>
                    <option value="custom" {{ ($selectedQuick ?? '') == 'custom' ? 'selected' : '' }}>Tùy chỉnh</option>
                </select>
                
                {{-- CUSTOM DATE PICKER --}}
                <div id="custom_date_range" class="d-flex align-items-center gap-2" 
                     style="display: {{ ($selectedQuick ?? '') == 'custom' ? 'flex' : 'none' }};">
                    <label for="start_date" class="small mb-0">Từ:</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" 
                           value="{{ $queryStartFormatted ?? '' }}" style="width: 150px;">
                    
                    <label for="end_date" class="small mb-0">Đến:</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" 
                           value="{{ $queryEndFormatted ?? '' }}" style="width: 150px;">
                    
                    <button type="submit" class="btn btn-sm btn-primary">Lọc</button>
                </div>
                
                {{-- Nút Xuất báo cáo đã chuyển sang Sidebar --}}
            </form>
        </div>
    </div>

    {{-- HIỂN THỊ KHOẢNG THỜI GIAN LỌC --}}
    <h4 style="margin-bottom: 20px; font-size: 18px;">
        <i class="fas fa-calendar-alt"></i> Báo cáo cho kỳ: 
        <strong>{{ \Carbon\Carbon::parse($queryStartFormatted)->format('d/m/Y') }}</strong>
        đến
        <strong>{{ \Carbon\Carbon::parse($queryEndFormatted)->format('d/m/Y') }}</strong>
    </h4>

    {{-- Báo cáo Thống kê chính (Stats Cards) --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-money-bill-wave"></i></div>
            <div class="stat-content">
                <h3>DOANH THU (HOÀN THÀNH)</h3>
                <div class="stat-value">{{ number_format($tongDoanhThu ?? 0, 0, ',', '.') }}₫</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon purple"><i class="fas fa-receipt"></i></div>
            <div class="stat-content">
                <h3>TỔNG ĐƠN HÀNG ĐÃ BÁN</h3>
                <div class="stat-value">{{ $tongDonHangHoanThanh ?? 0 }}</div>
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
                <h3>ĐANG XỬ LÝ (TỔNG)</h3>
                <div class="stat-value">{{ $donDangXuLy ?? 0 }}</div>
            </div>
        </div>
    </div>

    {{-- CHI TIẾT SẢN PHẨM & TỒN KHO --}}
    <div class="dashboard-row" style="margin-top: 30px; gap: 20px;">
        
        {{-- COL 1: BÁN CHẠY & BÁN CHẬM --}}
        <div class="col-6">
            <div class="dashboard-card" style="margin-bottom: 20px;">
                <div class="card-header">
                    <h3><i class="fas fa-chart-bar"></i> Sản phẩm Bán chạy & Bán chậm</h3>
                    <small class="text-muted">Tính theo số lượng bán ra trong kỳ.</small>
                </div>
                
                <h5 style="margin-top: 15px; color: green;"><i class="fas fa-arrow-up"></i> TOP 5 Sản phẩm Bán chạy nhất</h5>
                <div class="low-stock-list" style="border: 1px solid #e9e9e9; padding: 10px; border-radius: 6px;">
                    @forelse($topSellingProducts ?? [] as $key => $product)
                        <div class="stock-item" style="padding: 8px 0; border-bottom: 1px dashed #eee;">
                            <strong>{{ $key + 1 }}. {{ \Illuminate\Support\Str::limit($product->ten, 40) }}</strong>
                            <span style="float: right; font-weight: 600; color: green;">
                                {{ number_format($product->tong_so_luong_ban) }} SP
                            </span>
                        </div>
                    @empty
                        <p class="text-center small text-muted">Không có dữ liệu bán chạy.</p>
                    @endforelse
                </div>

                <h5 style="margin-top: 25px; color: red;"><i class="fas fa-arrow-down"></i> BOTTOM 5 Sản phẩm Bán chậm nhất</h5>
                <div class="low-stock-list" style="border: 1px solid #e9e9e9; padding: 10px; border-radius: 6px;">
                    @forelse($bottomSellingProducts ?? [] as $key => $product)
                        <div class="stock-item" style="padding: 8px 0; border-bottom: 1px dashed #eee;">
                            <strong>{{ $key + 1 }}. {{ \Illuminate\Support\Str::limit($product->ten, 40) }}</strong>
                            <span style="float: right; font-weight: 600; color: red;">
                                {{ number_format($product->tong_so_luong_ban) }} SP
                            </span>
                        </div>
                    @empty
                        <p class="text-center small text-muted">Không có dữ liệu bán chậm (hoặc ít hơn 5 sản phẩm bán được).</p>
                    @endforelse
                </div>
            </div>
            
            {{-- TỒN KHO NHIỀU NHẤT --}}
             <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-warehouse"></i> Top 5 Sản phẩm Tồn kho nhiều</h3>
                    <small class="text-muted">Tính theo số lượng tồn kho hiện tại.</small>
                </div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Biến thể</th>
                                <th>SKU</th>
                                <th>Tồn kho</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topStockProducts ?? [] as $product)
                                <tr>
                                    <td>{{ \Illuminate\Support\Str::limit($product->ten_san_pham, 25) }}</td>
                                    <td>{{ $product->mau_sac }} / {{ $product->dung_luong_gb ? $product->dung_luong_gb . 'GB' : '-' }}</td>
                                    <td>{{ $product->sku }}</td>
                                    <td style="font-weight: bold; color: #ff9800;">{{ number_format($product->ton_kho) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center">Không có dữ liệu tồn kho.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- COL 2: DOANH THU THEO DANH MỤC & BIẾN THỂ --}}
        <div class="col-6">
            {{-- DOANH THU THEO DÒNG MÁY (DANH MỤC) --}}
            <div class="dashboard-card" style="margin-bottom: 20px;">
                <div class="card-header">
                    <h3><i class="fas fa-tags"></i> Doanh thu theo Dòng máy/Danh mục</h3>
                    <small class="text-muted">Doanh thu hoàn thành (Đã bao gồm thuế, giảm giá).</small>
                </div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Dòng máy/Danh mục</th>
                                <th>Doanh thu</th>
                                <th>Tỷ trọng (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalRevenue = $revenueByCategory->sum('tong_doanh_thu'); @endphp
                            @forelse($revenueByCategory ?? [] as $item)
                                @php
                                    $percentage = $totalRevenue > 0 ? ($item->tong_doanh_thu / $totalRevenue) * 100 : 0;
                                @endphp
                                <tr>
                                    <td>{{ $item->ten_danh_muc }}</td>
                                    <td style="font-weight: bold;">{{ number_format($item->tong_doanh_thu) }}₫</td>
                                    <td>{{ number_format($percentage, 2) }}%</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center">Chưa có doanh thu theo danh mục trong kỳ.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- SỐ LƯỢNG BÁN THEO TỪNG MẪU MÁY (BIẾN THỂ) --}}
            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-boxes"></i> Top 10 Bán theo Mẫu máy (Biến thể)</h3>
                    <small class="text-muted">Hiệu suất bán hàng chi tiết theo SKU/Màu sắc/Dung lượng.</small>
                </div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Biến thể</th>
                                <th>SKU</th>
                                <th>Số lượng bán</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($salesByVariant ?? [] as $variant)
                                <tr>
                                    <td>{{ \Illuminate\Support\Str::limit($variant->ten_san_pham, 25) }}</td>
                                    <td>{{ $variant->mau_sac }} / {{ $variant->dung_luong_gb ? $variant->dung_luong_gb . 'GB' : '-' }}</td>
                                    <td>{{ $variant->sku }}</td>
                                    <td style="font-weight: bold; color: var(--primary-color);">{{ number_format($variant->tong_so_luong_ban) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center">Chưa có dữ liệu bán theo biến thể trong kỳ.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
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
        const form = document.getElementById('reportFilterForm');
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        
        // Lấy nút Xuất báo cáo từ SIDEBAR (Đã được định nghĩa trong Layout)
        const sidebarExportBtn = document.getElementById('sidebar-export-btn');

        // Hàm tạo tham số lọc từ form
        function getFilterParams() {
            const params = new URLSearchParams();
            const selectedValue = selectElement.value;
            params.set('quick_select', selectedValue);
            
            if (selectedValue === 'custom') {
                params.set('start_date', startDateInput.value);
                params.set('end_date', endDateInput.value);
            }
            return params;
        }
        
        // GẮN SỰ KIỆN CLICK SỬ DỤNG DEFERRED EVENT LISTENER
        document.addEventListener('click', function(e) {
            const clickedElement = e.target.closest('#sidebar-export-btn');
            
            if (clickedElement) {
                e.preventDefault();
                
                // VALIDATION: Kiểm tra Custom Date trước khi chuyển hướng
                if (selectElement.value === 'custom' && (!startDateInput.value || !endDateInput.value)) {
                    alert('Vui lòng chọn cả Ngày bắt đầu và Ngày kết thúc cho báo cáo tùy chỉnh!');
                    return; 
                }
                
                // Chuyển hướng đến trang xác nhận với các tham số lọc
                const params = getFilterParams();
                let confirmUrl = '{{ route('staff.reports.confirm.export') }}'; 
                window.location.href = confirmUrl + '?' + params.toString();
            }
        });

        // --- Logic Lọc và Hiển thị ---
        const initialSelectedQuick = '{{ $selectedQuick ?? "this_month" }}';
        
        function toggleCustomDate(selectedValue) {
            if (selectedValue === 'custom') {
                customDateRange.style.display = 'flex';
            } else {
                customDateRange.style.display = 'none';
            }
        }

        // Xử lý khi chọn giá trị mới trong Selectbox
        selectElement.addEventListener('change', function() {
            const selectedValue = this.value;
            if (selectedValue !== 'custom') {
                // Áp dụng lọc ngay lập tức nếu không phải tùy chỉnh
                startDateInput.value = ''; 
                endDateInput.value = '';
                form.submit();
            } else {
                // Chỉ hiển thị Date Picker nếu là tùy chỉnh
                toggleCustomDate(selectedValue);
            }
        });
        
        // Khởi tạo trạng thái ban đầu
        toggleCustomDate(initialSelectedQuick);
        
        // Validation khi bấm nút Lọc (submit form)
        form.addEventListener('submit', function(e) {
            if (selectElement.value === 'custom') {
                if (!startDateInput.value || !endDateInput.value) {
                    e.preventDefault();
                    alert('Vui lòng chọn cả Ngày bắt đầu và Ngày kết thúc cho báo cáo tùy chỉnh!'); 
                }
            }
        });
    });
</script>
@endpush