<table>
    <thead>
        @if($type == 'doanh_thu' || $type == 'don_hang')
            <tr>
                <th style="font-weight: bold; background-color: #ffff00; border: 1px solid #000;">Mã đơn hàng</th>
                <th style="font-weight: bold; background-color: #ffff00; border: 1px solid #000;">Tên người nhận</th>
                <th style="font-weight: bold; background-color: #ffff00; border: 1px solid #000;">Số điện thoại</th>
                <th style="font-weight: bold; background-color: #ffff00; border: 1px solid #000;">Địa chỉ giao</th>
                <th style="font-weight: bold; background-color: #ffff00; border: 1px solid #000;">Tổng tiền (đ)</th>
                <th style="font-weight: bold; background-color: #ffff00; border: 1px solid #000;">Phương thức TT</th>
                <th style="font-weight: bold; background-color: #ffff00; border: 1px solid #000;">Ngày đặt</th>
            </tr>
        @elseif($type == 'san_pham')
            <tr>
                <th style="font-weight: bold; background-color: #00ff00; border: 1px solid #000;">Tên sản phẩm ghi nhận</th>
                <th style="font-weight: bold; background-color: #00ff00; border: 1px solid #000;">Mã SKU</th>
                <th style="font-weight: bold; background-color: #00ff00; border: 1px solid #000;">Số lượng bán</th>
                <th style="font-weight: bold; background-color: #00ff00; border: 1px solid #000;">Doanh thu (đ)</th>
            </tr>
        @elseif($type == 'khach_hang')
            <tr>
                <th style="font-weight: bold; background-color: #00ffff; border: 1px solid #000;">Họ tên</th>
                <th style="font-weight: bold; background-color: #00ffff; border: 1px solid #000;">Email</th>
                <th style="font-weight: bold; background-color: #00ffff; border: 1px solid #000;">Số điện thoại</th>
                <th style="font-weight: bold; background-color: #00ffff; border: 1px solid #000;">Vai trò</th>
                <th style="font-weight: bold; background-color: #00ffff; border: 1px solid #000;">Ngày đăng ký</th>
            </tr>
        @endif
    </thead>
    <tbody>
        @foreach($data as $item)
            @if($type == 'doanh_thu' || $type == 'don_hang')
                <tr>
                    <td>{{ $item->ma }}</td>
                    <td>{{ $item->ten_nguoi_nhan }}</td>
                    <td>{{ $item->sdt_nguoi_nhan }}</td>
                    <td>{{ $item->dia_chi_giao }}</td>
                    <td>{{ $item->thanh_tien }}</td>
                    <td>{{ $item->phuong_thuc_tt }}</td>
                    <td>{{ $item->ngay_dat }}</td>
                </tr>
            @elseif($type == 'san_pham')
                <tr>
                    <td>{{ $item->ten_sp_ghi_nhan }}</td>
                    <td>{{ $item->sku_ghi_nhan }}</td>
                    <td>{{ $item->total_qty }}</td>
                    <td>{{ $item->total_amount }}</td>
                </tr>
            @elseif($type == 'khach_hang')
                <tr>
                    <td>{{ $item->ho_ten }}</td>
                    <td>{{ $item->email }}</td>
                    <td>{{ $item->sdt }}</td>
                    <td>{{ $item->vai_tro }}</td>
                    <td>{{ $item->created_at }}</td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>