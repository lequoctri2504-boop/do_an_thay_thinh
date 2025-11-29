@extends('layouts.admin')
@section('title', 'Thêm danh mục')
@section('content')
<section class="dashboard-section active">
    <div class="section-header">
        <h1>Thêm danh mục mới</h1>
        <a href="{{ route('admin.categories') }}" class="btn btn-secondary">Quay lại</a>
    </div>
    <div class="dashboard-card" style="padding: 20px;">
        <form action="{{ route('admin.categories.store') }}" method="POST">
            @csrf
            <div class="form-group" style="margin-bottom:15px">
                <label>Tên danh mục (*)</label>
                <input type="text" name="ten" class="form-control" required>
            </div>
            <div class="form-group" style="margin-bottom:15px">
                <label>Danh mục cha (Tùy chọn)</label>
                <select name="cha_id" class="form-control">
                    <option value="">-- Là danh mục gốc --</option>
                    @foreach($parentCategories as $pCat)
                        <option value="{{ $pCat->id }}">{{ $pCat->ten }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Lưu danh mục</button>
        </form>
    </div>
</section>
@endsection