@extends('layouts.staff')

@section('title', 'Quản lý sản phẩm')

@section('content')
<section class="dashboard-section active">

    <div class="section-header">
        <h1>Quản lý sản phẩm</h1>
        <div class="header-actions">
            <input type="text" class="form-control" placeholder="Tìm kiếm sản phẩm...">
        </div>
    </div>

    <div class="dashboard-card">
        <div class="table-responsive">

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Hình ảnh</th>
                        <th>Sản phẩm</th>
                        <th>Thương hiệu</th>
                        <th>Giá</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach($products as $p)
                    <tr>
                        <td><img src="{{ asset('img/' . $p->hinh_anh) }}" class="product-thumb"></td>
                        <td>{{ $p->ten_san_pham }}</td>
                        <td>{{ $p->hang }}</td>
                        <td><strong>{{ number_format($p->gia) }}₫</strong></td>
                    </tr>
                    @endforeach

                    @if($products->isEmpty())
                    <tr><td colspan="4" class="text-center">Không có sản phẩm</td></tr>
                    @endif

                </tbody>
            </table>

        </div>

        {{ $products->links() }}
    </div>

</section>
@endsection
