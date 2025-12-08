@extends('layouts.app')

@section('title', 'Giới thiệu về LQTshop')

@section('content')
<div class="breadcrumb">
    <div class="container">
        <a href="{{ route('home') }}">Trang chủ</a>
        <i class="fas fa-chevron-right"></i>
        <span>Giới thiệu</span>
    </div>
</div>

<section class="products-page">
    <div class="container">
        <h1 style="margin-bottom: 20px;"><i class="fas fa-info-circle"></i> Giới thiệu về LQTshop</h1>
        
        <div class="dashboard-card" style="padding: 30px;">
            <h2>Uy tín - Chất lượng - Giá tốt nhất</h2>
            <p>LQTshop được thành lập với sứ mệnh mang đến những sản phẩm điện thoại di động chính hãng, chất lượng cao với mức giá cạnh tranh nhất trên thị trường.</p>
            
            <h3>Giá trị cốt lõi</h3>
            <ul>
                <li><strong>Khách hàng là trọng tâm:</strong> Luôn đặt lợi ích và sự hài lòng của khách hàng lên hàng đầu.</li>
                <li><strong>Sản phẩm chính hãng:</strong> Cam kết 100% sản phẩm được nhập khẩu chính ngạch và có nguồn gốc rõ ràng.</li>
                <li><strong>Dịch vụ tận tâm:</strong> Đội ngũ nhân viên tư vấn chuyên nghiệp, hỗ trợ 24/7.</li>
            </ul>
            
            <p class="text-muted mt-4">Cảm ơn quý khách đã tin tưởng và đồng hành cùng LQTshop!</p>
        </div>
    </div>
</section>
@endsection