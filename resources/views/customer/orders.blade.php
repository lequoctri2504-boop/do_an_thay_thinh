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
            {{-- Sidebar --}}
            @include('customer.sidebar')

            <div class="account-content">
                <h2><i class="fas fa-shopping-bag"></i> Đơn hàng của tôi</h2>
                
                @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
                @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

                {{-- Tabs Lọc Đơn hàng --}}
                <div class="order-tabs">
                    @php
                        $statuses = [
                            'all' => 'Tất cả', 
                            'DANG_XU_LY' => 'Đang xử lý', 
                            'DANG_GIAO' => 'Đang giao', 
                            'HOAN_THANH' => 'Hoàn thành', 
                            'HUY' => 'Đã hủy'
                        ];
                    @endphp

                    @foreach($statuses as $value => $label)
                        <a href="{{ route('customer.orders', ['status' => $value]) }}" 
                           class="order-tab {{ $currentStatus == $value ? 'active' : '' }}">
                           {{ $label }} ({{ $statusCounts[$value] ?? 0 }})
                        </a>
                    @endforeach
                </div>

                <div class="orders-list">
                    @forelse($orders as $order)
                    @php
                        // Mapping trạng thái
                        $statusClass = strtolower($order->trang_thai);
                        if ($statusClass == 'dang_xu_ly') $statusClass = 'processing';
                        elseif ($statusClass == 'dang_giao') $statusClass = 'shipping';
                        elseif ($statusClass == 'hoan_thanh') $statusClass = 'delivered';
                        else $statusClass = 'cancelled';
                    @endphp
                    
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-id">
                                <strong>Đơn hàng #{{ $order->ma }}</strong>
                                <span class="order-date">{{ \Carbon\Carbon::parse($order->ngay_dat)->format('d/m/Y H:i') }}</span>
                            </div>
                            <span class="order-status status-{{ $statusClass }}">{{ $order->trang_thai }}</span>
                        </div>
                        
                        <div class="order-body">
                            @foreach($order->chiTiet as $item)
                                @php
                                    $sanPham = $item->bienThe->sanPham ?? null;
                                    $anhChinh = $sanPham && $sanPham->hinh_anh_mac_dinh ? asset('uploads/' . $sanPham->hinh_anh_mac_dinh) : 'https://via.placeholder.com/80';
                                @endphp
                                <div class="order-product">
                                    <img src="{{ $anhChinh }}" alt="{{ $item->ten_sp_ghi_nhan }}">
                                    <div class="order-product-info">
                                        <h4>{{ $item->ten_sp_ghi_nhan }}</h4>
                                        <p>
                                            @if($item->bienThe) 
                                                {{ $item->bienThe->mau_sac ?? '' }} {{ $item->bienThe->dung_luong_gb ? '| ' . $item->bienThe->dung_luong_gb . 'GB' : '' }} 
                                            @endif
                                            | x{{ $item->so_luong }}
                                        </p>
                                    </div>
                                    <div class="order-product-price">{{ number_format($item->gia) }}₫</div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="order-footer">
                            <div class="order-total">
                                Tổng tiền: <strong>{{ number_format($order->thanh_tien) }}₫</strong>
                            </div>
                            <div class="order-actions">
                                {{-- Nút Chi tiết --}}
                                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-secondary btn-sm">Chi tiết</a>
                                
                                {{-- Nút Hủy đơn (Chỉ cho phép khi DANG_XU_LY) --}}
                                @if($order->trang_thai == 'DANG_XU_LY')
                                    <form action="{{ route('orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?');" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-outline btn-sm">Hủy đơn</button>
                                    </form>
                                @endif

                                {{-- Nút Đánh giá (Chỉ cho phép khi HOAN_THANH) --}}
                                @if($order->trang_thai == 'HOAN_THANH')
                                    <button class="btn btn-primary btn-sm">Đánh giá</button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center p-5" style="border: 1px dashed #ddd; border-radius: 8px;">
                         <i class="fas fa-box-open" style="font-size: 50px; color: #ccc;"></i>
                        <p class="text-muted mt-3">Bạn chưa có đơn hàng nào ở trạng thái **{{ $statuses[$currentStatus] }}**.</p>
                    </div>
                    @endforelse
                    
                    <div class="pagination-wrapper" style="margin-top: 20px;">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection