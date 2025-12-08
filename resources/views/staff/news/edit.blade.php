@extends('layouts.staff')
@section('title', 'Sửa bài viết: ' . $news->tieu_de)
@section('content')
<section class="dashboard-section active">
    <div class="section-header">
        <h1>Sửa bài viết: {{ $news->tieu_de }}</h1>
        <a href="{{ route('staff.news') }}" class="btn btn-secondary">Quay lại</a>
    </div>
    
    <div class="dashboard-card">
        @if ($errors->any())
            <div class="alert alert-danger"><ul>@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul></div>
        @endif
        
        <form action="{{ route('staff.news.update', $news->id) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            
            <div class="form-group" style="margin-bottom:15px">
                <label>Tiêu đề (*)</label>
                <input type="text" name="tieu_de" class="form-control" value="{{ old('tieu_de', $news->tieu_de) }}" required>
            </div>
            
            <div class="form-group" style="margin-bottom:15px">
                <label>Mô tả ngắn</label>
                <textarea name="mo_ta_ngan" class="form-control" rows="3">{{ old('mo_ta_ngan', $news->mo_ta_ngan) }}</textarea>
            </div>
            
            <div class="form-group" style="margin-bottom:15px">
                <label>Nội dung chi tiết (*)</label>
                <textarea name="noi_dung" class="form-control" rows="10" required>{{ old('noi_dung', $news->noi_dung) }}</textarea>
            </div>
            
            <div class="form-group" style="margin-bottom:15px">
                <label>Ảnh đại diện hiện tại</label>
                @if($news->hinh_anh_chinh)
                    <img src="{{ asset('uploads/' . $news->hinh_anh_chinh) }}" width="150px" style="display:block; margin-bottom: 10px;">
                @endif
                <label>Chọn ảnh mới (Nếu muốn thay đổi)</label>
                <input type="file" name="hinh_anh_chinh_moi" class="form-control" accept="image/*">
            </div>
            
            <div class="form-group" style="margin-bottom:15px">
                <label>Trạng thái</label>
                <select name="trang_thai" class="form-control">
                    <option value="NHAP" {{ old('trang_thai', $news->trang_thai) == 'NHAP' ? 'selected' : '' }}>Nháp</option>
                    <option value="XUAT_BAN" {{ old('trang_thai', $news->trang_thai) == 'XUAT_BAN' ? 'selected' : '' }}>Xuất bản</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Cập nhật bài viết</button>
        </form>
    </div>
</section>
@endsection