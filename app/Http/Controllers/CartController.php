<?php

namespace App\Http\Controllers;

use App\Models\GioHang;
use App\Models\GioHangChiTiet;
use App\Models\BienTheSanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        
        // 1. Tính Tạm tính (Subtotal)
        $subtotal = $cartItems->sum(function($item) {
            // Dựa trên code view, ta dùng giá hiện tại của biến thể
            return $item->so_luong * $item->bienThe->gia; 
        });
        
        // 2. Tính Phí vận chuyển (Shipping Fee) - Giả định miễn phí ship từ 500k
        $shippingFee = $subtotal >= 500000 ? 0 : 30000;
        
        // 3. Tính Tổng cộng (Total)
        $total = $subtotal + $shippingFee;
        
        // Truyền cả 3 biến sang view
        return view('cart.index', compact('cartItems', 'subtotal', 'shippingFee', 'total'));
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
        
        if ($request->so_luong > $bienThe->ton_kho) {
            return response()->json([
                'success' => false,
                'message' => 'Vượt quá số lượng tồn kho!'
            ], 400);
        }
        
        $cartItem->so_luong = $request->so_luong;
        $cartItem->save();
        
        $cart = $this->getOrCreateCart();
        $cartItems = GioHangChiTiet::with('bienThe')->where('gio_hang_id', $cart->id)->get();
        
        // Tính lại tổng tiền sau khi cập nhật
        $cartSubtotal = $cartItems->sum(function($item) {
            return $item->so_luong * $item->bienThe->gia;
        });
        
        $shippingFee = $cartSubtotal >= 500000 ? 0 : 30000;
        $cartTotal = $cartSubtotal + $shippingFee;
        $cartCount = $cartItems->sum('so_luong');
        
        session(['cart_count' => $cartCount]);
        
        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật giỏ hàng!',
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
        
        session(['cart_count' => 0]);
        
        return redirect()->back()->with('success', 'Đã xóa toàn bộ giỏ hàng!');
    }
}