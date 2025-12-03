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
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                <tr>
                    <th>Hình ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Giá</th>
                    <th>Kho</th>
                    <th>Hiển thị</th>
                    <th>Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse($products as $sp)
                    @php
                        // FIX LỖI: Dùng bienTheSanPham thay vì bienThe
                        $minPrice = $sp->bienTheSanPham->min('gia');
                        $maxPrice = $sp->bienTheSanPham->max('gia');
                        $stock = $sp->bienTheSanPham->sum('ton_kho');
                    @endphp
                    <tr>
                        <td>
                            @if($sp->hinh_anh_mac_dinh)
                                <img src="{{ asset('uploads/' . $sp->hinh_anh_mac_dinh) }}" 
                                     alt="Product" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                            @else
                                <span style="font-size: 0.8rem; color: #888;">No Img</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $sp->ten }}</strong><br>
                            {{-- FIX LỖI: Dùng bienTheSanPham thay vì bienThe --}}
                            <small style="color: #666;">SKU: {{ $sp->bienTheSanPham->first()->sku ?? 'N/A' }}</small>
                        </td>
                        <td>
                            @if($minPrice)
                                {{ number_format($minPrice, 0, ',', '.') }}đ
                            @else
                                <span style="color: red;">Chưa có giá</span>
                            @endif
                        </td>
                        <td>{{ $stock }}</td>
                        <td>
                            @if($sp->hien_thi)
                                <span style="color: green; font-weight: bold;">Hiện</span>
                            @else
                                <span style="color: gray;">Ẩn</span>
                            @endif
                        </td>
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
                        <td colspan="6" class="text-center">Chưa có sản phẩm nào.</td>
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