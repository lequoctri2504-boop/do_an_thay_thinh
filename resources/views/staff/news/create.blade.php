@extends('layouts.staff')
@section('title', 'Tạo bài viết mới')
@section('content')
<section class="dashboard-section active">
    <div class="section-header">
        <h1>Tạo bài viết mới</h1>
        <a href="{{ route('staff.news') }}" class="btn btn-secondary">Quay lại</a>
    </div>
    
    <div class="dashboard-card">
        @if ($errors->any())
            <div class="alert alert-danger"><ul>@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul></div>
        @endif
        
        <form action="{{ route('staff.news.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="form-group" style="margin-bottom:15px">
                <label>Tiêu đề (*)</label>
                <input type="text" name="tieu_de" class="form-control" value="{{ old('tieu_de') }}" required>
            </div>
            
            <div class="form-group" style="margin-bottom:15px">
                <label>Mô tả ngắn</label>
                <textarea name="mo_ta_ngan" class="form-control" rows="3">{{ old('mo_ta_ngan') }}</textarea>
            </div>
            
            <div class="form-group" style="margin-bottom:15px">
                <label>Nội dung chi tiết (*)</label>
                <textarea name="noi_dung" class="form-control" rows="10" required>{{ old('noi_dung') }}</textarea>
            </div>
            
            <div class="form-group" style="margin-bottom:15px">
                <label>Ảnh đại diện</label>
                <input type="file" name="hinh_anh_chinh" class="form-control" accept="image/*">
            </div>
            
            <div class="form-group" style="margin-bottom:15px">
                <label>Trạng thái</label>
                <select name="trang_thai" class="form-control">
                    <option value="NHAP" {{ old('trang_thai') == 'NHAP' ? 'selected' : '' }}>Nháp</option>
                    <option value="XUAT_BAN" {{ old('trang_thai') == 'XUAT_BAN' ? 'selected' : '' }}>Xuất bản</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Lưu bài viết</button>
        </form>
    </div>
</section>
@endsection