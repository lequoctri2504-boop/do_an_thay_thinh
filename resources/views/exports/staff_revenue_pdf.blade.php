// resources/views/exports/staff_revenue_pdf.blade.php

<!DOCTYPE html>
<html>
<head>
    <title>Báo cáo Doanh thu</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        h1, h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .footer { margin-top: 30px; text-align: right; font-size: 12px; font-weight: bold; }
        .total { color: #D70018; }
    </style>
</head>
<body>
    <h1>BÁO CÁO DOANH THU NHÂN VIÊN</h1>
    <h2>Từ {{ $startDate->format('d/m/Y') }} đến {{ $endDate->format('d/m/Y') }}</h2>

    <table>
        <thead>
            <tr>
                <th>Mã Đơn</th>
                <th>Ngày Đặt</th>
                <th>Khách Hàng</th>
                <th>Tổng Tiền</th>
                <th>Giảm Giá</th>
                <th>Thành Tiền</th>
                <th>PTTT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ordersToReport as $order)
                <tr>
                    <td>{{ $order->ma }}</td>
                    <td>{{ \Carbon\Carbon::parse($order->ngay_dat)->format('d/m/Y H:i') }}</td>
                    <td>{{ $order->ten_nguoi_nhan }}</td>
                    <td>{{ number_format($order->tong_tien, 0, ',', '.') }}₫</td>
                    <td>{{ number_format($order->giam_gia, 0, ',', '.') }}₫</td>
                    <td>{{ number_format($order->thanh_tien, 0, ',', '.') }}₫</td>
                    <td>{{ $order->phuong_thuc_tt }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Tổng Doanh Thu Hoàn Thành: 
        <span class="total">{{ number_format($ordersToReport->sum('thanh_tien'), 0, ',', '.') }}₫</span>
    </div>
</body>
</html>