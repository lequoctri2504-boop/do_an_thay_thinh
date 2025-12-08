@extends('layouts.app')

@section('title', 'Chính sách bảo mật thông tin')

@section('content')
<div class="breadcrumb">
    <div class="container">
        <a href="{{ route('home') }}">Trang chủ</a>
        <i class="fas fa-chevron-right"></i>
        <span>Chính sách bảo mật</span>
    </div>
</div>

<section class="products-page">
    <div class="container">
        <h1 style="margin-bottom: 20px;"><i class="fas fa-lock"></i> Chính sách bảo mật thông tin</h1>
        
        <div class="dashboard-card" style="padding: 30px;">
            <h2>1. Mục đích thu thập thông tin</h2>
            <p>LQTshop thu thập thông tin cá nhân của khách hàng (họ tên, email, SĐT, địa chỉ) nhằm mục đích xử lý đơn hàng, giao hàng, hỗ trợ khách hàng và gửi các thông báo về chương trình khuyến mãi (khi có sự đồng ý của khách hàng).</p>
            
            <h2>2. Cam kết bảo mật</h2>
            <p>Chúng tôi cam kết không tiết lộ, bán hoặc trao đổi thông tin cá nhân của khách hàng cho bất kỳ bên thứ ba nào, trừ các trường hợp sau:</p>
            <ul>
                <li>Chia sẻ thông tin với đơn vị vận chuyển để phục vụ việc giao hàng.</li>
                <li>Cung cấp thông tin theo yêu cầu hợp pháp của cơ quan nhà nước có thẩm quyền.</li>
            </ul>
            
            <h2>3. Bảo mật mật khẩu</h2>
            <p>Mật khẩu của quý khách được lưu trữ dưới dạng mã hóa một chiều (hashing) và không thể bị giải mã ngược. Quý khách vui lòng tự bảo quản thông tin tài khoản của mình.</p>
        </div>
    </div>
</section>
@endsection