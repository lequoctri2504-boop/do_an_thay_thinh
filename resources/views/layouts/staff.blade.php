<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Nhân viên - PhoneShop')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="dashboard-body">
@php $u = Auth::user(); @endphp

<!-- Header -->
<header class="dashboard-header">
    <div class="dashboard-brand">
        <span><img src="{{ asset('img/logo_LQT1.png') }}" alt="" width="50px"></span>

        @if($u)
            <span class="role-badge staff">
                Xin chào {{ $u->ho_ten }} ({{ $u->vai_tro }})
            </span>
        @else
            <span class="role-badge staff">Xin Chào Nhân viên</span>
        @endif
    </div>

    <div class="dashboard-user">
        <div class="notifications">
            <button class="notif-btn">
                <i class="fas fa-bell"></i>
                <span class="badge">5</span>
            </button>
        </div>
        <div class="user-dropdown">
            <button class="user-btn">
                <i class="fas fa-user-circle"></i>
                <span>{{ $u->ho_ten ?? 'Nhân viên' }}</span>
                <i class="fas fa-chevron-down"></i>
            </button>
            <div class="dropdown-menu">
                <a href="#"><i class="fas fa-user"></i> Thông tin cá nhân</a>
                <a href="#"><i class="fas fa-cog"></i> Cài đặt</a>
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
            <a href="{{ route('staff.dashboard') }}"
               class="nav-item {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i>
                <span>Tổng quan</span>
            </a>

            <a href="{{ route('staff.orders') }}"
               class="nav-item {{ request()->routeIs('staff.orders') ? 'active' : '' }}">
                <i class="fas fa-shopping-bag"></i>
                <span>Quản lý đơn hàng</span>
            </a>

            <a href="{{ route('staff.products') }}"
               class="nav-item {{ request()->routeIs('staff.products') ? 'active' : '' }}">
                <i class="fas fa-box"></i>
                <span>Quản lý sản phẩm</span>
            </a>

            <a href="{{ route('staff.customers') }}"
               class="nav-item {{ request()->routeIs('staff.customers') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Quản lý khách hàng</span>
            </a>

            <a href="{{ route('staff.reports') }}"
               class="nav-item {{ request()->routeIs('staff.reports') ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i>
                <span>Báo cáo - Thống kê</span>
            </a>

            <a href="{{ route('staff.support') }}"
               class="nav-item {{ request()->routeIs('staff.support') ? 'active' : '' }}">
                <i class="fas fa-headset"></i>
                <span>Hỗ trợ khách hàng</span>
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
