@extends('layouts.app')

@section('title', 'Liên hệ với chúng tôi')

@section('content')
<div class="breadcrumb">
    <div class="container">
        <a href="{{ route('home') }}">Trang chủ</a>
        <i class="fas fa-chevron-right"></i>
        <span>Liên hệ</span>
    </div>
</div>

<section class="products-page">
    <div class="container">
        <h1 style="margin-bottom: 20px;"><i class="fas fa-headset"></i> Liên hệ với chúng tôi</h1>
        
        <div class="dashboard-card" style="padding: 30px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
                <div>
                    <h3>Thông tin liên hệ</h3>
                    <p><i class="fas fa-map-marker-alt" style="color: var(--primary-color);"></i> Địa chỉ: 61 Phạm Hùng, Phường 4,Quận 8 Thành phố Hồ Chí Minh</p>
                    <p><i class="fas fa-phone" style="color: var(--primary-color);"></i> Hotline: 0962371176</p>
                    <p><i class="fas fa-envelope" style="color: var(--primary-color);"></i> Email: lequoctri2504@gmail.com</p>
                    <p><i class="fas fa-clock" style="color: var(--primary-color);"></i> Giờ làm việc: 8:00 - 22:00 (Kể cả T7, CN)</p>
                </div>
                
                <form action="#" method="POST" style="border: 1px solid #eee; padding: 20px; border-radius: 8px;">
                    <h3>Gửi yêu cầu hỗ trợ</h3>
                    <div class="form-group">
                        <label>Họ tên (*)</label>
                        <input type="text" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email (*)</label>
                        <input type="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Nội dung (*)</label>
                        <textarea class="form-control" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Gửi đi</button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection