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

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($cartItems->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart" style="font-size: 100px; color: #ddd;"></i>
                <h3 class="mt-4">Giỏ hàng trống</h3>
                <p class="text-muted">Bạn chưa có sản phẩm nào trong giỏ hàng</p>
                <a href="{{ route('home') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-arrow-left me-2"></i>Tiếp tục mua sắm
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
                                       data-price="{{ $bienThe->gia }}">
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
                    
                    <div class="voucher-input">
                        <input type="text" placeholder="Nhập mã giảm giá">
                        <button class="btn btn-secondary">Áp dụng</button>
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

<style>
    /* CSS cần thiết để tạo bố cục từ cart.html (Các style này thường nằm trong style.css) */
    .cart-page { padding: 30px 0; }
    .cart-layout { display: grid; grid-template-columns: 1fr 400px; gap: 30px; }
    .cart-items { display: flex; flex-direction: column; gap: 20px; }
    .cart-item { display: grid; grid-template-columns: 150px 1fr 150px 200px; gap: 20px; padding: 20px; border: 1px solid var(--border-color); border-radius: 12px; }
    .item-image img { width: 100%; height: 150px; object-fit: cover; border-radius: 8px; }
    .item-quantity { display: flex; align-items: center; gap: 10px; }
    .item-quantity input { width: 60px; text-align: center; }
    .item-price { text-align: right; }
    .cart-summary { position: sticky; top: 20px; height: fit-content; padding: 25px; border: 1px solid var(--border-color); border-radius: 12px; }
    .summary-total { display: flex; justify-content: space-between; border-top: 2px solid var(--border-color); margin-top: 20px; padding-top: 20px;}
    .voucher-input { display: flex; gap: 10px; margin: 20px 0; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

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
                if (input) input.value = newQty;
                
                const price = parseFloat(input.dataset.price);
                const subtotalElement = document.querySelector('.item-subtotal[data-item-id="' + itemId + '"]');
                if (subtotalElement) subtotalElement.textContent = formatCurrency(price * newQty);
                
                document.getElementById('cart-subtotal').textContent = formatCurrency(data.cart_subtotal);
                document.getElementById('cart-total').textContent = formatCurrency(data.cart_total);
                
                window.PhoneShop && window.PhoneShop.showToast ? PhoneShop.showToast('Đã cập nhật giỏ hàng!', 'success') : console.log('Đã cập nhật giỏ hàng!');
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
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const row = document.getElementById('cart-item-' + itemId);
                if (row) {
                    row.style.transition = 'opacity 0.3s';
                    row.style.opacity = '0';
                }
                
                setTimeout(() => {
                    if (row) row.remove();
                    
                    if (data.cart_count === 0) {
                        location.reload();
                    } else {
                        document.getElementById('cart-subtotal').textContent = formatCurrency(data.cart_subtotal);
                        document.getElementById('cart-total').textContent = formatCurrency(data.cart_total);
                    }
                }, 300);
                
                window.PhoneShop && window.PhoneShop.showToast ? PhoneShop.showToast('Đã xóa sản phẩm khỏi giỏ hàng!', 'success') : console.log('Đã xóa sản phẩm khỏi giỏ hàng!');
            } else {
                alert(data.message || 'Có lỗi xảy ra!');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi xóa sản phẩm!');
        });
    }
    
    // Format tiền tệ (Lấy từ main.js/home.js nếu có)
    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + '₫';
    }


    // Event listeners cho nút tăng/giảm
    document.addEventListener('click', function(e) {
        const button = e.target.closest('.qty-btn');
        if (button) {
            const action = button.dataset.action;
            const itemId = button.closest('.cart-item').querySelector('.quantity-input').dataset.itemId;
            const input = button.closest('.cart-item').querySelector('.quantity-input');
            if (!input) return;

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
@endsection