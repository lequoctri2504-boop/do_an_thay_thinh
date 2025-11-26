<?php

namespace App\Http\Controllers;

use App\Models\SanPham;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Lấy sản phẩm nổi bật (hiển thị trên trang chủ)
        $featuredProducts = SanPham::where('hien_thi', 1)
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // Lấy sản phẩm mới nhất
        $newProducts = SanPham::where('hien_thi', 1)
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        // Lấy sản phẩm theo thương hiệu (ví dụ: Apple)
        $appleProducts = SanPham::where('hien_thi', 1)
            ->where('thuong_hieu_id', 1) // ID thương hiệu Apple
            ->limit(4)
            ->get();

        return view('welcome', compact(
            'featuredProducts',
            'newProducts',
            'appleProducts'
        ));
    }
}