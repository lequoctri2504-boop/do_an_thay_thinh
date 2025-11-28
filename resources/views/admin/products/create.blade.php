@extends('layouts.admin')

@section('title', 'Thêm sản phẩm mới')

@section('content')
<section class="dashboard-section active">
    <div class="section-header">
        <h1>Thêm sản phẩm mới</h1>
        <a href="{{ route('admin.products') }}" class="btn btn-secondary">Quay lại</a>
    </div>

    <div class="dashboard-card" style="padding: 20px;">
        @if ($errors->any())
            <div class="alert alert-danger" style="color: red; margin-bottom: 15px;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>- {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
                <div class="left-col">
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Tên sản phẩm (*)</label>
                        <input type="text" name="ten" class="form-control" value="{{ old('ten') }}" required style="width: 100%; padding: 8px;">
                    </div>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Mô tả ngắn</label>
                        <textarea name="mo_ta_ngan" class="form-control" rows="3" style="width: 100%; padding: 8px;">{{ old('mo_ta_ngan') }}</textarea>
                    </div>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Mô tả chi tiết</label>
                        <textarea name="mo_ta_day_du" class="form-control" rows="8" style="width: 100%; padding: 8px;">{{ old('mo_ta_day_du') }}</textarea>
                    </div>
                </div>

                <div class="right-col">
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Thương hiệu</label>
                        <select name="thuong_hieu_id" class="form-control" style="width: 100%; padding: 8px;">
                            <option value="">-- Chọn thương hiệu --</option>
                            @foreach($thuongHieu as $th)
                                <option value="{{ $th->id }}" {{ old('thuong_hieu_id') == $th->id ? 'selected' : '' }}>
                                    {{ $th->ten }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 15px; border: 1px solid #ddd; padding: 10px; border-radius: 5px; background: #f9f9f9;">
                        <strong>Thông tin bán hàng (Mặc định)</strong>
                        <hr style="margin: 5px 0;">
                        
                        <label style="display:block; margin-top: 5px;">Mã SKU (*)</label>
                        <input type="text" name="sku" class="form-control" value="{{ old('sku') }}" required placeholder="VD: IP15-BLK" style="width: 100%; padding: 5px;">

                        <label style="display:block; margin-top: 5px;">Giá bán (*)</label>
                        <input type="number" name="gia" class="form-control" value="{{ old('gia') }}" required style="width: 100%; padding: 5px;">

                        <label style="display:block; margin-top: 5px;">Giá gốc (So sánh)</label>
                        <input type="number" name="gia_so_sanh" class="form-control" value="{{ old('gia_so_sanh') }}" style="width: 100%; padding: 5px;">

                        <label style="display:block; margin-top: 5px;">Tồn kho</label>
                        <input type="number" name="ton_kho" class="form-control" value="{{ old('ton_kho', 10) }}" style="width: 100%; padding: 5px;">
                    </div>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Hình ảnh đại diện</label>
                        <input type="file" name="hinh_anh" class="form-control">
                    </div>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>
                            <input type="checkbox" name="hien_thi" checked> Hiển thị ngay
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 10px;">Lưu sản phẩm</button>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection