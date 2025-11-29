@extends('layouts.admin')
@section('title', 'Sửa danh mục')
@section('content')
<section class="dashboard-section active">
    <div class="section-header">
        <h1>Sửa danh mục: {{ $category->ten }}</h1>
        <a href="{{ route('admin.categories') }}" class="btn btn-secondary">Quay lại</a>
    </div>
    <div class="dashboard-card" style="padding: 20px;">
        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="form-group" style="margin-bottom:15px">
                <label>Tên danh mục (*)</label>
                <input type="text" name="ten" class="form-control" value="{{ $category->ten }}" required>
            </div>
            <div class="form-group" style="margin-bottom:15px">
                <label>Danh mục cha</label>
                <select name="cha_id" class="form-control">
                    <option value="">-- Là danh mục gốc --</option>
                    @foreach($parentCategories as $pCat)
                        <option value="{{ $pCat->id }}" {{ $category->cha_id == $pCat->id ? 'selected' : '' }}>
                            {{ $pCat->ten }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Cập nhật</button>
        </form>
    </div>
</section>
@endsection