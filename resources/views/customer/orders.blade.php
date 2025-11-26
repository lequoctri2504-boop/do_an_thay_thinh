@extends('layouts.app')

@section('title', 'Đơn hàng của tôi')

@section('content')
<div class="breadcrumb">
    <div class="container">
        <a href="{{ route('home') }}">Trang chủ</a>
        <i class="fas fa-chevron-right"></i>
        <span>Đơn hàng</span>
    </div>
</div>

<section class="account-page">
    <div class="container">
        <div class="account-layout">
            @include('customer.sidebar')

            <div class="account-content">
                <h2>Đơn hàng của tôi</h2>

                <div class="orders-list">
                    @forelse($orders as $order)
                    <div class="order-card">
                        <div class="order-header">
                            <div>
                                <strong>Đơn hàng #{{ $order->ma }}</strong>
                                <span class="order-date">{{ $order->ngay_dat }}</span>
                            </div>
                            <span class="order-status">{{ $order->trang_thai }}</span>
                        </div>
                        <div class="order-body">
                            <p><strong>Tổng:</strong> {{ number_format($order->thanh_tien) }}₫</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-center">Bạn chưa có đơn hàng</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
