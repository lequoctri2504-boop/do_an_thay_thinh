@extends('layouts.staff')

@section('title', 'Cập nhật sản phẩm: ' . $product->ten)

@section('content')
<section class="dashboard-section active">
    <div class="section-header">
        <h1>Cập nhật sản phẩm: {{ $product->ten }}</h1>
        <a href="{{ route('staff.products') }}" class="btn btn-secondary">Quay lại quản lý sản phẩm</a>
    </div>

    {{-- Hiển thị thông báo và lỗi --}}
    @if(session('success')) <div class="alert alert-success" style="padding: 10px; background: #d4edda; color: #155724; margin-bottom: 20px;">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger" style="padding: 10px; background: #f8d7da; color: #721c24; margin-bottom: 20px;">{{ session('error') }}</div> @endif
    @if ($errors->any())
        <div class="alert alert-danger"><ul>@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul></div>
    @endif
    
    <form action="{{ route('staff.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="dashboard-row" style="gap: 20px;">
            
            {{-- Cột trái: Thông tin & Biến thể --}}
            <div class="col-8">
                {{-- Card 1: Thông tin chung --}}
                <div class="dashboard-card" style="margin-bottom: 20px;">
                    <h3 class="card-header"><i class="fas fa-info-circle"></i> Thông tin sản phẩm</h3>
                    
                    {{-- INPUT ẨN CẦN THIẾT CHO VALIDATION --}}
                    <input type="hidden" name="thuong_hieu_id" value="{{ $product->thuong_hieu_id ?? '' }}">

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Tên sản phẩm (*)</label>
                        <input type="text" name="ten" class="form-control" value="{{ old('ten', $product->ten) }}" required>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Mô tả ngắn</label>
                        <textarea name="mo_ta_ngan" class="form-control" rows="3">{{ old('mo_ta_ngan', $product->mo_ta_ngan) }}</textarea>
                    </div>
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Mô tả chi tiết</label>
                        <textarea name="mo_ta_day_du" class="form-control" rows="8">{{ old('mo_ta_day_du', $product->mo_ta_day_du) }}</textarea>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Trạng thái Hiển thị</label>
                        <select name="hien_thi" class="form-control" required>
                            <option value="1" {{ old('hien_thi', $product->hien_thi) == 1 ? 'selected' : '' }}>Hiển thị</option>
                            <option value="0" {{ old('hien_thi', $product->hien_thi) == 0 ? 'selected' : '' }}>Ẩn</option>
                        </select>
                    </div>
                    
                    <hr style="margin: 20px 0;">

                    <div class="form-group row" style="margin: 0;">
                        <label class="col-sm-3 col-form-label" style="padding-left: 0; padding-right: 0;">Đánh dấu Đặc biệt</label>
                        <div class="col-sm-9" style="padding-right: 0;">
                            <div style="margin-bottom: 10px;">
                                <input type="checkbox" name="la_flash_sale" id="la_flash_sale" value="1" {{ old('la_flash_sale', $product->la_flash_sale) ? 'checked' : '' }}>
                                <label for="la_flash_sale" style="font-weight: normal;"> Flash Sale</label>
                            </div>
                            <div>
                                <input type="checkbox" name="la_noi_bat" id="la_noi_bat" value="1" {{ old('la_noi_bat', $product->la_noi_bat) ? 'checked' : '' }}>
                                <label for="la_noi_bat" style="font-weight: normal;"> Sản phẩm Nổi bật</label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Quản lý Biến thể (Variants) --}}
                <div class="dashboard-card" style="margin-bottom: 20px;">
                    <h3 class="card-header">
                        <i class="fas fa-cubes"></i> Quản lý Biến thể
                        <button type="button" id="add-variant-btn" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Thêm biến thể
                        </button>
                    </h3>
                    
                    <div class="table-responsive">
                        <table class="data-table" id="variants-table">
                            <thead>
                                <tr>
                                    <th>Mã SKU</th>
                                    <th>Màu/Dung lượng</th>
                                    <th>Giá bán (₫)</th>
                                    <th>Tồn kho</th>
                                    <th>Xóa</th>
                                </tr>
                            </thead>
                            <tbody id="variants-body">
                                @php $variantIndex = 0; @endphp
                                @foreach($product->bienTheSanPham as $variant)
                                    <tr class="variant-row" id="variant-row-{{ $variant->id }}">
                                        <td>
                                            <input type="text" name="variants[{{ $variantIndex }}][sku]" class="form-control" value="{{ old("variants.{$variantIndex}.sku", $variant->sku) }}" required>
                                            <input type="hidden" name="variants[{{ $variantIndex }}][id]" value="{{ $variant->id }}">
                                        </td>
                                        <td>
                                            <input type="text" name="variants[{{ $variantIndex }}][mau_sac]" placeholder="Màu sắc" class="form-control" value="{{ old("variants.{$variantIndex}.mau_sac", $variant->mau_sac) }}" style="width: 50%; display: inline-block;">
                                            <input type="number" name="variants[{{ $variantIndex }}][dung_luong_gb]" placeholder="GB" class="form-control" value="{{ old("variants.{$variantIndex}.dung_luong_gb", $variant->dung_luong_gb) }}" style="width: 40%; display: inline-block;">
                                            <input type="hidden" name="variants[{{ $variantIndex }}][gia_so_sanh]" value="{{ old("variants.{$variantIndex}.gia_so_sanh", $variant->gia_so_sanh) }}">
                                        </td>
                                        <td>
                                            <input type="number" name="variants[{{ $variantIndex }}][gia]" class="form-control" value="{{ old("variants.{$variantIndex}.gia", $variant->gia) }}" min="0" required>
                                        </td>
                                        <td>
                                            <input type="number" name="variants[{{ $variantIndex }}][ton_kho]" class="form-control" value="{{ old("variants.{$variantIndex}.ton_kho", $variant->ton_kho) }}" min="0" required>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                    @php $variantIndex++; @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- TEMPLATE CHO BIẾN THỂ MỚI (Dùng để JS clone) --}}
                    <template id="new-variant-template">
                        <tr class="variant-row">
                            <td><input type="text" name="new_variants[NEW_INDEX][sku]" class="form-control" required></td>
                            <td>
                                <input type="text" name="new_variants[NEW_INDEX][mau_sac]" placeholder="Màu sắc" class="form-control" style="width: 50%; display: inline-block;">
                                <input type="number" name="new_variants[NEW_INDEX][dung_luong_gb]" placeholder="GB" class="form-control" style="width: 40%; display: inline-block;">
                            </td>
                            <td><input type="number" name="new_variants[NEW_INDEX][gia]" class="form-control" min="0" required></td>
                            <td><input type="number" name="new_variants[NEW_INDEX][ton_kho]" class="form-control" min="0" required></td>
                            <td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-trash"></i></button></td>
                        </tr>
                    </template>
                </div>
            </div>

            {{-- Cột phải: Ảnh --}}
            <div class="col-4">
                {{-- Card 3: Ảnh chính --}}
                <div class="dashboard-card" style="margin-bottom: 20px;">
                    <h3 class="card-header"><i class="fas fa-image"></i> Ảnh đại diện</h3>
                    <div style="text-align: center; margin-bottom: 15px;">
                        @if($product->hinh_anh_mac_dinh)
                            <img src="{{ asset('uploads/' . $product->hinh_anh_mac_dinh) }}" width="150" style="margin: 0 auto; border: 1px solid #ddd; border-radius: 4px;">
                        @endif
                    </div>
                    <div class="form-group">
                        <label>Chọn ảnh mới</label>
                        <input type="file" name="hinh_anh_mac_dinh" class="form-control">
                    </div>
                </div>

                {{-- Card 4: Ảnh phụ --}}
                <div class="dashboard-card">
                    <h3 class="card-header"><i class="fas fa-images"></i> Ảnh phụ</h3>
                    
                    <div class="form-group">
                        <label>Thêm ảnh mới</label>
                        <input type="file" name="new_images[]" class="form-control" multiple accept="image/*">
                    </div>

                    <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 15px;" id="existing-images-container">
                        @if($product->sanPhamAnh->isNotEmpty())
                            @foreach($product->sanPhamAnh as $image)
                                <div class="image-preview-item" data-image-id="{{ $image->id }}" style="position: relative;">
                                    <img src="{{ asset('uploads/' . $image->url) }}" alt="Ảnh phụ" style="width: 80px; height: 80px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;">
                                    <button type="button" class="btn btn-danger btn-sm delete-existing-image" data-id="{{ $image->id }}" 
                                            style="position: absolute; top: -5px; right: -5px; padding: 2px 5px; font-size: 10px; line-height: 1; border-radius: 50%;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    {{-- Input hidden này chỉ được kích hoạt khi bấm nút xóa, dùng để gửi ID ảnh cần xóa --}}
                                    <input type="hidden" name="delete_images[]" id="delete-flag-{{ $image->id }}" value="" disabled>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted small">Chưa có ảnh phụ nào.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-top: 30px; text-align: center;">
            <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Cập nhật Sản phẩm</button>
        </div>
    </form>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let variantCounter = {{ $product->bienTheSanPham->count() }}; 
        const variantsBody = document.getElementById('variants-body');
        const addButton = document.getElementById('add-variant-btn'); 

        // Hàm xử lý khi bấm nút xóa
        function handleRemoveRow() {
             if (confirm('Xóa biến thể này? Nếu là biến thể đã tồn tại, nó sẽ bị xóa vĩnh viễn khỏi DB sau khi lưu.')) {
                 this.closest('tr').remove();
             }
        }
        
        // Hàm gắn sự kiện xóa (chạy cho cả hàng cũ và hàng mới)
        function setupRemoveRowListeners() {
            variantsBody.querySelectorAll('.remove-row').forEach(button => {
                button.removeEventListener('click', handleRemoveRow);
                button.addEventListener('click', handleRemoveRow);
            });
        }
        
        // 1. Gắn sự kiện xóa cho các hàng hiện có khi tải trang
        setupRemoveRowListeners();


        // 2. Quản lý Biến thể (Thêm hàng mới)
        if (addButton) {
            addButton.addEventListener('click', function(e) {
                e.preventDefault(); 
                
                const template = document.getElementById('new-variant-template');
                if (!template) {
                    console.error('Template not found!');
                    return;
                }
                
                // Lấy HTML content và thay thế placeholder
                let htmlContent = template.innerHTML;
                htmlContent = htmlContent.replace(/NEW_INDEX/g, variantCounter);
                
                // Chèn HTML mới vào <tbody>
                variantsBody.insertAdjacentHTML('beforeend', htmlContent);

                // Tăng bộ đếm
                variantCounter++;
                
                // Gắn lại sự kiện xóa cho TẤT CẢ các nút xóa
                setupRemoveRowListeners();
            });
        }
        
        // 3. Quản lý Hình ảnh (Xóa ảnh hiện có) - Giữ nguyên logic này
        document.getElementById('existing-images-container').addEventListener('click', function(e) {
            const deleteBtn = e.target.closest('.delete-existing-image');
            if (deleteBtn) {
                e.preventDefault();
                const imageId = deleteBtn.dataset.id;
                const parentItem = deleteBtn.closest('.image-preview-item');
                const deleteFlagInput = document.getElementById('delete-flag-' + imageId);
                
                if (confirm('Bạn có chắc muốn xóa ảnh này? Nó sẽ bị xóa khỏi cơ sở dữ liệu sau khi cập nhật.')) {
                    deleteFlagInput.value = imageId;
                    deleteFlagInput.disabled = false;
                    
                    if (parentItem) {
                        parentItem.style.opacity = 0.3;
                        parentItem.style.border = '1px dashed red';
                        deleteBtn.remove();
                    }
                }
            }
        });

    });
</script>
@endpush