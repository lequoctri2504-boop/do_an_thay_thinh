<div class="account-sidebar">
    {{-- Hiển thị thông tin người dùng --}}
    <div class="user-info">
        <i class="fas fa-user-circle"></i>
        {{-- $user có sẵn từ CustomerController --}}
        <h4>Xin chào, {{ Auth::user()->ho_ten ?? 'Khách hàng' }}</h4>
        <p>Email: {{ Auth::user()->email ?? 'N/A' }}</p>
    </div>
    
    <ul class="sidebar-menu">
        {{-- Route::is() dùng để kiểm tra route hiện tại và thêm class 'active' --}}
        <li class="{{ Route::is('customer.profile') ? 'active' : '' }}">
            <a href="{{ route('customer.profile') }}"><i class="fas fa-user"></i> Thông tin tài khoản</a>
        </li>
        <li class="{{ Route::is('customer.orders') ? 'active' : '' }}">
            <a href="{{ route('customer.orders') }}"><i class="fas fa-receipt"></i> Đơn hàng của tôi</a>
        </li>
        {{-- Giả định có route cho Wishlist --}}
        <li class="{{ Route::is('customer.wishlist') ? 'active' : '' }}">
            <a href="{{ route('customer.wishlist') }}"><i class="far fa-heart"></i> Sản phẩm yêu thích</a>
        </li>
        {{-- Giả định có route cho Đánh giá --}}
        <li>
            <a href="#"><i class="fas fa-star"></i> Đánh giá của tôi</a>
        </li>
        <li>
            {{-- Form Đăng xuất --}}
            <form action="{{ route('logout') }}" method="POST" style="display: block;">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                </button>
            </form>
        </li>
    </ul>
</div>