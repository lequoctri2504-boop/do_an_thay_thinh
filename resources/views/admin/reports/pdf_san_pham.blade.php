<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #000; padding: 5px; text-align: left; }
        .table th { background-color: #eee; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>BÁO CÁO THỐNG KÊ SẢN PHẨM</h2>
        <p>Từ: {{ $start }} - Đến: {{ $end }}</p>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>STT</th>
                <th>Tên sản phẩm</th>
                <th>Mã SKU</th>
                <th class="text-center">Số lượng bán</th>
                <th class="text-right">Doanh thu</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $key => $item)
            <tr>
                <td class="text-center">{{ $key + 1 }}</td>
                <td>{{ $item->ten_sp_ghi_nhan }}</td>
                <td>{{ $item->sku_ghi_nhan }}</td>
                <td class="text-center">{{ number_format($item->total_qty) }}</td>
                <td class="text-right">{{ number_format($item->total_amount) }}đ</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>