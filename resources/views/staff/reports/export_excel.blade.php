<table>
    <thead>
        <tr><th colspan="4" style="font-weight: bold; text-align: center; font-size: 16px;">BÁO CÁO NHÂN VIÊN ({{ strtoupper($reportType) }})</th></tr>
        <tr><th colspan="4" style="text-align: center;">Từ ngày: {{ $startDate }} - Đến ngày: {{ $endDate }}</th></tr>
    </thead>
    <tbody>
        <tr><td colspan="4"></td></tr>
        <tr><td colspan="2" style="font-weight: bold; background: #ffff00;">TỔNG QUAN</td><td colspan="2"></td></tr>
        <tr><td>Doanh thu:</td><td>{{ number_format($doanhThu) }}₫</td><td>Tổng đơn hàng:</td><td>{{ $tongDonHang }}</td></tr>
        <tr><td>Đơn đang xử lý:</td><td>{{ $donDangXuLy }}</td><td>Tổng khách hàng:</td><td>{{ $tongKhachHang }}</td></tr>
        
        <tr><td colspan="4"></td></tr>
        <tr><td colspan="4" style="font-weight: bold; background: #00ccff;">DỮ LIỆU CHI TIẾT</td></tr>

        @if($reportType == 'doanh_thu')
            <tr><th>Mã đơn</th><th>Ngày đặt</th><th>Khách nhận</th><th>Thành tiền</th></tr>
            @foreach($dataDetails as $item)
                <tr><td>#{{ $item->ma }}</td><td>{{ $item->ngay_dat }}</td><td>{{ $item->ten_nguoi_nhan }}</td><td>{{ $item->thanh_tien }}</td></tr>
            @endforeach
        @elseif($reportType == 'ban_chay' || $reportType == 'ban_cham')
            <tr><th colspan="2">Tên sản phẩm</th><th colspan="2">Số lượng</th></tr>
            @foreach($dataDetails as $item)
                <tr><td colspan="2">{{ $item->ten_sp_ghi_nhan ?? $item->ten }}</td><td colspan="2">{{ $item->total_qty ?? $item->total_sold ?? 0 }}</td></tr>
            @endforeach
        @elseif($reportType == 'ton_kho')
            <tr><th colspan="2">Sản phẩm</th><th>Màu sắc</th><th>Tồn kho</th></tr>
            @foreach($dataDetails as $item)
                <tr><td colspan="2">{{ $item->sanPham->ten }}</td><td>{{ $item->mau_sac }}</td><td>{{ $item->ton_kho }}</td></tr>
            @endforeach
        @endif
    </tbody>
</table>