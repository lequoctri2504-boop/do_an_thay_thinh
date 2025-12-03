<?php

namespace App\Http\Controllers;

use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WishlistController extends Controller
{
    /**
     * Hiển thị danh sách yêu thích
     */
    // public function index()
    // {
    //     if (!Auth::check()) {
    //         return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
    //     }
        
    //     $wishlistItems = DB::table('yeu_thich')
    //         ->join('san_pham', 'yeu_thich.san_pham_id', '=', 'san_pham.id')
    //         ->join('thuong_hieu', 'san_pham.thuong_hieu_id', '=', 'thuong_hieu.id')
    //         ->leftJoin('bien_the_san_pham', function($join) {
    //             $join->on('san_pham.id', '=', 'bien_the_san_pham.san_pham_id')
    //                  ->where('bien_the_san_pham.dang_ban', '=', 1)
    //                  ->whereRaw('bien_the_san_pham.id = (SELECT MIN(id) FROM bien_the_san_pham WHERE san_pham_id = san_pham.id AND dang_ban = 1)');
    //         })
    //         ->where('yeu_thich.nguoi_dung_id', Auth::id())
    //         ->whereNull('san_pham.deleted_at')
    //         ->select(
    //             'yeu_thich.id as wishlist_id',
    //             'san_pham.id',
    //             'san_pham.ten',
    //             'san_pham.slug',
    //             'san_pham.anh_chinh',
    //             'thuong_hieu.ten as thuong_hieu',
    //             'bien_the_san_pham.gia',
    //             'bien_the_san_pham.gia_so_sanh',
    //             'yeu_thich.created_at'
    //         )
    //         ->orderBy('yeu_thich.created_at', 'desc')
    //         ->get();
        
    //     return view('customer.wishlist', compact('wishlistItems'));
    // }
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
        }
        
        $wishlistItems = DB::table('yeu_thich')
            ->join('san_pham', 'yeu_thich.san_pham_id', '=', 'san_pham.id')
            ->join('thuong_hieu', 'san_pham.thuong_hieu_id', '=', 'thuong_hieu.id')
            ->leftJoin('bien_the_san_pham', function($join) {
                $join->on('san_pham.id', '=', 'bien_the_san_pham.san_pham_id')
                     ->where('bien_the_san_pham.dang_ban', '=', 1)
                     ->whereRaw('bien_the_san_pham.id = (SELECT MIN(id) FROM bien_the_san_pham WHERE san_pham_id = san_pham.id AND dang_ban = 1)');
            })
            ->where('yeu_thich.nguoi_dung_id', Auth::id())
            ->whereNull('san_pham.deleted_at')
            ->select(
                'yeu_thich.id as wishlist_id',
                'san_pham.id',
                'san_pham.ten',
                'san_pham.slug',
                'san_pham.hinh_anh_mac_dinh as anh_chinh', // ĐÃ SỬA TÊN CỘT
                'thuong_hieu.ten as thuong_hieu',
                'bien_the_san_pham.gia',
                'bien_the_san_pham.gia_so_sanh',
                'yeu_thich.created_at'
            )
            ->orderBy('yeu_thich.created_at', 'desc')
            ->get();
        
        return view('customer.wishlist', compact('wishlistItems'));
    }
    
    /**
     * Thêm sản phẩm vào danh sách yêu thích
     */
    public function add(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập!'
            ], 401);
        }
        
        $request->validate([
            'san_pham_id' => 'required|exists:san_pham,id'
        ]);
        
        try {
            // Kiểm tra đã tồn tại chưa
            $exists = DB::table('yeu_thich')
                ->where('nguoi_dung_id', Auth::id())
                ->where('san_pham_id', $request->san_pham_id)
                ->exists();
            
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sản phẩm đã có trong danh sách yêu thích!'
                ], 400);
            }
            
            // Thêm vào wishlist
            DB::table('yeu_thich')->insert([
                'nguoi_dung_id' => Auth::id(),
                'san_pham_id' => $request->san_pham_id,
                'created_at' => now()
            ]);
            
            // Đếm số lượng wishlist
            $wishlistCount = DB::table('yeu_thich')
                ->where('nguoi_dung_id', Auth::id())
                ->count();
            
            return response()->json([
                'success' => true,
                'message' => 'Đã thêm vào danh sách yêu thích!',
                'wishlist_count' => $wishlistCount
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Xóa sản phẩm khỏi danh sách yêu thích
     */
    public function remove($id)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập!'
            ], 401);
        }
        
        try {
            $deleted = DB::table('yeu_thich')
                ->where('id', $id)
                ->where('nguoi_dung_id', Auth::id())
                ->delete();
            
            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy sản phẩm trong danh sách yêu thích!'
                ], 404);
            }
            
            // Đếm số lượng wishlist còn lại
            $wishlistCount = DB::table('yeu_thich')
                ->where('nguoi_dung_id', Auth::id())
                ->count();
            
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa khỏi danh sách yêu thích!',
                'wishlist_count' => $wishlistCount
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Chuyển sản phẩm từ wishlist sang giỏ hàng
     */
    public function moveToCart($id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
        }
        
        try {
            DB::beginTransaction();
            
            // Lấy thông tin wishlist item
            $wishlistItem = DB::table('yeu_thich')
                ->where('id', $id)
                ->where('nguoi_dung_id', Auth::id())
                ->first();
            
            if (!$wishlistItem) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Không tìm thấy sản phẩm!');
            }
            
            // Lấy biến thể đầu tiên của sản phẩm
            $variant = DB::table('bien_the_san_pham')
                ->where('san_pham_id', $wishlistItem->san_pham_id)
                ->where('dang_ban', 1)
                ->where('ton_kho', '>', 0)
                ->first();
            
            if (!$variant) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Sản phẩm hiện không có sẵn!');
            }
            
            // Lấy hoặc tạo giỏ hàng
            $cart = DB::table('gio_hang')
                ->where('nguoi_dung_id', Auth::id())
                ->first();
            
            if (!$cart) {
                $cartId = DB::table('gio_hang')->insertGetId([
                    'nguoi_dung_id' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } else {
                $cartId = $cart->id;
            }
            
            // Kiểm tra sản phẩm đã có trong giỏ chưa
            $cartItem = DB::table('gio_hang_chi_tiet')
                ->where('gio_hang_id', $cartId)
                ->where('bien_the_id', $variant->id)
                ->first();
            
            if ($cartItem) {
                // Cập nhật số lượng
                DB::table('gio_hang_chi_tiet')
                    ->where('id', $cartItem->id)
                    ->increment('so_luong', 1);
            } else {
                // Thêm mới
                DB::table('gio_hang_chi_tiet')->insert([
                    'gio_hang_id' => $cartId,
                    'bien_the_id' => $variant->id,
                    'so_luong' => 1
                ]);
            }
            
            // Xóa khỏi wishlist
            DB::table('yeu_thich')->where('id', $id)->delete();
            
            // Cập nhật session cart count
            $cartCount = DB::table('gio_hang_chi_tiet')
                ->where('gio_hang_id', $cartId)
                ->sum('so_luong');
            session(['cart_count' => $cartCount]);
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Đã chuyển sản phẩm vào giỏ hàng!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    /**
     * Xóa toàn bộ danh sách yêu thích
     */
    public function clear()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
        }
        
        try {
            DB::table('yeu_thich')
                ->where('nguoi_dung_id', Auth::id())
                ->delete();
            
            return redirect()->back()->with('success', 'Đã xóa toàn bộ danh sách yêu thích!');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    
}