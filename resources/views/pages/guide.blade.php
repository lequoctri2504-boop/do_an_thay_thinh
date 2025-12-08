@extends('layouts.app')

@section('title', 'Hướng dẫn mua hàng')

@section('content')
<div class="breadcrumb">
    <div class="container">
        <a href="{{ route('home') }}">Trang chủ</a>
        <i class="fas fa-chevron-right"></i>
        <span>Hướng dẫn mua hàng</span>
    </div>
</div>

<section class="products-page">
    <div class="container">
        <h1 style="margin-bottom: 20px;"><i class="fas fa-book"></i> Hướng dẫn mua hàng</h1>
        
        <div class="dashboard-card" style="padding: 30px;">
            <h2>Quy trình mua hàng 4 bước đơn giản</h2>
            
            <ol style="margin-left: 20px; line-height: 2;">
                <li><strong>Tìm kiếm và chọn sản phẩm:</strong> Duyệt qua các danh mục hoặc sử dụng thanh tìm kiếm để tìm sản phẩm bạn cần. Truy cập trang chi tiết sản phẩm, chọn màu sắc/dung lượng (nếu có).</li>
                <li><strong>Thêm vào giỏ hàng:</strong> Nhấp vào nút Thêm vào giỏ hàng</span> hoặc <span class="btn btn-primary btn-sm">Mua ngay</span>. (Lưu ý: Nếu chưa đăng nhập, hệ thống sẽ yêu cầu bạn đăng nhập trước).</li>
                <li><strong>Hoàn tất thanh toán:</strong> Kiểm tra lại giỏ hàng, nhập mã giảm giá (nếu có), điền thông tin người nhận và địa chỉ giao hàng chi tiết tại trang thanh toán. Chọn phương thức thanh toán (COD, ZaloPay,...) và nhấp <span class="btn btn-primary btn-sm">Hoàn tất đặt hàng</span>.</li>
                <li><strong>Nhận hàng và xác nhận:</strong> LQTshop sẽ tiến hành xử lý và giao hàng cho bạn. Sau khi nhận hàng, bạn có thể kiểm tra trạng thái đơn hàng trong mục Tài khoản > Đơn hàng của tôi.</li>
            </ol>
            
            <p class="text-muted mt-4">Nếu gặp khó khăn trong quá trình đặt hàng, vui lòng liên hệ Hotline 0962371176.</p>
        </div>
    </div>
</section>
@endsection