@extends('layouts.app')

@section('title', 'Thanh toán')

@section('content')


<section class="checkout-page">
    <div class="container">
        {{-- Hiển thị thông báo lỗi/thành công --}}
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
        </div>
        @endif

        <form action="{{ route('orders.place') }}" method="POST" id="checkoutForm" class="checkout-layout">
            @csrf
            <div class="checkout-form">

                <div class="form-section">
                    <h2><i class="fas fa-user"></i> Thông tin giao hàng</h2>

                    <div class="form-group">
                        <label>Họ và tên người nhận <span class="required">*</span></label>
                        <input type="text" name="ten_nguoi_nhan" class="form-control @error('ten_nguoi_nhan') is-invalid @enderror"
                            value="{{ old('ten_nguoi_nhan', $user->ho_ten) }}" placeholder="Nhập họ và tên" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Số điện thoại <span class="required">*</span></label>
                            <input type="tel" name="sdt_nguoi_nhan" class="form-control @error('sdt_nguoi_nhan') is-invalid @enderror"
                                value="{{ old('sdt_nguoi_nhan', $user->sdt) }}" placeholder="0xxx xxx xxx" required>
                        </div>
                        
                        {{-- FIX: Thêm trường email KHÔNG bị disabled để giá trị được gửi đi --}}
                        <div class="form-group">
                            <label>Email xác nhận đơn hàng <span class="required">*</span></label>
                            <input type="email" name="email_khach" class="form-control @error('email_khach') is-invalid @enderror" 
                                value="{{ old('email_khach', $user->email) }}" placeholder="Nhập email của bạn" required>
                            @error('email_khach')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Địa chỉ giao hàng <span class="required">*</span></label>
                        <textarea name="dia_chi_giao" rows="3" class="form-control @error('dia_chi_giao') is-invalid @enderror"
                            placeholder="Số nhà, tên đường, Phường/Xã, Quận/Huyện, Tỉnh/Thành phố..."
                            required>{{ old('dia_chi_giao', $user->dia_chi) }}</textarea>
                        @error('dia_chi_giao')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Ghi chú đơn hàng</label>
                        <textarea name="ghi_chu" class="form-control" rows="2"
                            placeholder="Ghi chú về đơn hàng, ví dụ: thời gian hay chỉ dẫn địa điểm giao hàng chi tiết hơn">{{ old('ghi_chu') }}</textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h2><i class="fas fa-credit-card"></i> Phương thức thanh toán</h2>
                    <div class="payment-methods">

                        {{-- Thanh toán khi nhận hàng (COD) --}}
                        <label class="payment-option">
                            <input type="radio" name="phuong_thuc_tt" id="cod" value="COD" checked>
                            <div class="payment-content">
                                <div class="payment-icon"><i class="fas fa-money-bill-wave"></i></div>
                                <div class="payment-info">
                                    <strong>Thanh toán khi nhận hàng (COD)</strong>
                                    <p>Thanh toán bằng tiền mặt khi nhận hàng</p>
                                </div>
                            </div>
                        </label>

                        {{-- Thanh toán VNPAY --}}
                        <label class="payment-option">
                            <input class="form-check-input" type="radio" name="phuong_thuc_tt" id="vnpay" value="VNPAY">
                            <div class="payment-content">
                                <div class="payment-icon"><i class="fas fa-qrcode"></i></div>
                                <div class="payment-info">
                                    <strong>VNPAY</strong>
                                    <p>Thanh toán online qua cổng VNPAY</p>
                                </div>
                            </div>
                        </label>

                    </div>
                </div>

                <div class="form-section">
                    <label class="checkbox-wrapper">
                        {{-- Input required để xác nhận đồng ý điều khoản --}}
                        <input type="checkbox" required>
                        <span>Tôi đã đọc và đồng ý với <a href="{{ route('terms') }}">Điều khoản sử dụng</a> và <a href="{{ route('privacy') }}">Chính sách bảo mật</a></span>
                    </label>
                </div>

                {{-- Nút đặt hàng nằm ở Order Summary --}}

            </div>

            <div class="order-summary">
                <h3>Đơn hàng của bạn</h3>

                <div class="order-items">
                    @foreach($cartItems as $item)
                    @php
                    $sanPham = $item->bienThe->sanPham;
                    $bienThe = $item->bienThe;
                    $anhChinh = $sanPham->hinh_anh_mac_dinh ? asset('uploads/' . $sanPham->hinh_anh_mac_dinh) : 'https://via.placeholder.com/80';
                    $itemSubtotal = $bienThe->gia * $item->so_luong;
                    @endphp
                    <div class="order-item">
                        <img src="{{ $anhChinh }}" alt="{{ $sanPham->ten }}">
                        <div class="order-item-info">
                            <h4>{{ $sanPham->ten }}</h4>
                            <p>
                                @if($bienThe->mau_sac) Màu: {{ $bienThe->mau_sac }} @endif
                                @if($bienThe->dung_luong_gb) | {{ $bienThe->dung_luong_gb }}GB @endif
                                | Số lượng: {{ $item->so_luong }}
                            </p>
                        </div>
                        <div class="order-item-price">{{ number_format($itemSubtotal, 0, ',', '.') }}₫</div>
                    </div>
                    @endforeach
                </div>

                {{-- FORM ÁP DỤNG MÃ GIẢM GIÁ (Đã sửa) --}}
                <form action="{{ route('orders.apply.discount') }}" method="POST" style="margin-bottom: 20px;">
                    @csrf
                    @if($discountCode && $discountAmount > 0)
                    <div class="alert alert-success" style="padding: 10px; margin-bottom: 10px; font-size: 14px; color: var(--success-color); border: 1px solid var(--success-color);">
                        Đã áp dụng mã **{{ $discountCode }}**.
                        {{-- Nút Hủy Mã giảm giá --}}
                        <input type="hidden" name="coupon_code" value="">
                        <button type="submit" name="remove_coupon" value="1" class="btn-link text-danger" style="font-size: 14px; padding: 0; display: inline;">(Hủy)</button>
                    </div>
                    @endif
                    <div class="voucher-section" style="display: flex; gap: 10px;">
                        <input type="text" name="coupon_code" placeholder="Nhập mã giảm giá" class="form-control" value="{{ $discountCode ?? '' }}" @if($discountCode) disabled @endif>
                        <button type="submit" class="btn btn-secondary" @if($discountCode) disabled @endif>Áp dụng</button>
                    </div>
                </form>

                <div class="order-totals">
                    <div class="total-row">
                        <span>Tạm tính:</span>
                        <span>{{ number_format($subtotal, 0, ',', '.') }}₫</span>
                    </div>
                    <div class="total-row">
                        <span>Giảm giá:</span>
                        <span class="text-danger">-{{ number_format($discountAmount, 0, ',', '.') }}₫</span>
                    </div>
                    <div class="total-row">
                        <span>Phí vận chuyển:</span>
                        <span class="@if($shippingFee == 0) text-success @else text-danger @endif">
                            {{ $shippingFee == 0 ? 'Miễn phí' : number_format($shippingFee, 0, ',', '.') . '₫' }}
                        </span>
                    </div>
                    <div class="total-row grand-total">
                        <span>Tổng cộng:</span>
                        <span>{{ number_format($total, 0, ',', '.') }}₫</span>
                    </div>
                </div>

                <button type="submit" form="checkoutForm" class="btn btn-primary btn-block btn-large">
                    <i class="fas fa-check-circle"></i> Hoàn tất đặt hàng
                </button>

                <div class="security-badges">
                    <div class="badge-item">
                        <i class="fas fa-shield-alt"></i>
                        <span>Thanh toán an toàn</span>
                    </div>
                    <div class="badge-item">
                        <i class="fas fa-lock"></i>
                        <span>Bảo mật thông tin</span>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

{{-- Footer của checkout.html chỉ có footer-bottom, nhưng ta sẽ dùng footer của layouts.app --}}
@endsection

@push('scripts')
<script>
    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        // Tắt nút submit để tránh double click và báo hiệu đang xử lý
        const btn = this.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';
    });
</script>
@endpush