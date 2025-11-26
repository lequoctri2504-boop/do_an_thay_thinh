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
