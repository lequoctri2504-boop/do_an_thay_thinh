<?php

namespace App\Http\Controllers;

use App\Models\SanPham;
use App\Models\DanhMuc;
use App\Models\ThuongHieu;
use App\Models\BienTheSanPham;
use App\Models\DanhGia;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Hiển thị danh sách tất cả sản phẩm
     */
    public function index(Request $request)
    {
        $query = SanPham::with(['thuongHieu', 'bienTheSanPham' => function($q) {
            $q->where('dang_ban', 1)->orderBy('gia', 'asc');
        }])
        ->whereNull('deleted_at')
        ->where('hien_thi', 1)
        ->whereHas('bienTheSanPham', function($q) {
            $q->where('dang_ban', 1);
        });
        
        // Lọc theo thương hiệu
        if ($request->has('brand') && $request->brand != '') {
            $query->whereHas('thuongHieu', function($q) use ($request) {
                $q->where('slug', $request->brand);
            });
        }
        
        // Lọc theo giá
        if ($request->has('price') && $request->price != '') {
            $priceRange = explode('-', $request->price);
            if (count($priceRange) == 2) {
                $query->whereHas('bienTheSanPham', function($q) use ($priceRange) {
                    $q->whereBetween('gia', [(int)$priceRange[0], (int)$priceRange[1]]);
                });
            }
        }
        
        // Sắp xếp
        $sortBy = $request->input('sort', 'newest');
        switch ($sortBy) {
            case 'price_asc':
                $query->join('bien_the_san_pham', 'san_pham.id', '=', 'bien_the_san_pham.san_pham_id')
                      ->where('bien_the_san_pham.dang_ban', 1)
                      ->orderBy('bien_the_san_pham.gia', 'asc')
                      ->select('san_pham.*')
                      ->distinct();
                break;
            case 'price_desc':
                $query->join('bien_the_san_pham', 'san_pham.id', '=', 'bien_the_san_pham.san_pham_id')
                      ->where('bien_the_san_pham.dang_ban', 1)
                      ->orderBy('bien_the_san_pham.gia', 'desc')
                      ->select('san_pham.*')
                      ->distinct();
                break;
            case 'popular':
                // Có thể thêm logic sắp xếp theo lượt xem hoặc đánh giá
                $query->orderBy('created_at', 'desc');
                break;
            default: // newest
                $query->orderBy('created_at', 'desc');
        }
        
        $products = $query->paginate(12);
        $categories = DanhMuc::whereNull('deleted_at')->get();
        $brands = ThuongHieu::whereNull('deleted_at')->get();
        
        return view('products.index', compact('products', 'categories', 'brands'));
    }
    
    /**
     * Hiển thị chi tiết sản phẩm
     */
    public function show($slug)
    {
        $product = SanPham::with([
            'thuongHieu',
            'bienTheSanPham' => function($q) {
                $q->where('dang_ban', 1);
            },
            'sanPhamAnh',
            'danhGia.nguoiDung',
            'danhMuc'
        ])
        ->where('slug', $slug)
        ->whereNull('deleted_at')
        ->where('hien_thi', 1) 
        ->firstOrFail();
        
        // Lấy sản phẩm liên quan
        $relatedProducts = SanPham::with(['thuongHieu', 'bienTheSanPham' => function($q) {
                $q->where('dang_ban', 1)->orderBy('gia', 'asc');
            }])
            ->where('thuong_hieu_id', $product->thuong_hieu_id)
            ->where('id', '!=', $product->id)
            ->whereNull('deleted_at')
            ->where('hien_thi', 1) 
            ->limit(4)
            ->get();
        
        // Tính điểm đánh giá trung bình
        $avgRating = $product->danhGia()->avg('so_sao');
        $ratingCount = $product->danhGia()->count();
        
        return view('products.show', compact('product', 'relatedProducts', 'avgRating', 'ratingCount'));
    }
    
    /**
     * Hiển thị sản phẩm theo danh mục
     */
    public function category($slug)
    {
        $category = DanhMuc::where('slug', $slug)->firstOrFail();
        
        $products = SanPham::with(['thuongHieu', 'bienTheSanPham' => function($q) {
                $q->where('dang_ban', 1)->orderBy('gia', 'asc');
            }])
            ->whereNull('deleted_at')
            ->where('hien_thi', 1) 
            ->whereHas('danhMuc', function($q) use ($category) {
                $q->where('danh_muc.id', $category->id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        $categories = DanhMuc::whereNull('deleted_at')->get();
        $brands = ThuongHieu::whereNull('deleted_at')->get();
        
        return view('products.category', compact('products', 'category', 'categories', 'brands'));
    }
    
    /**
     * Tìm kiếm sản phẩm
     */
    public function search(Request $request)
    {
        $keyword = $request->input('q', '');
        
        $products = SanPham::with(['thuongHieu', 'bienTheSanPham' => function($q) {
                $q->where('dang_ban', 1)->orderBy('gia', 'asc');
            }])
            ->whereNull('deleted_at')
            ->where('hien_thi', 1) 
            ->where(function($q) use ($keyword) {
                $q->where('ten', 'LIKE', "%{$keyword}%")
                  // **ĐÃ SỬA LỖI**: Thay mo_ta bằng mo_ta_ngan và mo_ta_day_du
                  ->orWhere('mo_ta_ngan', 'LIKE', "%{$keyword}%")
                  ->orWhere('mo_ta_day_du', 'LIKE', "%{$keyword}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        $categories = DanhMuc::whereNull('deleted_at')->get();
        $brands = ThuongHieu::whereNull('deleted_at')->get();
        
        return view('products.search', compact('products', 'categories', 'brands', 'keyword'));
    }
}