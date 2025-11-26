@extends('layouts.admin')

@section('title', 'Sao lưu & Khôi phục')

@section('content')
<section class="dashboard-section active">
    <div class="section-header">
        <h1>Sao lưu & Khôi phục</h1>
    </div>

    <div class="dashboard-card">
        <h3>Tạo bản sao lưu</h3>
        <button class="btn btn-primary btn-large">
            <i class="fas fa-database"></i> Sao lưu cơ sở dữ liệu
        </button>
    </div>

    <div class="dashboard-card" style="margin-top: 20px;">
        <h3>Lịch sử sao lưu</h3>
        <p class="text-center" style="padding: 20px;">Chưa có bản sao lưu nào</p>
    </div>
</section>
@endsection