<table>
    <thead>
        <tr>
            <th style="font-weight: bold;">Mã Đơn</th>
            <th style="font-weight: bold;">Khách Hàng</th>
            <th style="font-weight: bold;">Tổng Tiền</th>
            <th style="font-weight: bold;">Trạng Thái</th>
            <th style="font-weight: bold;">Ngày Đặt</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $order)
        <tr>
            <td>{{ $order->ma }}</td>
            <td>{{ $order->ten_nguoi_nhan }}</td>
            <td>{{ number_format($order->thanh_tien) }}</td>
            <td>{{ $order->trang_thai }}</td>
            <td>{{ $order->ngay_dat }}</td>
        </tr>
        @endforeach
    </tbody>
</table>