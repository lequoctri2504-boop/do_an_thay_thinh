@extends('layouts.admin')
@section('title', 'Sửa thương hiệu')
@section('content')
<section class="dashboard-section active">
    <div class="section-header">
        <h1>Sửa thương hiệu: {{ $brand->ten }}</h1>
        <a href="{{ route('admin.brands') }}" class="btn btn-secondary">Quay lại</a>
    </div>
    <div class="dashboard-card" style="padding: 20px;">
        <form action="{{ route('admin.brands.update', $brand->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="form-group" style="margin-bottom:15px">
                <label>Tên thương hiệu (*)</label>
                <input type="text" name="ten" class="form-control" value="{{ $brand->ten }}" required>
            </div>
            <button type="submit" class="btn btn-primary">Cập nhật</button>
        </form>
    </div>
</section>
@endsection