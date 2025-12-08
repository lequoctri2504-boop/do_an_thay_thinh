@extends('layouts.staff')

@section('title', 'Quản lý Tin tức Công nghệ')

@section('content')
<section class="dashboard-section active">
    <div class="section-header">
        <h1>Quản lý Tin tức Công nghệ</h1>
        <a href="{{ route('staff.news.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm bài viết mới</a>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
    
    <div class="dashboard-card">
        
        {{-- Search Bar --}}
        <div class="search-box" style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
            <form action="{{ route('staff.news') }}" method="GET" style="display: flex; gap: 15px; align-items: center;">
                <input type="text" name="keyword" class="form-control" 
                       placeholder="Tìm theo tiêu đề hoặc nội dung..." 
                       value="{{ $keyword ?? '' }}" style="width: 350px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Tìm</button>
                @if($keyword)
                    <a href="{{ route('staff.news') }}" class="btn btn-secondary">Hủy lọc</a>
                @endif
            </form>
        </div>
        
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 80px;">Ảnh</th>
                        <th>Tiêu đề</th>
                        <th>Tác giả</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
            @forelse($news as $baiViet)
                @php
                    $statusClass = $baiViet->trang_thai == 'XUAT_BAN' ? 'delivered' : 'processing';
                    $imagePath = $baiViet->hinh_anh_chinh ? asset('uploads/' . $baiViet->hinh_anh_chinh) : 'https://via.placeholder.com/60';
                @endphp
                <tr>
                    <td><img src="{{ $imagePath }}" class="product-thumb"></td>
                    <td><strong>{{ $baiViet->tieu_de }}</strong></td>
                    <td>{{ $baiViet->nguoiDung->ho_ten ?? 'N/A' }}</td>
                    <td><span class="status-badge status-{{ $statusClass }}">{{ $baiViet->trang_thai }}</span></td>
                    <td>{{ \Carbon\Carbon::parse($baiViet->created_at)->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ route('staff.news.edit', $baiViet->id) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('staff.news.destroy', $baiViet->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Xóa bài viết này?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">Chưa có bài viết nào.</td></tr>
            @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrapper">{{ $news->links('vendor.pagination.admin-custom-pagination') }}</div>
    </div>
</section>
@endsection