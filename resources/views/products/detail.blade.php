@extends('layouts.app')

@section('title', $product->ten)

@section('content')
<div class="container" style="padding: 40px 0;">
    <div class="product-detail-wrapper" style="display: flex; gap: 30px; margin-bottom: 50px;">
        <div class="product-gallery" style="flex: 1;">
            <div class="main-image" style="border: 1px solid #eee; padding: 10px; margin-bottom: 10px;">
                <img src="{{ asset('img/' . ($product->hinh_anh_mac_dinh ?? 'default.png')) }}" 
                     alt="{{ $product->ten }}" style="width: 100%; height: auto; object-fit: contain;">
            </div>
            <div class="sub-images" style="display: flex; gap: 10px; overflow-x: auto;">
                @foreach($product->anh as $anh)
                <img src="{{ asset('img/' . $anh->url) }}" style="width: 80px; height: 80px; border: 1px solid #ddd; cursor: pointer;">
                @endforeach
            </div>
        </div>

        <div class="product-info-detail" style="flex: 1;">
            <h1 style="font-size: 24px; font-weight: bold; margin-bottom: 15px;">{{ $product->ten }}</h1>
            
            <div class="price-section" style="margin-bottom: 20px;">
                @php
                    $minPrice = $product->bienTheDangBan->min('gia');
                    $maxPrice = $product->bienTheDangBan->max('gia');
                @endphp
                <p style="color: #d70018; font-size: 28px; font-weight: bold;">
                    {{ number_format($minPrice, 0, ',', '.') }}₫
                    @if($minPrice != $maxPrice)
                        - {{ number_format($maxPrice, 0, ',', '.') }}₫
                    @endif
                </p>
            </div>

            <form action="{{ route('cart.add', $product->id) }}" method="POST">
                @csrf
                <div class="variants" style="margin-bottom: 20px;">
                    <label style="font-weight: bold; display: block; margin-bottom: 10px;">Chọn phiên bản:</label>
                    <select name="bien_the_id" class="form-control" style="padding: 10px; width: 100%; border: 1px solid #ddd; border-radius: 5px;">
                        @foreach($product->bienTheDangBan as $variant)
                            <option value="{{ $variant->id }}">
                                {{ $variant->mau_sac }} - {{ $variant->dung_luong_gb }}GB 
                                ({{ number_format($variant->gia, 0, ',', '.') }}₫)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="actions" style="display: flex; gap: 15px;">
                    <input type="number" name="quantity" value="1" min="1" style="width: 60px; padding: 10px; text-align: center; border: 1px solid #ddd; border-radius: 5px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1; padding: 15px; background: #d70018; color: white; border: none; border-radius: 5px; font-weight: bold; cursor: pointer;">
                        <i class="fas fa-cart-plus"></i> THÊM VÀO GIỎ NGAY
                    </button>
                </div>
            </form>

            <div class="short-desc" style="margin-top: 30px; background: #f5f5f5; padding: 15px; border-radius: 8px;">
                <h4><i class="fas fa-info-circle"></i> Mô tả ngắn</h4>
                <p>{{ $product->mo_ta_ngan }}</p>
            </div>
        </div>
    </div>
</div>
@endsection