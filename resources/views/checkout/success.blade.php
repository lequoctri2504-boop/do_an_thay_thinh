@extends('layouts.app')
@section('content')
<div class="container" style="text-align: center; padding: 60px 0;">
    <i class="fas fa-check-circle" style="font-size: 60px; color: green; margin-bottom: 20px;"></i>
    <h2>Đặt hàng thành công!</h2>
    <p>Mã đơn hàng của bạn là: <strong>{{ session('order_id') }}</strong></p>
    <p>Cảm ơn bạn đã mua sắm tại PhoneShop.</p>
    <a href="{{ route('home') }}" class="btn btn-primary" style="margin-top: 20px; display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;">Tiếp tục mua sắm</a>
</div>
@endsection