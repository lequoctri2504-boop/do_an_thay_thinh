@extends('layouts.admin')

@section('title', 'Quản lý Đơn hàng')

@section('content')
<section class="dashboard-section active">
    <div class="section-header">
        <h1>Quản lý Đơn hàng</h1>
        <div class="header-actions">
            <input type="text" class="form-control" placeholder="Tìm kiếm đơn hàng...">
            <button class="btn btn-primary"><i class="fas fa-filter"></i> Lọc</button>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6" class="text-center">Chưa có đơn hàng</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection