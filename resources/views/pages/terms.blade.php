@extends('layouts.app')

@section('title', 'Điều khoản sử dụng')

@section('content')
<div class="breadcrumb">
    <div class="container">
        <a href="{{ route('home') }}">Trang chủ</a>
        <i class="fas fa-chevron-right"></i>
        <span>Điều khoản sử dụng</span>
    </div>
</div>

<section class="products-page">
    <div class="container">
        <h1 style="margin-bottom: 20px;"><i class="fas fa-file-contract"></i> Điều khoản sử dụng</h1>
        
        <div class="dashboard-card" style="padding: 30px;">
            <h2>1. Chấp nhận Điều khoản</h2>
            <p>Việc truy cập và sử dụng website PhoneShop đồng nghĩa với việc bạn đồng ý và chấp nhận mọi điều khoản sử dụng được quy định tại đây. Nếu bạn không đồng ý với bất kỳ điều khoản nào, vui lòng ngừng sử dụng dịch vụ của chúng tôi.</p>
            
            <h2>2. Hành vi người dùng</h2>
            <p>Nghiêm cấm các hành vi sau trên website:</p>
            <ul>
                <li>Sử dụng thông tin hoặc hình ảnh của PhoneShop mà không có sự cho phép bằng văn bản.</li>
                <li>Đăng tải nội dung phỉ báng, quấy rối, hoặc vi phạm pháp luật.</li>
                <li>Cố gắng truy cập trái phép vào hệ thống hoặc tài khoản người dùng khác.</li>
            </ul>
            
            <h2>3. Bản quyền</h2>
            <p>Tất cả nội dung, hình ảnh, logo và thiết kế trên website PhoneShop thuộc quyền sở hữu của PhoneShop và được bảo hộ theo luật bản quyền Việt Nam.</p>
        </div>
    </div>
</section>
@endsection