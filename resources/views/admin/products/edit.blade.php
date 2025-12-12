@extends('layouts.admin')

@section('title', 'Cập nhật sản phẩm')

@section('content')
<section class="dashboard-section active">
    <div class="section-header">
        <h1>Cập nhật: {{ $product->ten }}</h1>
        <a href="{{ route('admin.products') }}" class="btn btn-secondary">Quay lại</a>
    </div>

    <div class="dashboard-card" style="padding: 20px;">
        @if ($errors->any())
            <div class="alert alert-danger" style="color: red;">
                <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
            </div>
        @endif

        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
                <div class="left-col">
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Tên sản phẩm (*)</label>
                        <input type="text" name="ten" class="form-control" value="{{ old('ten', $product->ten) }}" required style="width: 100%; padding: 8px;">
                    </div>
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Mô tả ngắn</label>
                        <textarea name="mo_ta_ngan" class="form-control" rows="3" style="width: 100%; padding: 8px;">{{ old('mo_ta_ngan', $product->mo_ta_ngan) }}</textarea>
                    </div>
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Mô tả chi tiết</label>
                        <textarea name="mo_ta_day_du" class="form-control" rows="8" style="width: 100%; padding: 8px;">{{ old('mo_ta_day_du', $product->mo_ta_day_du) }}</textarea>
                    </div>
                </div>

                <div class="right-col">
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Thương hiệu</label>
                        <select name="thuong_hieu_id" class="form-control" style="width: 100%; padding: 8px;">
                            <option value="">-- Chọn thương hiệu --</option>
                            @foreach($thuongHieu as $th)
                                <option value="{{ $th->id }}" {{ (old('thuong_hieu_id', $product->thuong_hieu_id) == $th->id) ? 'selected' : '' }}>
                                    {{ $th->ten }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Danh mục (Chọn nhiều)</label>
                        <select name="danh_muc_ids[]" class="form-control" multiple required style="width: 100%; padding: 8px; height: 150px;">
                            @php
                                $currentCategoryIds = $product->danhMuc->pluck('id')->toArray();
                            @endphp
                            @foreach($danhMuc as $dm)
                                @php
                                    $isSelected = in_array($dm->id, old('danh_muc_ids', $currentCategoryIds));
                                @endphp
                                <option value="{{ $dm->id }}" {{ $isSelected ? 'selected' : '' }}>
                                    {{ $dm->ten }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Giữ Ctrl hoặc Command để chọn nhiều.</small>
                    </div>

                    <div class="form-group" style="margin-bottom: 15px; border: 1px solid #ddd; padding: 10px; border-radius: 5px; background: #f9f9f9;">
                        <strong>Biến thể chính</strong>
                        <hr style="margin: 5px 0;">
                        <label>Mã SKU</label>
                        <input type="text" name="sku" class="form-control" value="{{ old('sku', $firstVariant->sku ?? '') }}" required style="width: 100%; padding: 5px;">
                        
                        <label style="margin-top:5px; display:block">Giá bán</label>
                        <input type="number" name="gia" class="form-control" value="{{ old('gia', isset($firstVariant) ? (int)$firstVariant->gia : 0) }}" required style="width: 100%; padding: 5px;">
                        
                        <label style="margin-top:5px; display:block">Giá gốc</label>
                        <input type="number" name="gia_so_sanh" class="form-control" value="{{ old('gia_so_sanh', isset($firstVariant) ? (int)$firstVariant->gia_so_sanh : '') }}" style="width: 100%; padding: 5px;">
                        
                        <label style="margin-top:5px; display:block">Tồn kho</label>
                        <input type="number" name="ton_kho" class="form-control" value="{{ old('ton_kho', $firstVariant->ton_kho ?? 0) }}" style="width: 100%; padding: 5px;">
                    </div>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Hình ảnh hiện tại</label><br>
                        @if($product->hinh_anh_mac_dinh)
                            <img src="{{ asset('uploads/' . $product->hinh_anh_mac_dinh) }}" width="100" style="margin-bottom: 5px;">
                        @endif
                        <input type="file" name="hinh_anh" class="form-control">
                    </div>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label><input type="checkbox" name="hien_thi" {{ $product->hien_thi ? 'checked' : '' }}> Hiển thị</label>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 10px;">Cập nhật</button>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection