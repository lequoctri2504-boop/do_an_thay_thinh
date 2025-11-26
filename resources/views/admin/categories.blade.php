@extends('layouts.admin')

@section('title', 'Quản lý Danh mục & Thương hiệu')

@section('content')
<section class="dashboard-section active">
    <div class="section-header">
        <h1>Quản lý Danh mục & Thương hiệu</h1>
        <div class="header-actions">
            <button class="btn btn-primary"><i class="fas fa-plus"></i> Thêm danh mục</button>
            <button class="btn btn-secondary"><i class="fas fa-plus"></i> Thêm thương hiệu</button>
        </div>
    </div>

    <div class="dashboard-row">
        <div class="dashboard-card col-6">
            <div class="card-header">
                <h3><i class="fas fa-tags"></i> Danh mục sản phẩm</h3>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tên danh mục</th>
                            <th>Slug</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4" class="text-center">Chưa có dữ liệu</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="dashboard-card col-6">
            <div class="card-header">
                <h3><i class="fas fa-copyright"></i> Thương hiệu</h3>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tên thương hiệu</th>
                            <th>Slug</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4" class="text-center">Chưa có dữ liệu</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection