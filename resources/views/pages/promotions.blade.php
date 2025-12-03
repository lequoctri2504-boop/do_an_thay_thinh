@extends('layouts.app')

@section('title', 'Chương trình Khuyến mãi')

@section('content')
<div class="breadcrumb">
    <div class="container">
        <a href="{{ route('home') }}">Trang chủ</a>
        <i class="fas fa-chevron-right"></i>
        <span>Khuyến mãi</span>
    </div>
</div>

<section class="products-page">
    <div class="container">
        <h1 style="margin-bottom: 20px;"><i class="fas fa-fire"></i> Chương trình Khuyến mãi hiện có</h1>
        
        <div class="dashboard-card" style="padding: 30px; text-align: center;">
            <p>Hiện tại chưa có chương trình khuyến mãi công khai nào.</p>
            <p class="text-muted">Vui lòng quay lại sau để xem các ưu đãi hấp dẫn.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary mt-4">
                <i class="fas fa-arrow-left"></i> Quay lại mua sắm
            </a>
        </div>
        
    </div>
</section>
@endsection