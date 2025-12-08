@extends('layouts.admin')

@section('title', 'Quản lý Đơn hàng')

@section('content')
<section class="dashboard-section active">
    <div class="section-header">
        <h1>Quản lý Đơn hàng</h1>
        <div class="header-actions">
            {{-- Không có nút thêm đơn hàng nhanh --}}
        </div>
    </div>
    
    <div class="dashboard-card">
        {{-- Search and Filter Bar --}}
        <div class="search-box" style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
            <form action="{{ route('admin.orders') }}" method="GET" style="display: flex; gap: 15px; align-items: center;">
                
                {{-- Lọc Trạng thái --}}
                <select name="trang_thai" class="form-control" style="width: 150px;">
                    <option value="">-- Trạng thái --</option>
                    <option value="DANG_XU_LY" {{ ($trangThai ?? '') == 'DANG_XU_LY' ? 'selected' : '' }}>Đang xử lý</option>
                    <option value="DANG_GIAO" {{ ($trangThai ?? '') == 'DANG_GIAO' ? 'selected' : '' }}>Đang giao</option>
                    <option value="HOAN_THANH" {{ ($trangThai ?? '') == 'HOAN_THANH' ? 'selected' : '' }}>Hoàn thành</option>
                    <option value="HUY" {{ ($trangThai ?? '') == 'HUY' ? 'selected' : '' }}>Đã hủy</option>
                </select>

                {{-- Search Input --}}
                <input type="text" name="keyword" class="form-control" 
                       placeholder="Tìm Mã đơn/Người nhận..." 
                       value="{{ $keyword ?? '' }}" style="width: 250px;">
                
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Lọc/Tìm</button>
                
                @if(request()->filled('keyword') || ($trangThai ?? ''))
                    <a href="{{ route('admin.orders') }}" class="btn btn-secondary">Hủy lọc</a>
                @endif
            </form>
        </div>
        {{-- End Search and Filter Bar --}}

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        {{-- Helper functions for sorting --}}
                        @php
                            $getSortUrl = function($column) use ($sortBy, $sortOrder) {
                                $newOrder = ($sortBy == $column && $sortOrder == 'asc') ? 'desc' : 'asc';
                                return route('admin.orders', array_merge(request()->except(['sort_by', 'sort_order', 'page']), ['sort_by' => $column, 'sort_order' => $newOrder]));
                            };
                            $showSortIcon = function($column) use ($sortBy, $sortOrder) {
                                if ($sortBy != $column) return '';
                                return $sortOrder == 'asc' ? '<i class="fas fa-sort-up sort-icon"></i>' : '<i class="fas fa-sort-down sort-icon"></i>';
                            };
                        @endphp
                        
                        <th>
                            <a href="{{ $getSortUrl('ma') }}" class="sort-link">Mã đơn {!! $showSortIcon('ma') !!}</a>
                        </th>
                        <th>Khách hàng</th>
                        <th>
                            <a href="{{ $getSortUrl('ngay_dat') }}" class="sort-link">Ngày đặt {!! $showSortIcon('ngay_dat') !!}</a>
                        </th>
                        <th>
                            <a href="{{ $getSortUrl('thanh_tien') }}" class="sort-link">Tổng tiền {!! $showSortIcon('thanh_tien') !!}</a>
                        </th>
                        <th>
                            <a href="{{ $getSortUrl('trang_thai') }}" class="sort-link">Trạng thái {!! $showSortIcon('trang_thai') !!}</a>
                        </th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($orders as $order)
                    @php
                        $status = strtolower($order->trang_thai);
                        if ($status == 'dang_xu_ly') $statusClass = 'processing';
                        elseif ($status == 'dang_giao') $statusClass = 'shipping';
                        elseif ($status == 'hoan_thanh') $statusClass = 'delivered';
                        else $statusClass = 'cancelled';
                        
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
                            {{-- Nút Chi tiết & Cập nhật --}}
                            <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-sm btn-primary" title="Chi tiết & Cập nhật">
                                <i class="fas fa-edit"></i>
                            </a>
                            
                            {{-- Nút Xóa (Soft Delete) --}}
                            <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" 
                                  onsubmit="return confirm('Bạn có chắc muốn XÓA (Soft Delete) đơn hàng này? Việc này sẽ hoàn lại tồn kho nếu đơn hàng chưa bị hủy.');" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
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
            {{ $orders->links('vendor.pagination.admin-custom-pagination') }}
        </div>
    </div>
</section>
@endsection