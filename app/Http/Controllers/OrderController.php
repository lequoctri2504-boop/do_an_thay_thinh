<?php

namespace App\Http\Controllers;

use App\Models\GioHang;
use App\Models\GioHangChiTiet;
use App\Models\DonHang;
use App\Models\DonHangChiTiet;
use App\Models\BienTheSanPham;
use App\Models\KhuyenMai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Carbon\Carbon;

use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    /**
     * Hiển thị trang thanh toán
     */
    public function checkout()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
        }
        
        $cart = GioHang::where('nguoi_dung_id', Auth::id())->first();
        
        if (!$cart) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống!');
        }
        
        $cartItems = GioHangChiTiet::with(['bienThe.sanPham.thuongHieu'])
            ->where('gio_hang_id', $cart->id)
            ->get();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống!');
        }
        
        // 1. Tính tổng tiền (Subtotal)
        $subtotal = $cartItems->sum(function($item) {
            return $item->so_luong * $item->bienThe->gia;
        });
        
        // 2. Lấy thông tin khuyến mãi từ Session
        $discountAmount = Session::get('discount_amount', 0);
        $discountCode = Session::get('discount_code');
        
        // 3. Tính phí vận chuyển (Shipping Fee) - Giả định miễn phí ship từ 500k
        $shippingFee = $subtotal >= 500000 ? 0 : 30000; 
        
        // 4. Tính Tổng cộng (Total)
        $total = $subtotal - $discountAmount + $shippingFee;
        
        // Lấy thông tin user
        $user = Auth::user();
        
        return view('orders.checkout', compact('cartItems', 'subtotal', 'shippingFee', 'total', 'user', 'discountAmount', 'discountCode'));
    }
    
    /**
     * Xử lý áp dụng mã giảm giá (GỬI TỪ FORM CHECKOUT)
     */
    public function applyDiscount(Request $request)
    {
        $code = trim($request->input('coupon_code'));
        
        // Logic Hủy Mã giảm giá (Nếu coupon_code rỗng và có cờ remove_coupon)
        if (empty($code) || $request->has('remove_coupon')) {
             Session::forget(['discount_code', 'discount_amount']);
             return back()->with('success', 'Đã hủy áp dụng mã giảm giá.');
        }

        // 1. Tìm và kiểm tra Khuyến mãi
        $coupon = KhuyenMai::where('ma', $code)
            ->where('ngay_bat_dau', '<=', Carbon::now())
            ->where('ngay_ket_thuc', '>=', Carbon::now())
            ->first();
            
        if (!$coupon) {
            Session::forget(['discount_code', 'discount_amount']);
            return back()->with('error', 'Mã giảm giá không hợp lệ hoặc đã hết hạn.');
        }
        
        // 2. Lấy giỏ hàng để tính tổng tiền
        $cart = GioHang::where('nguoi_dung_id', Auth::id())->first();
        if (!$cart) {
            return back()->with('error', 'Giỏ hàng trống, không thể áp dụng mã.');
        }
        $subtotal = $cart->chiTiet->sum(fn($item) => $item->so_luong * $item->bienThe->gia);
        
        // 3. Tính toán giá trị giảm
        $discountValue = 0;
        $giaTri = trim($coupon->gia_tri);

        if (Str::endsWith($giaTri, '%')) {
            $percent = (float) Str::before($giaTri, '%');
            $discountValue = $subtotal * ($percent / 100);
        } else {
            // Giá trị cố định
            $discountValue = (float) $giaTri; 
        }
        
        if ($discountValue <= 0) {
            Session::forget(['discount_code', 'discount_amount']);
            return back()->with('error', 'Mã giảm giá không áp dụng được cho đơn hàng này.');
        }

        // 4. Lưu vào Session
        Session::put('discount_code', $code);
        Session::put('discount_amount', $discountValue);

        return back()->with('success', 'Đã áp dụng mã giảm giá **' . $code . '** (Giảm ' . number_format($discountValue, 0, ',', '.') . '₫) thành công!');
    }
    
    /**
     * Xử lý đặt hàng
     */
    public function place(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
        }
        
        $request->validate([
            'ten_nguoi_nhan' => 'required|string|max:191',
            'sdt_nguoi_nhan' => 'required|string|max:32',
            'dia_chi_giao' => 'required|string',
            'phuong_thuc_tt' => 'required|in:COD,CHUYEN_KHOAN,VNPAY',
            'ghi_chu' => 'nullable|string|max:500'
        ], [
            'ten_nguoi_nhan.required' => 'Vui lòng nhập tên người nhận',
            'sdt_nguoi_nhan.required' => 'Vui lòng nhập số điện thoại',
            'dia_chi_giao.required' => 'Vui lòng nhập địa chỉ giao hàng',
            'phuong_thuc_tt.required' => 'Vui lòng chọn phương thức thanh toán'
        ]);
        
        DB::beginTransaction();
        try {
            // Lấy giỏ hàng
            $cart = GioHang::where('nguoi_dung_id', Auth::id())->first();
            
            if (!$cart) {
                throw new \Exception('Giỏ hàng không tồn tại!');
            }
            
            $cartItems = GioHangChiTiet::with(['bienThe.sanPham'])
                ->where('gio_hang_id', $cart->id)
                ->get();
            
            if ($cartItems->isEmpty()) {
                throw new \Exception('Giỏ hàng trống!');
            }
            
            // Kiểm tra tồn kho
            foreach ($cartItems as $item) {
                if ($item->bienThe->ton_kho < $item->so_luong) {
                    throw new \Exception("Sản phẩm {$item->bienThe->sanPham->ten} không đủ số lượng trong kho!");
                }
            }
            
            // Tính tổng tiền
            $tongTien = $cartItems->sum(function($item) {
                return $item->so_luong * $item->bienThe->gia;
            });
            
            // LẤY GIẢM GIÁ TỪ SESSION
            $giamGia = Session::get('discount_amount', 0); 

            $phiVanChuyen = $tongTien >= 500000 ? 0 : 30000;
            $thanhTien = $tongTien - $giamGia + $phiVanChuyen; // <-- FINAL CALCULATION
            
            // Tạo mã đơn hàng
            $maDonHang = 'DH' . date('YmdHis') . rand(1000, 9999);
            
            // Tạo đơn hàng
            $donHang = new DonHang();
            $donHang->ma = $maDonHang;
            $donHang->nguoi_dung_id = Auth::id();
            $donHang->trang_thai = 'DANG_XU_LY';
            $donHang->tong_tien = $tongTien;
            $donHang->giam_gia = $giamGia; // <-- SAVE DISCOUNT
            $donHang->phi_van_chuyen = $phiVanChuyen;
            $donHang->thanh_tien = $thanhTien;
            $donHang->ten_nguoi_nhan = $request->ten_nguoi_nhan;
            $donHang->sdt_nguoi_nhan = $request->sdt_nguoi_nhan;
            $donHang->dia_chi_giao = $request->dia_chi_giao;
            $donHang->phuong_thuc_tt = $request->phuong_thuc_tt;
            $donHang->trang_thai_tt = $request->phuong_thuc_tt == 'COD' ? 'CHUA_TT' : 'CHUA_TT';
            $donHang->ngay_dat = now();
            $donHang->ghi_chu = $request->ghi_chu;
            $donHang->save();
            
            // Tạo chi tiết đơn hàng và cập nhật tồn kho
            foreach ($cartItems as $item) {
                // Thêm chi tiết đơn hàng
                $chiTiet = new DonHangChiTiet();
                $chiTiet->don_hang_id = $donHang->id;
                $chiTiet->san_pham_id = $item->bienThe->san_pham_id;
                $chiTiet->bien_the_id = $item->bienThe->id;
                $chiTiet->ten_sp_ghi_nhan = $item->bienThe->sanPham->ten;
                $chiTiet->sku_ghi_nhan = $item->bienThe->sku;
                $chiTiet->gia = $item->bienThe->gia;
                $chiTiet->so_luong = $item->so_luong;
                $chiTiet->thanh_tien = $item->so_luong * $item->bienThe->gia;
                $chiTiet->save();
                
                // Giảm tồn kho
                $item->bienThe->ton_kho = $item->bienThe->ton_kho - $item->so_luong;
                $item->bienThe->save();
            }
            
            // Xóa giỏ hàng VÀ KHUYẾN MÃI TRONG SESSION
            GioHangChiTiet::where('gio_hang_id', $cart->id)->delete();
            Session::forget(['cart_count', 'discount_code', 'discount_amount']); // <-- CLEAR DISCOUNT

            
            DB::commit();
            
            // Nếu thanh toán ZaloPay, chuyển đến trang thanh toán
            if ($request->phuong_thuc_tt == 'VNPAY') {
                return redirect()->route('payment.zalopay.create', ['order_id' => $donHang->id]);
            }
            
            return redirect()->route('orders.show', $donHang->id)
                ->with('success', 'Đặt hàng thành công! Mã đơn hàng: ' . $maDonHang);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    /**
     * Hiển thị chi tiết đơn hàng
     */
    public function show($id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
        }
        
        $order = DonHang::with(['chiTiet.sanPham.thuongHieu', 'chiTiet.bienThe'])
            ->where('id', $id)
            ->where('nguoi_dung_id', Auth::id())
            ->firstOrFail();
        
        return view('orders.show', compact('order'));
    }
    
    /**
     * Hủy đơn hàng
     */
    public function cancel($id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
        }
        
        DB::beginTransaction();
        try {
            $order = DonHang::where('id', $id)
                ->where('nguoi_dung_id', Auth::id())
                ->firstOrFail();
            
            // Chỉ cho phép hủy đơn hàng đang xử lý
            if ($order->trang_thai != 'DANG_XU_LY') {
                throw new \Exception('Không thể hủy đơn hàng ở trạng thái này!');
            }
            
            // Cập nhật trạng thái
            $order->trang_thai = 'HUY';
            $order->save();
            
            // Hoàn lại tồn kho
            foreach ($order->chiTiet as $item) {
                if ($item->bienThe) {
                    $item->bienThe->ton_kho = $item->bienThe->ton_kho + $item->so_luong;
                    $item->bienThe->save();
                }
            }
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Đã hủy đơn hàng thành công!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    /**
     * Tạo thanh toán ZaloPay
     */
    // public function zalopayCreate($order_id)
    // {
    //     if (!Auth::check()) {
    //         return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
    //     }
        
    //     $order = DonHang::where('id', $order_id)
    //         ->where('nguoi_dung_id', Auth::id())
    //         ->firstOrFail();
            
    //     // --- CẤU HÌNH VNPAY ĐƯỢC LẤY TỪ .ENV ---
    //     $vnp_TmnCode = env('VNPAY_TMNCODE'); 
    //     $vnp_HashSecret = env('VNPAY_HASHSECRET');
    //     $vnp_Url = env('VNPAY_URL'); 
        
    //     // Route callback VNPAY sẽ trả về route cũ (payment.zalopay.callback)
    //     $vnp_ReturnUrl = route('payment.zalopay.callback'); 
        
    //     $vnp_TxnRef = $order->ma; 
    //     $vnp_Amount = $order->thanh_tien * 100; // VNPAY yêu cầu số tiền * 100
    //     $vnp_OrderInfo = "Thanh toan don hang #" . $order->ma;
    //     $vnp_OrderType = 'billpayment';
    //     $vnp_Locale = 'vn';
    //     $vnp_IpAddr = request()->ip();
    //     $vnp_CurrCode = 'VND';
    //     $vnp_Command = 'pay';
        
    //     $inputData = array(
    //         "vnp_Version" => "2.1.0",
    //         "vnp_TmnCode" => $vnp_TmnCode,
    //         "vnp_Amount" => $vnp_Amount,
    //         "vnp_Command" => $vnp_Command,
    //         "vnp_CreateDate" => date('YmdHis'),
    //         "vnp_CurrCode" => $vnp_CurrCode,
    //         "vnp_IpAddr" => $vnp_IpAddr,
    //         "vnp_Locale" => $vnp_Locale,
    //         "vnp_OrderInfo" => $vnp_OrderInfo,
    //         "vnp_OrderType" => $vnp_OrderType,
    //         "vnp_ReturnUrl" => $vnp_ReturnUrl,
    //         "vnp_TxnRef" => $vnp_TxnRef,
    //     );

    //     ksort($inputData);
    //     $query = "";
    //     $hashdata = "";
    //     $i = 0;
    //     foreach ($inputData as $key => $value) {
    //         if ($i == 1) {
    //             $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
    //         } else {
    //             $hashdata .= urlencode($key) . "=" . urlencode($value);
    //             $i = 1;
    //         }
    //         $query .= urlencode($key) . "=" . urlencode($value) . '&';
    //     }

    //     $vnp_SecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
    //     $vnp_Url .= "?" . $query . "vnp_SecureHash=" . $vnp_SecureHash;

    //     // Chuyển hướng đến cổng thanh toán VNPAY
    //     return redirect()->to($vnp_Url); 
    // }
    
    /**
     * Callback từ VNPAY (THAY THẾ zalopayCallback)
     * Route: POST thanh-toan/zalopay/callback (payment.zalopay.callback)
     */
    // public function zalopayCallback(Request $request)
    // {
    //     $vnp_HashSecret = env('VNPAY_HASHSECRET'); // Secret Key
    //     $inputData = $request->all();
    //     $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
    //     unset($inputData['vnp_SecureHash']);

    //     ksort($inputData);
    //     $hashData = "";
    //     $i = 0;
    //     foreach ($inputData as $key => $value) {
    //         if ($i == 1) {
    //             $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
    //         } else {
    //             $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
    //             $i = 1;
    //         }
    //     }

    //     $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        
    //     $result = [];

    //     if ($secureHash == $vnp_SecureHash) {
    //         // Lấy thông tin đơn hàng dựa trên mã tham chiếu (vnp_TxnRef)
    //         $order = DonHang::where('ma', $inputData['vnp_TxnRef'])->first();
            
    //         if ($order) {
    //             if ($inputData['vnp_ResponseCode'] == '00') {
    //                 // Kiểm tra số tiền có khớp không (VNPAY Amount đã nhân 100)
    //                 if ($order->thanh_tien * 100 == $inputData['vnp_Amount']) {
    //                     // Cập nhật trạng thái thanh toán
    //                     $order->trang_thai_tt = 'DA_TT';
    //                     $order->ngay_thanh_toan = now();
    //                     $order->save();
                        
    //                     $result["RspCode"] = "00";
    //                     $result["Message"] = "Thanh toan thanh cong";
    //                 } else {
    //                      $result["RspCode"] = "04"; // Số tiền không hợp lệ
    //                      $result["Message"] = "Invalid amount";
    //                 }
    //             } else {
    //                  $result["RspCode"] = "02"; // Giao dịch thất bại
    //                  $result["Message"] = "Payment failed";
    //             }
    //         } else {
    //             $result["RspCode"] = "01"; // Đơn hàng không tìm thấy
    //             $result["Message"] = "Order not found";
    //         }
    //     } else {
    //         $result["RspCode"] = "97";
    //         $result["Message"] = "Chu ky khong hop le";
    //     }
        
    //     // Trả về kết quả cho VNPAY theo định dạng JSON
    //     return response()->json($result);
    // }

    /**
     * TẠO GIAO DỊCH VNPAY (Tái sử dụng tên phương thức zalopayCreate)
     */
    public function zalopayCreate($order_id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
        }
        
        $order = DonHang::where('id', $order_id)
            ->where('nguoi_dung_id', Auth::id())
            ->firstOrFail();
            
        // --- CẤU HÌNH VNPAY ĐƯỢC LẤY TỪ .ENV ---
        $vnp_TmnCode = env('VNPAY_TMNCODE', '70F0B2OD'); 
        $vnp_HashSecret = env('VNPAY_HASHSECRET', 'ZA0OT6THCG92FA3BYZV892OO1OY7UFGN');
        $vnp_Url = env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'); 
        
        // FIX: Đổi route name thành route Return URL mới
        $vnp_ReturnUrl = route('payment.vnpay.return'); // <<< SỬ DỤNG ROUTE MỚI >>>
        
        $vnp_TxnRef = $order->ma; 
        $vnp_Amount = $order->thanh_tien * 100; // VNPAY yêu cầu số tiền * 100
        $vnp_OrderInfo = "Thanh toan don hang #" . $order->ma;
        $vnp_OrderType = 'billpayment';
        $vnp_Locale = 'vn';
        $vnp_IpAddr = request()->ip();
        $vnp_CurrCode = 'VND';
        $vnp_Command = 'pay';
        
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => $vnp_Command,
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => $vnp_CurrCode,
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_ReturnUrl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        ksort($inputData);
        $query = "";
        $hashdata = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_SecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url .= "?" . $query . "vnp_SecureHash=" . $vnp_SecureHash;

        // Chuyển hướng đến cổng thanh toán VNPAY
        return redirect()->to($vnp_Url); 
    }

    /**
     * XỬ LÝ RETURN URL VNPAY (Browser Redirect - Dùng GET)
     * Hàm này được gọi khi trình duyệt của khách hàng quay lại.
     */
    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = env('VNPAY_HASHSECRET');
        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash']);

        ksort($inputData);
        $hashData = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        
        // Kiểm tra Hash có hợp lệ không
        if ($secureHash == $vnp_SecureHash) {
            $order = DonHang::where('ma', $inputData['vnp_TxnRef'])->first();
            
            if ($order) {
                // Kiểm tra mã phản hồi VNPAY và trạng thái đơn hàng
                if ($inputData['vnp_ResponseCode'] == '00' && $inputData['vnp_TransactionStatus'] == '00') {
                    if ($order->thanh_tien * 100 == $inputData['vnp_Amount']) {
                        // Trạng thái DA_TT chỉ được cập nhật ở IPN, ở đây chỉ thông báo thành công
                        // Tuy nhiên, để đơn giản hóa, ta update ở đây
                        $order->trang_thai_tt = 'DA_TT'; 
                        $order->ngay_thanh_toan = now();
                        $order->save();
                        
                        return redirect()->route('orders.show', $order->id)
                            ->with('success', 'Thanh toán VNPAY thành công! Mã giao dịch: ' . $inputData['vnp_TransactionNo']);
                    } else {
                        return redirect()->route('orders.show', $order->id)
                            ->with('error', 'Lỗi thanh toán: Số tiền không khớp. Vui lòng liên hệ hỗ trợ.');
                    }
                } else {
                    return redirect()->route('orders.show', $order->id)
                        ->with('error', 'Thanh toán VNPAY thất bại. Mã lỗi: ' . $inputData['vnp_ResponseCode'] . ' - ' . $inputData['vnp_OrderInfo']);
                }
            } else {
                 return redirect()->route('home')->with('error', 'Lỗi: Không tìm thấy đơn hàng');
            }
        } else {
             return redirect()->route('home')->with('error', 'Lỗi: Chữ ký bảo mật không hợp lệ.');
        }
    }
    
    /**
     * XỬ LÝ IPN VNPAY (Server to Server - Dùng POST)
     */
    public function vnpayIPN(Request $request)
    {
        // ... (Logic vnpayIPN đã được cung cấp ở câu trả lời trước, không cần thay đổi)
        $vnp_HashSecret = env('VNPAY_HASHSECRET'); 
        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash']);

        ksort($inputData);
        $hashData = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        
        $result = ["RspCode" => "99", "Message" => "Unknown error"];

        if ($secureHash == $vnp_SecureHash) {
            $order = DonHang::where('ma', $inputData['vnp_TxnRef'])->first();
            
            if ($order) {
                if ($order->thanh_tien * 100 == $inputData['vnp_Amount']) {
                    if ($inputData['vnp_ResponseCode'] == '00' && $inputData['vnp_TransactionStatus'] == '00') {
                        // Đã thanh toán, update trạng thái
                        if ($order->trang_thai_tt != 'DA_TT') {
                             $order->trang_thai_tt = 'DA_TT';
                             $order->ngay_thanh_toan = now();
                             $order->save();
                        }
                        $result["RspCode"] = "00";
                        $result["Message"] = "Confirm Success";
                    } else {
                         // VNPAY yêu cầu trả về 00 dù giao dịch thất bại nếu logic xử lý thành công
                         $result["RspCode"] = "00"; 
                         $result["Message"] = "Confirm Success";
                    }
                } else {
                    $result["RspCode"] = "04"; 
                    $result["Message"] = "Invalid amount";
                }
            } else {
                $result["RspCode"] = "01"; 
                $result["Message"] = "Order not found";
            }
        } else {
            $result["RspCode"] = "97"; 
            $result["Message"] = "Invalid Checksum";
        }
        
        return response()->json($result);
    }
}