@extends('layouts.admin')

@section('title', 'Báo cáo Hệ thống')

@section('content')
<div class="report-wrapper">
    <div class="page-header">
        <div class="header-title">
            <i class="fas fa-chart-pie"></i>
            <div>
                <h4>Báo cáo & Thống kê Quản trị</h4>
                <small>Phân tích dữ liệu kinh doanh định kỳ</small>
            </div>
        </div>
    </div>

    {{-- BỘ LỌC DỮ LIỆU --}}
    <section class="report-filter-section">
        <form action="{{ route('admin.reports') }}" method="GET" id="reportForm" class="filter-card">
            <div class="filter-grid">
                <div class="filter-group">
                    <label><i class="fas fa-list-ul"></i> Loại báo cáo</label>
                    <select name="report_type" class="custom-select">
                        <option value="doanh_thu" {{ $type == 'doanh_thu' ? 'selected' : '' }}>📊 Báo cáo Doanh thu</option>
                        <option value="don_hang" {{ $type == 'don_hang' ? 'selected' : '' }}>📦 Danh sách Đơn hàng</option>
                        <option value="san_pham" {{ $type == 'san_pham' ? 'selected' : '' }}>📱 Thống kê Sản phẩm</option>
                        <option value="khach_hang" {{ $type == 'khach_hang' ? 'selected' : '' }}>👥 Danh sách Khách hàng</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label><i class="fas fa-calendar-alt"></i> Từ ngày</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="custom-input">
                </div>

                <div class="filter-group">
                    <label><i class="fas fa-calendar-check"></i> Đến ngày</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="custom-input">
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn-filter">
                        <i class="fas fa-sync-alt"></i> Cập nhật dữ liệu
                    </button>
                </div>
            </div>
        </form>
    </section>

    {{-- HIỂN THỊ DỮ LIỆU --}}
    <div class="report-content-card">
        <div class="card-top-bar">
            <h5 class="card-title">
                @if($type == 'doanh_thu') Chi tiết doanh thu
                @elseif($type == 'don_hang') Danh sách đơn hàng
                @elseif($type == 'san_pham') Top sản phẩm bán chạy
                @else Danh sách khách hàng mới @endif
            </h5>
            <div class="export-buttons">
                @php $params = ['report_type'=>$type, 'start_date'=>$startDate, 'end_date'=>$endDate]; @endphp
                <a href="{{ route('admin.reports.export', array_merge($params, ['format'=>'excel'])) }}" class="btn-export excel">
                    <i class="fas fa-file-excel"></i> Excel
                </a>
                <a href="{{ route('admin.reports.export', array_merge($params, ['format'=>'pdf'])) }}" class="btn-export pdf">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
            </div>
        </div>

        <div class="table-container">
            <table class="styled-table">
                <thead>
                    @if($type == 'doanh_thu' || $type == 'don_hang')
                        <tr>
                            <th>Mã đơn</th>
                            <th>Người nhận</th>
                            <th>Số điện thoại</th>
                            <th class="text-right">Tổng tiền</th>
                            <th class="text-center">Thanh toán</th>
                            <th>Ngày đặt</th>
                        </tr>
                    @elseif($type == 'san_pham')
                        <tr>
                            <th>Tên sản phẩm</th>
                            <th>SKU</th>
                            <th class="text-center">Số lượng</th>
                            <th class="text-right">Doanh thu</th>
                        </tr>
                    @elseif($type == 'khach_hang')
                        <tr>
                            <th>Họ tên</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th class="text-center">Vai trò</th>
                            <th>Ngày tham gia</th>
                        </tr>
                    @endif
                </thead>
                <tbody>
                    @forelse($data as $item)
                        @if($type == 'doanh_thu' || $type == 'don_hang')
                            <tr>
                                <td class="id-cell">#{{ $item->ma }}</td>
                                <td>{{ $item->ten_nguoi_nhan }}</td>
                                <td>{{ $item->sdt_nguoi_nhan }}</td>
                                <td class="text-right amount-cell">{{ number_format($item->thanh_tien) }}₫</td>
                                <td class="text-center">
                                    <span class="status-pill info">{{ $item->phuong_thuc_tt }}</span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($item->ngay_dat)->format('d/m/Y H:i') }}</td>
                            </tr>
                        @elseif($type == 'san_pham')
                            <tr>
                                <td class="product-cell">{{ $item->ten_sp_ghi_nhan }}</td>
                                <td><code>{{ $item->sku_ghi_nhan }}</code></td>
                                <td class="text-center fw-bold">{{ number_format($item->total_qty) }}</td>
                                <td class="text-right amount-cell">{{ number_format($item->total_amount) }}₫</td>
                            </tr>
                        @elseif($type == 'khach_hang')
                            <tr>
                                <td>{{ $item->ho_ten }}</td>
                                <td>{{ $item->email }}</td>
                                <td>{{ $item->sdt ?? '---' }}</td>
                                <td class="text-center">
                                    <span class="status-pill {{ $item->vai_tro == 'admin' ? 'admin' : 'user' }}">
                                        {{ $item->vai_tro }}
                                    </span>
                                </td>
                                <td>{{ $item->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="10" class="empty-state">
                                <i class="fas fa-folder-open"></i>
                                <p>Không tìm thấy dữ liệu trong khoảng thời gian này</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    /* Tổng thể - Tông màu Đỏ */
    .report-wrapper { padding: 20px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    
    .page-header { margin-bottom: 25px; border-bottom: 2px solid #f8d7da; padding-bottom: 15px; }
    .header-title { display: flex; align-items: center; gap: 15px; }
    .header-title i { font-size: 2rem; color: #dc3545; } /* Đỏ chính */
    .header-title h4 { margin: 0; font-size: 1.5rem; color: #721c24; }
    .header-title small { color: #842029; opacity: 0.8; }

    /* Bộ lọc */
    .filter-card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(220, 53, 69, 0.1); margin-bottom: 25px; border: 1px solid #f5c2c7; }
    .filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; align-items: flex-end; }
    .filter-group { display: flex; flex-direction: column; gap: 8px; }
    .filter-group label { font-weight: 600; font-size: 0.9rem; color: #842029; }
    .custom-select, .custom-input { padding: 10px; border: 1px solid #dee2e6; border-radius: 8px; outline: none; transition: 0.3s; }
    .custom-select:focus, .custom-input:focus { border-color: #dc3545; box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.15); }
    
    .btn-filter { background: #dc3545; color: white; border: none; padding: 11px 20px; border-radius: 8px; cursor: pointer; font-weight: 600; transition: 0.3s; display: flex; align-items: center; justify-content: center; gap: 8px; }
    .btn-filter:hover { background: #bb2d3b; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }

    /* Bảng dữ liệu */
    .report-content-card { background: white; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid #eee; }
    .card-top-bar { padding: 20px; border-bottom: 1px solid #f8d7da; display: flex; justify-content: space-between; align-items: center; background: #fff5f5; }
    .card-title { margin: 0; color: #dc3545; font-size: 1.1rem; font-weight: bold; }
    
    .btn-export { padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 600; margin-left: 10px; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s; }
    .btn-export.excel { color: #198754; border: 1px solid #198754; }
    .btn-export.excel:hover { background: #198754; color: white; }
    .btn-export.pdf { color: #dc3545; border: 1px solid #dc3545; }
    .btn-export.pdf:hover { background: #dc3545; color: white; }

    .table-container { overflow-x: auto; }
    .styled-table { width: 100%; border-collapse: collapse; min-width: 800px; }
    .styled-table thead { background: #fffafa; }
    .styled-table th { padding: 15px; text-align: left; font-size: 0.85rem; color: #842029; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #f8d7da; }
    .styled-table td { padding: 15px; border-bottom: 1px solid #f1f1f1; font-size: 0.95rem; color: #2c3e50; }
    .styled-table tbody tr:hover { background-color: #fff8f8; }
    
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .amount-cell { font-weight: 700; color: #dc3545; } /* Số tiền màu đỏ cho nổi bật */
    .id-cell { font-weight: 600; color: #000; }
    
    /* Status Pills */
    .status-pill { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
    .status-pill.info { background: #f8d7da; color: #dc3545; border: 1px solid #f5c2c7; }
    .status-pill.admin { background: #2c3e50; color: #fff; }
    .status-pill.user { background: #e9ecef; color: #495057; }

    .empty-state { text-align: center; padding: 60px !important; color: #adb5bd; }
    .empty-state i { font-size: 3.5rem; margin-bottom: 15px; display: block; }
</style>
@endsection