<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - PhoneShop</title>
    <link rel="stylesheet" href="{{('css/style.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="dashboard-body">
    <!-- Dashboard Header -->
    <header class="dashboard-header">
        <div class="dashboard-brand">         
            <span><img src="{{('img/logo_LQT1.png')}}" alt="" width="50px"></span>
            <span class="role-badge admin">Xin Chào Admin</span>
        </div>
        <div class="dashboard-user">
            <div class="notifications">
                <button class="notif-btn">
                    <i class="fas fa-bell"></i>
                    <span class="badge">8</span>
                </button>
            </div>
            <div class="user-dropdown">
                <button class="user-btn">
                    <i class="fas fa-user-shield"></i>
                    <span>Admin</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="dropdown-menu">
                    <a href="#"><i class="fas fa-user"></i> Thông tin cá nhân</a>
                    <a href="#"><i class="fas fa-cog"></i> Cài đặt hệ thống</a>
                    <a href="index.html"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                </div>
            </div>
        </div>
    </header>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="dashboard-sidebar">
            <nav class="sidebar-nav">
                <a href="#" class="nav-item active" data-section="overview">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <div class="nav-divider">QUẢN LÝ</div>
                <a href="{{ route('admin.accounts') }}" class="nav-item" data-section="accounts">
                    <i class="fas fa-users-cog"></i>
                    <span>Tài khoản</span>
                </a>
                <a href="#" class="nav-item" data-section="products">
                    <i class="fas fa-box"></i>
                    <span>Sản phẩm</span>
                </a>
                <a href="#" class="nav-item" data-section="categories">
                    <i class="fas fa-tags"></i>
                    <span>Danh mục & Thương hiệu</span>
                </a>
                <a href="#" class="nav-item" data-section="orders">
                    <i class="fas fa-shopping-bag"></i>
                    <span>Đơn hàng</span>
                    <span class="badge-count">24</span>
                </a>
                <a href="#" class="nav-item" data-section="promotions">
                    <i class="fas fa-gift"></i>
                    <span>Khuyến mãi</span>
                </a>
                <a href="#" class="nav-item" data-section="reviews">
                    <i class="fas fa-star"></i>
                    <span>Đánh giá</span>
                </a>
                <div class="nav-divider">BÁO CÁO</div>
                <a href="#" class="nav-item" data-section="reports">
                    <i class="fas fa-chart-line"></i>
                    <span>Báo cáo tổng hợp</span>
                </a>
                
                <div class="nav-divider">HỆ THỐNG</div>
                <a href="#" class="nav-item" data-section="backup">
                    <i class="fas fa-database"></i>
                    <span>Sao lưu & Khôi phục</span>
                </a>
                <a href="#" class="nav-item" data-section="settings">
                    <i class="fas fa-cog"></i>
                    <span>Cài đặt</span>
                </a>
            </nav>
        </aside>

        @yield('content')
    </div>

    <script src="{{('main.js')}}"></script>
</body>
</html>