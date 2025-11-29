@extends('layouts.admin')
@section('title', 'Quản lý thương hiệu')
@section('content')
<section class="dashboard-section active">
    <div class="section-header">
        <h1>Quản lý thương hiệu</h1>
        <a href="{{ route('admin.brands.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm thương hiệu</a>
    </div>
    
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

    <div class="dashboard-card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên thương hiệu</th>
                    <th>Slug</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
            @forelse($brands as $brand)
                <tr>
                    <td>{{ $brand->id }}</td>
                    <td><strong>{{ $brand->ten }}</strong></td>
                    <td>{{ $brand->slug }}</td>
                    <td>{{ $brand->created_at->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ route('admin.brands.edit', $brand->id) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.brands.destroy', $brand->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Xóa thương hiệu này?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">Chưa có thương hiệu nào.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="pagination-wrapper">{{ $brands->links() }}</div>
    </div>
</section>
@endsection