<?php

namespace App\Http\Controllers;

use App\Models\SanPham;
use App\Models\DanhMuc;
use App\Models\ThuongHieu;
use App\Models\BaiViet; // <<< BỔ SUNG MODEL TIN TỨC >>>
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str; // Cần cho Str::limit trong view

class HomeController extends Controller
{
    public function index()
    {
        $categories = DanhMuc::whereNull('deleted_at')->orderBy('ten', 'asc')->get();
        $brands = ThuongHieu::whereNull('deleted_at')->orderBy('ten', 'asc')->get();
        
        // DỮ LIỆU BANNER MÔ PHỎNG (Sử dụng 3 ảnh có sẵn)
        $banners = [
            [
                'image' => asset('images/banners/banner1.jpg'),
                'title' => 'UY TÍN - CHẤT LƯỢNG',
                'subtitle' => 'Trả góp 0%, Hỗ trợ thu cũ đổi mới',
                'link' => '#',
            ],
            [
                'image' => asset('images/banners/banner2.jpg'),
                'title' => 'ĐỔI ĐIỆN THOẠI CŨ',
                'subtitle' => 'SẮM ĐIỆN THOẠI MỚI (Thủ tục cực dễ)',
                'link' => '#',
            ],
            [
                'image' => asset('images/banners/banner3.jpg'),
                'title' => 'SAMSUNG - IPHONE RẺ BẤT NGỜ',
                'subtitle' => 'Tặng 12 tháng bảo hành vàng',
                'link' => '#',
            ],
        ];

        // Lấy tất cả sản phẩm đang hiển thị
        // $allProductsQuery = SanPham::with(['thuongHieu', 'bienTheSanPham' => function($query) {
        //         $query->where('dang_ban', 1)->orderBy('gia', 'asc');
        //     }, 'danhGia'
        //     ])
        //     ->whereNull('deleted_at')
        //     ->where('hien_thi', 1)
        //     ->whereHas('bienTheSanPham', function($query) {
        //         $query->where('dang_ban', 1);
        //     })
        //     ->orderBy('created_at', 'desc');

        // $allProducts = $allProductsQuery->get();
        // $flashSaleProducts = $allProducts->take(4);
        // $featuredProducts = $allProducts->skip(4)->take(4);
        $productBaseQuery = SanPham::with(['thuongHieu', 'bienTheSanPham' => function($query) {
                $query->where('dang_ban', 1)->orderBy('gia', 'asc');
            }, 'danhGia'
            ])
            ->whereNull('deleted_at')
            ->where('hien_thi', 1)
            ->whereHas('bienTheSanPham', function($query) {
                $query->where('dang_ban', 1);
            });

        // TÁCH QUERY DỰA TRÊN CỜ MỚI
        $flashSaleProducts = $productBaseQuery->clone()->where('la_flash_sale', 1)->limit(4)->get();
        $featuredProducts = $productBaseQuery->clone()->where('la_noi_bat', 1)->limit(4)->get();
        
        // Ensure relationships are loaded (mặc dù clone đã giữ lại with(), nhưng thêm load() cho an toàn)
        $flashSaleProducts->load(['thuongHieu', 'bienTheSanPham', 'danhGia']);
        $featuredProducts->load(['thuongHieu', 'bienTheSanPham', 'danhGia']);
        
        // <<< LẤY TIN TỨC TỪ DB (MỚI) >>>
        $newsArticles = BaiViet::where('trang_thai', 'XUAT_BAN')
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();


        $cartCount = Session::get('cart_count', 0);
        $wishlistCount = 0;
        
        if (Auth::check()) {
            if ($cartCount === 0) {
                 $gioHang = \App\Models\GioHang::where('nguoi_dung_id', Auth::id())->first();
                 if ($gioHang) {
                    $cartCount = \App\Models\GioHangChiTiet::where('gio_hang_id', $gioHang->id)->sum('so_luong');
                    Session::put('cart_count', $cartCount);
                 }
            }
            
            $wishlistCount = DB::table('yeu_thich')
                                ->where('nguoi_dung_id', Auth::id())
                                ->count();
            Session::put('wishlist_count', $wishlistCount);
        }

        // Truyền thêm newsArticles vào view
        return view('welcome', compact('categories', 'brands', 'flashSaleProducts', 'featuredProducts', 'cartCount', 'wishlistCount', 'banners', 'newsArticles'));
    }
}