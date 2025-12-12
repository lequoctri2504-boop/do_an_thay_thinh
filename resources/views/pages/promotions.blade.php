@extends('layouts.app')

@section('title', 'Chương trình Khuyến mãi')

@section('content')
<div class="container" style="padding: 40px 0;">
    <h1 class="text-center" style="margin-bottom: 30px; font-size: 32px; color: var(--primary-color);">
        <i class="fas fa-bullhorn"></i> Chương Trình Khuyến Mãi Hấp Dẫn
    </h1>
    
    <div class="promotion-list" style="display: grid; grid-template-columns: 1fr; gap: 30px;">
        @forelse($promotions as $promo)
            @php
                // Sử dụng thuộc tính ảo từ Model
                $loaiGiamGia = $promo->loai_giam_gia;
                $giaTri = $promo->gia_tri;
                $maKhuyenMai = $promo->ma; // Sử dụng cột 'ma' thực tế

                $hienThiGiaTri = '';
                if ($loaiGiamGia == 'PHAN_TRAM') {
                    $hienThiGiaTri = trim($giaTri) . '%';
                } else {
                    $hienThiGiaTri = number_format((float) $giaTri, 0, ',', '.') . '₫';
                }
            @endphp
            <div class="promotion-card" style="background: #fff; border: 1px solid #ddd; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); transition: box-shadow 0.3s;">
                <div style="padding: 20px;">
                    <h2 style="font-size: 24px; color: #333; margin-bottom: 10px;">{{ $promo->ten }}</h2>
                    <div style="margin-bottom: 15px;">
                        
                        {{-- HIỂN THỊ GIÁ TRỊ VÀ LOẠI GIẢM GIÁ --}}
                        <span class="badge" style="background-color: var(--primary-color); color: white; padding: 5px 10px; border-radius: 5px; font-weight: bold;">
                            GIẢM {{ $hienThiGiaTri }}
                        </span>
                        
                        @php
                            $startDate = \Carbon\Carbon::parse($promo->ngay_bat_dau);
                            $endDate = \Carbon\Carbon::parse($promo->ngay_ket_thuc);
                        @endphp

                        <span class="badge" style="background-color: #28a745; color: white; padding: 5px 10px; border-radius: 5px; margin-left: 10px;">
                            Thời hạn: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}
                        </span>
                    </div>

                    {{-- Phần mô tả (Nếu DB có cột mo_ta, nếu không thì hiển thị N/A) --}}
                    @if(!empty($promo->mo_ta))
                         <p style="color: #666; margin-bottom: 20px; line-height: 1.6;">{!! nl2br(e($promo->mo_ta)) !!}</p>
                    @endif
                    
                    {{-- HIỂN THỊ MÃ KHUYẾN MÃI --}}
                    @if($maKhuyenMai)
                        <div style="background: #f0f0f0; padding: 10px; border-radius: 5px; display: inline-block;">
                            <strong style="color: #007bff;">Mã áp dụng:</strong> 
                            <span style="font-size: 18px; font-weight: bold; color: #dc3545;">{{ $maKhuyenMai }}</span>
                            <button class="btn btn-sm btn-outline-secondary" onclick="copyCode('{{ $maKhuyenMai }}')" style="margin-left: 10px;">
                                Sao chép
                            </button>
                        </div>
                    @else
                         <p class="text-muted">Áp dụng tự động, không cần mã.</p>
                    @endif
                </div>
            </div>
        @empty
            <div class="alert alert-info text-center" style="grid-column: 1 / -1; padding: 20px;">
                Hiện tại không có chương trình khuyến mãi nào đang diễn ra.
            </div>
        @endforelse
    </div>

</div>

@push('scripts')
<script>
    function copyCode(code) {
        navigator.clipboard.writeText(code).then(() => {
            if (window.PhoneShop && typeof window.PhoneShop.showToast === 'function') {
                window.PhoneShop.showToast('Đã sao chép mã: ' + code, 'success');
            } else {
                alert('Đã sao chép mã khuyến mãi: ' + code);
            }
        }, (err) => {
            console.error('Không thể sao chép văn bản: ', err);
            alert('Lỗi: Không thể sao chép mã khuyến mãi.');
        });
    }
</script>
@endpush
@endsection