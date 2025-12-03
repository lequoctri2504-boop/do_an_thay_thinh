@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng #' . $order->ma)

@section('content')
    <header class="">
        <div class="container">

            <div class="checkout-steps">
                <div class="step active">
                    <span class="step-number">1</span>
                    <span class="step-text">Giỏ hàng</span>
                </div>
                <div class="step active">
                    <span class="step-number">2</span>
                    <span class="step-text">Thanh toán</span>
                </div>
                <div class="step active"> 
                    <span class="step-number">3</span>
                    <span class="step-text">Hoàn thành</span>
                </div>
            </div>
        </div>
    </header>

    <section class="order-show-page">
        <div class="container">
            {{-- Thông báo thành công --}}
            @if(session('success'))
            <div class="alert-success-box">
                <i class="fas fa-check-circle success-icon"></i>
                <h2>{{ session('success') }}</h2>
                <p>Mã đơn hàng của bạn là: <strong>#{{ $order->ma }}</strong></p>
            </div>
            @endif

            <div class="order-show-layout">
                <div class="order-info-column">
                    <div class="info-section status-box">
                        <h3><i class="fas fa-receipt"></i> Trạng thái & Tổng quan</h3>
                        <div class="status-detail">
                            <p><strong>Mã đơn hàng:</strong> <span>#{{ $order->ma }}</span></p>
                            <p><strong>Ngày đặt:</strong> <span>{{ \Carbon\Carbon::parse($order->ngay_dat)->format('H:i:s d/m/Y') }}</span></p>
                            <p><strong>Trạng thái ĐH:</strong> <span class="badge status-{{ strtolower($order->trang_thai) }}">{{ $order->trang_thai }}</span></p>
                            <p><strong>Trạng thái TT:</strong> <span class="badge payment-{{ strtolower($order->trang_thai_tt) }}">{{ $order->trang_thai_tt }}</span></p>
                        </div>
                    </div>
                    
                    <div class="info-section shipping-info">
                        <h3><i class="fas fa-truck"></i> Thông tin giao hàng</h3>
                        <p><strong>Người nhận:</strong> {{ $order->ten_nguoi_nhan }}</p>
                        <p><strong>SĐT:</strong> {{ $order->sdt_nguoi_nhan }}</p>
                        <p><strong>Địa chỉ:</strong> {{ $order->dia_chi_giao }}</p>
                    </div>

                    <div class="info-section payment-info">
                        <h3><i class="fas fa-credit-card"></i> Phương thức thanh toán</h3>
                        <p><strong>Phương thức:</strong> {{ $order->phuong_thuc_tt == 'COD' ? 'Thanh toán khi nhận hàng (COD)' : ($order->phuong_thuc_tt == 'ZALOPAY' ? 'ZaloPay' : 'Chuyển khoản Ngân hàng') }}</p>
                        @if ($order->ghi_chu)
                            <p><strong>Ghi chú:</strong> <em>{{ $order->ghi_chu }}</em></p>
                        @endif
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="{{ route('customer.orders') }}" class="btn btn-secondary">
                            <i class="fas fa-list"></i> Xem tất cả đơn hàng
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
                        </a>
                    </div>
                </div>

                <div class="order-details-column">
                    <div class="order-summary-box">
                        <h3><i class="fas fa-box-open"></i> Chi tiết các sản phẩm</h3>
                        
                        <div class="product-list">
                            @foreach($order->chiTiet as $item)
                                @php
                                    $sanPham = $item->bienThe->sanPham ?? null;
                                    // Sửa lỗi: Cần kiểm tra $item->bienThe có tồn tại không trước khi truy cập gia
                                    $itemPrice = $item->bienThe ? $item->bienThe->gia : $item->gia;
                                    $anhChinh = $sanPham && $sanPham->hinh_anh_mac_dinh ? asset('uploads/' . $sanPham->hinh_anh_mac_dinh) : 'https://via.placeholder.com/60';
                                    $itemTotal = $itemPrice * $item->so_luong;
                                @endphp
                                <div class="product-item">
                                    <img src="{{ $anhChinh }}" alt="{{ $item->ten_sp_ghi_nhan }}">
                                    <div class="product-item-info">
                                        <h4>{{ $item->ten_sp_ghi_nhan }}</h4>
                                        <p>
                                            @if($item->bienThe) 
                                                @if($item->bienThe->mau_sac) Màu: {{ $item->bienThe->mau_sac }} @endif
                                                @if($item->bienThe->dung_luong_gb) | {{ $item->bienThe->dung_luong_gb }}GB @endif
                                            @endif
                                            <br>
                                            <span class="quantity">x {{ $item->so_luong }}</span>
                                        </p>
                                    </div>
                                    <div class="product-item-price">{{ number_format($itemTotal, 0, ',', '.') }}₫</div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Order Totals --}}
                        <div class="order-totals">
                            <div class="total-row">
                                <span>Tạm tính:</span>
                                {{-- Sử dụng biến subtotal đã tính trong controller (hoặc $order->tong_tien nếu tong_tien là subtotal) --}}
                                <span>{{ number_format($order->subtotal ?? $order->tong_tien, 0, ',', '.') }}₫</span>
                            </div>
                            <div class="total-row">
                                <span>Giảm giá:</span>
                                <span class="text-danger">-{{ number_format($order->giam_gia, 0, ',', '.') }}₫</span> 
                            </div>
                            <div class="total-row">
                                <span>Phí vận chuyển:</span>
                                <span class="@if($order->phi_van_chuyen == 0) text-success @else text-danger @endif">
                                    {{ $order->phi_van_chuyen == 0 ? 'Miễn phí' : number_format($order->phi_van_chuyen, 0, ',', '.') . '₫' }}
                                </span>
                            </div>
                            <div class="total-row grand-total">
                                <span>Tổng thanh toán:</span>
                                <span class="price-final">{{ number_format($order->thanh_tien, 0, ',', '.') }}₫</span>
                            </div>
                        </div>

                        <div class="security-badges mt-3">
                            <div class="badge-item">
                                <i class="fas fa-shield-alt"></i>
                                <span>Giao dịch đã được ghi nhận an toàn</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<style>
    
    .order-show-page {
        padding: 50px 0;
        background-color: #f5f5f7;
    }
    
    /* ENFORCE HORIZONTAL LAYOUT */
    .order-show-layout {
        display: grid;
        grid-template-columns: 2fr 3fr; /* Chia 2 cột: Cột Trái (2 phần) và Cột Phải (3 phần) */
        gap: 30px;
        align-items: flex-start; /* Giữ các cột cố định ở phía trên */
    }

    @media (max-width: 992px) {
        /* Trên tablet/màn hình nhỏ, chuyển về bố cục xếp chồng */
        .order-show-layout {
            grid-template-columns: 1fr;
        }
    }
    
    /* Thông báo thành công */
    .alert-success-box {
        text-align: center; padding: 30px; margin-bottom: 30px; border-radius: 12px; background-color: #e6ffed; 
        border: 1px solid #c8e6c9; color: #1b5e20;
    }
    .success-icon { font-size: 40px; color: #4caf50; margin-bottom: 15px; }
    .alert-success-box h2 { font-size: 24px; margin-bottom: 10px; color: #1b5e20; }

    /* Info Sections */
    .info-section {
        background-color: #fff; padding: 20px 25px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #eee;
    }
    .info-section h3 {
        font-size: 18px; color: var(--primary-color); border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px;
    }
    .info-section p { margin-bottom: 5px; font-size: 14px; }
    .info-section strong { display: inline-block; min-width: 120px; font-weight: 600; color: #333; }
    
    /* Order Details (Right Column) */
    .order-summary-box {
        background-color: #fff; padding: 25px; border-radius: 8px; border: 1px solid #eee;
        position: sticky; top: 20px;
    }
    .product-list {
        border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 15px;
    }
    .product-item { display: flex; gap: 10px; padding: 10px 0; align-items: center; border-bottom: 1px dashed #f0f0f0; }
    .product-item:last-child { border-bottom: none; }
    .product-item img { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; }
    .product-item-info { flex-grow: 1; }
    .product-item-info h4 { font-size: 14px; margin: 0; color: #333; }
    
    /* Totals */
    .total-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 15px; }
    .grand-total {
        border-top: 2px solid #ddd; padding-top: 15px; margin-top: 15px !important; font-size: 20px; font-weight: bold;
    }
    .price-final { color: var(--primary-color); }
</style>
@endsection