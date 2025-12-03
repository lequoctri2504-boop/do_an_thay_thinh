<?php
// Bắt buộc phải có các biến này để header hoạt động
$user = Auth::user();
$cartCount = session('cart_count', 0);
$wishlistCount = session('wishlist_count', 0);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PhoneShop - Điện thoại chính hãng')</title>
    {{-- style.css đã bao gồm các quy tắc cho Header/Footer/Sản phẩm --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('styles')
    
    {{-- Thêm CSS tùy chỉnh để làm nổi bật badge --}}
    <style>
        /* Tinh chỉnh Badge số lượng */
        .header-actions .badge {
            top: -5px !important; /* Đẩy badge lên cao hơn */
            right: -5px !important; /* Kéo badge sát vào icon */
            background: #FFD700 !important; /* Màu vàng Gold nổi bật */
            color: var(--text-dark) !important; /* Chữ đen để dễ nhìn */
            font-weight: 700 !important;
            box-shadow: 0 0 0 2px var(--white); /* Thêm viền trắng để nổi trên nền đỏ */
        }
    </style>
</head>
<body>
<header class="header">
    <div class="header-top">
        <div class="container">
            <div class="header-top-content">
                <div class="header-left">
                    <a href="#"><i class="fas fa-phone"></i> hotline: 0962371176</a>
                    <a href="#"><i class="fas fa-map-marker-alt"></i> Tìm cửa hàng</a>
                </div>
                <div class="header-right">
                    @auth
                        <a href="{{ route('customer.profile') }}">
                            <i class="fas fa-user"></i> {{ $user->ho_ten }}
                        </a>
                        <a href="{{ route('customer.orders') }}"><i class="fas fa-receipt"></i> Đơn hàng</a>
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" style="background: none; border: none; color: inherit; cursor: pointer;">
                                <i class="fas fa-sign-out-alt"></i> Đăng xuất
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" id="loginBtn"><i class="fas fa-user"></i> Đăng nhập</a>
                        <a href="{{ route('register') }}"><i class="fas fa-user-plus"></i> Đăng ký</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
    
    <div class="header-main">
        <div class="container">
            <div class="header-main-content">
                <div class="logo">
                    <a href="{{ route('home') }}">
                        <span><img src="{{ asset('img/logo_LQT1.png') }}" alt="Logo PhoneShop" width="70px"></span>
                    </a>
                </div>
                
                {{-- KHỐI TÌM KIẾM: Sử dụng đúng cấu trúc CSS để NÚT NẰM SÁT PHẢI --}}
                <div class="search-box">
                    <form action="{{ route('products.search') }}" method="GET" style="display: flex; width: 100%;">
                        <input type="text" name="q" placeholder="Tìm kiếm điện thoại, phụ kiện..." value="{{ request('q') }}">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
                
                <div class="header-actions">
                    <a href="{{ route('customer.wishlist') }}" class="wishlist-btn">
                        <i class="far fa-heart"></i>
                        {{-- Badge số lượng được làm nổi bật bằng CSS tùy chỉnh ở trên --}}
                        <span class="badge">{{ $wishlistCount }}</span>
                    </a>
                    <a href="{{ route('cart.index') }}" class="cart-btn">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="badge">{{ $cartCount }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <nav class="navbar">
        <div class="container">
            <ul class="nav-menu">
                <li><a href="{{ route('products.category', 'dien-thoai') }}"><i class="fas fa-mobile-alt"></i> Điện thoại</a></li>                
                <li><a href="{{ route('products.category', 'phu-kien') }}"><i class="fas fa-headphones"></i> Phụ kiện</a></li>
                <li><a href="#"><i class="fas fa-empire"></i> Thu cũ đổi mới</a></li>
                <li><a href="#"><i class="fa fa-gamepad" aria-hidden="true"></i> chơi tạo voucher</a></li>
                <li class="hot"><a href="{{ route('promotions') }}"><i class="fas fa-fire"></i> Khuyến mãi</a></li>
            </ul>
        </div>
    </nav>
</header>
<main>
    @yield('content')
</main>

 <footer class="footer">
    <div class="container">
        <div class="footer-top">
            <div class="footer-col">
                <h4>Về PhoneShop</h4>
                <ul>
                    <li><a href="{{ route('about') }}">Giới thiệu công ty</a></li>
                    <li><a href="#">Tuyển dụng</a></li>
                    <li><a href="#">Tin tức</a></li>
                    <li><a href="{{ route('contact') }}">Liên hệ</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Chính sách</h4>
                <ul>
                    <li><a href="{{ route('warranty') }}">Chính sách bảo hành</a></li>
                    <li><a href="{{ route('return') }}">Chính sách đổi trả</a></li>
                    <li><a href="#">Chính sách vận chuyển</a></li>
                    <li><a href="{{ route('privacy') }}">Chính sách bảo mật</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Hỗ trợ khách hàng</h4>
                <ul>
                    <li><a href="{{ route('guide') }}">Hướng dẫn mua hàng</a></li>
                    <li><a href="#">Hướng dẫn thanh toán</a></li>
                    <li><a href="{{ route('customer.orders') }}">Tra cứu đơn hàng</a></li>
                    <li><a href="#">Câu hỏi thường gặp</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Kết nối với chúng tôi</h4>
                <div class="social-links">
                    <a href="https://www.facebook.com/quoctri.le.319" target="_blank"><i class="fab fa-facebook"></i></a>
                    <a href="#" target="_blank"><i class="fab fa-youtube"></i></a>
                    <a href="https://www.tiktok.com/@lqt254" target="_blank"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; Desginer by lê quốc trí D22_TH14. All rights reserved.</p>
        </div>
    </div>
</footer>
<div class="chat-widget">
    <button class="chat-toggle">
        <i class="fas fa-comments"></i>
    </button>
</div>

<div class="toast" id="toast"></div>

<script src="{{ asset('js/main.js') }}"></script>
<script src="{{ asset('js/home.js') }}"></script>
@stack('scripts')
</body>
</html>