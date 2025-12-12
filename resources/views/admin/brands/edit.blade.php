@extends('layouts.admin')
@section('title', 'Sửa thương hiệu')
@section('content')
<section class="dashboard-section active">
    <div class="section-header">
        <h1>Sửa thương hiệu: {{ $brand->ten }}</h1>
        <a href="{{ route('admin.brands') }}" class="btn btn-secondary">Quay lại</a>
    </div>

    @if ($errors->any()) <div class="alert alert-danger"><ul>@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul></div> @endif
    
    <div class="dashboard-card" style="max-width: 500px; margin: 0 auto; padding: 20px;">
        {{-- FIX: THÊM enctype="multipart/form-data" --}}
        <form action="{{ route('admin.brands.update', $brand->id) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            
            <div class="form-group" style="margin-bottom:15px">
                <label>Tên thương hiệu (*)</label>
                <input type="text" name="ten" class="form-control" value="{{ old('ten', $brand->ten) }}" required>
            </div>
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label>Logo hiện tại</label>
                @if($brand->hinh_anh)
                    {{-- FIX: ĐƯỜNG DẪN ẢNH --}}
                    <img src="{{ asset('images/brands/' . $brand->hinh_anh) }}" alt="Logo" width="100" style="display: block; margin-bottom: 10px; border: 1px solid #eee;">
                @else
                    <p class="text-muted">Chưa có logo.</p>
                @endif
                
                <label>Chọn Logo mới (Để trống nếu không đổi)</label>
                {{-- FIX: INPUT FILE MỚI --}}
                <input type="file" name="hinh_anh_moi" class="form-control" accept="image/*">
            </div>

            <button type="submit" class="btn btn-primary">Cập nhật</button>
        </form>
    </div>
</section>
@endsection