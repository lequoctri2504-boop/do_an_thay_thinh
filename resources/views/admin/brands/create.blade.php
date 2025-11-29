@extends('layouts.admin')
@section('title', 'Thêm thương hiệu')
@section('content')
<section class="dashboard-section active">
    <div class="section-header">
        <h1>Thêm thương hiệu mới</h1>
        <a href="{{ route('admin.brands') }}" class="btn btn-secondary">Quay lại</a>
    </div>
    <div class="dashboard-card" style="padding: 20px;">
        <form action="{{ route('admin.brands.store') }}" method="POST">
            @csrf
            <div class="form-group" style="margin-bottom:15px">
                <label>Tên thương hiệu (*)</label>
                <input type="text" name="ten" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Lưu thương hiệu</button>
        </form>
    </div>
</section>
@endsection