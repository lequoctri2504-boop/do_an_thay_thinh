<?php

namespace App\Http\Controllers;

use App\Models\GioHang;
use App\Models\GioHangChiTiet;
use App\Models\DonHang;
use App\Models\DonHangChiTiet;
use App\Models\BienTheSanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Hiển thị trang thanh toán
     */
    // public function checkout()
    // {
    //     if (!Auth::check()) {
    //         return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
    //     }
        
    //     $cart = GioHang::where('nguoi_dung_id', Auth::id())->first();
        
    //     if (!$cart) {
    //         return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống!');
    //     }
        
    //     $cartItems = GioHangChiTiet::with(['bienThe.sanPham.thuongHieu'])
    //         ->where('gio_hang_id', $cart->id)
    //         ->get();
        
    //     if ($cartItems->isEmpty()) {
    //         return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống!');
    //     }
        
    //     // Tính tổng tiền
    //     $subtotal = $cartItems->sum(function($item) {
    //         return $item->so_luong * $item->bienThe->gia;
    //     });
        
    //     $shippingFee = $subtotal >= 500000 ? 0 : 30000; // Miễn phí ship từ 500k
    //     $total = $subtotal + $shippingFee;
        
    //     // Lấy thông tin user
    //     $user = Auth::user();
        
    //     return view('orders.checkout', compact('cartItems', 'subtotal', 'shippingFee', 'total', 'user'));
    // }
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
        
        // Tính tổng tiền (Subtotal)
        $subtotal = $cartItems->sum(function($item) {
            return $item->so_luong * $item->bienThe->gia;
        });
        
        $shippingFee = $subtotal >= 500000 ? 0 : 30000; // Miễn phí ship từ 500k
        $total = $subtotal + $shippingFee;
        
        // Lấy thông tin user
        $user = Auth::user();
        
        // Lệnh gọi view đang cố gắng tìm file tại: resources/views/orders/checkout.blade.php
        return view('orders.checkout', compact('cartItems', 'subtotal', 'shippingFee', 'total', 'user'));
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
            'phuong_thuc_tt' => 'required|in:COD,CHUYEN_KHOAN,ZALOPAY',
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
            
            $phiVanChuyen = $tongTien >= 500000 ? 0 : 30000;
            $thanhTien = $tongTien + $phiVanChuyen;
            
            // Tạo mã đơn hàng
            $maDonHang = 'DH' . date('YmdHis') . rand(1000, 9999);
            
            // Tạo đơn hàng
            $donHang = new DonHang();
            $donHang->ma = $maDonHang;
            $donHang->nguoi_dung_id = Auth::id();
            $donHang->trang_thai = 'DANG_XU_LY';
            $donHang->tong_tien = $tongTien;
            $donHang->giam_gia = 0;
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
            
            // Xóa giỏ hàng
            GioHangChiTiet::where('gio_hang_id', $cart->id)->delete();
            session(['cart_count' => 0]);
            
            DB::commit();
            
            // Nếu thanh toán ZaloPay, chuyển đến trang thanh toán
            if ($request->phuong_thuc_tt == 'ZALOPAY') {
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
    public function zalopayCreate($order_id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
        }
        
        $order = DonHang::where('id', $order_id)
            ->where('nguoi_dung_id', Auth::id())
            ->firstOrFail();
        
        // Cấu hình ZaloPay
        $config = [
            "app_id" => env('ZALOPAY_APP_ID', '2553'),
            "key1" => env('ZALOPAY_KEY1', 'PcY4iZIKFCIdgZvA6ueMcMHHUbRLYjPL'),
            "key2" => env('ZALOPAY_KEY2', 'kLtgPl8HHhfvMuDHPwKfgfsY4Ydm9eIz'),
            "endpoint" => env('ZALOPAY_ENDPOINT', 'https://sb-openapi.zalopay.vn/v2/create')
        ];
        
        $embeddata = json_encode([
            'redirecturl' => route('payment.zalopay.callback')
        ]);
        
        $items = json_encode([]);
        $transID = rand(0, 1000000);
        
        $order_data = [
            "app_id" => $config["app_id"],
            "app_trans_id" => date("ymd") . "_" . $transID,
            "app_user" => Auth::user()->email,
            "app_time" => round(microtime(true) * 1000),
            "item" => $items,
            "embed_data" => $embeddata,
            "amount" => $order->thanh_tien,
            "description" => "Thanh toán đơn hàng #" . $order->ma,
            "bank_code" => "",
            "callback_url" => route('payment.zalopay.callback')
        ];
        
        // Tạo MAC
        $data = $config["app_id"] . "|" . $order_data["app_trans_id"] . "|" . $order_data["app_user"] . "|" . 
                $order_data["amount"] . "|" . $order_data["app_time"] . "|" . $order_data["embed_data"] . "|" . 
                $order_data["item"];
        $order_data["mac"] = hash_hmac("sha256", $data, $config["key1"]);
        
        // Gọi API ZaloPay
        $context = stream_context_create([
            "http" => [
                "header" => "Content-type: application/x-www-form-urlencoded\r\n",
                "method" => "POST",
                "content" => http_build_query($order_data)
            ]
        ]);
        
        $resp = @file_get_contents($config["endpoint"], false, $context);
        $result = json_decode($resp, true);
        
        if (isset($result['order_url'])) {
            // Lưu app_trans_id để verify sau
            $order->ma_giao_dich = $order_data["app_trans_id"];
            $order->save();
            
            return redirect($result['order_url']);
        } else {
            return redirect()->route('orders.show', $order->id)
                ->with('error', 'Không thể tạo thanh toán ZaloPay. Vui lòng thử lại sau!');
        }
    }
    
    /**
     * Callback từ ZaloPay
     */
    public function zalopayCallback(Request $request)
    {
        $config = [
            "key2" => env('ZALOPAY_KEY2', 'kLtgPl8HHhfvMuDHPwKfgfsY4Ydm9eIz')
        ];
        
        $result = [];
        
        try {
            $postdata = file_get_contents('php://input');
            $postdatajson = json_decode($postdata, true);
            $mac = hash_hmac("sha256", $postdatajson["data"], $config["key2"]);
            
            if (strcmp($mac, $postdatajson["mac"]) != 0) {
                $result["return_code"] = -1;
                $result["return_message"] = "mac not equal";
            } else {
                $datajson = json_decode($postdatajson["data"], true);
                
                // Tìm đơn hàng
                $order = DonHang::where('ma_giao_dich', $datajson["app_trans_id"])->first();
                
                if ($order) {
                    // Cập nhật trạng thái thanh toán
                    $order->trang_thai_tt = 'DA_TT';
                    $order->ngay_thanh_toan = now();
                    $order->save();
                    
                    $result["return_code"] = 1;
                    $result["return_message"] = "success";
                } else {
                    $result["return_code"] = 0;
                    $result["return_message"] = "Order not found";
                }
            }
        } catch (\Exception $e) {
            $result["return_code"] = 0;
            $result["return_message"] = $e->getMessage();
        }
        
        return response()->json($result);
    }
}