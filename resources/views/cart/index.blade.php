@extends('layouts.app')

@section('title', 'Giỏ hàng của bạn')

@section('content')
<div class="container" style="padding: 40px 0;">
    <h2 style="margin-bottom: 20px;"><i class="fas fa-shopping-cart"></i> Giỏ hàng</h2>

    @if(session('cart') && count(session('cart')) > 0)
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
            <thead>
                <tr style="background: #f8f9fa; text-align: left;">
                    <th style="padding: 15px;">Sản phẩm</th>
                    <th style="padding: 15px;">Giá</th>
                    <th style="padding: 15px;">Số lượng</th>
                    <th style="padding: 15px;">Thành tiền</th>
                    <th style="padding: 15px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach(session('cart') as $id => $details)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 15px; display: flex; align-items: center; gap: 15px;">
                        <img src="{{ asset('img/' . $details['image']) }}" width="60" height="60" style="object-fit: cover;">
                        <div>
                            <div style="font-weight: bold;">{{ $details['name'] }}</div>
                            <small style="color: #666;">{{ $details['variant_info'] }}</small>
                        </div>
                    </td>
                    <td style="padding: 15px;">{{ number_format($details['price'], 0, ',', '.') }}₫</td>
                    <td style="padding: 15px;">
                        <span style="padding: 5px 10px; background: #eee; border-radius: 4px;">{{ $details['quantity'] }}</span>
                    </td>
                    <td style="padding: 15px; font-weight: bold; color: #d70018;">
                        {{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }}₫
                    </td>
                    <td style="padding: 15px;">
                        <form action="{{ route('cart.remove') }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="id" value="{{ $id }}">
                            <button type="submit" style="color: red; border: none; background: none; cursor: pointer;">
                                <i class="fas fa-trash"></i> Xóa
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="display: flex; justify-content: space-between; align-items: center; background: #f8f9fa; padding: 20px; border-radius: 8px;">
            <h3>Tổng tiền: <span style="color: #d70018;">{{ number_format($total, 0, ',', '.') }}₫</span></h3>
            <a href="{{ route('checkout.index') }}" class="btn btn-primary" style="padding: 15px 30px; background: #d70018; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">
                TIẾN HÀNH THANH TOÁN <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    @else
        <div style="text-align: center; padding: 50px;">
            <p>Giỏ hàng của bạn đang trống!</p>
            <a href="{{ route('home') }}" style="color: #007bff;">Quay lại mua sắm</a>
        </div>
    @endif
</div>
@endsection