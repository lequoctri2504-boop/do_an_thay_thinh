<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #000; padding: 5px; }
        .table th { background-color: #eee; }
    </style>
</head>
<body>
    <div class="header">
        <h2>DANH SÁCH KHÁCH HÀNG MỚI</h2>
        <p>Từ: {{ $start }} - Đến: {{ $end }}</p>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>Họ tên</th>
                <th>Email</th>
                <th>Số điện thoại</th>
                <th>Địa chỉ</th>
                <th>Ngày đăng ký</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
            <tr>
                <td>{{ $item->ho_ten }}</td>
                <td>{{ $item->email }}</td>
                <td>{{ $item->sdt }}</td>
                <td>{{ $item->dia_chi }}</td>
                <td>{{ $item->created_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>