@extends('layouts.app')

@section('title', $product->ten)

@section('content')
    <div class="breadcrumb">
        <div class="container">
            <a href="{{ route('home') }}">Trang chủ</a>
            <i class="fas fa-chevron-right"></i>
            <a href="{{ route('products.index') }}">Sản phẩm</a>
            <i class="fas fa-chevron-right"></i>
            @if($product->danhMuc->isNotEmpty())
                <a href="{{ route('products.category', $product->danhMuc->first()->slug) }}">
                    {{ $product->danhMuc->first()->ten }}
                </a>
                <i class="fas fa-chevron-right"></i>
            @endif
            <span>{{ $product->ten }}</span>
        </div>
    </div>

    <section class="product-detail">
        <div class="container">
            {{-- Bố cục 2 cột chính --}}
            <div class="detail-layout">
                
                <div class="product-gallery">
                    <div class="main-image">
                        @php
                            // Lấy ảnh chính từ cột hinh_anh_mac_dinh
                            $mainImage = $product->hinh_anh_mac_dinh ? asset('uploads/' . $product->hinh_anh_mac_dinh) : 'https://via.placeholder.com/600';
                        @endphp
                        <img src="{{ $mainImage }}" 
                             alt="{{ $product->ten }}" 
                             id="mainProductImage">
                        <button class="zoom-btn"><i class="fas fa-search-plus"></i></button>
                    </div>
                    
                    <div class="thumbnail-list">
                        {{-- Thumbnail ảnh chính --}}
                        <img src="{{ $mainImage }}" 
                             class="thumbnail active" 
                             data-image="{{ $mainImage }}">
                        
                        {{-- Thumnail các ảnh phụ --}}
                        @foreach($product->sanPhamAnh as $anh)
                            @php
                                $anhUrl = asset('uploads/' . $anh->url);
                            @endphp
                            <img src="{{ $anhUrl }}" 
                                 class="thumbnail" 
                                 data-image="{{ $anhUrl }}">
                        @endforeach
                    </div>
                </div>

                <div class="product-detail-info">
                    @php
                        $firstVariant = $product->bienTheSanPham->first();
                        $minPrice = $product->bienTheSanPham->min('gia');
                        $maxPrice = $product->bienTheSanPham->max('gia');
                        $discount = 0;
                        $isInWishlist = Auth::check() ? \Illuminate\Support\Facades\DB::table('yeu_thich')->where('nguoi_dung_id', Auth::id())->where('san_pham_id', $product->id)->exists() : false;
                        
                        if($firstVariant && $firstVariant->gia_so_sanh && $firstVariant->gia_so_sanh > $firstVariant->gia) {
                            $discount = round((($firstVariant->gia_so_sanh - $firstVariant->gia) / $firstVariant->gia_so_sanh) * 100);
                        }

                        // Lọc các giá trị duy nhất cho biến thể
                        $colors = $product->bienTheSanPham->pluck('mau_sac')->unique()->filter();
                        $storages = $product->bienTheSanPham->pluck('dung_luong_gb')->unique()->filter()->sort();
                    @endphp
                    
                    <h1>{{ $product->ten }}</h1>
                    
                    <div class="product-meta">
                        <div class="rating">
                            <div class="stars">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($avgRating)) <i class="fas fa-star"></i>
                                    @elseif($i - $avgRating < 1) <i class="fas fa-star-half-alt"></i>
                                    @else <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                            <span>{{ number_format($avgRating, 1) }} ({{ $ratingCount }} đánh giá)</span>
                        </div>
                        <div class="stock in-stock">
                            <i class="fas fa-box"></i> <span id="currentStockStatus">{{ $product->hien_thi ? 'Còn hàng' : 'Hết hàng' }}</span>
                        </div>
                    </div>

                    <div class="price-section">
                        <div class="main-price">
                            <span class="price-new" id="productPrice">{{ number_format($minPrice, 0, ',', '.') }}₫</span>
                            @if($discount > 0)
                                <span class="price-old" id="productComparePrice">{{ number_format($firstVariant->gia_so_sanh, 0, ',', '.') }}₫</span>
                                <span class="discount-badge" id="productDiscountBadge">-{{ $discount }}%</span>
                            @endif
                        </div>
                    </div>

                    @if($colors->isNotEmpty())
                        <div class="option-group">
                            <label>Màu sắc: <span class="selected-option"></span></label>
                            <div class="color-options" id="colorOptions">
                                @foreach($colors as $color)
                                    <button class="color-btn" data-color="{{ $color }}"
                                            style="background: {{ strtolower($color) === 'titan đen' ? '#2c2c2c' : (strtolower($color) === 'titan trắng' ? '#f5f5f5' : (strtolower($color) === 'xanh' ? '#5b7c99' : (strtolower($color) === 'vàng' ? '#FFD700' : '')))}}"></button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($storages->isNotEmpty())
                        <div class="option-group">
                            <label>Dung lượng:</label>
                            <div class="storage-options" id="storageOptions">
                                @foreach($storages as $storage)
                                    <button class="storage-btn" data-storage="{{ $storage }}">{{ $storage }}GB</button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <input type="hidden" id="selectedVariantId" value="{{ $firstVariant->id ?? '' }}">
                    <input type="hidden" id="selectedVariantPrice" value="{{ $firstVariant->gia ?? 0 }}">
                    <input type="hidden" id="selectedVariantStock" value="{{ $firstVariant->ton_kho ?? 0 }}">
                    <div id="stockStatus" style="margin-bottom: 15px;"></div>

                    <div class="promotions-box">
                        <h3><i class="fas fa-gift"></i> Khuyến mãi đặc biệt</h3>
                        <ul>
                            <li><i class="fas fa-check"></i> Giảm thêm 500.000₫ khi thu cũ đổi mới</li>
                            <li><i class="fas fa-check"></i> Trả góp 0% qua thẻ tín dụng (3-6 tháng)</li>
                            <li><i class="fas fa-check"></i> Tặng ốp lưng + cáp sạc trị giá 500.000₫</li>
                            <li><i class="fas fa-check"></i> Bảo hành 12 tháng chính hãng</li>
                        </ul>
                    </div>

                    <div class="purchase-actions">
                        <div class="quantity-selector">
                            <button class="qty-btn" id="decreaseQty"><i class="fas fa-minus"></i></button>
                            <input type="number" value="1" min="1" id="quantity">
                            <button class="qty-btn" id="increaseQty"><i class="fas fa-plus"></i></button>
                        </div>
                        <button class="btn btn-cart btn-large" id="addToCartBtn"><i class="fas fa-shopping-cart"></i> Thêm vào giỏ hàng</button>
                        <button class="btn btn-primary btn-large" id="buyNowBtn"><i class="fas fa-bolt"></i> Mua ngay</button>
                        <button class="btn-wishlist {{ $isInWishlist ? 'active' : '' }}" 
                                data-product-id="{{ $product->id }}" 
                                data-is-added="{{ $isInWishlist ? 'true' : 'false' }}">
                            <i class="{{ $isInWishlist ? 'fas' : 'far' }} fa-heart"></i>
                        </button>
                    </div>

                    <div class="policies">
                        <div class="policy-item">
                            <i class="fas fa-shield-alt"></i>
                            <div>
                                <strong>Bảo hành chính hãng</strong>
                                <p>12 tháng</p>
                            </div>
                        </div>
                        <div class="policy-item">
                            <i class="fas fa-undo"></i>
                            <div>
                                <strong>Đổi trả trong 7 ngày</strong>
                                <p>Nếu sản phẩm lỗi</p>
                            </div>
                        </div>
                        <div class="policy-item">
                            <i class="fas fa-truck"></i>
                            <div>
                                <strong>Giao hàng miễn phí</strong>
                                <p>Đơn hàng trên 500K</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="product-tabs">
                <div class="tab-nav">
                    <button class="tab-btn active" data-tab="description-content">Mô tả sản phẩm</button>
                    <button class="tab-btn" data-tab="specifications-content">Thông số kỹ thuật</button>
                    <button class="tab-btn" data-tab="reviews-content">Đánh giá ({{ $ratingCount }})</button>
                </div>

                <div class="tab-content active" id="description-content">
                    <div class="description-content">
                        {!! $product->mo_ta_day_du !!}
                    </div>
                </div>

                <div class="tab-content" id="specifications-content">
                    <div class="specifications">
                        <table class="specs-table">
                            <tr><td>Thương hiệu</td><td>{{ $product->thuongHieu->ten }}</td></tr>
                            @if($colors->isNotEmpty())<tr><td>Màu sắc</td><td>{{ $colors->implode(', ') }}</td></tr>@endif
                            @if($storages->isNotEmpty())<tr><td>Dung lượng</td><td>{{ $storages->implode('GB, ') }}GB</td></tr>@endif
                            <tr><td>Tình trạng</td><td>{{ $product->hien_thi ? 'Còn hàng' : 'Hết hàng' }}</td></tr>
                        </table>
                    </div>
                </div>

                <div class="tab-content" id="reviews-content">
                    @if($product->danhGia->isEmpty())
                        <p class="text-muted">Chưa có đánh giá nào cho sản phẩm này.</p>
                    @else
                        <div class="review-list">
                            @foreach($product->danhGia->where('duyet', 1) as $review)
                                <div class="review-item">
                                    <div class="review-header">
                                        <div class="reviewer-info">
                                            <div class="avatar">{{ substr($review->nguoiDung->ho_ten, 0, 1) }}</div>
                                            <div>
                                                <strong>{{ $review->nguoiDung->ho_ten }}</strong>
                                                <div class="review-rating">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $review->so_sao) <i class="fas fa-star"></i> @else <i class="far fa-star"></i> @endif
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                        <small class="review-date">{{ \Carbon\Carbon::parse($review->created_at)->format('d/m/Y') }}</small>
                                    </div>
                                    <div class="review-content"><p>{{ $review->noi_dung }}</p></div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            @if($relatedProducts->isNotEmpty())
                <div class="related-products">
                    <div class="section-header"><h2><i class="fas fa-box-open"></i> SẢN PHẨM TƯƠNG TỰ</h2></div>
                    <div class="product-grid">
                        @foreach($relatedProducts as $relatedProduct)
                            @php
                                $relatedVariant = $relatedProduct->bienTheSanPham->first();
                                $relatedMinPrice = $relatedVariant ? $relatedVariant->gia : 0;
                                $relatedImagePath = $relatedProduct->hinh_anh_mac_dinh ? asset('uploads/' . $relatedProduct->hinh_anh_mac_dinh) : 'https://via.placeholder.com/300';
                                $relatedAvgRating = $relatedProduct->danhGia()->avg('so_sao') ?? 0;
                                $relatedReviewCount = $relatedProduct->danhGia()->count();
                            @endphp
                            <div class="product-card">
                                <a href="{{ route('products.show', $relatedProduct->slug) }}" class="product-image">
                                    <img src="{{ $relatedImagePath }}" alt="{{ $relatedProduct->ten }}">
                                </a>
                                <div class="product-info">
                                    <h3><a href="{{ route('products.show', $relatedProduct->slug) }}">{{ $relatedProduct->ten }}</a></h3>
                                    <div class="product-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($relatedAvgRating)) <i class="fas fa-star"></i> @else <i class="far fa-star"></i> @endif
                                        @endfor
                                        <span>({{ $relatedReviewCount }})</span>
                                    </div>
                                    <div class="product-price">
                                        <span class="price-new">{{ number_format($relatedMinPrice, 0, ',', '.') }}₫</span>
                                    </div>
                                    <button class="btn btn-cart add-to-cart-btn" data-variant-id="{{ $relatedVariant->id ?? '' }}">
                                        <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const variants = @json($product->bienTheSanPham);
    
    let selectedColor = null;
    let selectedStorage = null;
    let selectedVariant = variants.length > 0 ? variants[0] : null; 

    // Helper function to format currency
    const formatCurrency = (amount) => new Intl.NumberFormat('vi-VN').format(amount) + '₫';

    // 1. GALLERY THUMBNAILS
    document.querySelectorAll('.thumbnail').forEach(thumb => {
        thumb.addEventListener('click', function() {
            document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('mainProductImage').src = this.dataset.image;
        });
    });

    // 2. VARIANT SELECTION LOGIC
    document.querySelectorAll('.color-btn').forEach(btn => {
         btn.addEventListener('click', function() {
            document.querySelectorAll('.color-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            selectedColor = this.dataset.color;
            updateVariant();
         });
    });
    document.querySelectorAll('.storage-btn').forEach(btn => {
         btn.addEventListener('click', function() {
            document.querySelectorAll('.storage-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            selectedStorage = this.dataset.storage;
            updateVariant();
         });
    });

    // Hàm cập nhật biến thể, giá, và tồn kho
    function updateVariant() {
        const potentialVariant = variants.find(v => {
            const colorMatch = !selectedColor || v.mau_sac === selectedColor;
            const storageMatch = !selectedStorage || v.dung_luong_gb == selectedStorage;
            return colorMatch && storageMatch && v.dang_ban === 1;
        });

        const stockStatus = document.getElementById('stockStatus');
        const addToCartBtn = document.getElementById('addToCartBtn');
        const buyNowBtn = document.getElementById('buyNowBtn');
        const priceElement = document.getElementById('productPrice');
        const comparePriceElement = document.getElementById('productComparePrice');
        const selectedColorText = document.querySelector('.selected-option');
        
        if (potentialVariant) {
            selectedVariant = potentialVariant;
            document.getElementById('selectedVariantId').value = selectedVariant.id;
            document.getElementById('selectedVariantPrice').value = selectedVariant.gia;
            document.getElementById('selectedVariantStock').value = selectedVariant.ton_kho;
            
            // Update selected option text
            if (selectedColorText) {
                selectedColorText.textContent = selectedColor || '';
            }
            
            // Update price
            priceElement.textContent = formatCurrency(selectedVariant.gia);
            
            // Update compare price
            if (comparePriceElement) {
                comparePriceElement.textContent = selectedVariant.gia_so_sanh && selectedVariant.gia_so_sanh > selectedVariant.gia
                    ? formatCurrency(selectedVariant.gia_so_sanh)
                    : '';
            }
            
            // Update stock status and button state
            if (selectedVariant.ton_kho > 0) {
                stockStatus.innerHTML = '<span class="badge bg-success">Còn ' + selectedVariant.ton_kho + ' sản phẩm</span>';
                addToCartBtn.disabled = false;
                buyNowBtn.disabled = false;
                document.getElementById('quantity').max = selectedVariant.ton_kho;
            } else {
                stockStatus.innerHTML = '<span class="badge bg-danger">Hết hàng</span>';
                addToCartBtn.disabled = true;
                buyNowBtn.disabled = true;
                document.getElementById('quantity').value = 1;
                document.getElementById('quantity').max = 1;
            }
        } else {
            selectedVariant = null;
            document.getElementById('selectedVariantId').value = '';
            document.getElementById('selectedVariantStock').value = '';
            priceElement.textContent = '---';
            if (comparePriceElement) comparePriceElement.textContent = '';
            if (selectedColorText) selectedColorText.textContent = '';
            stockStatus.innerHTML = '<span class="badge bg-warning text-dark">Vui lòng chọn biến thể</span>';
            addToCartBtn.disabled = true;
            buyNowBtn.disabled = true;
        }
    }
    
    // 3. QUANTITY CONTROLS
     document.getElementById('decreaseQty')?.addEventListener('click', function() {
        const qtyInput = document.getElementById('quantity');
        let currentQty = parseInt(qtyInput.value);
        if (currentQty > 1) {
            qtyInput.value = currentQty - 1;
        }
    });

    document.getElementById('increaseQty')?.addEventListener('click', function() {
        const qtyInput = document.getElementById('quantity');
        const maxStock = parseInt(document.getElementById('selectedVariantStock').value) || 999;
        let currentQty = parseInt(qtyInput.value);
        if (currentQty < maxStock) {
            qtyInput.value = currentQty + 1;
        }
    });


    // 4. ADD TO CART AJAX
    document.getElementById('addToCartBtn')?.addEventListener('click', function() {
        const variantId = document.getElementById('selectedVariantId').value;
        const quantity = parseInt(document.getElementById('quantity').value);
        
        if (!variantId || !quantity || quantity <= 0) {
             window.PhoneShop.showToast('Vui lòng chọn biến thể và số lượng!', 'error');
             return;
        }
        
        performAddToCart(variantId, quantity);
    });

    // 5. BUY NOW AJAX (Add to cart then redirect)
    document.getElementById('buyNowBtn')?.addEventListener('click', function() {
        const variantId = document.getElementById('selectedVariantId').value;
        const quantity = parseInt(document.getElementById('quantity').value);
        
        if (!variantId || !quantity || quantity <= 0) {
             window.PhoneShop.showToast('Vui lòng chọn biến thể và số lượng!', 'error');
             return;
        }
        
        this.disabled = true;
        performAddToCart(variantId, quantity, true); // true = redirect to checkout
    });
    
    // 6. COMMON ADD TO CART FUNCTION
    function performAddToCart(variantId, quantity, redirectToCheckout = false) {
        const btn = redirectToCheckout ? document.getElementById('buyNowBtn') : document.getElementById('addToCartBtn');
        btn.disabled = true;

        fetch('{{ route('cart.add') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                bien_the_id: variantId,
                so_luong: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false; 

            if (data.success) {
                if (window.updateCartBadge) updateCartBadge(data.cart_count);
                
                if (redirectToCheckout) {
                    window.location.href = '{{ route('orders.checkout') }}'; 
                } else {
                    window.PhoneShop.showToast('Đã thêm vào giỏ hàng!', 'success');
                }
            } else {
                window.PhoneShop.showToast(data.message || 'Lỗi khi thêm vào giỏ!', 'error');
            }
        })
        .catch(error => {
            btn.disabled = false;
            window.PhoneShop.showToast('Lỗi kết nối hoặc xử lý dữ liệu!', 'error');
        });
    }
    
    // 7. INITIAL LOAD: Auto select first variant (if possible)
    const initSelection = () => {
        if (variants.length > 0) {
            let isAutoSelected = false;
            if (colorOptions.length === 1 && storageOptions.length === 1) {
                colorOptions[0].click();
                storageOptions[0].click();
                isAutoSelected = true;
            } else if (colorOptions.length === 1 && storageOptions.length === 0) {
                colorOptions[0].click();
                isAutoSelected = true;
            } else if (storageOptions.length === 1 && colorOptions.length === 0) {
                storageOptions[0].click();
                isAutoSelected = true;
            } else if (variants.length === 1) {
                 // Trường hợp chỉ có 1 biến thể không có màu/dung lượng
                 updateVariant();
                 isAutoSelected = true;
            }

            // Nếu không tự động chọn được, nhưng có nhiều biến thể
            if (!isAutoSelected && (colorOptions.length > 1 || storageOptions.length > 1)) {
                document.getElementById('stockStatus').innerHTML = '<span class="badge bg-warning text-dark">Vui lòng chọn biến thể</span>';
            } else if (!isAutoSelected && variants.length === 0) {
                document.getElementById('stockStatus').innerHTML = '<span class="badge bg-danger">Sản phẩm hiện không có sẵn!</span>';
            }
        } else {
            // Trường hợp không có biến thể nào
            document.getElementById('stockStatus').innerHTML = '<span class="badge bg-danger">Sản phẩm hiện không có sẵn!</span>';
        }
    };
    
    // 8. INITIALIZE TABS
    document.querySelectorAll('.tab-nav .tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.dataset.tab;
            
            // Remove active from all buttons and content
            document.querySelectorAll('.tab-nav .tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.product-tabs .tab-content').forEach(c => c.classList.remove('active'));

            // Add active to selected button and content
            this.classList.add('active');
            document.getElementById(targetId)?.classList.add('active');
        });
    });
    
    initSelection();
});
</script>
@endpush