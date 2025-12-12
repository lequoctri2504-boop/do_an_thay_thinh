@extends('layouts.admin')

@section('title', 'Quản lý Thương hiệu')

@section('content')
<section class="dashboard-section active">
    <div class="section-header">
        <h1>Quản lý Thương hiệu</h1>
        <a href="{{ route('admin.brands.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm Thương hiệu</a>
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
                        <th style="width: 50px;">ID</th>
                        <th>Tên</th>
                        <th style="width: 100px;">Logo</th> {{-- CỘT MỚI --}}
                        <th>Slug</th>
                        <th style="width: 150px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($brands as $brand)
                    <tr>
                        <td>{{ $brand->id }}</td>
                        <td>{{ $brand->ten }}</td>
                        <td>
                            @if($brand->hinh_anh)
                                {{-- FIX: SỬ DỤNG ĐƯỜNG DẪN public/images/brands --}}
                                <img src="{{ asset('images/brands/' . $brand->hinh_anh) }}" alt="{{ $brand->ten }} Logo" width="50" style="object-fit: contain; border: 1px solid #eee; padding: 3px; border-radius: 4px;">
                            @else
                                N/A
                            @endif
                        </td>
                        <td>{{ $brand->slug }}</td>
                        <td>
                            <a href="{{ route('admin.brands.edit', $brand->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Sửa
                            </a>
                            <form action="{{ route('admin.brands.destroy', $brand->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa thương hiệu này? Thao tác này sẽ xóa vĩnh viễn và ảnh hưởng đến các sản phẩm liên quan.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Chưa có thương hiệu nào được thêm.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Giả định $brands là collection đã được paginate --}}
        <div class="table-pagination">
            {{ $brands->links('vendor.pagination.admin-custom-pagination') }}
        </div>
    </div>
</section>
@endsection