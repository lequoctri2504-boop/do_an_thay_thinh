@extends('layouts.app')

@section('title', 'Chính sách bảo hành')

@section('content')
<div class="breadcrumb">
    <div class="container">
        <a href="{{ route('home') }}">Trang chủ</a>
        <i class="fas fa-chevron-right"></i>
        <span>Chính sách bảo hành</span>
    </div>
</div>

<section class="products-page">
    <div class="container">
        <h1 style="margin-bottom: 20px;"><i class="fas fa-shield-alt"></i> Chính sách bảo hành</h1>
        
        <div class="dashboard-card" style="padding: 30px;">
            <h2>1. Thời gian bảo hành</h2>
            <p>Tất cả sản phẩm điện thoại di động được bán tại LQTshop đều được bảo hành chính hãng theo thời gian quy định của nhà sản xuất, tối thiểu là 12 tháng kể từ ngày mua hàng.</p>
            
            <h2>2. Điều kiện bảo hành hợp lệ</h2>
            <ul>
                <li>Sản phẩm còn trong thời hạn bảo hành.</li>
                <li>Phiếu bảo hành hoặc hóa đơn mua hàng còn nguyên vẹn, không rách nát.</li>
                <li>Sản phẩm gặp lỗi kỹ thuật do nhà sản xuất (như lỗi mainboard, màn hình, pin).</li>
            </ul>
            
            <h2>3. Các trường hợp từ chối bảo hành</h2>
            <ul>
                <li>Sản phẩm bị hư hỏng do rơi vỡ, va đập, vào nước hoặc các tác động vật lý khác.</li>
                <li>Sản phẩm đã bị tháo lắp, sửa chữa bởi các kỹ thuật viên không được ủy quyền.</li>
                <li>Sản phẩm hết thời gian bảo hành.</li>
            </ul>
            
            <p class="text-muted mt-4">Vui lòng liên hệ Hotline 0962371176 để được hỗ trợ kiểm tra chi tiết.</p>
        </div>
    </div>
</section>
@endsection