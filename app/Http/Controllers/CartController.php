<?php

namespace App\Http\Controllers;

use App\Models\SanPham;
use App\Models\BienTheSanPham;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        foreach($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return view('cart.index', compact('cart', 'total'));
    }

    public function addToCart(Request $request, $id)
    {
        $product = SanPham::findOrFail($id);
        $variant = BienTheSanPham::findOrFail($request->bien_the_id);
        
        $cart = session()->get('cart', []);
        
        // Key của giỏ hàng là ID biến thể để phân biệt các màu/dung lượng khác nhau
        $cartKey = $variant->id;

        if(isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $request->quantity;
        } else {
            $cart[$cartKey] = [
                "product_id" => $product->id,
                "variant_id" => $variant->id,
                "name" => $product->ten,
                "variant_info" => $variant->mau_sac . " / " . $variant->dung_luong_gb . "GB",
                "quantity" => $request->quantity,
                "price" => $variant->gia,
                "image" => $product->hinh_anh_mac_dinh
            ];
        }

        session()->put('cart', $cart);
        return redirect()->route('cart.index')->with('success', 'Đã thêm sản phẩm vào giỏ hàng!');
    }

    public function remove(Request $request)
    {
        if($request->id) {
            $cart = session()->get('cart');
            if(isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
            return redirect()->back()->with('success', 'Đã xóa sản phẩm!');
        }
    }
}