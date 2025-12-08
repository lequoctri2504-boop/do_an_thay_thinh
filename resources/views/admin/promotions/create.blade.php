@extends('layouts.admin')
@section('title', 'Tạo Khuyến mãi mới')
@section('content')
<section class="dashboard-section active">
    <div class="section-header">
        <h1>Tạo Khuyến mãi mới</h1>
        <a href="{{ route('admin.promotions') }}" class="btn btn-secondary">Quay lại</a>
    </div>
    
    <div class="dashboard-card" style="padding: 30px; max-width: 600px; margin: 0 auto;">
        @if ($errors->any())
            <div class="alert alert-danger"><ul>@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul></div>
        @endif
        
        <form action="{{ route('admin.promotions.store') }}" method="POST">
            @csrf
            <h4 style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">Thông tin chương trình</h4>
            
            <div class="form-group" style="margin-bottom:15px">
                <label>Tên chương trình (*)</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>
            
            <div class="form-group" style="margin-bottom:15px">
                <label>Mã giảm giá (Code) (*)</label>
                <input type="text" name="code" class="form-control" value="{{ old('code') }}" required>
            </div>
            
            <div class="form-group" style="margin-bottom:15px">
                <label>Giá trị giảm (VD: 10% hoặc 50000) (*)</label>
                <input type="text" name="discount_value" class="form-control" value="{{ old('discount_value') }}" required>
            </div>
            
            <div class="form-row" style="display: flex; gap: 15px;">
                <div class="form-group" style="margin-bottom:15px; flex: 1;">
                    <label>Ngày bắt đầu (*)</label>
                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" required>
                </div>
                <div class="form-group" style="margin-bottom:15px; flex: 1;">
                    <label>Ngày kết thúc (*)</label>
                    <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}" required>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block" style="margin-top: 20px;">Lưu Khuyến mãi</button>
        </form>
    </div>
</section>
@endsection