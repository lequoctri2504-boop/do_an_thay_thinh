<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DonHang;
use DonHangChiTiet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart');
        if(!$cart || count($cart) == 0) {
            return redirect()->route('home');
        }
        
        $total = 0;
        foreach($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('checkout.index', compact('cart', 'total'));
    }

    public function process(Request $request)
    {
        $cart = session()->get('cart');
        $total = 0;
        foreach($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        DB::beginTransaction();
        try {
            // 1. Tạo đơn hàng
            $donHang = new DonHang();
            $donHang->ma = 'DH' . strtoupper(Str::random(8)); // Ví dụ: DHABC123
            $donHang->nguoi_dung_id = Auth::id();
            $donHang->ten_nguoi_nhan = $request->ten_nguoi_nhan;
            $donHang->sdt_nguoi_nhan = $request->sdt_nguoi_nhan;
            $donHang->dia_chi_giao = $request->dia_chi_giao;
            $donHang->tong_tien = $total;
            $donHang->thanh_tien = $total; // Có thể trừ giảm giá nếu có
            $donHang->trang_thai = 'DANG_XU_LY';
            $donHang->phuong_thuc_tt = $request->payment_method; // COD hoặc CHUYEN_KHOAN
            $donHang->save();

            // 2. Tạo chi tiết đơn hàng
            foreach($cart as $key => $item) {
                $chiTiet = new DonHangChiTiet();
                $chiTiet->don_hang_id = $donHang->id;
                $chiTiet->san_pham_id = $item['product_id'];
                $chiTiet->bien_the_id = $item['variant_id'];
                $chiTiet->ten_sp_ghi_nhan = $item['name'] . ' (' . $item['variant_info'] . ')';
                $chiTiet->gia = $item['price'];
                $chiTiet->so_luong = $item['quantity'];
                $chiTiet->thanh_tien = $item['price'] * $item['quantity'];
                $chiTiet->save();
            }

            DB::commit();
            session()->forget('cart'); // Xóa giỏ hàng sau khi mua thành công

            return redirect()->route('checkout.success')->with('order_id', $donHang->ma);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function success()
    {
        return view('checkout.success');
    }
}