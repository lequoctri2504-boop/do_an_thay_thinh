<?php

namespace App\Http\Controllers;

use App\Models\GioHang;
use App\Models\GioHangChiTiet;
use App\Models\BienTheSanPham;
use App\Models\KhuyenMai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CartController extends Controller
{
    /**
     * Lấy hoặc tạo giỏ hàng cho người dùng
     */
    private function getOrCreateCart()
    {
        $cart = GioHang::firstOrCreate(
            ['nguoi_dung_id' => Auth::id()],
            ['nguoi_dung_id' => Auth::id()]
        );
        
        return $cart;
    }
    
    /**
     * Hiển thị giỏ hàng
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để xem giỏ hàng!');
        }
        
        $cart = $this->getOrCreateCart();
        $cartItems = GioHangChiTiet::with(['bienThe.sanPham.thuongHieu'])
            ->where('gio_hang_id', $cart->id)
            ->get();
        
        if ($cartItems->isEmpty()) {
            // Xóa luôn giảm giá nếu giỏ hàng trống
            Session::forget(['discount_code', 'discount_amount']);
            // Truyền các biến cần thiết (0)
            return view('cart.index', compact('cartItems'))
                ->with(['subtotal' => 0, 'shippingFee' => 0, 'total' => 0, 'discountAmount' => 0, 'discountCode' => null]);
        }

        // 1. Tính Tạm tính (Subtotal)
        $subtotal = $cartItems->sum(function($item) {
            return $item->so_luong * $item->bienThe->gia; 
        });
        
        // 2. Lấy thông tin khuyến mãi từ Session
        $discountAmount = Session::get('discount_amount', 0);
        $discountCode = Session::get('discount_code');
        
        // 3. Tính Phí vận chuyển (Shipping Fee) - Giả định miễn phí ship từ 500k
        $shippingFee = $subtotal >= 500000 ? 0 : 30000;
        
        // 4. Tính Tổng cộng (Total) - Áp dụng giảm giá
        $total = $subtotal - $discountAmount + $shippingFee;
        
        // Truyền các biến cần thiết sang view
        return view('cart.index', compact('cartItems', 'subtotal', 'shippingFee', 'total', 'discountAmount', 'discountCode'));
    }

    /**
     * Xử lý áp dụng mã giảm giá
     */
    public function applyDiscount(Request $request)
    {
        if (!Auth::check()) {
            return back()->with('error', 'Vui lòng đăng nhập để áp dụng mã giảm giá!');
        }

        $code = trim($request->input('coupon_code'));
        
        // Logic Hủy Mã giảm giá
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
        $cart = $this->getOrCreateCart();
        if ($cart->chiTiet->isEmpty()) {
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
     * Thêm sản phẩm vào giỏ hàng
     */
    public function add(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để thêm vào giỏ hàng!'
            ], 401);
        }
        
        $request->validate([
            'bien_the_id' => 'required|exists:bien_the_san_pham,id',
            'so_luong' => 'required|integer|min:1'
        ]);
        
        $bienThe = BienTheSanPham::findOrFail($request->bien_the_id);
        
        // Kiểm tra tồn kho
        if ($bienThe->ton_kho < $request->so_luong) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm không đủ số lượng trong kho!'
            ], 400);
        }
        
        DB::beginTransaction();
        try {
            $cart = $this->getOrCreateCart();
            
            // Kiểm tra sản phẩm đã có trong giỏ chưa
            $cartItem = GioHangChiTiet::where('gio_hang_id', $cart->id)
                ->where('bien_the_id', $request->bien_the_id)
                ->first();
            
            if ($cartItem) {
                // Cập nhật số lượng
                $newQuantity = $cartItem->so_luong + $request->so_luong;
                if ($newQuantity > $bienThe->ton_kho) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Vượt quá số lượng tồn kho!'
                    ], 400);
                }
                $cartItem->so_luong = $newQuantity;
                $cartItem->save();
            } else {
                // Thêm mới vào giỏ
                GioHangChiTiet::create([
                    'gio_hang_id' => $cart->id,
                    'bien_the_id' => $request->bien_the_id,
                    'so_luong' => $request->so_luong,
                    'gia_tai_thoi_diem' => $bienThe->gia 
                ]);
            }
            
            // Nếu giỏ hàng thay đổi, hủy giảm giá cũ (Vì mã có thể không còn hợp lệ)
            Session::forget(['discount_code', 'discount_amount']);

            $cartCount = GioHangChiTiet::where('gio_hang_id', $cart->id)->sum('so_luong');
            session(['cart_count' => $cartCount]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Đã thêm sản phẩm vào giỏ hàng!',
                'cart_count' => $cartCount
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Cập nhật số lượng sản phẩm trong giỏ
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'so_luong' => 'required|integer|min:1'
        ]);
        
        $cartItem = GioHangChiTiet::findOrFail($id);
        $bienThe = $cartItem->bienThe;
        $newQty = $request->so_luong; // Lấy số lượng mới

        if ($newQty > $bienThe->ton_kho) {
            return response()->json([
                'success' => false,
                'message' => 'Vượt quá số lượng tồn kho!'
            ], 400);
        }
        
        $cartItem->so_luong = $newQty;
        $cartItem->save();
        
        // Nếu số lượng thay đổi, hủy giảm giá cũ
        Session::forget(['discount_code', 'discount_amount']);

        $cart = $this->getOrCreateCart();
        $cartItems = GioHangChiTiet::with('bienThe')->where('gio_hang_id', $cart->id)->get();
        
        // Tính lại tổng tiền sau khi cập nhật (Không có giảm giá)
        $cartSubtotal = $cartItems->sum(function($item) {
            return $item->so_luong * $item->bienThe->gia;
        });
        
        $itemSubtotal = $newQty * $bienThe->gia; // <-- TÍNH LẠI ITEM SUBTOTAL
        
        $shippingFee = $cartSubtotal >= 500000 ? 0 : 30000;
        $cartTotal = $cartSubtotal + $shippingFee;
        $cartCount = $cartItems->sum('so_luong');
        
        session(['cart_count' => $cartCount]);
        
        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật giỏ hàng!',
            'new_qty' => $newQty, // <-- TRẢ VỀ SỐ LƯỢNG MỚI
            'item_subtotal' => $itemSubtotal, // <-- TRẢ VỀ ITEM SUBTOTAL MỚI
            'cart_count' => $cartCount,
            'cart_subtotal' => $cartSubtotal, // Cung cấp giá trị mới cho AJAX
            'cart_total' => $cartTotal
        ]);
    }
    
    /**
     * Xóa sản phẩm khỏi giỏ hàng
     */
    public function remove($id)
    {
        $cartItem = GioHangChiTiet::findOrFail($id);
        $cartItem->delete();
        
        // Nếu sản phẩm bị xóa, hủy giảm giá cũ
        Session::forget(['discount_code', 'discount_amount']);

        $cart = $this->getOrCreateCart();
        $cartItems = GioHangChiTiet::with('bienThe')->where('gio_hang_id', $cart->id)->get();
        
        $cartSubtotal = $cartItems->sum(function($item) {
            return $item->so_luong * $item->bienThe->gia;
        });
        
        $shippingFee = $cartSubtotal >= 500000 ? 0 : 30000;
        $cartTotal = $cartSubtotal + $shippingFee;
        $cartCount = $cartItems->sum('so_luong');
        
        session(['cart_count' => $cartCount]);
        
        return response()->json([
            'success' => true,
            'message' => 'Đã xóa sản phẩm khỏi giỏ hàng!',
            'cart_count' => $cartCount,
            'cart_subtotal' => $cartSubtotal, // Cung cấp giá trị mới cho AJAX
            'cart_total' => $cartTotal
        ]);
    }
    
    /**
     * Xóa toàn bộ giỏ hàng
     */
    public function clear()
    {
        $cart = $this->getOrCreateCart();
        GioHangChiTiet::where('gio_hang_id', $cart->id)->delete();
        
        // Xóa luôn giảm giá
        Session::forget(['cart_count', 'discount_code', 'discount_amount']);

        session(['cart_count' => 0]);
        
        return redirect()->back()->with('success', 'Đã xóa toàn bộ giỏ hàng!');
    }
}