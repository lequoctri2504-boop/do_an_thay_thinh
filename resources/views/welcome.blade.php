<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PhoneShop - Điện thoại chính hãng giá rẻ</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-top">
            <div class="container">
                <div class="header-top-content">
                    <div class="header-left">
                        <a href="#"><i class="fas fa-phone"></i> hotline: 0962371176</a>
                        <a href="#"><i class="fas fa-map-marker-alt"></i> Tìm cửa hàng</a>
                    </div>
                    <div class="header-right">
                        @guest
                            <a href="{{ route('login') }}"><i class="fas fa-user"></i> Đăng nhập</a>
                            <a href="{{ route('register') }}"><i class="fas fa-user-plus"></i> Đăng ký</a>
                        @else
                            <a href="#"><i class="fas fa-user"></i> {{ Auth::user()->ho_ten }}</a>
                            @if(Auth::user()->vai_tro === 'ADMIN')
                                <a href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Quản trị</a>
                            @elseif(Auth::user()->vai_tro === 'NHAN_VIEN')
                                <a href="{{ route('staff.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Nhân viên</a>
                            @endif
                            <form action="{{ route('logout') }}" method="post" style="display:inline;">
                                @csrf
                                <button type="submit" style="background:none;border:none;color:#fff;cursor:pointer;">
                                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                                </button>
                            </form>
                        @endguest
                        <a href="#"><i class="fas fa-receipt"></i> Tra cứu đơn hàng</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="header-main">
            <div class="container">
                <div class="header-main-content">
                    <div class="logo">
                        <a href="{{ route('home') }}">
                            <span><img src="{{ asset('img/logo_LQT1.png') }}" alt="" width="70px"></span>
                        </a>
                    </div>
                    
                    <div class="search-box">
                        <input type="text" placeholder="Tìm kiếm điện thoại, phụ kiện...">
                        <button><i class="fas fa-search"></i></button>
                    </div>
                    
                    <div class="header-actions">
                        <a href="#" class="wishlist-btn">
                            <i class="far fa-heart"></i>
                            <span class="badge">0</span>
                        </a>
                        <a href="#" class="cart-btn">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="badge">0</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <nav class="navbar">
            <div class="container">
                <ul class="nav-menu">
                    <li><a href="#"><i class="fas fa-mobile-alt"></i> Điện thoại</a></li>                
                    <li><a href="#"><i class="fas fa-headphones"></i> Phụ kiện</a></li>
                    <li><a href="#"><i class="fas fa-empire"></i> Thu cũ đổi mới</a></li>
                    <li><a href="#"><i class="fa fa-gamepad" aria-hidden="true"></i> chơi tạo voucher</a></li>
                    <li class="hot"><a href="#"><i class="fas fa-fire"></i> Khuyến mãi</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Banner Slider -->
    <section class="banner-slider">
        <div class="container">
            <div class="slider-wrapper">
                <div class="slide active">
                    <img src="https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=1200&h=400&fit=crop" alt="Banner 1">
                    <div class="slide-content">
                        <h2>iPhone 17 Pro Max</h2>
                        <p>Titanium. Mạnh mẽ. Nhẹ hơn bao giờ hết</p>
                        <a href="#" class="btn btn-primary">Xem ngay</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Flash Sale -->
    <section class="flash-sale">
        <div class="container">
            <div class="section-header">
                <h2><i class="fas fa-bolt"></i> SẢN PHẨM NỔI BẬT</h2>
                <div class="countdown">
                    <span>02</span>:<span>35</span>:<span>48</span>
                </div>
            </div>
            <div class="product-grid">
                @forelse($featuredProducts as $product)
                    @php
                        $bienThe = $product->bienTheDangBan->first();
                        $giaThapNhat = $product->bienTheDangBan->min('gia');
                        $giaCaoNhat = $product->bienTheDangBan->max('gia');
                        $giaSoSanh = $bienThe->gia_so_sanh ?? null;
                    @endphp
                    <div class="product-card">
                        @if($giaSoSanh)
                            @php
                                $phanTram = round((($giaSoSanh - $bienThe->gia) / $giaSoSanh) * 100);
                            @endphp
                            <div class="product-badge">-{{ $phanTram }}%</div>
                        @endif
                        <div class="product-image">
                            <img src="{{ asset('img/' . ($product->hinh_anh_mac_dinh ?? 'default.png')) }}" 
                                 alt="{{ $product->ten }}"
                                 onerror="this.src='https://via.placeholder.com/300x300?text=No+Image'">
                            <button class="quick-view"><i class="fas fa-eye"></i></button>
                        </div>
                        <div class="product-info">
                            <h3>{{ $product->ten }}</h3>
                            <div class="product-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                                <span>(234)</span>
                            </div>
                            <div class="product-price">
                                @if($giaThapNhat == $giaCaoNhat)
                                    <span class="price-new">{{ number_format($giaThapNhat, 0, ',', '.') }}₫</span>
                                @else
                                    <span class="price-new">
                                        {{ number_format($giaThapNhat, 0, ',', '.') }}₫ - 
                                        {{ number_format($giaCaoNhat, 0, ',', '.') }}₫
                                    </span>
                                @endif
                                @if($giaSoSanh)
                                    <span class="price-old">{{ number_format($giaSoSanh, 0, ',', '.') }}₫</span>
                                @endif
                            </div>
                            <button class="btn btn-cart"><i class="fas fa-shopping-cart"></i> Thêm vào giỏ</button>
                        </div>
                    </div>
                @empty
                    <p>Chưa có sản phẩm nào</p>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Hot Brands -->
    <section class="hot-brands">
        <div class="container">
            <div class="section-header">
                <h2><i class="fas fa-fire"></i> THƯƠNG HIỆU NỔI BẬT</h2>
            </div>
            <div class="brands-grid">
                <a href="#" class="brand-card">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/f/fa/Apple_logo_black.svg" alt="Apple">
                    <span>Apple</span>
                </a>
                <a href="#" class="brand-card">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/2/24/Samsung_Logo.svg" alt="Samsung">
                    <span>Samsung</span>
                </a>
                <a href="#" class="brand-card">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/a/ae/Xiaomi_logo_%282021-%29.svg" alt="Xiaomi">
                    <span>Xiaomi</span>
                </a>
                <a href="#" class="brand-card">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/8/8a/OPPO_LOGO_2019.svg" alt="OPPO">
                    <span>OPPO</span>
                </a>
                <a href="#" class="brand-card">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/51/Vivo_logo_2019.svg" alt="Vivo">
                    <span>Vivo</span>
                </a>
                <a href="#" class="brand-card">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/6/6a/Realme_logo.svg" alt="Realme">
                    <span>Realme</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="featured-products">
        <div class="container">
            <div class="section-header">
                <h2><i class="fas fa-crown"></i> SẢN PHẨM MỚI NHẤT</h2>
                <a href="#" class="view-all">Xem tất cả <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="product-grid">
                @foreach($newProducts as $product)
                    @php
                        $bienThe = $product->bienTheDangBan->first();
                        $giaThapNhat = $product->bienTheDangBan->min('gia');
                    @endphp
                    <div class="product-card">
                        <div class="product-badge new">MỚI</div>
                        <div class="product-image">
                            <img src="{{ asset('img/' . ($product->hinh_anh_mac_dinh ?? 'default.png')) }}" 
                                 alt="{{ $product->ten }}"
                                 onerror="this.src='https://via.placeholder.com/300x300?text=No+Image'">
                            <button class="quick-view"><i class="fas fa-eye"></i></button>
                        </div>
                        <div class="product-info">
                            <h3>{{ $product->ten }}</h3>
                            <div class="product-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <span>(567)</span>
                            </div>
                            <div class="product-price">
                                <span class="price-new">{{ number_format($giaThapNhat, 0, ',', '.') }}₫</span>
                            </div>
                            <button class="btn btn-cart"><i class="fas fa-shopping-cart"></i> Thêm vào giỏ</button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-top">
                <div class="footer-col">
                    <h4>Về PhoneShop</h4>
                    <ul>
                        <li><a href="#">Giới thiệu công ty</a></li>
                        <li><a href="#">Tuyển dụng</a></li>
                        <li><a href="#">Tin tức</a></li>
                        <li><a href="#">Liên hệ</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Chính sách</h4>
                    <ul>
                        <li><a href="#">Chính sách bảo hành</a></li>
                        <li><a href="#">Chính sách đổi trả</a></li>
                        <li><a href="#">Chính sách vận chuyển</a></li>
                        <li><a href="#">Chính sách bảo mật</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Hỗ trợ khách hàng</h4>
                    <ul>
                        <li><a href="#">Hướng dẫn mua hàng</a></li>
                        <li><a href="#">Hướng dẫn thanh toán</a></li>
                        <li><a href="#">Tra cứu đơn hàng</a></li>
                        <li><a href="#">Câu hỏi thường gặp</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Kết nối với chúng tôi</h4>
                    <div class="social-links">
                        <a href="https://www.facebook.com/quoctri.le.319"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                        <a href="https://www.tiktok.com/@lqt254"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; Designer by lê quốc trí D22_TH14. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Chat Widget -->
    <div class="chat-widget">
        <button class="chat-toggle">
            <i class="fas fa-comments"></i>
        </button>
    </div>

    <!-- Toast Notification -->
    <div class="toast" id="toast"></div>

    <script src="{{ asset('js/main.js') }}"></script>
</body>
</html>