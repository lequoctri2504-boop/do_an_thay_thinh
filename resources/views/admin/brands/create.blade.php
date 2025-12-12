@extends('layouts.admin')
@section('title', 'Thêm thương hiệu')
@section('content')
<section class="dashboard-section active">
    <div class="section-header">
        <h1>Thêm thương hiệu mới</h1>
        <a href="{{ route('admin.brands') }}" class="btn btn-secondary">Quay lại</a>
    </div>

    @if ($errors->any()) <div class="alert alert-danger"><ul>@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul></div> @endif
    
    <div class="dashboard-card" style="max-width: 500px; margin: 0 auto; padding: 20px;">
        {{-- FIX: THÊM enctype="multipart/form-data" --}}
        <form action="{{ route('admin.brands.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="form-group" style="margin-bottom:15px">
                <label>Tên thương hiệu (*)</label>
                <input type="text" name="ten" class="form-control" value="{{ old('ten') }}" required>
            </div>
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label>Logo thương hiệu (*)</label>
                {{-- FIX: INPUT FILE --}}
                <input type="file" name="hinh_anh" class="form-control" accept="image/*" required>
                <small class="text-muted">Kích thước logo nên là hình vuông (ví dụ: 100x100).</small>
            </div>
            
            <button type="submit" class="btn btn-primary">Lưu thương hiệu</button>
        </form>
    </div>
</section>
@endsection