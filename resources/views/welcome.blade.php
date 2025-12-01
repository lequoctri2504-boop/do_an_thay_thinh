@extends('layouts.app')

@section('title', 'Trang chủ - PhoneShop')

@section('content')
<section class="banner-slider">
    <div class="container">
        <div class="slider-wrapper">
            <div class="slide active">
                <img src="https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=1200&h=400&fit=crop" alt="Banner 1">
                <div class="slide-content">
                    <h2>iPhone 17 Pro Max</h2>
                    <p>Titanium. Mạnh mẽ. Nhẹ hơn bao giờ hết</p>
                    <a href="#" class="btn btn-primary">Xem ngay</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="flash-sale">
    <div class="container">
        <div class="section-header">
            <h2><i class="fas fa-bolt"></i> SẢN PHẨM NỔI BẬT</h2>
            <div class="countdown">
                <span>02</span>:<span>35</span>:<span>48</span>
            </div>
        </div>
        <div class="product-grid">
            @forelse($featuredProducts as $product)
            @php
            $bienThe = $product->bienTheDangBan->first();
            $giaThapNhat = $product->bienTheDangBan->min('gia');
            $giaCaoNhat = $product->bienTheDangBan->max('gia');
            $giaSoSanh = $bienThe->gia_so_sanh ?? null;
            @endphp
            <!-- <div class="product-card">
                        @if($giaSoSanh && $giaSoSanh > $giaThapNhat)
                            @php
                                $phanTram = round((($giaSoSanh - $giaThapNhat) / $giaSoSanh) * 100);
                            @endphp
                            <div class="product-badge">-{{ $phanTram }}%</div>
                        @endif
                        <div class="product-image">
                            <img src="{{ asset('img/' . ($product->hinh_anh_mac_dinh ?? 'default.png')) }}" 
                                 alt="{{ $product->ten }}"
                                 onerror="this.src='https://via.placeholder.com/300x300?text=No+Image'">
                            <button class="quick-view"><i class="fas fa-eye"></i></button>
                        </div>
                        <div class="product-info">
                            <h3>{{ $product->ten }}</h3>
                            <div class="product-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                                <span>(234)</span>
                            </div>
                            <div class="product-price">
                                @if($giaThapNhat == $giaCaoNhat)
                                    <span class="price-new">{{ number_format($giaThapNhat, 0, ',', '.') }}₫</span>
                                @else
                                    <span class="price-new">
                                        {{ number_format($giaThapNhat, 0, ',', '.') }}₫ - 
                                        {{ number_format($giaCaoNhat, 0, ',', '.') }}₫
                                    </span>
                                @endif
                                @if($giaSoSanh)
                                    <span class="price-old">{{ number_format($giaSoSanh, 0, ',', '.') }}₫</span>
                                @endif
                            </div>
                            <button class="btn btn-cart"><i class="fas fa-shopping-cart"></i> Thêm vào giỏ</button>
                        </div>
                    </div> -->

            <div class="product-card">
                {{-- Logic hiển thị nhãn giảm giá hoặc nhãn Mới --}}
                @if(isset($giaSoSanh) && $giaSoSanh > $giaThapNhat)
                @php $phanTram = round((($giaSoSanh - $giaThapNhat) / $giaSoSanh) * 100); @endphp
                <div class="product-badge">-{{ $phanTram }}%</div>
                @elseif(isset($product->created_at) && $product->created_at->diffInDays(now()) < 30)
                    <div class="product-badge new">MỚI
            </div>
            @endif

            <div class="product-image">
                {{-- THAY ĐỔI QUAN TRỌNG 1: Thêm đường link vào ảnh --}}
                <a href="{{ route('product.detail', $product->slug) }}">
                    <img src="{{ asset('img/' . ($product->hinh_anh_mac_dinh ?? 'default.png')) }}"
                        alt="{{ $product->ten }}"
                        onerror="this.src='https://via.placeholder.com/300x300?text=No+Image'">
                </a>
                {{-- Nút xem nhanh có thể giữ nguyên hoặc cũng link sang chi tiết --}}
                <a href="{{ route('product.detail', $product->slug) }}" class="quick-view"><i class="fas fa-eye"></i></a>
            </div>

            <div class="product-info">
                {{-- THAY ĐỔI QUAN TRỌNG 2: Thêm đường link vào tên sản phẩm --}}
                <h3>
                    <a href="{{ route('product.detail', $product->slug) }}" style="color: inherit; text-decoration: none;">
                        {{ $product->ten }}
                    </a>
                </h3>

                <div class="product-rating">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    <span>(50)</span>
                </div>

                <div class="product-price">
                    {{-- Giữ nguyên logic hiển thị giá --}}
                    @if(isset($giaCaoNhat) && $giaThapNhat != $giaCaoNhat)
                    <span class="price-new">{{ number_format($giaThapNhat, 0, ',', '.') }}₫ - {{ number_format($giaCaoNhat, 0, ',', '.') }}₫</span>
                    @else
                    <span class="price-new">{{ number_format($giaThapNhat, 0, ',', '.') }}₫</span>
                    @endif

                    @if(isset($giaSoSanh) && $giaSoSanh > $giaThapNhat)
                    <span class="price-old">{{ number_format($giaSoSanh, 0, ',', '.') }}₫</span>
                    @endif
                </div>

                {{-- THAY ĐỔI QUAN TRỌNG 3: Nút mua hàng dẫn sang trang chi tiết --}}
                <a href="{{ route('product.detail', $product->slug) }}" class="btn btn-cart">
                    <i class="fas fa-shopping-cart"></i> Chọn mua
                </a>
            </div>
        </div>
        @empty
        <p>Chưa có sản phẩm nổi bật nào</p>
        @endforelse
    </div>
    </div>
</section>

<section class="hot-brands">
    <div class="container">
        <div class="section-header">
            <h2><i class="fas fa-fire"></i> THƯƠNG HIỆU NỔI BẬT</h2>
        </div>
        <div class="brands-grid">
            <a href="#" class="brand-card">
                <img src="https://upload.wikimedia.org/wikipedia/commons/f/fa/Apple_logo_black.svg" alt="Apple">
                <span>Apple</span>
            </a>
            <a href="#" class="brand-card">
                <img src="https://upload.wikimedia.org/wikipedia/commons/2/24/Samsung_Logo.svg" alt="Samsung">
                <span>Samsung</span>
            </a>
            <a href="#" class="brand-card">
                <img src="https://upload.wikimedia.org/wikipedia/commons/a/ae/Xiaomi_logo_%282021-%29.svg" alt="Xiaomi">
                <span>Xiaomi</span>
            </a>
            <a href="#" class="brand-card">
                <img src="https://upload.wikimedia.org/wikipedia/commons/8/8a/OPPO_LOGO_2019.svg" alt="OPPO">
                <span>OPPO</span>
            </a>
            <a href="#" class="brand-card">
                <img src="https://upload.wikimedia.org/wikipedia/commons/5/51/Vivo_logo_2019.svg" alt="Vivo">
                <span>Vivo</span>
            </a>
            <a href="#" class="brand-card">
                <img src="https://upload.wikimedia.org/wikipedia/commons/6/6a/Realme_logo.svg" alt="Realme">
                <span>Realme</span>
            </a>
        </div>
    </div>
</section>

@if(isset($appleProducts) && $appleProducts->count() > 0)
<section class="apple-products" style="padding: 40px 0; background-color: #f8f9fa;">
    <div class="container">
        <div class="section-header">
            <h2><i class="fab fa-apple"></i> SẢN PHẨM APPLE</h2>
            <a href="#" class="view-all">Xem tất cả Apple <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="product-grid">
            @foreach($appleProducts as $product)
            @php
            $giaThapNhat = $product->bienTheDangBan->min('gia');
            @endphp
            <div class="product-card">
                <div class="product-image">
                    <img src="{{ asset('img/' . ($product->hinh_anh_mac_dinh ?? 'default.png')) }}"
                        alt="{{ $product->ten }}"
                        onerror="this.src='https://via.placeholder.com/300x300?text=No+Image'">
                    <button class="quick-view"><i class="fas fa-eye"></i></button>
                </div>
                <div class="product-info">
                    <h3>{{ $product->ten }}</h3>
                    <div class="product-price">
                        <span class="price-new">{{ number_format($giaThapNhat, 0, ',', '.') }}₫</span>
                    </div>
                    <button class="btn btn-cart"><i class="fas fa-shopping-cart"></i> Thêm vào giỏ</button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="featured-products">
    <div class="container">
        <div class="section-header">
            <h2><i class="fas fa-crown"></i> SẢN PHẨM MỚI NHẤT</h2>
            <a href="#" class="view-all">Xem tất cả <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="product-grid">
            @foreach($newProducts as $product)
            @php
            $giaThapNhat = $product->bienTheDangBan->min('gia');
            @endphp
            <div class="product-card">
                <div class="product-badge new">MỚI</div>
                <div class="product-image">
                    <img src="{{ asset('img/' . ($product->hinh_anh_mac_dinh ?? 'default.png')) }}"
                        alt="{{ $product->ten }}"
                        onerror="this.src='https://via.placeholder.com/300x300?text=No+Image'">
                    <button class="quick-view"><i class="fas fa-eye"></i></button>
                </div>
                <div class="product-info">
                    <h3>{{ $product->ten }}</h3>
                    <div class="product-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <span>(567)</span>
                    </div>
                    <div class="product-price">
                        <span class="price-new">{{ number_format($giaThapNhat, 0, ',', '.') }}₫</span>
                    </div>
                    <!-- <button class="btn btn-cart"><i class="fas fa-shopping-cart"></i> Thêm vào giỏ</button> -->
                     <a href="{{ route('product.detail', $product->slug) }}" class="btn btn-cart">
    <i class="fas fa-shopping-cart"></i> Chọn mua
</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection