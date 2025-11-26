<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Admin - PhoneShop')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="dashboard-body">
    <!-- Header -->
    <header class="dashboard-header">
        <div class="dashboard-brand">
            <span><img src="{{ asset('img/logo_LQT1.png') }}" alt="" width="50px"></span>
            @php $u = Auth::user(); @endphp
            @if($u)
                <span class="role-badge admin">
                    Xin chào {{ $u->ho_ten }} ({{ $u->vai_tro }})
                </span>
            @else
                <span class="role-badge admin">Xin chào</span>
            @endif
        </div>
        <div class="dashboard-user">
            <div class="notifications">
                <button class="notif-btn">
                    <i class="fas fa-bell"></i>
                    <span class="badge">2</span>
                </button>
            </div>
            <div class="user-dropdown">
                <button class="user-btn">
                    <i class="fas fa-user-shield"></i>
                    <span>{{ $u->ho_ten ?? 'Admin' }}</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="dropdown-menu">
                    <a href="#"><i class="fas fa-user"></i> Thông tin cá nhân</a>
                    <a href="{{ route('admin.settings') }}"><i class="fas fa-cog"></i> Cài đặt hệ thống</a>
                    <form action="{{ route('logout') }}" method="post" style="margin:0;">
                        @csrf
                        <button type="submit" class="dropdown-item"
                                style="width:100%;text-align:left;border:none;background:none;cursor:pointer;">
                            <i class="fas fa-sign-out-alt"></i> Đăng xuất
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="dashboard-sidebar">
            <nav class="sidebar-nav">
                <a href="{{ route('admin.dashboard') }}"
                   class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>

                <div class="nav-divider">QUẢN LÝ</div>
                <a href="{{ route('admin.accounts') }}"
                   class="nav-item {{ request()->routeIs('admin.accounts') ? 'active' : '' }}">
                    <i class="fas fa-users-cog"></i>
                    <span>Tài khoản</span>
                </a>
                <a href="{{ route('admin.products') }}"
                   class="nav-item {{ request()->routeIs('admin.products') ? 'active' : '' }}">
                    <i class="fas fa-box"></i>
                    <span>Sản phẩm</span>
                </a>
                <a href="{{ route('admin.categories') }}"
                   class="nav-item {{ request()->routeIs('admin.categories') ? 'active' : '' }}">
                    <i class="fas fa-tags"></i>
                    <span>Danh mục & Thương hiệu</span>
                </a>
                <a href="{{ route('admin.orders') }}"
                   class="nav-item {{ request()->routeIs('admin.orders') ? 'active' : '' }}">
                    <i class="fas fa-shopping-bag"></i>
                    <span>Đơn hàng</span>
                </a>
                <a href="{{ route('admin.promotions') }}"
                   class="nav-item {{ request()->routeIs('admin.promotions') ? 'active' : '' }}">
                    <i class="fas fa-gift"></i>
                    <span>Khuyến mãi</span>
                </a>
                <a href="{{ route('admin.reviews') }}"
                   class="nav-item {{ request()->routeIs('admin.reviews') ? 'active' : '' }}">
                    <i class="fas fa-star"></i>
                    <span>Đánh giá</span>
                </a>

                <div class="nav-divider">BÁO CÁO</div>
                <a href="{{ route('admin.reports') }}"
                   class="nav-item {{ request()->routeIs('admin.reports') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>Báo cáo tổng hợp</span>
                </a>

                <div class="nav-divider">HỆ THỐNG</div>
                <a href="{{ route('admin.backup') }}"
                   class="nav-item {{ request()->routeIs('admin.backup') ? 'active' : '' }}">
                    <i class="fas fa-database"></i>
                    <span>Sao lưu & Khôi phục</span>
                </a>
                <a href="{{ route('admin.settings') }}"
                   class="nav-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i>
                    <span>Cài đặt</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="dashboard-main">
            @yield('content')
        </main>
    </div>

    <script src="{{ asset('js/main.js') }}"></script>
</body>
</html>
