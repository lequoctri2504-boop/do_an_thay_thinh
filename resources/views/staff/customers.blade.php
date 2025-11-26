@extends('layouts.staff')

@section('title', 'Quản lý khách hàng')

@section('content')
<section class="dashboard-section">

    <div class="section-header">
        <h1>Quản lý khách hàng</h1>
    </div>

    <div class="dashboard-card">
        <div class="table-responsive">

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>SĐT</th>
                        <th>Ngày tạo</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($customers as $c)
                    <tr>
                        <td>{{ $c->ho_ten }}</td>
                        <td>{{ $c->email }}</td>
                        <td>{{ $c->sdt }}</td>
                        <td>{{ $c->created_at }}</td>
                    </tr>
                    @endforeach

                    @if($customers->isEmpty())
                    <tr><td colspan="4" class="text-center">Không có khách hàng</td></tr>
                    @endif
                </tbody>
            </table>

        </div>

        {{ $customers->links() }}
    </div>

</section>
@endsection
