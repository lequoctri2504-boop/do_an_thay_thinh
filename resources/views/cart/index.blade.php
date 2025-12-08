@extends('layouts.app')

@section('title', 'Giỏ hàng')

@section('content')
<div class="breadcrumb">
    <div class="container">
        <a href="{{ route('home') }}">Trang chủ</a>
        <i class="fas fa-chevron-right"></i>
        <span>Giỏ hàng</span>
    </div>
</div>

<section class="cart-page">
    <div class="container">
        <h1><i class="fas fa-shopping-cart"></i> Giỏ hàng của bạn</h1>

        @if(session('success') && !Session::has('discount_code'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            </div>
        @endif

        @if($cartItems->isEmpty())
            {{-- Giao diện Giỏ hàng trống --}}
            <div class="empty-cart-message text-center py-5" style="padding: 60px 0; border: 1px dashed #D70018; border-radius: 12px; margin: 40px 0; background: #fff5f5;">
                <i class="fas fa-box-open" style="font-size: 80px; color: #D70018; margin-bottom: 20px;"></i>
                <h2 style="font-size: 24px; color: #333; margin-bottom: 10px;">Giỏ hàng của bạn đang trống!</h2>
                <p class="text-muted" style="font-size: 16px; margin-bottom: 30px;">Hãy bắt đầu mua sắm để khám phá những sản phẩm tuyệt vời.</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary btn-large">
                    <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
                </a>
            </div>
        @else
            <div class="cart-layout">
                <div class="cart-items">
                    @foreach($cartItems as $item)
                        @php
                            $sanPham = $item->bienThe->sanPham;
                            $bienThe = $item->bienThe;
                            $anhChinh = $sanPham->hinh_anh_mac_dinh ? asset('uploads/' . $sanPham->hinh_anh_mac_dinh) : 'https://via.placeholder.com/150';
                            $itemSubtotal = $bienThe->gia * $item->so_luong;
                            $hasComparePrice = $bienThe->gia_so_sanh && $bienThe->gia_so_sanh > $bienThe->gia;
                        @endphp
                        
                        <div class="cart-item" id="cart-item-{{ $item->id }}">
                            <div class="item-image">
                                <img src="{{ $anhChinh }}" alt="{{ $sanPham->ten }}">
                            </div>
                            <div class="item-info">
                                <h3><a href="{{ route('products.show', $sanPham->slug) }}">{{ $sanPham->ten }}</a></h3>
                                <p class="item-variant">
                                    @if($bienThe->mau_sac)
                                        Màu: {{ $bienThe->mau_sac }}
                                    @endif
                                    @if($bienThe->dung_luong_gb)
                                        | Dung lượng: {{ $bienThe->dung_luong_gb }}GB
                                    @endif
                                </p>
                                <div class="item-actions">
                                    <button class="btn-link btn-remove text-danger" 
                                            data-item-id="{{ $item->id }}">
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                </div>
                            </div>
                            <div class="item-quantity">
                                <button class="qty-btn" type="button" data-action="decrease" data-item-id="{{ $item->id }}">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" 
                                       class="quantity-input" 
                                       value="{{ $item->so_luong }}" 
                                       min="1" 
                                       max="{{ $bienThe->ton_kho }}"
                                       data-item-id="{{ $item->id }}"
                                       data-price="{{ $bienThe->gia }}"
                                       id="qty-input-{{ $item->id }}">
                                <button class="qty-btn" type="button" data-action="increase" data-item-id="{{ $item->id }}">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <div class="item-price">
                                <span class="price-current item-subtotal" id="subtotal-{{ $item->id }}">
                                    {{ number_format($itemSubtotal, 0, ',', '.') }}₫
                                </span>
                                @if($hasComparePrice)
                                    <span class="price-original">
                                        {{ number_format($bienThe->gia_so_sanh, 0, ',', '.') }}₫
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    {{-- Nút Xóa toàn bộ --}}
                    <div class="text-center pt-3 mt-3">
                        <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa toàn bộ giỏ hàng?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-large">
                                <i class="fas fa-trash-alt me-2"></i>Xóa toàn bộ giỏ hàng
                            </button>
                        </form>
                    </div>
                </div>

                <div class="cart-summary">
                    <h3>Thông tin đơn hàng</h3>
                    
                    <div class="summary-row">
                        <span>Tạm tính:</span>
                        <span id="cart-subtotal">{{ number_format($subtotal, 0, ',', '.') }}₫</span>
                    </div>

                    {{-- FORM ÁP DỤNG MÃ GIẢM GIÁ --}}
                    <form action="{{ route('cart.apply.discount') }}" method="POST" style="margin: 20px 0;">
                        @csrf
                        
                        {{-- Hiển thị thông báo khi áp dụng mã thành công --}}
                        @if(session('success') && Session::has('discount_code'))
                            <div class="alert alert-success" style="padding: 10px; margin-bottom: 10px; font-size: 14px; color: var(--success-color); border: 1px solid var(--success-color);">
                                <i class="fas fa-check-circle"></i> {{ session('success') }}
                                <input type="hidden" name="coupon_code" value="">
                                <button type="submit" name="remove_coupon" value="1" class="btn-link text-danger" style="font-size: 14px; padding: 0; display: inline; margin-left: 10px;">(Hủy)</button>
                            </div>
                        @endif

                        <div class="voucher-input">
                            <input type="text" name="coupon_code" placeholder="Nhập mã giảm giá" class="form-control" value="{{ $discountCode ?? '' }}" @if($discountCode) disabled @endif>
                            <button type="submit" class="btn btn-secondary" @if($discountCode) disabled @endif>Áp dụng</button>
                        </div>
                    </form>
                    
                    {{-- Dòng Giảm giá --}}
                    <div class="summary-row">
                        <span>Giảm giá:</span>
                        <span class="text-danger">-{{ number_format($discountAmount, 0, ',', '.') }}₫</span>
                    </div>

                    <div class="summary-row">
                        <span>Phí vận chuyển:</span>
                        <span class="@if($shippingFee == 0) text-success @else text-danger @endif">
                            @if($shippingFee == 0)
                                Miễn phí
                            @else
                                {{ number_format($shippingFee, 0, ',', '.') }}₫
                            @endif
                        </span>
                    </div>
                    
                    <div class="summary-total">
                        <span>Tổng cộng:</span>
                        <span class="total-price" id="cart-total">{{ number_format($total, 0, ',', '.') }}₫</span>
                    </div>

                    {{-- CHUYỂN TRANG THANH TOÁN --}}
                    <a href="{{ route('orders.checkout') }}" class="btn btn-primary btn-block btn-large">
                        <i class="fas fa-credit-card"></i> Tiến hành thanh toán
                    </a>
                    <a href="{{ route('home') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-arrow-left"></i> Tiếp tục mua hàng
                    </a>

                    <div class="payment-methods-info">
                        <p><strong>Chấp nhận thanh toán:</strong></p>
                        <div class="payment-icons">              
                            <img src="{{ asset('img/zalopay.jpg') }}" alt="ZaloPay" height="20">
                        </div>
                    </div>
                </div>
            </div>
        @endif
        
       
        <div class="suggested-products">
            <div class="section-header">
                <h2><i class="fas fa-lightbulb"></i> CÓ THỂ BẠN QUAN TÂM</h2>
            </div>
            <p>Sản phẩm gợi ý sẽ được hiển thị ở đây .</p>
        </div>
        
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // Hàm format tiền tệ (Đảm bảo có sẵn)
    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + '₫';
    }


    // Hàm cập nhật số lượng
    function updateQuantity(itemId, newQty) {
        fetch('{{ url('gio-hang') }}/' + itemId, { 
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                so_luong: newQty
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const input = document.querySelector('.quantity-input[data-item-id="' + itemId + '"]');
                if (input) input.value = data.new_qty; 

                // Cập nhật giá sản phẩm riêng lẻ
                const subtotalElement = document.getElementById('subtotal-' + itemId);
                if (subtotalElement) subtotalElement.textContent = formatCurrency(data.item_subtotal); // <-- Cập nhật giá SP
                
                // Cập nhật giá tổng cộng
                document.getElementById('cart-subtotal').textContent = formatCurrency(data.cart_subtotal);
                document.getElementById('cart-total').textContent = formatCurrency(data.cart_total);
                
                window.PhoneShop && window.PhoneShop.showToast ? PhoneShop.showToast('Đã cập nhật giỏ hàng!', 'success') : console.log('Đã cập nhật giỏ hàng!');

                // Yêu cầu tải lại trang để hiển thị lại cột Giảm giá (vì nó đã bị hủy)
                if (data.cart_subtotal != data.cart_total) {
                     // Nếu tổng phụ và tổng cộng khác nhau (nghĩa là có phí ship, không liên quan giảm giá)
                     // Không cần làm gì
                } else if (document.querySelector('.text-danger').textContent.includes('-0₫')) {
                     // Đảm bảo không tải lại nếu không có giảm giá
                } else {
                     location.reload(); // Tải lại để loại bỏ khuyến mãi đã bị hủy
                }
                

            } else {
                alert(data.message || 'Có lỗi xảy ra!');
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi cập nhật giỏ hàng!');
        });
    }
    
    // Hàm xóa sản phẩm
    function removeItem(itemId) {
        fetch('{{ url('gio-hang') }}/' + itemId, { 
            method: 'DELETE', // Gửi yêu cầu DELETE đến route cart.remove
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Xóa item khỏi DOM
                const itemElement = document.getElementById('cart-item-' + itemId);
                if (itemElement) itemElement.remove(); 
                
                // Cập nhật giá tổng cộng
                document.getElementById('cart-subtotal').textContent = formatCurrency(data.cart_subtotal);
                document.getElementById('cart-total').textContent = formatCurrency(data.cart_total);
                
                // Cập nhật số lượng trên badge (Nếu hàm updateCartBadge có sẵn)
                if (window.updateCartBadge) updateCartBadge(data.cart_count);

                window.PhoneShop && window.PhoneShop.showToast ? PhoneShop.showToast('Đã xóa sản phẩm khỏi giỏ hàng!', 'success') : console.log('Đã xóa!');
                
                // Tải lại trang nếu giỏ hàng trống hoặc nếu giảm giá bị hủy do thay đổi
                if (data.cart_count === 0 || document.querySelector('.alert-success')) {
                    location.reload(); 
                }

            } else {
                alert(data.message || 'Có lỗi xảy ra khi xóa sản phẩm!');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Lỗi kết nối hoặc xử lý dữ liệu!');
        });
    }


    // Event listeners cho nút tăng/giảm và input
    document.addEventListener('click', function(e) {
        const button = e.target.closest('.qty-btn');
        if (button) {
            const action = button.dataset.action;
            const input = button.closest('.cart-item').querySelector('.quantity-input');
            const itemId = input.dataset.itemId;

            let currentQty = parseInt(input.value);
            const maxStock = parseInt(input.max);
            
            let newQty = currentQty;
            if (action === 'increase') {
                newQty = currentQty + 1;
            } else if (action === 'decrease') {
                newQty = currentQty - 1;
            }
            
            if (newQty < 1) {
                if (confirm('Bạn có muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                    removeItem(itemId);
                }
                return;
            }
            
            if (newQty > maxStock) {
                alert('Số lượng vượt quá tồn kho! Tồn kho: ' + maxStock);
                return;
            }
            
            updateQuantity(itemId, newQty);
        }
        
        const removeBtn = e.target.closest('.btn-remove');
        if (removeBtn) {
            const itemId = removeBtn.dataset.itemId;
            if (confirm('Bạn có chắc muốn xóa sản phẩm này?')) {
                removeItem(itemId);
            }
        }
    });

    // Event listener cho input thay đổi
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            const itemId = this.dataset.itemId;
            const maxStock = parseInt(this.max);
            let newQty = parseInt(this.value);
            
            if (isNaN(newQty) || newQty < 1) {
                if (confirm('Bạn có muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                    removeItem(itemId);
                } else {
                    this.value = 1;
                    newQty = 1;
                }
            }
            
            if (newQty > maxStock) {
                alert('Số lượng vượt quá tồn kho! Tồn kho: ' + maxStock);
                this.value = maxStock;
                newQty = maxStock;
            }
            
            updateQuantity(itemId, newQty);
        });
    });

});
</script>
@endpush