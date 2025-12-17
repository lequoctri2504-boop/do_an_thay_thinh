<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .title { text-transform: uppercase; font-size: 18px; color: #333; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f2f2f2; }
        .footer { margin-top: 30px; text-align: right; font-style: italic; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2 class="title">Báo Cáo Doanh Thu</h2>
        <p>Từ ngày: {{ date('d/m/Y', strtotime($start)) }} - Đến ngày: {{ date('d/m/Y', strtotime($end)) }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Mã Đơn</th>
                <th>Người Nhận</th>
                <th>Tổng Tiền</th>
                <th>Ngày Đặt</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($data as $item)
                <tr>
                    <td>#{{ $item->ma }}</td>
                    <td>{{ $item->ten_nguoi_nhan }}</td>
                    <td class="text-right">{{ number_format($item->thanh_tien) }}đ</td>
                    <td>{{ date('d/m/Y H:i', strtotime($item->ngay_dat)) }}</td>
                </tr>
                @php $total += $item->thanh_tien; @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="text-right"><strong>TỔNG DOANH THU:</strong></td>
                <td class="text-right" style="color: red;"><strong>{{ number_format($total) }}đ</strong></td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Ngày xuất báo cáo: {{ date('d/m/Y H:i') }}</p>
        <p>Người xuất: {{ Auth::user()->ho_ten }}</p>
    </div>
</body>
</html>