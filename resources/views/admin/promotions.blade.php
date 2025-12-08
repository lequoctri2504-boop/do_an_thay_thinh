@extends('layouts.admin')

@section('title', 'Quản lý Khuyến mãi')

@section('content')
<section class="dashboard-section active">
    <div class="section-header">
        <h1>Quản lý Khuyến mãi</h1>
        <div class="header-actions">
            <a href="{{ route('admin.promotions.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tạo khuyến mãi</a>
        </div>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

    <div class="dashboard-card">
        
        {{-- Search Bar --}}
        <div class="search-box" style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
            <form action="{{ route('admin.promotions') }}" method="GET" style="display: flex; gap: 15px; align-items: center;">
                <input type="text" name="keyword" class="form-control" 
                       placeholder="Tìm theo tên/mã khuyến mãi..." 
                       value="{{ $keyword ?? '' }}" style="width: 300px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Tìm</button>
                @if($keyword)
                    <a href="{{ route('admin.promotions') }}" class="btn btn-secondary">Hủy tìm kiếm</a>
                @endif
            </form>
        </div>
        
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        {{-- Helper functions for sorting --}}
                        @php
                            $getSortUrl = function($column) use ($sortBy, $sortOrder) {
                                $newOrder = ($sortBy == $column && $sortOrder == 'asc') ? 'desc' : 'asc';
                                return route('admin.promotions', array_merge(request()->except(['sort_by', 'sort_order', 'page']), ['sort_by' => $column, 'sort_order' => $newOrder]));
                            };
                            $showSortIcon = function($column) use ($sortBy, $sortOrder) {
                                if ($sortBy != $column) return '';
                                return $sortOrder == 'asc' ? '<i class="fas fa-sort-up sort-icon"></i>' : '<i class="fas fa-sort-down sort-icon"></i>';
                            };
                        @endphp
                        
                        <th>
                            <a href="{{ $getSortUrl('ten') }}" class="sort-link">Tên chương trình {!! $showSortIcon('ten') !!}</a>
                        </th>
                        <th>
                            <a href="{{ $getSortUrl('ma') }}" class="sort-link">Mã giảm giá {!! $showSortIcon('ma') !!}</a>
                        </th>
                        <th>Giảm (%) / Tiền</th>
                        <th>
                            <a href="{{ $getSortUrl('ngay_bat_dau') }}" class="sort-link">Ngày bắt đầu {!! $showSortIcon('ngay_bat_dau') !!}</a>
                        </th>
                        <th>Ngày kết thúc</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($promotions as $promo)
                        @php
                            $statusText = $promo->current_status;
                            $statusClass = 'processing';
                            if ($statusText == 'Đang diễn ra') $statusClass = 'shipping'; 
                            elseif ($statusText == 'Đã kết thúc') $statusClass = 'cancelled';
                            else $statusClass = 'processing'; 
                        @endphp
                    <tr>
                        <td>{{ $promo->ten }}</td>
                        <td><strong>{{ $promo->ma }}</strong></td>
                        <td>{{ $promo->gia_tri }}</td>
                        <td>{{ $promo->ngay_bat_dau->format('d/m/Y') }}</td>
                        <td>{{ $promo->ngay_ket_thuc->format('d/m/Y') }}</td>
                        <td><span class="status-badge status-{{ $statusClass }}">{{ $statusText }}</span></td>
                        <td>
                            <a href="{{ route('admin.promotions.edit', $promo->id) }}" class="btn btn-sm btn-primary" title="Sửa"><i class="fas fa-edit"></i></a>
                            
                            <form action="{{ route('admin.promotions.destroy', $promo->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Xóa khuyến mãi này?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger" title="Xóa"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                        <tr><td colspan="7" class="text-center">Hiện chưa có chương trình khuyến mãi nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrapper">{{ $promotions->links('vendor.pagination.admin-custom-pagination') }}</div>
    </div>
</section>
@endsection

@push('styles')
<style>
    /* Custom styles for sort links */
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
</style>
@endpush