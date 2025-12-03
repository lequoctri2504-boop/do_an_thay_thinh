@extends('layouts.admin')
@section('title', 'Tạo Khuyến mãi mới')
@section('content')
<section class="dashboard-section active">
    <div class="section-header">
        <h1>Tạo Khuyến mãi mới</h1>
        <a href="{{ route('admin.promotions') }}" class="btn btn-secondary">Quay lại</a>
    </div>
    <div class="dashboard-card" style="padding: 20px;">
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> Lưu ý: Hiện tại chưa có bảng `khuyen_mai` trong cơ sở dữ liệu. Đây là form mẫu.
        </div>
        <form>
            @csrf
            <div class="form-group" style="margin-bottom:15px">
                <label>Tên chương trình (*)</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group" style="margin-bottom:15px">
                <label>Mã giảm giá (Code) (*)</label>
                <input type="text" name="code" class="form-control" required>
            </div>
            <div class="form-group" style="margin-bottom:15px">
                <label>Giá trị giảm</label>
                <input type="text" name="discount_value" class="form-control" placeholder="10% hoặc 50000">
            </div>
            <button type="submit" class="btn btn-primary">Lưu Khuyến mãi</button>
        </form>
    </div>
</section>
@endsection