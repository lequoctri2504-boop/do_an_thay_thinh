@extends('layouts.admin')

@section('title', 'Quản lý sản phẩm')

@section('content')
<section class="dashboard-section active" id="products">
    <div class="section-header">
        <h1>Quản lý sản phẩm</h1>
        <div class="header-actions">
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm sản phẩm
            </a>
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
            <form action="{{ route('admin.products') }}" method="GET" style="display: flex; gap: 15px; align-items: center;">
                
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

                {{-- Display Filter --}}
                <select name="hien_thi" class="form-control" style="width: 150px;">
                    <option value="">-- Trạng thái --</option>
                    <option value="1" {{ ($hienThi ?? '') === '1' ? 'selected' : '' }}>Hiển thị</option>
                    <option value="0" {{ ($hienThi ?? '') === '0' ? 'selected' : '' }}>Ẩn</option>
                </select>

                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Lọc</button>
                
                @if(request()->filled('keyword') || request()->filled('thuong_hieu_id') || request()->filled('hien_thi'))
                    <a href="{{ route('admin.products') }}" class="btn btn-secondary">Hủy lọc</a>
                @endif
            </form>
        </div>
        {{-- End Search and Filter Bar --}}

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                <tr>
                    {{-- Hàm tạo URL sắp xếp --}}
                    @php
                        $getSortUrl = function($column) use ($sortBy, $sortOrder) {
                            $newOrder = ($sortBy == $column && $sortOrder == 'asc') ? 'desc' : 'asc';
                            return route('admin.products', array_merge(request()->except(['sort_by', 'sort_order', 'page']), ['sort_by' => $column, 'sort_order' => $newOrder]));
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
                        <a href="{{ $getSortUrl('ton_kho') }}" class="sort-link">
                            Kho (Tổng) {!! $showSortIcon('ton_kho') !!}
                        </a>
                    </th>
                    <th>
                        <a href="{{ $getSortUrl('hien_thi') }}" class="sort-link">
                            Hiển thị {!! $showSortIcon('hien_thi') !!}
                        </a>
                    </th>
                    <th>
                        <a href="{{ $getSortUrl('created_at') }}" class="sort-link">
                            Ngày tạo {!! $showSortIcon('created_at') !!}
                        </a>
                    </th>
                    <th style="width: 100px;">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse($products as $sp)
                    @php
                        // FIX LỖI: Dùng bienTheSanPham thay vì bienThe
                        $minPrice = $sp->bienTheSanPham->min('gia');
                        $stock = $sp->bienTheSanPham->sum('ton_kho');
                    @endphp
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
                            <small style="color: #666;">SKU: {{ $sp->bienTheSanPham->first()->sku ?? 'N/A' }}</small>
                        </td>
                        <td>
                            {{ $sp->thuongHieu->ten ?? 'N/A' }}
                        </td>
                        <td>
                            @if($minPrice)
                                <strong>{{ number_format($minPrice, 0, ',', '.') }}₫</strong>
                            @else
                                <span style="color: red;">Chưa có giá</span>
                            @endif
                        </td>
                        <td>{{ $stock }}</td>
                        <td>
                            @if($sp->hien_thi)
                                <span class="status-badge status-approved">Hiện</span>
                            @else
                                <span class="status-badge status-cancelled">Ẩn</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($sp->created_at)->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('admin.products.edit', $sp->id) }}" class="btn btn-sm btn-primary" title="Sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            
                            <form action="{{ route('admin.products.destroy', $sp->id) }}" method="POST" 
                                  onsubmit="return confirm('Bạn có chắc muốn xóa không?');" style="display: inline-block;">
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
                        <td colspan="8" class="text-center">Không tìm thấy sản phẩm nào.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrapper">
            {{ $products->links('vendor.pagination.admin-custom-pagination') }}
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
    
    /* Ensure sorting links look clickable */
    .data-table thead th a.sort-link {
        display: flex;
        align-items: center;
        gap: 5px;
        color: var(--text-dark); 
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s ease;
    }
    .data-table thead th a.sort-link:hover {
        color: var(--primary-color);
    }
    .data-table thead th a .sort-icon {
        font-size: 14px;
        color: var(--primary-color);
    }

    /* Đảm bảo style của data-table đang sử dụng đúng */
    .data-table th a {
        text-decoration: none !important;
        color: inherit !important;
    }
</style>
@endpush