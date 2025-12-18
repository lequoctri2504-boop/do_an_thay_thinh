@extends('layouts.app')

@section('title', 'Tất cả thương hiệu - PhoneShop')

@section('content')
{{-- BREADCRUMB HIỆN ĐẠI --}}
<div class="breadcrumb-nav py-3 border-bottom bg-light">
    <div class="container">
        <div class="d-flex align-items-center" style="font-size: 14px;">
            <a href="{{ route('home') }}" class="text-decoration-none text-muted"><i class="fas fa-home me-1"></i> Trang chủ</a>
            <span class="mx-2 text-muted"><i class="fas fa-angle-right" style="font-size: 12px;"></i></span>
            <span class="fw-bold text-dark">Thương hiệu đối tác</span>
        </div>
    </div>
</div>

<section class="all-brands py-5" style="background-color: #fcfcfc;">
    <div class="container">
        {{-- TIÊU ĐỀ TRUNG TÂM --}}
        <div class="text-center mb-5">
            <h2 class="brand-section-title">
                <span class="title-decoration"></span>
                Hệ Thống Thương Hiệu
                <span class="title-decoration"></span>
            </h2>
            <p class="text-muted mt-2">Khám phá các dòng sản phẩm chính hãng từ những đối tác hàng đầu của PhoneShop</p>
        </div>

        {{-- GRID THƯƠNG HIỆU (4 CỘT TRÊN PC) --}}
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
            @forelse($brands as $brand)
                <div class="col">
                    @php
                        $logoPath = $brand->hinh_anh ? asset('images/brands/' . $brand->hinh_anh) : 'https://via.placeholder.com/200x100?text=LOGO';
                    @endphp

                    <a href="{{ route('products.index', ['brand' => $brand->slug]) }}" class="brand-link-wrapper">
                        <div class="brand-premium-card h-100">
                            <div class="brand-logo-container">
                                <img src="{{ $logoPath }}" alt="{{ $brand->ten }}" class="brand-logo-img">
                            </div>
                            <div class="brand-footer">
                                <h5 class="brand-name">{{ $brand->ten }}</h5>
                                <span class="view-products">Xem ngay <i class="fas fa-arrow-right ms-1"></i></span>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="empty-state-box">
                        <i class="fas fa-tags fa-3x text-light mb-3"></i>
                        <p class="text-muted">Dữ liệu thương hiệu hiện đang được cập nhật...</p>
                        <a href="{{ route('home') }}" class="btn btn-outline-danger btn-sm">Quay lại trang chủ</a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>

<style>
    /* Tiêu đề phần */
    .brand-section-title {
        font-weight: 800;
        color: #2c3e50;
        font-size: 1.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 15px;
        text-transform: uppercase;
    }
    .title-decoration {
        height: 3px;
        width: 40px;
        background: #ff4d4d;
        border-radius: 2px;
    }

    /* Thẻ thương hiệu Premium */
    .brand-link-wrapper {
        text-decoration: none !important;
    }

    .brand-premium-card {
        background: #ffffff;
        border-radius: 15px;
        border: 1px solid #eee;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .brand-logo-container {
        padding: 30px;
        height: 140px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff;
        transition: transform 0.4s ease;
    }

    .brand-logo-img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        filter: grayscale(10%);
        transition: filter 0.3s ease;
    }

    .brand-footer {
        padding: 15px;
        background: #f8f9fa;
        text-align: center;
        border-top: 1px solid #f1f1f1;
        transition: background 0.3s ease;
    }

    .brand-name {
        margin-bottom: 5px;
        font-size: 1.1rem;
        font-weight: 700;
        color: #333;
        transition: color 0.3s ease;
    }

    .view-products {
        font-size: 0.8rem;
        color: #ff4d4d;
        font-weight: 600;
        opacity: 0;
        transform: translateY(10px);
        display: block;
        transition: all 0.3s ease;
    }

    /* Hiệu ứng Hover */
    .brand-premium-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.08);
        border-color: #ff4d4d;
    }

    .brand-premium-card:hover .brand-logo-container {
        transform: scale(1.05);
    }

    .brand-premium-card:hover .brand-logo-img {
        filter: grayscale(0%);
    }

    .brand-premium-card:hover .brand-footer {
        background: #fffafa;
    }

    .brand-premium-card:hover .brand-name {
        color: #ff4d4d;
    }

    .brand-premium-card:hover .view-products {
        opacity: 1;
        transform: translateY(0);
    }

    /* Breadcrumb hover */
    .breadcrumb-nav a:hover {
        color: #ff4d4d !important;
    }

    /* Trạng thái trống */
    .empty-state-box {
        background: #fff;
        padding: 40px;
        border-radius: 15px;
        border: 1px dashed #ccc;
    }
</style>
@endsection
<script>
    // Chuyển dữ liệu biến thể từ PHP sang JSON
    const variants = {!! json_encode($product->bienThe) !!};

    document.addEventListener('DOMContentLoaded', function() {
        const colorRadios = document.querySelectorAll('.color-radio');
        const capacityItems = document.querySelectorAll('.capacity-item');
        const capacityRadios = document.querySelectorAll('.capacity-radio');
        const priceDisplay = document.getElementById('display-price');
        const colorNameDisplay = document.getElementById('selected-color-name');

        colorRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                const selectedColor = this.value;
                colorNameDisplay.innerText = this.dataset.colorName;

                // 1. Lọc các dung lượng khả dụng cho màu này
                const availableCapacities = variants
                    .filter(v => v.mau_sac === selectedColor)
                    .map(v => v.dung_luong);

                // 2. Ẩn/Hiện các nút dung lượng
                capacityRadios.forEach(capRadio => {
                    const parent = capRadio.closest('.capacity-item');
                    if (availableCapacities.includes(capRadio.value)) {
                        parent.style.display = 'block';
                        capRadio.disabled = false;
                    } else {
                        parent.style.display = 'none';
                        capRadio.disabled = true;
                        capRadio.checked = false; // Bỏ chọn nếu nó đang được chọn mà bị ẩn
                    }
                });

                updatePrice();
            });
        });

        // Cập nhật giá khi chọn dung lượng
        capacityRadios.forEach(radio => {
            radio.addEventListener('change', updatePrice);
        });

        function updatePrice() {
            const color = document.querySelector('.color-radio:checked')?.value;
            const cap = document.querySelector('.capacity-radio:checked')?.value;

            if (color && cap) {
                const variant = variants.find(v => v.mau_sac === color && v.dung_luong === cap);
                if (variant) {
                    priceDisplay.innerText = new Intl.NumberFormat('vi-VN').format(variant.gia) + '₫';
                }
            }
        }
        
        // Tự động chọn màu đầu tiên khi load trang
        if(colorRadios.length > 0) colorRadios[0].click();
    });
</script>