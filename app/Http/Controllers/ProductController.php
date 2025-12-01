<?php

namespace App\Http\Controllers;

use App\Models\SanPham;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show($slug)
    {
        // Lấy sản phẩm kèm ảnh và biến thể đang bán
        $product = SanPham::where('slug', $slug)
            ->where('hien_thi', 1)
            ->with(['bienTheDangBan', 'anh']) // Eager loading để tối ưu query
            ->firstOrFail();

        // Lấy các sản phẩm liên quan cùng thương hiệu
        $relatedProducts = SanPham::where('thuong_hieu_id', $product->thuong_hieu_id)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        return view('products.detail', compact('product', 'relatedProducts'));
    }
}