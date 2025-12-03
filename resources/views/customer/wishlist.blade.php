@extends('layouts.app')

@section('title', 'Danh sách yêu thích')

@section('content')
<div class="breadcrumb">
    <div class="container">
        <a href="{{ route('home') }}">Trang chủ</a>
        <i class="fas fa-chevron-right"></i>
        <span>Yêu thích</span>
    </div>
</div>

<section class="account-page">
    <div class="container">
        <div class="account-layout">
            {{-- Bao gồm Sidebar điều hướng tài khoản --}}
            @include('customer.sidebar')

            <div class="account-content">
                <h2><i class="fas fa-heart"></i> Danh sách sản phẩm yêu thích</h2>

                @if(session('success'))
                    <div class="alert alert-success" style="padding:10px; margin-bottom: 15px;">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger" style="padding:10px; margin-bottom: 15px;">{{ session('error') }}</div>
                @endif

                @if($wishlistItems->isEmpty())
                    <div class="text-center py-5">
                        <i class="far fa-heart" style="font-size: 80px; color: #ddd;"></i>
                        <h3 class="mt-4">Bạn chưa có sản phẩm nào trong danh sách yêu thích.</h3>
                        <a href="{{ route('products.index') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
                        </a>
                    </div>
                @else
                    <div class="wishlist-actions mb-4 d-flex justify-content-end">
                        <form action="{{ route('wishlist.clear') }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa toàn bộ danh sách yêu thích?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash-alt"></i> Xóa toàn bộ
                            </button>
                        </form>
                    </div>

                    <div class="wishlist-grid">
                        @foreach($wishlistItems as $item)
                            @php
                                $imagePath = $item->anh_chinh ? asset('uploads/' . $item->anh_chinh) : 'https://via.placeholder.com/200';
                                $canAddToCart = $item->variant_id && $item->ton_kho > 0;
                            @endphp
                            <div class="product-card wishlist-card">
                                
                                {{-- Nút xóa (đặt ở góc trên phải) --}}
                                <form action="{{ route('wishlist.remove', $item->wishlist_id) }}" method="POST" onsubmit="return confirm('Xóa sản phẩm này khỏi danh sách yêu thích?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="wishlist-remove" title="Xóa khỏi yêu thích">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>

                                <div class="product-image">
                                    <a href="{{ route('products.show', $item->slug) }}">
                                        <img src="{{ $imagePath }}" alt="{{ $item->ten }}">
                                    </a>
                                </div>
                                
                                <div class="product-info">
                                    <span class="text-muted small mb-1">{{ $item->thuong_hieu }}</span>
                                    <h3><a href="{{ route('products.show', $item->slug) }}">{{ $item->ten }}</a></h3>
                                    
                                    <div class="product-price">
                                        <span class="price-new">{{ number_format($item->gia, 0, ',', '.') }}₫</span>
                                        @if($item->gia_so_sanh > $item->gia)
                                            <span class="price-old">{{ number_format($item->gia_so_sanh, 0, ',', '.') }}₫</span>
                                        @endif
                                    </div>

                                    <div class="stock-status mb-3">
                                        @if($item->ton_kho > 0)
                                            <span class="text-success fw-bold"><i class="fas fa-check-circle"></i> Còn hàng ({{ $item->ton_kho }})</span>
                                        @else
                                            <span class="text-danger fw-bold"><i class="fas fa-times-circle"></i> Hết hàng</span>
                                        @endif
                                    </div>
                                    
                                    {{-- Nút chuyển sang giỏ hàng --}}
                                    <form action="{{ route('wishlist.moveToCart', $item->wishlist_id) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                                class="btn btn-primary btn-sm btn-block"
                                                @if(!$canAddToCart) disabled @endif>
                                            <i class="fas fa-shopping-cart"></i> Chuyển vào giỏ hàng
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- Thêm CSS tùy chỉnh cho trang Wishlist --}}
    <style>
        .wishlist-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .wishlist-card {
            padding: 15px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            position: relative;
            transition: all 0.3s ease;
        }

        .wishlist-card:hover {
            box-shadow: var(--shadow-hover);
            transform: translateY(-5px);
        }

        .wishlist-card .product-image img {
            height: 200px;
            object-fit: contain;
            width: 100%;
        }

        .wishlist-remove {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid var(--border-color);
            border-radius: 50%;
            width: 30px;
            height: 30px;
            font-size: 14px;
            color: var(--text-dark);
            box-shadow: var(--shadow);
            z-index: 10;
        }
        
        .wishlist-remove:hover {
            background: var(--danger-color);
            color: var(--white);
            border-color: var(--danger-color);
        }

        .wishlist-card .product-info h3 {
            font-size: 16px;
            height: 40px; /* Giữ chiều cao cố định cho tiêu đề */
            overflow: hidden;
            line-height: 1.4;
            margin-bottom: 10px;
        }
        
        .wishlist-card .product-price {
            margin-bottom: 10px;
        }
        
        .wishlist-card .price-new {
            font-size: 18px;
        }
        
        .wishlist-card .price-old {
            font-size: 14px;
        }
        
        .btn-block {
            width: 100%;
            margin-top: auto;
        }
    </style>
@endsection