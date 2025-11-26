@extends('layouts.admin')

@section('title', 'Quản lý Khuyến mãi')

@section('content')
<section class="dashboard-section active">
    <div class="section-header">
        <h1>Quản lý Khuyến mãi</h1>
        <div class="header-actions">
            <button class="btn btn-primary"><i class="fas fa-plus"></i> Tạo khuyến mãi</button>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tên chương trình</th>
                        <th>Mã giảm giá</th>
                        <th>Giảm (%)</th>
                        <th>Ngày bắt đầu</th>
                        <th>Ngày kết thúc</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="7" class="text-center">Chưa có khuyến mãi</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection