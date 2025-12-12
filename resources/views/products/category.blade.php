@extends('layouts.app')

@section('title', 'Danh mục ' . $category->ten)

@section('content')
<div class="breadcrumb">
    <div class="container">
        <a href="{{ route('home') }}">Trang chủ</a>
        <i class="fas fa-chevron-right"></i>
        <a href="{{ route('products.index') }}">Sản phẩm</a>
        <i class="fas fa-chevron-right"></i>
        <span>{{ $category->ten }}</span>
    </div>
</div>

<section class="products-page">
    <div class="container">
        <h1>{{ $category->ten }}</h1>
        
        <div class="products-layout">
            
            {{-- Filter Sidebar (Left Column) --}}
            <aside class="filter-sidebar">
                <div class="filter-box">
                    <h3><i class="fas fa-filter"></i> Lọc sản phẩm</h3>
                    
                    {{-- FIX: BAO BỌC BỘ LỌC BẰNG FORM GỬI ĐẾN ROUTE CATEGORY HIỆN TẠI --}}
                    <form action="{{ route('products.category', $category->slug) }}" method="GET" id="mainFilterForm">
                        
                        {{-- Hidden Input để giữ lại trạng thái Sắp xếp khi lọc --}}
                        <input type="hidden" name="sort" value="{{ request('sort', 'newest') }}">
                        
                        {{-- Lọc theo Danh mục (Giữ nguyên logic redirect) --}}
                        <div class="filter-group">
                            <h4>Danh mục</h4>
                            @foreach($categories as $cat)
                                <label>
                                    <input type="radio" name="category_redirect" value="{{ $cat->slug }}" 
                                           onclick="window.location.href='{{ route('products.category', $cat->slug) }}'"
                                           {{ $cat->id == $category->id ? 'checked' : '' }}> 
                                    {{ $cat->ten }}
                                </label>
                            @endforeach
                        </div>

                        {{-- Lọc theo Thương hiệu (Sử dụng checkbox) --}}
                        <div class="filter-group">
                            <h4>Thương hiệu</h4>
                            @php $selectedBrands = (array) request('brand', []); @endphp
                            @foreach($brands as $brand)
                                <label>
                                    <input type="checkbox" name="brand[]" value="{{ $brand->slug }}" 
                                           {{ in_array($brand->slug, $selectedBrands) ? 'checked' : '' }}> 
                                    {{ $brand->ten }}
                                </label>
                            @endforeach
                        </div>
                        
                        {{-- Lọc theo Giá (Sử dụng radio) --}}
                        <div class="filter-group">
                            <h4>Khoảng giá</h4>
                            <label><input type="radio" name="price" value="0-5000000" {{ request('price') == '0-5000000' ? 'checked' : '' }}> Dưới 5 triệu</label>
                            <label><input type="radio" name="price" value="5000000-10000000" {{ request('price') == '5000000-10000000' ? 'checked' : '' }}> 5 - 10 triệu</label>
                            <label><input type="radio" name="price" value="10000000-20000000" {{ request('price') == '10000000-20000000' ? 'checked' : '' }}> 10 - 20 triệu</label>
                            <label><input type="radio" name="price" value="20000000-100000000" {{ request('price') == '20000000-100000000' ? 'checked' : '' }}> Trên 20 triệu</label>
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm mt-2" style="width:100%">
                            <i class="fas fa-filter"></i> Áp dụng bộ lọc
                        </button>
                        
                        @if(!empty($selectedBrands) || request('price'))
                            <a href="{{ route('products.category', $category->slug) }}" class="btn btn-secondary btn-sm mt-2" style="width:100%">
                                Hủy lọc
                            </a>
                        @endif
                    </form>
                    {{-- END FORM --}}
                </div>
            </aside>

            {{-- Main Content & Product Grid (Right Column) --}}
            <div class="products-content">
                <div class="products-toolbar">
                    <div class="result-count">Hiển thị {{ $products->firstItem() }} - {{ $products->lastItem() }} trong tổng số {{ $products->total() }} sản phẩm</div>
                    <div class="toolbar-right">
                        <span>Sắp xếp theo:</span>
                        {{-- FIX: Sửa onchange để giữ lại các tham số lọc hiện có --}}
                        <select class="sort-select" onchange="window.location.href = updateQueryString(this.value)">
                            <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá thấp nhất</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá cao nhất</option>
                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Phổ biến nhất</option>
                        </select>
                    </div>
                </div>
                
                <div class="product-grid">
                    @forelse($products as $product)
                        @php
                            $variant = $product->bienTheSanPham->first();
                            $minPrice = $variant ? $variant->gia : 0;
                            $comparePrice = $variant ? $variant->gia_so_sanh : 0;
                            $discount = $comparePrice > $minPrice ? round((($comparePrice - $minPrice) / $comparePrice) * 100) : 0;
                            $imagePath = $product->hinh_anh_mac_dinh ? asset('uploads/' . $product->hinh_anh_mac_dinh) : 'https://via.placeholder.com/300';
                            $avgRating = $product->danhGia->avg('so_sao') ?? 0;
                            $reviewCount = $product->danhGia->count();
                        @endphp
                        
                        <div class="product-card">
                            @if($discount > 0)
                                <div class="product-badge">-{{ $discount }}%</div>
                            @endif
                            
                            <a href="{{ route('products.show', $product->slug) }}" class="product-image"> 
                                <img src="{{ $imagePath }}" alt="{{ $product->ten }}">
                            </a>
                            
                            <div class="product-info">
                                <h3><a href="{{ route('products.show', $product->slug) }}">{{ $product->ten }}</a></h3>
                                
                                <div class="product-rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($avgRating)) <i class="fas fa-star"></i>
                                        @elseif($i - 0.5 <= $avgRating) <i class="fas fa-star-half-alt"></i>
                                        @else <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                    <span>({{ $reviewCount }})</span>
                                </div>
                                
                                <div class="product-price">
                                    <span class="price-new">{{ number_format($minPrice, 0, ',', '.') }}₫</span>
                                    @if($comparePrice > $minPrice)
                                        <span class="price-old">{{ number_format($comparePrice, 0, ',', '.') }}₫</span>
                                    @endif
                                </div>
                                
                                <button class="btn btn-cart add-to-cart-btn" 
                                        data-variant-id="{{ $variant->id ?? '' }}" 
                                        data-product-name="{{ $product->ten }}"
                                        @if(!$variant) disabled @endif>
                                    <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="text-center" style="grid-column: 1 / -1;">Không tìm thấy sản phẩm nào trong danh mục **{{ $category->ten }}**.</p>
                    @endforelse
                </div>
                
                {{-- Pagination (Giữ lại các tham số lọc) --}}
                <div class="pagination-wrapper">{{ $products->appends(request()->except('page'))->links() }}</div>
            </div>
            
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    const csrfToken = '{{ csrf_token() }}';
    const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
    const loginUrl = '{{ route("login") }}';
    
    function updateCartBadge(count) {
        const badges = document.querySelectorAll('.header-actions .cart-btn .badge');
        badges.forEach(badge => {
            badge.textContent = count;
        });
    }

    // Hàm Helper để giữ lại các tham số lọc khi thay đổi Sort
    function updateQueryString(sortValue) {
        const url = new URL(window.location.href);
        const params = url.searchParams;
        
        // Xóa tham số sắp xếp cũ nếu tồn tại
        params.delete('sort');
        
        // Thêm tham số sắp xếp mới
        if (sortValue !== 'newest') {
            params.set('sort', sortValue);
        }
        
        // Xóa tham số phân trang
        params.delete('page'); 

        return url.pathname + url.search;
    }
    
    // Logic Add to Cart (Giữ nguyên)
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (this.disabled) return;

            const variantId = this.dataset.variantId;
            
            if (!isLoggedIn) {
                window.PhoneShop && window.PhoneShop.showToast ? PhoneShop.showToast('Vui lòng đăng nhập để thêm vào giỏ hàng!', 'error') : alert('Vui lòng đăng nhập!');
                setTimeout(() => { window.location.href = loginUrl; }, 1000);
                return;
            }
            
            if (!variantId) {
                window.PhoneShop && window.PhoneShop.showToast ? PhoneShop.showToast('Sản phẩm không có biến thể hợp lệ!', 'error') : alert('Sản phẩm không có biến thể hợp lệ!');
                return;
            }
            
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang thêm...';

            fetch('{{ route('cart.add') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    bien_the_id: variantId,
                    so_luong: 1
                })
            })
            .then(response => response.json())
            .then(data => {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-shopping-cart"></i> Thêm vào giỏ';

                if (data.success) {
                    window.PhoneShop && window.PhoneShop.showToast ? PhoneShop.showToast(data.message, 'success') : alert(data.message);
                    updateCartBadge(data.cart_count); 
                } else {
                    window.PhoneShop && window.PhoneShop.showToast ? PhoneShop.showToast(data.message || 'Có lỗi xảy ra!', 'error') : alert(data.message);
                }
            })
            .catch(error => {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-shopping-cart"></i> Thêm vào giỏ';
                console.error('Error adding to cart:', error);
                window.PhoneShop && window.PhoneShop.showToast ? PhoneShop.showToast('Lỗi kết nối máy chủ!', 'error') : alert('Lỗi kết nối máy chủ!');
            });
        });
    });
</script>
@endpush