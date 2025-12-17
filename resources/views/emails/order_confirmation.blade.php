{{-- resources/views/emails/order_confirmation.blade.php --}}

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Xác nhận Đơn hàng #{{ $order->ma }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6;">

    <div style="max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 20px;">
        <h2 style="color: #4CAF50;">🎉 Cảm ơn bạn đã đặt hàng!</h2>
        
        <p>Xin chào {{ $order->ten_nguoi_nhan }},</p>
        
        <p>Đơn hàng của bạn đã được tiếp nhận thành công. Chúng tôi sẽ xử lý đơn hàng #{{ $order->ma }} của bạn sớm nhất có thể.</p>

        <h3 style="border-bottom: 1px solid #ddd; padding-bottom: 5px;">Chi tiết Đơn hàng: #{{ $order->ma }}</h3>

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr>
                    <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2; text-align: left;">Sản phẩm</th>
                    <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2; text-align: right;">Số lượng</th>
                    <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2; text-align: right;">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                {{-- Giả định mối quan hệ DonHang::chiTiet trả về DonHangChiTiet --}}
                @foreach($order->chiTiet as $item)
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px;">
                            {{ $item->ten_sp_ghi_nhan }} ({{ $item->sku_ghi_nhan }})
                        </td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">{{ $item->so_luong }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">{{ number_format($item->thanh_tien) }}₫</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" style="border: 1px solid #ddd; padding: 8px; text-align: right; font-weight: bold;">Tổng cộng (Chưa giảm):</td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">{{ number_format($order->tong_tien) }}₫</td>
                </tr>
                @if ($order->giam_gia > 0)
                <tr>
                    <td colspan="2" style="border: 1px solid #ddd; padding: 8px; text-align: right; font-weight: bold;">Giảm giá:</td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right; color: red;">-{{ number_format($order->giam_gia) }}₫</td>
                </tr>
                @endif
                <tr>
                    <td colspan="2" style="border: 1px solid #ddd; padding: 8px; text-align: right; font-weight: bold; background-color: #ffc;">Tổng thanh toán:</td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right; font-weight: bold; background-color: #ffc;">{{ number_format($order->thanh_tien) }}₫</td>
                </tr>
            </tfoot>
        </table>

        <p><strong>Địa chỉ giao hàng:</strong> {{ $order->dia_chi_giao }}</p>
        <p><strong>Số điện thoại:</strong> {{ $order->sdt_nguoi_nhan }}</p>
        <p><strong>Phương thức thanh toán:</strong> {{ $order->phuong_thuc_tt }}</p>
        <p><strong>Trạng thái thanh toán:</strong> {{ $order->trang_thai_tt == 'DA_TT' ? 'Đã thanh toán' : 'Chưa thanh toán' }}</p>

        <p>Nếu bạn có bất kỳ thắc mắc nào, vui lòng liên hệ với chúng tôi qua số điện 0962371176.</p>
        
        <p>Trân trọng,<br>Đội ngũ {{ config('app.name') }}</p>
    </div>
</body>
</html>