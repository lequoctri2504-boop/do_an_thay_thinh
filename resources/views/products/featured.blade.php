@extends('layouts.app')

@section('title', 'Sản phẩm Nổi bật')

@section('content')
<div class="breadcrumb">
    <div class="container">
        <a href="{{ route('home') }}">Trang chủ</a>
        <i class="fas fa-chevron-right"></i>
        <span>Sản phẩm Nổi bật</span>
    </div>
</div>

<section class="products-page">
    <div class="container">
        <h1>Sản phẩm Nổi bật</h1>
        
        <div class="products-layout">
            
            {{-- Filter Sidebar (Left Column) --}}
            <aside class="filter-sidebar">
                <div class="filter-box">
                    <h3><i class="fas fa-filter"></i> Lọc sản phẩm</h3>
                    
                    {{-- Lọc theo Danh mục --}}
                    <div class="filter-group">
                        <h4>Danh mục</h4>
                        @foreach($categories as $cat)
                            <label>
                                <input type="radio" name="category" value="{{ $cat->slug }}" 
                                       onclick="window.location.href='{{ route('products.category', $cat->slug) }}'"> 
                                {{ $cat->ten }}
                            </label>
                        @endforeach
                    </div>

                    {{-- Lọc theo Thương hiệu --}}
                    <div class="filter-group">
                        <h4>Thương hiệu</h4>
                        <form action="{{ route('products.featured') }}" method="GET" id="brandFilterForm">
                            @foreach($brands as $brand)
                                <label>
                                    <input type="checkbox" name="brand" value="{{ $brand->slug }}" 
                                           onchange="this.closest('form').submit()"> 
                                    {{ $brand->ten }}
                                </label>
                            @endforeach
                        </form>
                    </div>
                    
                    {{-- Lọc theo Giá --}}
                    <div class="filter-group">
                        <h4>Khoảng giá</h4>
                        <form action="{{ route('products.featured') }}" method="GET">
                            <label><input type="radio" name="price" value="0-5000000" onchange="this.closest('form').submit()"> Dưới 5 triệu</label>
                            <label><input type="radio" name="price" value="5000000-10000000" onchange="this.closest('form').submit()"> 5 - 10 triệu</label>
                            <label><input type="radio" name="price" value="10000000-20000000" onchange="this.closest('form').submit()"> 10 - 20 triệu</label>
                            <label><input type="radio" name="price" value="20000000-100000000" onchange="this.closest('form').submit()"> Trên 20 triệu</label>
                        </form>
                    </div>
                </div>
            </aside>

            {{-- Main Content & Product Grid (Right Column) --}}
            <div class="products-content">
                <div class="products-toolbar">
                    <div class="result-count">Hiển thị {{ $products->firstItem() }} - {{ $products->lastItem() }} trong tổng số {{ $products->total() }} sản phẩm</div>
                    <div class="toolbar-right">
                        <span>Sắp xếp theo:</span>
                        {{-- Form sắp xếp --}}
                        <select class="sort-select" onchange="window.location.href = '{{ route('products.featured') }}?sort=' + this.value">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá thấp nhất</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá cao nhất</option>
                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Phổ biến nhất</option>
                        </select>
                    </div>
                </div>
                
                <div class="product-grid">
                    @forelse($products as $product)
                        @php
                            $variant = $product->bienTheSanPham->first();
                            $minPrice = $variant ? $variant->gia : 0;
                            $comparePrice = $variant ? $variant->gia_so_sanh : 0;
                            $discount = $comparePrice > $minPrice ? round((($comparePrice - $minPrice) / $comparePrice) * 100) : 0;
                            $imagePath = $product->hinh_anh_mac_dinh ? asset('uploads/' . $product->hinh_anh_mac_dinh) : 'https://via.placeholder.com/300';
                            $avgRating = $product->danhGia->avg('so_sao') ?? 0;
                            $reviewCount = $product->danhGia->count();
                        @endphp
                        
                        <div class="product-card">
                            @if($discount > 0)
                                <div class="product-badge">-{{ $discount }}%</div>
                            @endif
                            <a href="{{ route('products.show', $product->slug) }}" class="product-image">
                                <img src="{{ $imagePath }}" alt="{{ $product->ten }}">
                            </a>
                            <div class="product-info">
                                <h3><a href="{{ route('products.show', $product->slug) }}">{{ $product->ten }}</a></h3>
                                
                                <div class="product-rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($avgRating)) <i class="fas fa-star"></i>
                                        @elseif($i - 0.5 <= $avgRating) <i class="fas fa-star-half-alt"></i>
                                        @else <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                    <span>({{ $reviewCount }})</span>
                                </div>
                                
                                <div class="product-price">
                                    <span class="price-new">{{ number_format($minPrice, 0, ',', '.') }}₫</span>
                                    @if($comparePrice > $minPrice)
                                        <span class="price-old">{{ number_format($comparePrice, 0, ',', '.') }}₫</span>
                                    @endif
                                </div>
                                
                                {{-- Nút thêm giỏ hàng --}}
                                <button class="btn btn-cart add-to-cart-btn" 
                                        data-variant-id="{{ $variant->id ?? '' }}" 
                                        data-product-name="{{ $product->ten }}"
                                        @if(!$variant) disabled @endif>
                                    <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="text-center" style="grid-column: 1 / -1;">Không tìm thấy sản phẩm nổi bật nào.</p>
                    @endforelse
                </div>
                
                {{-- Pagination --}}
                <div class="pagination-wrapper">{{ $products->links() }}</div>
            </div>
            
        </div>
    </div>
</section>
@endsection