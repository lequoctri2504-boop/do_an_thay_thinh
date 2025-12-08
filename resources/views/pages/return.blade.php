@extends('layouts.app')

@section('title', 'Chính sách đổi trả hàng')

@section('content')
<div class="breadcrumb">
    <div class="container">
        <a href="{{ route('home') }}">Trang chủ</a>
        <i class="fas fa-chevron-right"></i>
        <span>Chính sách đổi trả</span>
    </div>
</div>

<section class="products-page">
    <div class="container">
        <h1 style="margin-bottom: 20px;"><i class="fas fa-undo"></i> Chính sách đổi trả hàng</h1>
        
        <div class="dashboard-card" style="padding: 30px;">
            <h2>1. Thời gian áp dụng</h2>
            <p>Khách hàng có quyền đổi trả sản phẩm trong vòng <strong>7 ngày</strong> kể từ ngày nhận hàng.</p>
            
            <h2>2. Điều kiện đổi trả</h2>
            <ul>
                <li>Sản phẩm bị lỗi kỹ thuật nặng, không khắc phục được (ví dụ: lỗi màn hình, pin, camera).</li>
                <li>Sản phẩm không đúng mẫu mã, chủng loại như quý khách đã đặt hàng.</li>
                <li>Sản phẩm phải còn nguyên hộp, đầy đủ phụ kiện, không trầy xước, không bị vào nước, và còn nguyên tem bảo hành.</li>
            </ul>
            
            <h2>3. Quy trình đổi trả</h2>
            <ol>
                <li>Liên hệ ngay với bộ phận hỗ trợ khách hàng của LQTshop qua Hotline hoặc Email.</li>
                <li>Cung cấp mã đơn hàng, mô tả chi tiết lỗi/vấn đề gặp phải.</li>
                <li>Chờ xác nhận và hướng dẫn gửi trả sản phẩm.</li>
            </ol>
            
            <p class="text-danger mt-4">Chúng tôi có quyền từ chối đổi trả nếu sản phẩm không đáp ứng các điều kiện trên.</p>
        </div>
    </div>
</section>
@endsection