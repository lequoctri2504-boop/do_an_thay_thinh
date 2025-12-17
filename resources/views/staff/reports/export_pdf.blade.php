<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        /* QUAN TRỌNG: Khai báo font DejaVu Sans */
        body { 
            font-family: 'DejaVu Sans', sans-serif; 
            font-size: 13px; 
            line-height: 1.5;
            color: #333;
        }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .section-title { background: #f4f4f4; padding: 8px; font-weight: bold; margin: 20px 0 10px 0; border-left: 4px solid #3498db; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .footer { margin-top: 50px; text-align: right; font-style: italic; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin:0; text-transform: uppercase;">BÁO CÁO THỐNG KÊ NHÂN VIÊN</h2>
        <p>Khoảng thời gian: {{ date('d/m/Y', strtotime($startDate)) }} đến {{ date('d/m/Y', strtotime($endDate)) }}</p>
    </div>

    <div class="section-title">I. CHỈ SỐ TỔNG QUAN</div>
    <table>
        <tr>
            <td>Doanh thu đạt được:</td>
            <td class="text-end"><strong>{{ number_format($doanhThu) }} VNĐ</strong></td>
        </tr>
        <tr>
            <td>Tổng số đơn hàng:</td>
            <td class="text-end">{{ $tongDonHang }} đơn</td>
        </tr>
        <tr>
            <td>Đơn đang xử lý:</td>
            <td class="text-end">{{ $donDangXuLy }} đơn</td>
        </tr>
    </table>

    <div class="section-title">II. DỮ LIỆU CHI TIẾT ({{ strtoupper($reportType) }})</div>
    
    @if($reportType == 'doanh_thu')
        <table>
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Ngày đặt</th>
                    <th>Khách hàng</th>
                    <th class="text-end">Thành tiền</th>
                </tr>
            </thead>
            @foreach($dataDetails as $item)
                <tr>
                    <td>#{{ $item->ma }}</td>
                    <td>{{ date('d/m/Y', strtotime($item->ngay_dat)) }}</td>
                    <td>{{ $item->ten_nguoi_nhan }}</td>
                    <td class="text-end">{{ number_format($item->thanh_tien) }}đ</td>
                </tr>
            @endforeach
        </table>
    @elseif($reportType == 'ban_chay')
        <table>
            <thead>
                <tr>
                    <th>Tên sản phẩm</th>
                    <th class="text-center">Số lượng bán</th>
                </tr>
            </thead>
            @foreach($banChay as $sp)
                <tr>
                    <td>{{ $sp->ten_sp_ghi_nhan }}</td>
                    <td class="text-center">{{ $sp->total_qty }}</td>
                </tr>
            @endforeach
        </table>
    @endif

    <div class="footer">
        Ngày xuất báo cáo: {{ date('d/m/Y H:i') }}<br>
        Nhân viên thực hiện: {{ auth()->user()->ho_ten }}
    </div>
</body>
</html>