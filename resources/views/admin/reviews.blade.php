@extends('layouts.admin')

@section('title', 'Quản lý Đánh giá')

@section('content')
<section class="dashboard-section active">
    <div class="section-header">
        <h1>Quản lý Đánh giá</h1>
        <div class="header-actions">
            <select class="form-select">
                <option>Tất cả</option>
                <option>Chờ duyệt</option>
                <option>Đã duyệt</option>
            </select>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Khách hàng</th>
                        <th>Sản phẩm</th>
                        <th>Đánh giá</th>
                        <th>Nội dung</th>
                        <th>Ngày</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="7" class="text-center">Chưa có đánh giá</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection