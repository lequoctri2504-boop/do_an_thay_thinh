@extends('layouts.app')

@section('title', 'PhoneShop - Điện thoại chính hãng giá rẻ')

@section('content')
<section class="banner-slider">
    <div class="container">
        {{-- SỬ DỤNG STYLE INLINE TRÊN SLIDER-WRAPPER ĐỂ CÓ CHIỀU CAO CỐ ĐỊNH --}}
        <div class="slider-wrapper" style="position: relative; height: 450px; overflow: hidden;">
            
            {{-- NÚT ĐIỀU HƯỚNG --}}
            <button class="slider-arrow arrow-left"><i class="fas fa-chevron-left"></i></button>
            <button class="slider-arrow arrow-right"><i class="fas fa-chevron-right"></i></button>

            @foreach($banners as $index => $banner)
                <div class="slide {{ $index === 0 ? 'active' : '' }}">
                    <img src="{{ $banner['image'] }}" alt="Banner {{ $index + 1 }}">
                    <div class="slide-content">
                        <h2>{{ $banner['title'] }}</h2>
                        <p>{{ $banner['subtitle'] }}</p>
                        <a href="{{ $banner['link'] }}" class="btn btn-primary">Xem ngay</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="flash-sale">
    <div class="container">
        <div class="section-header">
            <h2><i class="fas fa-bolt"></i> FLASH SALE - KẾT THÚC TRONG</h2>
            <div class="countdown">
                <span id="hours">02</span>:<span id="minutes">35</span>:<span id="seconds">48</span>
            </div>
        </div>
        <div class="product-grid">
            @forelse($flashSaleProducts as $product)
                @php
                    $variant = $product->bienTheSanPham->first();
                    $minPrice = $variant ? $variant->gia : 0;
                    $comparePrice = $variant ? $variant->gia_so_sanh : 0;
                    $discount = $comparePrice > $minPrice ? round((($comparePrice - $minPrice) / $comparePrice) * 100) : 0;
                    $imagePath = $product->hinh_anh_mac_dinh ? asset('uploads/' . $product->hinh_anh_mac_dinh) : 'https://via.placeholder.com/300';
                    $avgRating = $product->danhGia->avg('so_sao') ?? 0;
                    $reviewCount = $product->danhGia->count();
                    $isInWishlist = Auth::check() ? \Illuminate\Support\Facades\DB::table('yeu_thich')->where('nguoi_dung_id', Auth::id())->where('san_pham_id', $product->id)->exists() : false;
                @endphp
                <div class="product-card">
                    <button class="wishlist-icon {{ $isInWishlist ? 'added' : '' }}" data-product-id="{{ $product->id }}" data-is-added="{{ $isInWishlist ? 'true' : 'false' }}">
                        <i class="{{ $isInWishlist ? 'fas' : 'far' }} fa-heart"></i>
                    </button>
                    
                    @if($discount > 0)
                        <div class="product-badge">-{{ $discount }}%</div>
                    @endif
                    
                    {{-- FIX: Wrap toàn bộ product-image bằng link chi tiết --}}
                    <a href="{{ route('products.show', $product->slug) }}" class="product-image">
                        <img src="{{ $imagePath }}" alt="{{ $product->ten }}">
                        {{-- Quick-view button gây lỗi đã được loại bỏ khỏi đây --}}
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
                <p class="text-center col-12">Không có sản phẩm Flash Sale nào.</p>
            @endforelse
        </div>
    </div>
</section>

<section class="hot-brands">
    <div class="container">
        <div class="section-header">
            <h2><i class="fas fa-fire"></i> THƯƠNG HIỆU NỔI BẬT</h2>
        </div>
        <div class="brands-grid">
            @foreach($brands as $brand)
                @php
                    $logo = '';
                    if ($brand->slug == 'apple') $logo = asset('images/brands/apple.png');
                    elseif ($brand->slug == 'samsung') $logo = asset('images/brands/samsung.png');
                    elseif ($brand->slug == 'xiaomi') $logo = asset('images/brands/xiaomi.png');
                    elseif ($brand->slug == 'oppo') $logo = asset('images/brands/oppo.png');
                    elseif ($brand->slug == 'vivo') $logo = asset('images/brands/vivo.png');
                    else $logo = 'https://via.placeholder.com/80x40';
                @endphp
                <a href="{{ route('products.index', ['brand' => $brand->slug]) }}" class="brand-card">
                    <img src="{{ $logo }}" alt="{{ $brand->ten }}">
                    <span>{{ $brand->ten }}</span>
                </a>
            @endforeach
            <a href="#" class="brand-card">
                <img src="https://upload.wikimedia.org/wikipedia/commons/6/6a/Realme_logo.svg" alt="Realme">
                <span>Realme</span>
            </a>
        </div>
    </div>
</section>

<section class="featured-products">
    <div class="container">
        <div class="section-header">
            <h2><i class="fas fa-crown"></i> SẢN PHẨM NỔI BẬT</h2>
            <a href="{{ route('products.featured') }}" class="view-all">Xem tất cả <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="product-grid">
            @forelse($featuredProducts as $product)
                @php
                    $variant = $product->bienTheSanPham->first();
                    $minPrice = $variant ? $variant->gia : 0;
                    $comparePrice = $variant ? $variant->gia_so_sanh : 0;
                    $discount = $comparePrice > $minPrice ? round((($comparePrice - $minPrice) / $comparePrice) * 100) : 0;
                    $imagePath = $product->hinh_anh_mac_dinh ? asset('uploads/' . $product->hinh_anh_mac_dinh) : 'https://via.placeholder.com/300';
                    $avgRating = $product->danhGia->avg('so_sao') ?? 0;
                    $reviewCount = $product->danhGia->count();
                    $isInWishlist = Auth::check() ? \Illuminate\Support\Facades\DB::table('yeu_thich')->where('nguoi_dung_id', Auth::id())->where('san_pham_id', $product->id)->exists() : false;
                @endphp
                <div class="product-card">
                    <button class="wishlist-icon {{ $isInWishlist ? 'added' : '' }}" data-product-id="{{ $product->id }}" data-is-added="{{ $isInWishlist ? 'true' : 'false' }}">
                        <i class="{{ $isInWishlist ? 'fas' : 'far' }} fa-heart"></i>
                    </button>
                    
                    <div class="product-badge new">MỚI</div>
                    
                    {{-- FIX: Wrap toàn bộ product-image bằng link chi tiết --}}
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
                <p class="text-center col-12">Không có sản phẩm nổi bật nào.</p>
            @endforelse
        </div>
    </div>
</section>

<!-- <section class="news-section">
    <div class="container">
        <div class="section-header">
            <h2><i class="fas fa-newspaper"></i> TIN CÔNG NGHỆ</h2>
            <a href="#" class="view-all">Xem tất cả <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="news-grid">
            <article class="news-card">
                <img src="https://images.unsplash.com/photo-1556656793-08538906a9f8?w=400&h=250&fit=crop" alt="News">
                <div class="news-content">
                    <span class="news-date"><i class="far fa-calendar"></i> 15/12/2024</span>
                    <h3>iPhone 16 Pro Max có gì mới? Đáng mua không?</h3>
                    <p>Cùng tìm hiểu những nâng cấp đáng giá trên thế hệ iPhone mới nhất...</p>
                    <a href="#" class="read-more">Đọc thêm <i class="fas fa-arrow-right"></i></a>
                </div>
            </article>
            
            <article class="news-card">
                <img src="https://images.unsplash.com/photo-1617802690658-1173a3d117d2?w=400&h=250&fit=crop" alt="News">
                <div class="news-content">
                    <span class="news-date"><i class="far fa-calendar"></i> 14/12/2024</span>
                    <h3>So sánh Galaxy S24 Ultra vs iPhone 15 Pro Max</h3>
                    <p>Cuộc chiến flagship giữa hai ông lớn, điện thoại nào phù hợp với bạn...</p>
                    <a href="#" class="read-more">Đọc thêm <i class="fas fa-arrow-right"></i></a>
                </div>
            </article>
            
            <article class="news-card">
                <img src="https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=400&h=250&fit=crop" alt="News">
                <div class="news-content">
                    <span class="news-date"><i class="far fa-calendar"></i> 13/12/2024</span>
                    <h3>Top 5 điện thoại dưới 5 triệu đáng mua nhất</h3>
                    <p>Gợi ý những sản phẩm tốt nhất trong phân khúc giá rẻ...</p>
                    <a href="#" class="read-more">Đọc thêm <i class="fas fa-arrow-right"></i></a>
                </div>
            </article>
        </div>
    </div>
</section> -->


<section class="news-section">
    <div class="container">
        <div class="section-header">
            <h2><i class="fas fa-newspaper"></i> TIN CÔNG NGHỆ</h2>
            <a href="{{ route('news.index') }}" class="view-all">Xem tất cả <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="news-grid">
            @forelse($newsArticles as $article)
            @php
                $imagePath = $article->hinh_anh_chinh ? asset('uploads/' . $article->hinh_anh_chinh) : 'https://via.placeholder.com/400x250';
                $moTaNgan = $article->mo_ta_ngan ?? \Illuminate\Support\Str::limit(strip_tags($article->noi_dung), 100);
            @endphp
            <article class="news-card">
                <img src="{{ $imagePath }}" alt="{{ $article->tieu_de }}">
                <div class="news-content">
                    <span class="news-date"><i class="far fa-calendar"></i> {{ \Carbon\Carbon::parse($article->created_at)->format('d/m/Y') }}</span>
                    <h3><a href="{{ route('news.show', $article->slug) }}">{{ $article->tieu_de }}</a></h3>
                    <p>{{ $moTaNgan }}</p>
                    <a href="{{ route('news.show', $article->slug) }}" class="read-more">Đọc thêm <i class="fas fa-arrow-right"></i></a>
                </div>
            </article>
            @empty
            {{-- Giữ lại thông báo empty đã fix trước đó --}}
            <article class="news-card" style="grid-column: 1 / -1; text-align: center;">
                <p class="text-muted" style="padding: 20px;">Hiện chưa có bài viết nào được xuất bản.</p>
            </article>
            @endforelse
        </div>
    </div>
</section>
@endsection

@push('styles')
    <style>
        /* CSS BỔ SUNG QUAN TRỌNG CHO SLIDER ARROWS VÀ BANNER DISPLAY */
        .slider-wrapper {
            position: relative; 
            height: 450px; 
            overflow: hidden;
        }

        .slide {
            position: absolute; 
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0; 
            transition: opacity 1s ease-in-out;
        }

        .slide.active {
            opacity: 1; 
            z-index: 1; 
        }

        .slider-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            padding: 15px;
            z-index: 10;
            cursor: pointer;
            transition: all 0.3s;
            opacity: 0.8;
            border-radius: 5px;
        }

        .slider-arrow:hover {
            opacity: 1;
            background: var(--primary-color);
        }

        .arrow-left {
            left: 20px;
        }

        .arrow-right {
            right: 20px;
        }
    </style>
@endpush

@push('scripts')
<script>
    const csrfToken = '{{ csrf_token() }}';
    const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
    const loginUrl = '{{ route("login") }}';
    
    // FIX QUAN TRỌNG: GHI ĐÈ hàm cập nhật Badge để sử dụng giá trị trực tiếp từ Server (data.cart_count)
    function updateCartBadge(count) {
        const badges = document.querySelectorAll('.header-actions .cart-btn .badge');
        badges.forEach(badge => {
            badge.textContent = count; // <-- Sử dụng count mới
        });
    }
    
    // Hàm update wishlist (cũng cần fix để dùng count từ server)
    function updateWishlistBadge(count) {
        const badges = document.querySelectorAll('.header-actions .wishlist-btn .badge');
        badges.forEach(badge => {
            badge.textContent = count;
        });
    }

    // --- LOGIC THÊM VÀO GIỎ HÀNG (ADD TO CART) ---
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
                    updateCartBadge(data.cart_count); // <-- Cập nhật badge
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

    // --- LOGIC YÊU THÍCH (WISHLIST) ---
    document.querySelectorAll('.wishlist-icon').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const productId = this.dataset.productId;
            let isAdded = this.dataset.isAdded === 'true';
            
            if (!isLoggedIn) {
                window.PhoneShop && window.PhoneShop.showToast ? PhoneShop.showToast('Vui lòng đăng nhập để thêm vào yêu thích!', 'error') : alert('Vui lòng đăng nhập!');
                setTimeout(() => { window.location.href = loginUrl; }, 1000);
                return;
            }
            
            this.disabled = true;
            
            const url = isAdded 
                        ? '{{ route('wishlist.remove', ['id' => '__id__']) }}'.replace('__id__', productId)
                        : '{{ route('wishlist.add') }}';
            const method = isAdded ? 'DELETE' : 'POST';
            
            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: method === 'POST' ? JSON.stringify({ san_pham_id: productId }) : null
            })
            .then(response => response.json())
            .then(data => {
                this.disabled = false;
                if (data.success) {
                    window.PhoneShop && window.PhoneShop.showToast ? PhoneShop.showToast(data.message, 'success') : alert(data.message);
                    updateWishlistBadge(data.wishlist_count);
                    
                    if (isAdded) {
                        this.dataset.isAdded = 'false';
                        this.querySelector('i').classList.remove('fas');
                        this.querySelector('i').classList.add('far');
                        this.classList.remove('added');
                    } else {
                        this.dataset.isAdded = 'true';
                        this.querySelector('i').classList.remove('far');
                        this.querySelector('i').classList.add('fas');
                        this.classList.add('added');
                    }
                } else {
                    if(data.message && data.message.includes('đã có')) {
                        this.dataset.isAdded = 'true';
                        this.querySelector('i').classList.remove('far');
                        this.querySelector('i').classList.add('fas');
                        this.classList.add('added');
                    }
                    window.PhoneShop && window.PhoneShop.showToast ? PhoneShop.showToast(data.message || 'Có lỗi xảy ra!', 'error') : alert(data.message);
                }
            })
            .catch(error => {
                this.disabled = false;
                console.error('Error handling wishlist:', error);
                window.PhoneShop && window.PhoneShop.showToast ? PhoneShop.showToast('Lỗi kết nối máy chủ!', 'error') : alert('Lỗi kết nối máy chủ!');
            });
        });
    });

    // Khởi tạo Countdown Timer
    if (typeof initCountdownTimer !== 'undefined') {
        initCountdownTimer();
    }
</script>
@endpush