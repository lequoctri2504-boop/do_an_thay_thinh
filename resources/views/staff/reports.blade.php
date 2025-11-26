@extends('layouts.staff')

@section('title', 'Báo cáo - Thống kê')

@section('content')
<section class="dashboard-section active">

    <div class="section-header">
        <h1>Báo cáo - Thống kê</h1>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <i class="fas fa-money-bill-wave"></i>
            <div class="stat-content">
                <h3>Doanh thu</h3>
                <div class="stat-value">{{ number_format($tongDonHang ?? 0) }} đơn</div>
            </div>
        </div>
    </div>

</section>
@endsection
