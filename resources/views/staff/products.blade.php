@extends('layouts.staff')

@section('title', 'Quản lý sản phẩm')

@section('content')
<section class="dashboard-section active">

    <div class="section-header">
        <h1>Quản lý sản phẩm</h1>
        <div class="header-actions">
            {{-- Nhân viên không có quyền Thêm Sản phẩm --}}
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="padding: 10px; background: #d4edda; color: #155724; margin-bottom: 10px; border-radius: 4px;">
            {{ session('success') }}
        </div>
    @endif
    
    <div class="dashboard-card">
        {{-- Search and Filter Bar --}}
        <div class="search-box" style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
            <form action="{{ route('staff.products') }}" method="GET" style="display: flex; gap: 15px; align-items: center;">
                
                {{-- Search Input --}}
                <input type="text" name="keyword" class="form-control" 
                       placeholder="Tìm theo tên/SKU..." 
                       value="{{ $keyword ?? '' }}" style="width: 250px;">
                
                {{-- Brand Filter --}}
                <select name="thuong_hieu_id" class="form-control" style="width: 150px;">
                    <option value="">-- Thương hiệu --</option>
                    @foreach($thuongHieu as $th)
                        <option value="{{ $th->id }}" {{ ($brandId ?? '') == $th->id ? 'selected' : '' }}>
                            {{ $th->ten }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Lọc</button>
                
                @if(request()->filled('keyword') || request()->filled('thuong_hieu_id'))
                    <a href="{{ route('staff.products') }}" class="btn btn-secondary">Hủy lọc</a>
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
                            return route('staff.products', array_merge(request()->except(['sort_by', 'sort_order', 'page']), ['sort_by' => $column, 'sort_order' => $newOrder]));
                        };
                        $showSortIcon = function($column) use ($sortBy, $sortOrder) {
                            if ($sortBy != $column) return '';
                            return $sortOrder == 'asc' ? '<i class="fas fa-sort-up sort-icon"></i>' : '<i class="fas fa-sort-down sort-icon"></i>';
                        };
                    @endphp
                    
                    <th style="width: 80px;">Hình ảnh</th>
                    <th>
                        <a href="{{ $getSortUrl('ten') }}" class="sort-link">
                            Tên sản phẩm {!! $showSortIcon('ten') !!}
                        </a>
                    </th>
                    <th>Thương hiệu</th>
                    <th>
                        <a href="{{ $getSortUrl('gia') }}" class="sort-link">
                            Giá (Min) {!! $showSortIcon('gia') !!}
                        </a>
                    </th>
                    <th>
                        Kho (Tổng)
                    </th>
                    <th>Trạng thái</th>
                    <th>Flash Sale</th>
                    <th>Nổi bật</th>
                    <th style="width: 100px;">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse($products as $sp)
                    <tr>
                        <td>
                            @if($sp->hinh_anh_mac_dinh)
                                <img src="{{ asset('uploads/' . $sp->hinh_anh_mac_dinh) }}" 
                                     alt="Product" class="product-thumb">
                            @else
                                <span style="font-size: 0.8rem; color: #888;">No Img</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $sp->ten }}</strong><br>
                            <small style="color: #666;">SKU: {{ $sp->first_variant_sku }}</small>
                        </td>
                        <td>
                            {{ $sp->thuongHieu->ten ?? 'N/A' }}
                        </td>
                        <td>
                            @if($sp->min_price)
                                <strong>{{ number_format($sp->min_price, 0, ',', '.') }}₫</strong>
                            @else
                                <span style="color: red;">Chưa có giá</span>
                            @endif
                        </td>
                        <td>
                            {{ $sp->total_stock }}
                            @if($sp->total_stock < 10)
                                <span class="stock-badge danger">Thấp!</span>
                            @endif
                        </td>
                        <td>
                            @if($sp->hien_thi)
                                <span class="status-badge status-approved">Hiện</span>
                            @else
                                <span class="status-badge status-cancelled">Ẩn</span>
                            @endif
                        </td>
                        {{-- Cột Flash Sale --}}
                        <td>
                            <button type="button" 
                                    class="btn btn-sm toggle-flag-btn" 
                                    data-product-id="{{ $sp->id }}" 
                                    data-flag="la_flash_sale" 
                                    data-current-value="{{ $sp->la_flash_sale ? 1 : 0 }}">
                                <i class="fas fa-{{ $sp->la_flash_sale ? 'check-circle text-success' : 'times-circle text-danger' }}"></i>
                            </button>
                        </td>
                        {{-- Cột Nổi bật --}}
                        <td>
                            <button type="button" 
                                    class="btn btn-sm toggle-flag-btn" 
                                    data-product-id="{{ $sp->id }}" 
                                    data-flag="la_noi_bat" 
                                    data-current-value="{{ $sp->la_noi_bat ? 1 : 0 }}">
                                <i class="fas fa-{{ $sp->la_noi_bat ? 'check-circle text-success' : 'times-circle text-danger' }}"></i>
                            </button>
                        </td>
                        <td>
                            {{-- Nhân viên chỉ có quyền chỉnh sửa, không có quyền xóa --}}
                            <a href="{{ route('staff.products.edit', $sp->id) }}" class="btn btn-sm btn-primary" title="Sửa chi tiết (Giá/Kho)">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">Không tìm thấy sản phẩm nào.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="table-pagination">
            {{ $products->appends(request()->all())->links('vendor.pagination.admin-custom-pagination') }}
        </div>
    </div>

</section>
@endsection

@push('styles')
<style>
    /* Ensure the product image is small and contained */
    .product-thumb {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 4px;
    }
    /* Thêm style cho badge tồn kho (nếu cần) */
    .stock-badge.danger {
        background: #F8D7DA;
        color: #721C24;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
    }
    /* Tăng độ tương phản cho icon */
    .text-success { color: #28a745 !important; }
    .text-danger { color: #dc3545 !important; }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Lấy token CSRF từ meta tag (Giả định bạn đã khai báo trong layouts/staff.blade.php hoặc layouts/admin.blade.php)
        const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '';

        document.querySelectorAll('.toggle-flag-btn').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.productId;
                const flag = this.dataset.flag;
                const currentValue = parseInt(this.dataset.currentValue);
                const newValue = currentValue === 0 ? 1 : 0;
                
                // Cập nhật trạng thái hiển thị (loading)
                const icon = this.querySelector('i');
                const originalIconClass = icon.className;
                icon.className = 'fas fa-spinner fa-spin';
                this.disabled = true;

                fetch(`{{ url('staff/products') }}/${productId}/toggle-flag`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        flag: flag,
                        value: newValue
                    })
                })
                .then(response => response.json())
                .then(data => {
                    this.disabled = false;
                    if (data.success) {
                        // Cập nhật giao diện và data attribute
                        this.dataset.currentValue = data.new_value;
                        if (data.new_value === 1) {
                            icon.className = 'fas fa-check-circle text-success';
                        } else {
                            icon.className = 'fas fa-times-circle text-danger';
                        }
                        window.PhoneShop.showToast(data.message, 'success');
                    } else {
                        icon.className = originalIconClass; // Quay lại trạng thái cũ
                        window.PhoneShop.showToast(data.message, 'error');
                    }
                })
                .catch(error => {
                    this.disabled = false;
                    icon.className = originalIconClass;
                    window.PhoneShop.showToast('Lỗi kết nối máy chủ!', 'error');
                });
            });
        });
    });
</script>
@endpush