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
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên danh mục</th>
                    <th>Slug</th>
                    <th>Danh mục cha</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
            @forelse($categories as $cat)
                <tr>
                    <td>{{ $cat->id }}</td>
                    <td><strong>{{ $cat->ten }}</strong></td>
                    <td>{{ $cat->slug }}</td>
                    {{-- FIX LỖI: Thay thế $cat->danhMucCha bằng $cat->parent --}}
                    <td>{{ $cat->parent ? $cat->parent->ten : '---' }}</td>
                    <td>
                        <a href="{{ route('admin.categories.edit', $cat->id) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Xóa danh mục này?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">Chưa có danh mục nào.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="pagination-wrapper">{{ $categories->links() }}</div>
    </div>
</section>
@endsection