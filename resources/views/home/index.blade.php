@extends('layouts.app')

@section('title', 'Trang chủ - PhoneShop')

@section('content')

<!-- Banner Slider -->
<section class="hero-slider">
    <div class="slider-container">
        <div class="slide active">
            <img src="{{ asset('images/banner/banner1.jpg') }}" alt="Banner 1">
            <div class="slide-content">
                <h2>iPhone 15 Pro Max</h2>
                <p>Titanium. Mạnh mẽ. Nhẹ nhàng. Pro.</p>
                <a href="#" class="btn btn-primary">Xem ngay</a>
            </div>
        </div>
        <div class="slide">
            <img src="{{ asset('images/banner/banner2.jpg') }}" alt="Banner 2">
            <div class="slide-content">
                <h2>Samsung Galaxy S24 Ultra</h2>
                <p>Sức mạnh AI đỉnh cao</p>
                <a href="#" class="btn btn-primary">Khám phá</a>
            </div>
        </div>
        <div class="slide">
            <img src="{{ asset('images/banner/banner3.jpg') }}" alt="Banner 3">
            <div class="slide-content">
                <h2>Giảm giá lên đến 50%</h2>
                <p>Cơ hội sở hữu điện thoại cao cấp</p>
                <a href="#" class="btn btn-primary">Mua ngay</a>
            </div>
        </div>
    </div>
    <button class="slider-btn prev" onclick="changeSlide(-1)">
        <i class="fas fa-chevron-left"></i>
    </button>
    <button class="slider-btn next" onclick="changeSlide(1)">
        <i class="fas fa-chevron-right"></i>
    </button>
    <div class="slider-dots">
        <span class="dot active" onclick="currentSlide(0)"></span>
        <span class="dot" onclick="currentSlide(1)"></span>
        <span class="dot" onclick="currentSlide(2)"></span>
    </div>
</section>

<!-- Categories Section -->
<section class="categories-section">
    <div class="container">
        <div class="categories-grid">
            @foreach($danhMuc as $dm)
            <a href="{{ route('danh-muc', $dm->slug) }}" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <h3>{{ $dm->ten }}</h3>
            </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Flash Sale Section -->
<section class="flash-sale-section">
    <div class="container">
        <div class="section-header">
            <h2><i class="fas fa-bolt"></i> FLASH SALE HÔM NAY</h2>
            <div class="countdown">
                <span>Kết thúc sau:</span>
                <div class="time-box">
                    <span id="hours">12</span>
                    <small>Giờ</small>
                </div>
                <div class="time-box">
                    <span id="minutes">34</span>
                    <small>Phút</small>
                </div>
                <div class="time-box">
                    <span id="seconds">56</span>
                    <small>Giây</small>
                </div>
            </div>
            <a href="{{ route('search', ['sap_xep' => 'giam_gia']) }}" class="view-all">
                Xem tất cả <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
        <div class="products-grid">
            @foreach($sanPhamGiamGia->take(4) as $sp)
            @php
                $bienThe = $sp->bienThe->first();
                $giamGia = 0;
                if ($bienThe && $bienThe->gia_so_sanh > 0) {
                    $giamGia = round((($bienThe->gia_so_sanh - $bienThe->gia) / $bienThe->gia_so_sanh) * 100);
                }
                $bienTheId = $bienThe ? $bienThe->id : 'null';
            @endphp
            <div class="product-card flash-sale">
                @if($giamGia > 0)
                <div class="product-badge sale">-{{ $giamGia }}%</div>
                @endif
                <a href="{{ route('chi-tiet', $sp->slug) }}" class="product-image">
                    <img src="{{ asset('storage/products/' . $sp->hinh_anh_mac_dinh) }}" alt="{{ $sp->ten }}" loading="lazy">
                </a>
                <div class="product-info">
                    <h3 class="product-name">
                        <a href="{{ route('chi-tiet', $sp->slug) }}">{{ $sp->ten }}</a>
                    </h3>
                    <p class="product-brand">{{ $sp->thuongHieu->ten }}</p>
                    <div class="product-rating">
                        <div class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <span class="rating-count">({{ $sp->danhGia->count() }})</span>
                    </div>
                    @if($bienThe)
                    <div class="product-price">
                        <span class="current-price">{{ number_format($bienThe->gia) }}₫</span>
                        @if($bienThe->gia_so_sanh > 0)
                        <span class="old-price">{{ number_format($bienThe->gia_so_sanh) }}₫</span>
                        @endif
                    </div>
                    <div class="product-stock">
                        <div class="stock-bar">
                            @php
                                $stockPercent = min(($bienThe->ton_kho / 100) * 100, 100);
                            @endphp
                            <!-- <div class="stock-fill" style="width: {{ $stockPercent }}%"></div> -->
                        </div>
                        <span class="stock-text">Đã bán: {{ 100 - $bienThe->ton_kho }}</span>
                    </div>
                    @endif
                    <!-- <button class="btn btn-cart" onclick="addToCart({{ $sp->id }}, {{ $bienTheId }})">
                        <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                    </button> -->
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="products-section">
    <div class="container">
        <div class="section-header">
            <h2><i class="fas fa-fire"></i> SẢN PHẨM NỔI BẬT</h2>
            <a href="{{ route('search') }}" class="view-all">
                Xem tất cả <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
        <div class="products-grid">
            @foreach($sanPhamNoiBat as $sp)
            @php
                $bienThe = $sp->bienThe->first();
                $giamGia = 0;
                if ($bienThe && $bienThe->gia_so_sanh > 0) {
                    $giamGia = round((($bienThe->gia_so_sanh - $bienThe->gia) / $bienThe->gia_so_sanh) * 100);
                }
                $bienTheId = $bienThe ? $bienThe->id : 'null';
            @endphp
            <div class="product-card">
                @if($giamGia > 0)
                <div class="product-badge sale">-{{ $giamGia }}%</div>
                @elseif($sp->created_at->diffInDays(now()) <= 7)
                <div class="product-badge new">Mới</div>
                @endif
                <div class="product-actions">
                    <!-- <button class="action-btn" onclick="toggleWishlist({{ $sp->id }})" title="Yêu thích">
                        <i class="far fa-heart"></i>
                    </button>
                    <button class="action-btn" onclick="quickView({{ $sp->id }})" title="Xem nhanh">
                        <i class="far fa-eye"></i>
                    </button> -->
                </div>
                <a href="{{ route('chi-tiet', $sp->slug) }}" class="product-image">
                    <img src="{{ asset('storage/products/' . $sp->hinh_anh_mac_dinh) }}" alt="{{ $sp->ten }}" loading="lazy">
                </a>
                <div class="product-info">
                    <h3 class="product-name">
                        <a href="{{ route('chi-tiet', $sp->slug) }}">{{ $sp->ten }}</a>
                    </h3>
                    <p class="product-brand">{{ $sp->thuongHieu->ten }}</p>
                    <div class="product-rating">
                        <div class="stars">
                            @php
                                $avgRating = $sp->danhGia->avg('so_sao') ?? 0;
                            @endphp
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($avgRating))
                                    <i class="fas fa-star"></i>
                                @elseif($i - 0.5 <= $avgRating)
                                    <i class="fas fa-star-half-alt"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </div>
                        <span class="rating-count">({{ $sp->danhGia->count() }})</span>
                    </div>
                    @if($bienThe)
                    <div class="product-specs">
                        @if($bienThe->mau_sac)
                        <span class="spec-item">
                            <i class="fas fa-palette"></i> {{ $bienThe->mau_sac }}
                        </span>
                        @endif
                        @if($bienThe->dung_luong_gb)
                        <span class="spec-item">
                            <i class="fas fa-hdd"></i> {{ $bienThe->dung_luong_gb }}GB
                        </span>
                        @endif
                    </div>
                    <div class="product-price">
                        <span class="current-price">{{ number_format($bienThe->gia) }}₫</span>
                        @if($bienThe->gia_so_sanh > 0)
                        <span class="old-price">{{ number_format($bienThe->gia_so_sanh) }}₫</span>
                        @endif
                    </div>
                    @endif
                    <!-- <button class="btn btn-cart" onclick="addToCart({{ $sp->id }}, {{ $bienTheId }})">
                        <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                    </button> -->
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Best Sellers -->
<section class="products-section bg-light">
    <div class="container">
        <div class="section-header">
            <h2><i class="fas fa-trophy"></i> BÁN CHẠY NHẤT</h2>
            <a href="{{ route('search', ['sap_xep' => 'ban_chay']) }}" class="view-all">
                Xem tất cả <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
        <div class="products-grid">
            @foreach($sanPhamBanChay as $sp)
            @php
                $bienThe = $sp->bienThe->first();
                $giamGia = 0;
                if ($bienThe && $bienThe->gia_so_sanh > 0) {
                    $giamGia = round((($bienThe->gia_so_sanh - $bienThe->gia) / $bienThe->gia_so_sanh) * 100);
                }
                $bienTheId = $bienThe ? $bienThe->id : 'null';
            @endphp
            <div class="product-card">
                @if($giamGia > 0)
                <div class="product-badge sale">-{{ $giamGia }}%</div>
                @endif
                <div class="product-badge best-seller">
                    <i class="fas fa-crown"></i> Hot
                </div>
                <div class="product-actions">
                    <!-- <button class="action-btn" onclick="toggleWishlist({{ $sp->id }})" title="Yêu thích">
                        <i class="far fa-heart"></i>
                    </button>
                    <button class="action-btn" onclick="quickView({{ $sp->id }})" title="Xem nhanh">
                        <i class="far fa-eye"></i>
                    </button> -->
                </div>
                <a href="{{ route('chi-tiet', $sp->slug) }}" class="product-image">
                    <img src="{{ asset('storage/products/' . $sp->hinh_anh_mac_dinh) }}" alt="{{ $sp->ten }}" loading="lazy">
                </a>
                <div class="product-info">
                    <h3 class="product-name">
                        <a href="{{ route('chi-tiet', $sp->slug) }}">{{ $sp->ten }}</a>
                    </h3>
                    <p class="product-brand">{{ $sp->thuongHieu->ten }}</p>
                    <div class="product-rating">
                        <div class="stars">
                            @php
                                $avgRating = $sp->danhGia->avg('so_sao') ?? 0;
                            @endphp
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($avgRating))
                                    <i class="fas fa-star"></i>
                                @elseif($i - 0.5 <= $avgRating)
                                    <i class="fas fa-star-half-alt"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </div>
                        <span class="rating-count">({{ $sp->danhGia->count() }})</span>
                    </div>
                    @if($bienThe)
                    <div class="product-price">
                        <span class="current-price">{{ number_format($bienThe->gia) }}₫</span>
                        @if($bienThe->gia_so_sanh > 0)
                        <span class="old-price">{{ number_format($bienThe->gia_so_sanh) }}₫</span>
                        @endif
                    </div>
                    @endif
                    <!-- <button class="btn btn-cart" onclick="addToCart({{ $sp->id }}, {{ $bienTheId }})">
                        <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                    </button> -->
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Brands Section -->
<section class="brands-section">
    <div class="container">
        <div class="section-header">
            <h2><i class="fas fa-star"></i> THƯƠNG HIỆU NỔI BẬT</h2>
        </div>
        
        <div class="brands-grid">
            @foreach($thuongHieu as $th)
            <a href="{{ route('thuong-hieu', $th->slug) }}" class="brand-card">
                <div class="brand-logo">
                    @php
                        $brandImage = asset('images/brands/' . strtolower($th->slug) . '.png');
                        $defaultImage = asset('images/brands/default.png');
                    @endphp
                    <img src="{{ $brandImage }}" 
                         alt="{{ $th->ten }}" 
                         onerror="this.src='{{ $defaultImage }}'">
                </div>
                <h3>{{ $th->ten }}</h3>
                <p>{{ $th->san_pham_count }} sản phẩm</p>
            </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section">
    <div class="container">
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shipping-fast"></i>
                </div>
                <div class="feature-content">
                    <h3>Miễn phí vận chuyển</h3>
                    <p>Cho đơn hàng từ 500.000₫</p>
                </div>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="feature-content">
                    <h3>Bảo hành chính hãng</h3>
                    <p>12 tháng trên toàn quốc</p>
                </div>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-sync-alt"></i>
                </div>
                <div class="feature-content">
                    <h3>Đổi trả trong 7 ngày</h3>
                    <p>Nếu sản phẩm lỗi</p>
                </div>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <div class="feature-content">
                    <h3>Hỗ trợ 24/7</h3>
                    <p>Tư vấn miễn phí</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="newsletter-section">
    <div class="container">
        <div class="newsletter-content">
            <div class="newsletter-text">
                <h2>Đăng ký nhận tin</h2>
                <p>Nhận thông tin khuyến mãi và sản phẩm mới nhất</p>
            </div>
            <form class="newsletter-form" onsubmit="return subscribeNewsletter(event)">
                <input type="email" placeholder="Nhập email của bạn" required>
                <button type="submit" class="btn btn-primary">Đăng ký</button>
            </form>
        </div>
    </div>
</section>

<!-- Quick View Modal -->
<div id="quickViewModal" class="modal">
    <div class="modal-content quick-view-content">
        <span class="modal-close" onclick="closeQuickView()">&times;</span>
        <div id="quickViewBody"></div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Slider functionality
let currentSlideIndex = 0;
const slides = document.querySelectorAll('.slide');
const dots = document.querySelectorAll('.dot');

function showSlide(n) {
    if (n >= slides.length) currentSlideIndex = 0;
    if (n < 0) currentSlideIndex = slides.length - 1;
    
    slides.forEach(slide => slide.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));
    
    slides[currentSlideIndex].classList.add('active');
    dots[currentSlideIndex].classList.add('active');
}

function changeSlide(n) {
    currentSlideIndex += n;
    showSlide(currentSlideIndex);
}

function currentSlide(n) {
    currentSlideIndex = n;
    showSlide(currentSlideIndex);
}

setInterval(() => {
    currentSlideIndex++;
    showSlide(currentSlideIndex);
}, 5000);

// Countdown timer
function startCountdown() {
    const countdownDate = new Date().getTime() + (12 * 60 * 60 * 1000 + 34 * 60 * 1000 + 56 * 1000);
    
    const x = setInterval(function() {
        const now = new Date().getTime();
        const distance = countdownDate - now;
        
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        document.getElementById("hours").innerText = hours.toString().padStart(2, '0');
        document.getElementById("minutes").innerText = minutes.toString().padStart(2, '0');
        document.getElementById("seconds").innerText = seconds.toString().padStart(2, '0');
        
        if (distance < 0) {
            clearInterval(x);
            document.querySelector('.countdown').innerHTML = "EXPIRED";
        }
    }, 1000);
}

startCountdown();

// Add to cart
// function addToCart(productId, variantId) {
//     @if(Route::has('cart.add'))
//     fetch('{{ route("cart.add") }}', {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/json',
//             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
//         },
//         body: JSON.stringify({
//             san_pham_id: productId,
//             bien_the_id: variantId,
//             so_luong: 1
//         })
//     })
//     .then(response => response.json())
//     .then(data => {
//         if (data.success) {
//             showToast('Đã thêm vào giỏ hàng!', 'success');
//             updateCartCount();
//         } else {
//             showToast(data.message || 'Có lỗi xảy ra', 'error');
//         }
//     })
//     .catch(error => {
//         console.error('Error:', error);
//         showToast('Có lỗi xảy ra', 'error');
//     });
//     @else
//     showToast('Chức năng giỏ hàng chưa được cài đặt', 'error');
//     @endif
// }

// Toggle wishlist
// function toggleWishlist(productId) {
//     const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
    
//     if (!isLoggedIn) {
//         showToast('Vui lòng đăng nhập', 'error');
//         setTimeout(() => {
//             window.location.href = '{{ route("login") }}';
//         }, 1500);
//         return;
//     }
    
//     @if(Route::has('wishlist.toggle'))
//     fetch('{{ route("wishlist.toggle") }}', {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/json',
//             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
//         },
//         body: JSON.stringify({ san_pham_id: productId })
//     })
//     .then(response => response.json())
//     .then(data => {
//         if (data.success) {
//             const btn = event.currentTarget;
//             const icon = btn.querySelector('i');
//             if (data.action === 'added') {
//                 icon.classList.remove('far');
//                 icon.classList.add('fas');
//                 showToast('Đã thêm vào yêu thích', 'success');
//             } else {
//                 icon.classList.remove('fas');
//                 icon.classList.add('far');
//                 showToast('Đã xóa khỏi yêu thích', 'success');
//             }
//         }
//     })
//     .catch(error => {
//         console.error('Error:', error);
//         showToast('Có lỗi xảy ra', 'error');
//     });
//     @else
//     showToast('Chức năng yêu thích chưa được cài đặt', 'error');
//     @endif
// }

// Quick view
function quickView(productId) {
    document.getElementById('quickViewModal').style.display = 'block';
    fetch('/san-pham/quick-view/' + productId)
        .then(response => response.json())
        .then(data => {
            document.getElementById('quickViewBody').innerHTML = data.html;
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('quickViewBody').innerHTML = '<p>Không thể tải thông tin sản phẩm</p>';
        });
}

function closeQuickView() {
    document.getElementById('quickViewModal').style.display = 'none';
}

// // Newsletter subscription
// function subscribeNewsletter(e) {
//     e.preventDefault();
//     const email = e.target.querySelector('input[type="email"]').value;
    
//     @if(Route::has('newsletter.subscribe'))
//     fetch('{{ route("newsletter.subscribe") }}', {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/json',
//             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
//         },
//         body: JSON.stringify({ email: email })
//     })
//     .then(response => response.json())
//     .then(data => {
//         if (data.success) {
//             showToast('Đăng ký thành công!', 'success');
//             e.target.reset();
//         } else {
//             showToast(data.message || 'Có lỗi xảy ra', 'error');
//         }
//     })
//     .catch(error => {
//         console.error('Error:', error);
//         showToast('Có lỗi xảy ra', 'error');
//     });
//     @else
//     showToast('Cảm ơn bạn đã đăng ký!', 'success');
//     e.target.reset();
//     @endif
    
//     return false;
// }

// Toast notification helper
function showToast(message, type) {
    const toast = document.getElementById('toast');
    if (toast) {
        toast.textContent = message;
        toast.className = 'toast show ' + type;
        setTimeout(() => {
            toast.className = toast.className.replace('show', '');
        }, 3000);
    } else {
        alert(message);
    }
}

// Update cart count helper
function updateCartCount() {
    console.log('Cart count updated');
}
</script>
@endpush