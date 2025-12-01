@php
    $bienThe = $product->bienTheDangBan->first();
    // Nếu không có biến thể nào đang bán thì bỏ qua hoặc hiện "Hết hàng"
    if(!$bienThe) return; 

    $giaThapNhat = $product->bienTheDangBan->min('gia');
    $giaCaoNhat = $product->bienTheDangBan->max('gia');
    $giaSoSanh = $bienThe->gia_so_sanh ?? null;
    $phanTram = ($giaSoSanh > $bienThe->gia) ? round((($giaSoSanh - $bienThe->gia) / $giaSoSanh) * 100) : 0;
@endphp

<div class="product-card">
    @if($phanTram > 0)
        <div class="product-badge">-{{ $phanTram }}%</div>
    @endif
    
    <div class="product-image">
        <img src="{{ asset('img/' . ($product->hinh_anh_mac_dinh ?? 'default.png')) }}" 
             alt="{{ $product->ten }}"
             onerror="this.src='https://via.placeholder.com/300x300?text=No+Image'">
    </div>
    
    <div class="product-info">
        <h3>{{ $product->ten }}</h3>
        
        <div class="product-price">
            @if($giaThapNhat == $giaCaoNhat)
                <span class="price-new">{{ number_format($giaThapNhat, 0, ',', '.') }}₫</span>
            @else
                <span class="price-new">
                    {{ number_format($giaThapNhat, 0, ',', '.') }} - {{ number_format($giaCaoNhat, 0, ',', '.') }}₫
                </span>
            @endif
            
            @if($giaSoSanh > $giaThapNhat)
                <span class="price-old">{{ number_format($giaSoSanh, 0, ',', '.') }}₫</span>
            @endif
        </div>
        
        <button class="btn btn-cart"><i class="fas fa-shopping-cart"></i> Chọn mua</button>
    </div>
</div>