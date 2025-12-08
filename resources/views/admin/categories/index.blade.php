@extends('layouts.admin')
@section('title', 'Quản lý danh mục')
@section('content')
<section class="dashboard-section active">
    <div class="section-header">
        <h1>Quản lý danh mục</h1>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm danh mục</a>
    </div>
    
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

    <div class="dashboard-card">
        
        {{-- Search Bar --}}
        <div class="search-box" style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
            <form action="{{ route('admin.categories') }}" method="GET" style="display: flex; gap: 15px; align-items: center;">
                <input type="text" name="keyword" class="form-control" 
                       placeholder="Tìm theo tên danh mục..." 
                       value="{{ $keyword ?? '' }}" style="width: 300px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Tìm</button>
                @if($keyword)
                    <a href="{{ route('admin.categories') }}" class="btn btn-secondary">Hủy lọc</a>
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
                                return route('admin.categories', array_merge(request()->except(['sort_by', 'sort_order', 'page']), ['sort_by' => $column, 'sort_order' => $newOrder]));
                            };
                            $showSortIcon = function($column) use ($sortBy, $sortOrder) {
                                if ($sortBy != $column) return '';
                                return $sortOrder == 'asc' ? '<i class="fas fa-sort-up sort-icon"></i>' : '<i class="fas fa-sort-down sort-icon"></i>';
                            };
                        @endphp
                        
                        <th>
                            <a href="{{ $getSortUrl('id') }}" class="sort-link">ID {!! $showSortIcon('id') !!}</a>
                        </th>
                        <th>
                            <a href="{{ $getSortUrl('ten') }}" class="sort-link">Tên danh mục {!! $showSortIcon('ten') !!}</a>
                        </th>
                        <th>Slug</th>
                        <th>Danh mục cha</th>
                        <th>
                            <a href="{{ $getSortUrl('created_at') }}" class="sort-link">Ngày tạo {!! $showSortIcon('created_at') !!}</a>
                        </th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
            @forelse($categories as $cat)
                <tr>
                    <td>{{ $cat->id }}</td>
                    <td><strong>{{ $cat->ten }}</strong></td>
                    <td>{{ $cat->slug }}</td>
                    <td>{{ $cat->parent ? $cat->parent->ten : '---' }}</td>
                    <td>{{ \Carbon\Carbon::parse($cat->created_at)->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ route('admin.categories.edit', $cat->id) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Xóa danh mục này?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">Chưa có danh mục nào.</td></tr>
            @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrapper">{{ $categories->links('vendor.pagination.admin-custom-pagination') }}</div>
    </div>
</section>
@endsection