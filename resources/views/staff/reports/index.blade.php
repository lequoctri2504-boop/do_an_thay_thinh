@extends('layouts.staff')

@section('title', 'Báo Cáo Thống Kê - Hệ Thống')

@section('content')
<div class="report-container">
    {{-- TIÊU ĐỀ VÀ NÚT XUẤT FILE --}}
    <div class="page-header">
        <div class="header-left">
            <h4 class="page-title"><i class="fas fa-chart-line"></i> Báo Cáo Thống Kê</h4>
            <p class="page-subtitle">Dữ liệu phân tích hoạt động kinh doanh và kho vận</p>
        </div>
        <div class="header-right">
            <div class="export-actions">
                @php $params = ['format' => 'excel', 'report_type' => $reportType, 'start_date' => $startDate, 'end_date' => $endDate]; @endphp
                <a href="{{ route('staff.reports.export', $params) }}" class="btn-export excel">
                    <i class="fas fa-file-excel"></i> Xuất Excel
                </a>
                <a href="{{ route('staff.reports.export', array_merge($params, ['format' => 'pdf'])) }}" class="btn-export pdf">
                    <i class="fas fa-file-pdf"></i> Xuất PDF
                </a>
            </div>
        </div>
    </div>

    {{-- BỘ LỌC DỮ LIỆU --}}
    <section class="filter-section">
        <form action="{{ route('staff.reports') }}" method="GET" class="filter-card">
            <div class="filter-grid">
                <div class="filter-group">
                    <label class="filter-label"><i class="fas fa-filter"></i> Loại báo cáo</label>
                    <select name="report_type" class="filter-select">
                        <option value="doanh_thu" {{ $reportType == 'doanh_thu' ? 'selected' : '' }}>📊 Báo cáo Doanh thu</option>
                        <option value="ban_chay" {{ $reportType == 'ban_chay' ? 'selected' : '' }}>🔥 Top 5 Sản phẩm bán chạy</option>
                        <option value="ban_cham" {{ $reportType == 'ban_cham' ? 'selected' : '' }}>🐢 Sản phẩm bán chậm</option>
                        <option value="ton_kho" {{ $reportType == 'ton_kho' ? 'selected' : '' }}>📦 Top 10 Sản phẩm tồn kho</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label"><i class="fas fa-calendar-alt"></i> Từ ngày</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="filter-input">
                </div>
                <div class="filter-group">
                    <label class="filter-label"><i class="fas fa-calendar-check"></i> Đến ngày</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="filter-input">
                </div>
                <div class="filter-group actions">
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-sync-alt"></i> Cập nhật dữ liệu
                    </button>
                </div>
            </div>
        </form>
    </section>

    {{-- BẢNG DỮ LIỆU --}}
    <div class="data-card">
        <div class="card-header-inner">
            <h5 class="report-type-title">
                @if($reportType == 'doanh_thu') <i class="fas fa-money-bill-wave"></i> CHI TIẾT DOANH THU
                @elseif($reportType == 'ban_chay') <i class="fas fa-fire"></i> TOP 5 SẢN PHẨM BÁN CHẠY
                @elseif($reportType == 'ban_cham') <i class="fas fa-snail"></i> SẢN PHẨM BÁN CHẬM
                @else <i class="fas fa-warehouse"></i> TOP 10 SẢN PHẨM TỒN KHO NHIỀU @endif
            </h5>
        </div>
        <div class="table-wrapper">
            <table class="custom-table">
                <thead>
                    @if($reportType == 'doanh_thu')
                        <tr>
                            <th>Mã đơn</th>
                            <th>Ngày đặt</th>
                            <th>Khách hàng</th>
                            <th class="text-right">Thành tiền</th>
                        </tr>
                    @elseif($reportType == 'ban_chay')
                        <tr>
                            <th>Tên sản phẩm</th>
                            <th class="text-center">Số lượng đã bán</th>
                        </tr>
                    @elseif($reportType == 'ban_cham')
                        <tr>
                            <th>Tên sản phẩm</th>
                            <th class="text-center">Số lượng bán (3 tháng)</th>
                        </tr>
                    @elseif($reportType == 'ton_kho')
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Màu sắc</th>
                            <th class="text-center">Số lượng tồn</th>
                        </tr>
                    @endif
                </thead>
                <tbody>
                    @forelse($data as $item)
                        @if($reportType == 'doanh_thu')
                            <tr>
                                <td class="id-col">#{{ $item->ma }}</td>
                                <td>{{ date('d/m/Y', strtotime($item->ngay_dat)) }}</td>
                                <td>{{ $item->ten_nguoi_nhan }}</td>
                                <td class="text-right amount-text">{{ number_format($item->thanh_tien) }}₫</td>
                            </tr>
                        @elseif($reportType == 'ban_chay')
                            <tr>
                                <td class="product-name">{{ $item->ten_sp_ghi_nhan }}</td>
                                <td class="text-center fw-bold text-danger">{{ $item->total_qty }}</td>
                            </tr>
                        @elseif($reportType == 'ban_cham')
                            <tr>
                                <td class="product-name">{{ $item->ten }}</td>
                                <td class="text-center text-muted fw-bold">{{ $item->total_sold ?? 0 }}</td>
                            </tr>
                        @elseif($reportType == 'ton_kho')
                            <tr>
                                <td class="product-name">{{ $item->sanPham->ten }}</td>
                                <td><span class="color-badge">{{ $item->mau_sac }}</span></td>
                                <td class="text-center fw-bold text-danger">{{ $item->ton_kho }}</td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="10" class="empty-row">
                                <i class="fas fa-folder-open"></i>
                                <p>Không tìm thấy dữ liệu phù hợp trong thời gian này.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    /* Tổng thể tông màu Đỏ */
    .report-container { padding: 25px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }

    /* Page Header */
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 2px solid #f8d7da; padding-bottom: 15px; }
    .page-title { margin: 0; color: #721c24; font-weight: 700; font-size: 1.5rem; }
    .page-subtitle { margin: 0; color: #842029; opacity: 0.7; }

    /* Nút Xuất File */
    .export-actions { display: flex; gap: 10px; }
    .btn-export { padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: 0.3s; }
    .btn-export.excel { border: 1px solid #198754; color: #198754; }
    .btn-export.excel:hover { background: #198754; color: white; }
    .btn-export.pdf { border: 1px solid #dc3545; color: #dc3545; }
    .btn-export.pdf:hover { background: #dc3545; color: white; }

    /* Bộ lọc (Filter) */
    .filter-card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 4px 15px rgba(220, 53, 69, 0.08); margin-bottom: 25px; border: 1px solid #f5c2c7; }
    .filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: flex-end; }
    .filter-label { font-weight: 600; font-size: 0.85rem; color: #842029; margin-bottom: 8px; display: block; }
    .filter-select, .filter-input { width: 100%; padding: 10px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 0.9rem; transition: 0.3s; }
    .filter-select:focus, .filter-input:focus { border-color: #dc3545; box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.15); outline: none; }
    
    .btn-submit { background: #dc3545; color: white; border: none; padding: 11px; border-radius: 8px; font-weight: 600; width: 100%; cursor: pointer; transition: 0.3s; display: flex; align-items: center; justify-content: center; gap: 8px; }
    .btn-submit:hover { background: #bb2d3b; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }

    /* Thẻ hiển thị dữ liệu (Data Card) */
    .data-card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #eee; }
    .card-header-inner { padding: 15px 20px; background: #fff5f5; border-bottom: 1px solid #f8d7da; }
    .report-type-title { margin: 0; color: #dc3545; font-size: 1rem; font-weight: 700; }

    /* Bảng tùy chỉnh (Custom Table) */
    .table-wrapper { overflow-x: auto; }
    .custom-table { width: 100%; border-collapse: collapse; }
    .custom-table th { background: #fffafa; padding: 15px 20px; text-align: left; font-size: 0.85rem; color: #842029; text-transform: uppercase; border-bottom: 2px solid #f8d7da; letter-spacing: 0.5px; }
    .custom-table td { padding: 15px 20px; border-bottom: 1px solid #f1f1f1; font-size: 0.95rem; color: #2c3e50; }
    .custom-table tbody tr:hover { background: #fff8f8; }

    /* Trang trí ô dữ liệu */
    .id-col { font-weight: 700; color: #dc3545; }
    .amount-text { font-weight: 700; color: #dc3545; }
    .product-name { font-weight: 600; color: #2c3e50; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .color-badge { background: #f8d7da; color: #dc3545; padding: 3px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; }
    
    /* Trạng thái trống */
    .empty-row { text-align: center; padding: 60px !important; color: #adb5bd; }
    .empty-row i { font-size: 3.5rem; margin-bottom: 15px; display: block; }
</style>
@endsection