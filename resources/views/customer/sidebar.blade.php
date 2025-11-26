<div class="account-sidebar">
    <div class="user-info">
        <div class="user-avatar">
            <i class="fas fa-user-circle"></i>
        </div>
        <h3>{{ Auth::user()->ho_ten }}</h3>
        <p>{{ Auth::user()->email }}</p>
    </div>

    <div class="account-menu">
        <a href="{{ route('customer.profile') }}" class="menu-item {{ request()->routeIs('customer.profile') ? 'active' : '' }}">
            <i class="fas fa-user"></i>
            <span>Thông tin cá nhân</span>
        </a>
        <a href="{{ route('customer.orders') }}" class="menu-item {{ request()->routeIs('customer.orders') ? 'active' : '' }}">
            <i class="fas fa-shopping-bag"></i>
            <span>Đơn hàng của tôi</span>
        </a>
        <a href="{{ route('customer.wishlist') }}" class="menu-item {{ request()->routeIs('customer.wishlist') ? 'active' : '' }}">
            <i class="fas fa-heart"></i>
            <span>Yêu thích</span>
        </a>
        <a href="{{ route('customer.reviews') }}" class="menu-item {{ request()->routeIs('customer.reviews') ? 'active' : '' }}">
            <i class="fas fa-star"></i>
            <span>Đánh giá</span>
        </a>
        <form action="{{ route('logout') }}" method="post">
            @csrf
            <button type="submit" class="menu-item" style="width:100%; border:none; background:none; text-align:left; cursor:pointer;">
                <i class="fas fa-sign-out-alt"></i>
                <span>Đăng xuất</span>
            </button>
        </form>
    </div>
</div>