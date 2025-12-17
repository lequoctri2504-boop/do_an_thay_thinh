@extends('layouts.staff')

@section('title', 'Thêm Biến thể cho: ' . $product->ten)

@section('content')
<section class="dashboard-section active">
    <div class="section-header">
        <h1>Thêm Biến thể mới cho: {{ $product->ten }}</h1>
        <a href="{{ route('staff.products.edit', $product->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại chi tiết sản phẩm
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger"><ul>@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul></div>
    @endif
    
    <div class="dashboard-card" style="max-width: 800px; margin: 0 auto;">
        <form action="{{ route('staff.products.variants.store', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            {{-- THÔNG TIN CƠ BẢN CỦA BIẾN THỂ --}}
            <h3 class="card-header" style="margin-bottom: 20px;">Thông tin Biến thể</h3>
            <div class="dashboard-row" style="gap: 20px;">
                
                <div class="col-6">
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Mã SKU (*)</label>
                        <input type="text" name="sku" class="form-control" value="{{ old('sku') }}" required>
                        <small class="text-muted">Mã này phải là duy nhất.</small>
                    </div>
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Màu sắc</label>
                        <input type="text" name="mau_sac" class="form-control" value="{{ old('mau_sac') }}">
                    </div>
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Dung lượng (GB)</label>
                        <input type="number" name="dung_luong_gb" class="form-control" value="{{ old('dung_luong_gb') }}">
                    </div>
                </div>

                <div class="col-6">
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Giá bán (*)</label>
                        <input type="number" name="gia" class="form-control" value="{{ old('gia') }}" min="0" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Giá so sánh (Giá gốc)</label>
                        <input type="number" name="gia_so_sanh" class="form-control" value="{{ old('gia_so_sanh') }}" min="0">
                    </div>
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Tồn kho (*)</label>
                        <input type="number" name="ton_kho" class="form-control" value="{{ old('ton_kho', 0) }}" min="0" required>
                    </div>
                </div>
            </div>

            {{-- QUẢN LÝ ẢNH PHỤ CHO BIẾN THỂ (LƯU VÀO SAN_PHAM_ANH) --}}
            <h3 class="card-header" style="margin-top: 30px; margin-bottom: 20px;">Ảnh phụ cho Biến thể này</h3>
            <div class="form-group" style="margin-bottom: 20px;">
                <label>Chọn nhiều ảnh phụ</label>
                <input type="file" name="new_images[]" class="form-control" multiple accept="image/*">
                <small class="text-muted">Các ảnh này sẽ được thêm vào danh sách ảnh chung của sản phẩm.</small>
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Lưu Biến thể mới
                </button>
            </div>
        </form>
    </div>
</section>
@endsection