@extends('layouts.app')
@section('title', 'Thanh toán')
@section('content')
<div class="container" style="padding: 40px 0;">
    <h2>Thông tin thanh toán</h2>
    <form action="{{ route('checkout.process') }}" method="POST" style="display: flex; gap: 30px; margin-top: 20px;">
        @csrf
        <div style="flex: 1;">
            <div class="form-group" style="margin-bottom: 15px;">
                <label>Họ tên người nhận</label>
                <input type="text" name="ten_nguoi_nhan" value="{{ Auth::user()->ho_ten }}" class="form-control" required style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ddd;">
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label>Số điện thoại</label>
                <input type="text" name="sdt_nguoi_nhan" value="{{ Auth::user()->sdt }}" class="form-control" required style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ddd;">
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label>Địa chỉ giao hàng</label>
                <textarea name="dia_chi_giao" class="form-control" required style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ddd; height: 100px;"></textarea>
            </div>
        </div>

        <div style="flex: 1; background: #f8f9fa; padding: 20px; border-radius: 8px;">
            <h3>Đơn hàng của bạn</h3>
            <ul style="list-style: none; padding: 0; margin-bottom: 20px;">
                @foreach($cart as $item)
                <li style="display: flex; justify-content: space-between; border-bottom: 1px solid #eee; padding: 10px 0;">
                    <span>{{ $item['name'] }} <small>x {{ $item['quantity'] }}</small></span>
                    <span>{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}₫</span>
                </li>
                @endforeach
            </ul>
            <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 18px; margin-bottom: 20px;">
                <span>Tổng cộng:</span>
                <span style="color: #d70018;">{{ number_format($total, 0, ',', '.') }}₫</span>
            </div>

            <h4 style="margin-bottom: 10px;">Phương thức thanh toán</h4>
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 10px;">
                    <input type="radio" name="payment_method" value="COD" checked> Thanh toán khi nhận hàng (COD)
                </label>
                <label style="display: block;">
                    <input type="radio" name="payment_method" value="CHUYEN_KHOAN"> Chuyển khoản ngân hàng
                </label>
            </div>

            <button type="submit" style="width: 100%; padding: 15px; background: #d70018; color: white; border: none; font-weight: bold; cursor: pointer; border-radius: 5px;">
                ĐẶT HÀNG
            </button>
        </div>
    </form>
</div>
@endsection