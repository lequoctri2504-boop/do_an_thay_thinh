
<table>
    <thead>
        <tr>
            <th colspan="7" style="font-size: 16px; font-weight: bold; text-align: center;">BÁO CÁO DOANH THU NHÂN VIÊN</th>
        </tr>
        <tr>
            <th colspan="7" style="font-weight: bold; text-align: center;">Từ {{ $startDate->format('d/m/Y') }} đến {{ $endDate->format('d/m/Y') }}</th>
        </tr>
        <tr><td colspan="7"></td></tr>
        <tr>
            <th style="font-weight: bold; background-color: #f2f2f2;">Mã Đơn</th>
            <th style="font-weight: bold; background-color: #f2f2f2;">Ngày Đặt</th>
            <th style="font-weight: bold; background-color: #f2f2f2;">Khách Hàng</th>
            <th style="font-weight: bold; background-color: #f2f2f2;">Tổng Tiền (Chưa giảm)</th>
            <th style="font-weight: bold; background-color: #f2f2f2;">Giảm Giá</th>
            <th style="font-weight: bold; background-color: #f2f2f2;">Thành Tiền</th>
            <th style="font-weight: bold; background-color: #f2f2f2;">Phương Thức TT</th>
        </tr>
    </thead>
    <tbody>
        @foreach($ordersToReport as $order)
            <tr>
                <td>{{ $order->ma }}</td>
                <td>{{ \Carbon\Carbon::parse($order->ngay_dat)->format('d/m/Y H:i') }}</td>
                <td>{{ $order->ten_nguoi_nhan }} ({{ $order->nguoiDung->email ?? 'Khách lẻ' }})</td>
                <td>{{ $order->tong_tien }}</td>
                <td>{{ $order->giam_gia }}</td>
                <td>{{ $order->thanh_tien }}</td>
                <td>{{ $order->phuong_thuc_tt }}</td>
            </tr>
        @endforeach
        <tr><td colspan="7"></td></tr>
        <tr>
            <td colspan="5"></td>
            <td style="font-weight: bold;">TỔNG DOANH THU HOÀN THÀNH:</td>
            <td style="font-weight: bold; color: #D70018;">{{ number_format($ordersToReport->sum('thanh_tien'), 0, ',', '.') }}₫</td>
        </tr>
    </tbody>
</table>