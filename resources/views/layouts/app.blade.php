<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PhoneShop - Điện thoại chính hãng giá rẻ')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @yield('styles')
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
                            <span><img src="{{ asset('img/logo_LQT1.png') }}" alt="PhoneShop" width="70px"></span>
                        </a>
                    </div>
                    
                    <div class="search-box">
                        <input type="text" placeholder="Tìm kiếm điện thoại, phụ kiện...">
                        <button><i class="fas fa-search"></i></button>
                    </div>
                    
                    <!-- <div class="header-actions">
                        <a href="#" class="wishlist-btn">
                            <i class="far fa-heart"></i>
                            <span class="badge">0</span>
                        </a>
                        <a href="#" class="cart-btn">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="badge">0</span>
                        </a>
                    </div> -->
                    <div class="header-actions">
    <a href="#" class="wishlist-btn">
        <i class="far fa-heart"></i>
        <span class="badge">0</span>
    </a>
    
    <a href="{{ route('cart.index') }}" class="cart-btn">
        <i class="fas fa-shopping-cart"></i>
        <span class="badge">{{ session('cart') ? count(session('cart')) : 0 }}</span>
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
                    <li><a href="#"><i class="fa fa-gamepad" aria-hidden="true"></i> Chơi tạo voucher</a></li>
                    <li class="hot"><a href="#"><i class="fas fa-fire"></i> Khuyến mãi</a></li>
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

    <div class="chat-widget">
        <button class="chat-toggle">
            <i class="fas fa-comments"></i>
        </button>
    </div>
    <div class="toast" id="toast"></div>

    <script src="{{ asset('js/main.js') }}"></script>
    @yield('scripts')
</body>
</html>