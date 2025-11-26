@extends('layouts.staff')

@section('title', 'Quản lý đơn hàng')

@section('content')
<section class="dashboard-section active">

    <div class="section-header">
        <h1>Quản lý đơn hàng</h1>
        <div class="header-actions">
            <input type="text" class="form-control" placeholder="Tìm kiếm đơn hàng...">
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
                    </tr>
                </thead>
                <tbody>

                    @foreach($orders as $o)
                    <tr>
                        <td><strong>#{{ $o->id }}</strong></td>
                        <td>{{ $o->nguoiDung->ho_ten }}</td>
                        <td>{{ $o->ngay_dat }}</td>
                        <td>{{ number_format($o->thanh_tien) }}₫</td>
                        <td>{{ $o->trang_thai }}</td>
                    </tr>
                    @endforeach

                    @if($orders->isEmpty())
                    <tr><td colspan="5" class="text-center">Không có dữ liệu</td></tr>
                    @endif

                </tbody>
            </table>
        </div>

        <div class="table-pagination">
            {{ $orders->links() }}
        </div>

    </div>

</section>
@endsection
