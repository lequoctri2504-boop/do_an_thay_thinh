@extends('layouts.admin')

@section('title', 'Quản lý sản phẩm - PhoneShop')

@section('content')
<section class="dashboard-section active" id="products">
    <div class="section-header">
        <h1>Quản lý sản phẩm</h1>
        <div class="header-actions">
            <input type="text" class="form-control" placeholder="Tìm kiếm sản phẩm...">
            <button class="btn btn-secondary"><i class="fas fa-file-import"></i> Import</button>
            <button class="btn btn-primary"><i class="fas fa-plus"></i> Thêm sản phẩm</button>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                <tr>
                    <th><input type="checkbox"></th>
                    <th>Hình ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Giá (nếu có bảng biến thể thì sau này join)</th>
                    <th>Hiển thị</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse($products as $sp)
                    <tr>
                        <td><input type="checkbox"></td>
                        <td>
                            @if($sp->hinh_anh_mac_dinh)
                                <img src="{{ asset('uploads/' . $sp->hinh_anh_mac_dinh) }}"
                                     alt="Product" class="product-thumb">
                            @else
                                <span>Không có ảnh</span>
                            @endif
                        </td>
                        <td><strong>{{ $sp->ten }}</strong></td>
                        <td>
                            {{-- Nếu bạn có bảng biến thể thì hiển thị min / max giá. Tạm thời để trống --}}
                            (chưa xử lý)
                        </td>
                        <td>
                            @if($sp->hien_thi)
                                <span class="status-badge active">Đang hiển thị</span>
                            @else
                                <span class="status-badge inactive">Ẩn</span>
                            @endif
                        </td>
                        <td>{{ $sp->created_at?->format('d/m/Y') }}</td>
                        <td>
                            <button class="btn btn-sm btn-primary" title="Sửa"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-danger" title="Xóa"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Chưa có sản phẩm nào.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $products->links() }}
        </div>
    </div>
</section>
@endsection
