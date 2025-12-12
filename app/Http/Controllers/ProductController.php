<?php

namespace App\Http\Controllers;

use App\Models\SanPham;
use App\Models\DanhMuc;
use App\Models\ThuongHieu;
use App\Models\BienTheSanPham;
use App\Models\DanhGia;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Hiển thị danh sách tất cả sản phẩm
     */
    // public function index(Request $request)
    // {
    //     $query = SanPham::with(['thuongHieu', 'bienTheSanPham' => function($q) {
    //         $q->where('dang_ban', 1)->orderBy('gia', 'asc');
    //     }])
    //     ->whereNull('deleted_at')
    //     ->where('hien_thi', 1)
    //     ->whereHas('bienTheSanPham', function($q) {
    //         $q->where('dang_ban', 1);
    //     });
        
    //     // Lọc theo thương hiệu
    //     if ($request->has('brand') && $request->brand != '') {
    //         $query->whereHas('thuongHieu', function($q) use ($request) {
    //             $q->where('slug', $request->brand);
    //         });
    //     }
        
    //     // Lọc theo giá
    //     if ($request->has('price') && $request->price != '') {
    //         $priceRange = explode('-', $request->price);
    //         if (count($priceRange) == 2) {
    //             $query->whereHas('bienTheSanPham', function($q) use ($priceRange) {
    //                 $q->whereBetween('gia', [(int)$priceRange[0], (int)$priceRange[1]]);
    //             });
    //         }
    //     }
        
    //     // Sắp xếp
    //     $sortBy = $request->input('sort', 'newest');
    //     switch ($sortBy) {
    //         case 'price_asc':
    //         case 'price_desc':
    //             // FIX 2: Thêm SELECT và WHERE cho cột deleted_at để phân biệt
    //             $query->select('san_pham.*')
    //                   ->join('bien_the_san_pham', function($join) {
    //                       $join->on('san_pham.id', '=', 'bien_the_san_pham.san_pham_id')
    //                            // Thêm điều kiện loại bỏ biến thể đã xóa
    //                            ->whereNull('bien_the_san_pham.deleted_at'); 
    //                   })
    //                   ->where('bien_the_san_pham.dang_ban', 1)
    //                   ->orderBy('bien_the_san_pham.gia', $sortBy == 'price_asc' ? 'asc' : 'desc')
    //                   ->distinct();
    //             break;
    //         case 'popular':
    //             // Có thể thêm logic sắp xếp theo lượt xem hoặc đánh giá
    //             $query->orderBy('created_at', 'desc');
    //             break;
    //         default: // newest
    //             $query->orderBy('created_at', 'desc');
    //     }
        
    //     $products = $query->paginate(12);
    //     $categories = DanhMuc::whereNull('deleted_at')->get();
    //     $brands = ThuongHieu::whereNull('deleted_at')->get();
        
    //     return view('products.index', compact('products', 'categories', 'brands'));
    // }
    public function index(Request $request)
    {
        // Khởi tạo query base
        $query = SanPham::with(['thuongHieu', 'bienTheSanPham' => function($q) {
            $q->where('dang_ban', 1)->orderBy('gia', 'asc');
        }])
        ->whereNull('san_pham.deleted_at') // <<< FIX 1: Chỉ định rõ ràng bảng cho deleted_at của SanPham
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
        // $sortBy = $request->input('sort', 'newest');
        // switch ($sortBy) {
        //     case 'price_asc':
        //         $query->join('bien_the_san_pham', 'san_pham.id', '=', 'bien_the_san_pham.san_pham_id')
        //               ->where('bien_the_san_pham.dang_ban', 1)
        //               ->whereNull('bien_the_san_pham.deleted_at') // <<< FIX 2: Loại bỏ dữ liệu deleted của biến thể
        //               ->orderBy('bien_the_san_pham.gia', 'asc')
        //               ->select('san_pham.*')
        //               ->distinct();
        //         break;
        //     case 'price_desc':
        //         $query->join('bien_the_san_pham', 'san_pham.id', '=', 'bien_the_san_pham.san_pham_id')
        //               ->where('bien_the_san_pham.dang_ban', 1)
        //               ->whereNull('bien_the_san_pham.deleted_at') // <<< FIX 3: Loại bỏ dữ liệu deleted của biến thể
        //               ->orderBy('bien_the_san_pham.gia', 'desc')
        //               ->select('san_pham.*')
        //               ->distinct();
        //         break;
        //     case 'popular':
        //         // Có thể thêm logic sắp xếp theo lượt xem hoặc đánh giá
        //         $query->orderBy('san_pham.created_at', 'desc');
        //         break;
        //     default: // newest
        //         $query->orderBy('san_pham.created_at', 'desc');
        // }
        // 2. Sắp xếp
        $sortBy = $request->input('sort', 'newest');
        switch ($sortBy) {
            case 'price_asc':
                // FIX: Sử dụng GROUP BY và MIN(gia) để đảm bảo sắp xếp theo giá thấp nhất
                $query->leftJoin('bien_the_san_pham as bsp_sort', function($join) {
                        $join->on('san_pham.id', '=', 'bsp_sort.san_pham_id')
                             ->where('bsp_sort.dang_ban', '=', 1)
                             ->whereNull('bsp_sort.deleted_at');
                    })
                    // GROUP BY san_pham.id để mỗi sản phẩm chỉ xuất hiện 1 lần
                    ->groupBy('san_pham.id') 
                    // Sắp xếp theo giá thấp nhất trong tất cả các biến thể
                    ->orderByRaw('MIN(bsp_sort.gia) ASC') 
                    ->select('san_pham.*'); // Đảm bảo chỉ chọn các cột từ san_pham
                break;
            case 'price_desc':
                // FIX: Sử dụng GROUP BY và MAX(gia) để đảm bảo sắp xếp theo giá cao nhất
                $query->leftJoin('bien_the_san_pham as bsp_sort', function($join) {
                        $join->on('san_pham.id', '=', 'bsp_sort.san_pham_id')
                             ->where('bsp_sort.dang_ban', '=', 1)
                             ->whereNull('bsp_sort.deleted_at');
                    })
                    ->groupBy('san_pham.id') 
                    ->orderByRaw('MAX(bsp_sort.gia) DESC') 
                    ->select('san_pham.*');
                break;
            case 'popular':
                // Giữ nguyên logic cũ hoặc sắp xếp theo luot_xem/ban chay (tùy chọn)
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
    // public function category($slug)
    // {
    //     $category = DanhMuc::where('slug', $slug)->firstOrFail();
        
    //     $products = SanPham::with(['thuongHieu', 'bienTheSanPham' => function($q) {
    //             $q->where('dang_ban', 1)->orderBy('gia', 'asc');
    //         }])
    //         ->whereNull('deleted_at')
    //         ->where('hien_thi', 1) 
    //         ->whereHas('danhMuc', function($q) use ($category) {
    //             $q->where('danh_muc.id', $category->id);
    //         })
    //         ->orderBy('created_at', 'desc')
    //         ->paginate(12);
        
    //     $categories = DanhMuc::whereNull('deleted_at')->get();
    //     $brands = ThuongHieu::whereNull('deleted_at')->get();
        
    //     return view('products.category', compact('products', 'category', 'categories', 'brands'));
    // }
    public function category(Request $request, $slug)
    {
        $category = DanhMuc::where('slug', $slug)->firstOrFail();
        
        $query = SanPham::with(['thuongHieu', 'bienTheSanPham' => function($q) {
                $q->where('dang_ban', 1)->orderBy('gia', 'asc');
            }])
            ->whereNull('deleted_at')
            ->where('hien_thi', 1) 
            ->whereHas('danhMuc', function($q) use ($category) {
                $q->where('danh_muc.id', $category->id);
            });
            
        // <<< LOGIC LỌC >>>
        
        // Lọc theo thương hiệu (brand)
        if ($request->has('brand') && $request->brand != '') {
            $brandSlugs = is_array($request->brand) ? $request->brand : [$request->brand];
            $query->whereHas('thuongHieu', function($q) use ($brandSlugs) {
                $q->whereIn('slug', $brandSlugs);
            });
        }
        
        // Lọc theo giá (price)
        if ($request->has('price') && $request->price != '') {
            $priceRange = explode('-', $request->price);
            if (count($priceRange) == 2) {
                $query->whereHas('bienTheSanPham', function($q) use ($priceRange) {
                    $q->whereBetween('gia', [(int)$priceRange[0], (int)$priceRange[1]]);
                });
            }
        }
        
        // Sắp xếp
        // Bạn cần đảm bảo hàm applyPriceSorting hoặc logic sắp xếp của bạn được gọi ở đây.
        // Nếu không có hàm applyPriceSorting, bạn có thể sử dụng logic đã được fix ở các câu trả lời trước.
        
        // --- Sử dụng Logic Sắp xếp Mặc định ---
        $sortBy = $request->input('sort', 'newest');
        if ($sortBy == 'price_asc' || $sortBy == 'price_desc') {
             // Logic GROUP BY MIN/MAX (Chống lỗi DISTINCT)
             $query->leftJoin('bien_the_san_pham as bsp_sort', function($join) {
                     $join->on('san_pham.id', '=', 'bsp_sort.san_pham_id')
                          ->where('bsp_sort.dang_ban', '=', 1)
                          ->whereNull('bsp_sort.deleted_at');
                 })
                 ->groupBy('san_pham.id') 
                 ->orderByRaw(($sortBy == 'price_asc' ? 'MIN' : 'MAX') . '(bsp_sort.gia) ' . ($sortBy == 'price_asc' ? 'ASC' : 'DESC')) 
                 ->select('san_pham.*');
        } else {
             $query->orderBy('created_at', 'desc');
        }
        // --- Kết thúc Logic Sắp xếp Mặc định ---

        // <<< KẾT THÚC LOGIC LỌC >>>
        
        $products = $query->paginate(12);
        
        $categories = DanhMuc::whereNull('deleted_at')->get();
        $brands = ThuongHieu::whereNull('deleted_at')->get();
        
        // Truyền các tham số request hiện tại để giữ lại trạng thái lọc trên view
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
    /**
     * Hiển thị sản phẩm theo thương hiệu
     */
    public function brand($slug)
    {
        // 1. Tìm Thương hiệu
        $brand = ThuongHieu::where('slug', $slug)->firstOrFail();
        
        // 2. Lấy sản phẩm của thương hiệu đó
        $products = SanPham::with(['thuongHieu', 'bienTheSanPham' => function($q) {
                $q->where('dang_ban', 1)->orderBy('gia', 'asc');
            }])
            ->where('thuong_hieu_id', $brand->id)
            ->whereNull('deleted_at')
            ->where('hien_thi', 1) 
            ->whereHas('bienTheSanPham', function($q) {
                $q->where('dang_ban', 1);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        // Lấy danh sách danh mục và thương hiệu để hiển thị bộ lọc
        $categories = DanhMuc::whereNull('deleted_at')->get();
        $brands = ThuongHieu::whereNull('deleted_at')->get();
        
        return view('products.index', compact('products', 'brand', 'categories', 'brands')); 
        // Sử dụng lại view products.index hoặc tạo products.brand.blade.php tùy theo thiết kế
    }
    /**
     * Hiển thị danh sách sản phẩm nổi bật (Dùng cờ la_noi_bat)
     */
    public function featuredProducts(Request $request)
    {
        $query = SanPham::with(['thuongHieu', 'bienTheSanPham' => function($q) {
            $q->where('dang_ban', 1)->orderBy('gia', 'asc');
        }])
        ->where('la_noi_bat', 1) // <<< FILTER BY NEW FLAG
        ->whereNull('deleted_at')
        ->where('hien_thi', 1)
        ->whereHas('bienTheSanPham', function($q) {
            $q->where('dang_ban', 1);
        });
        
        // --- Logic Lọc theo thương hiệu và giá (Copy từ index()) ---
        
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
                $query->orderBy('created_at', 'desc');
                break;
            default: // newest
                $query->orderBy('created_at', 'desc');
        }
        
        $products = $query->paginate(12);
        
        // Lấy dữ liệu cho sidebar (Tái sử dụng code từ các phương thức khác)
        $categories = \App\Models\DanhMuc::whereNull('deleted_at')->get();
        $brands = \App\Models\ThuongHieu::whereNull('deleted_at')->get();
        
        return view('products.featured', compact('products', 'categories', 'brands', 'sortBy'));
    }

    public function storeVariant(Request $request, SanPham $sanPham)
    {
        // 1. Validation dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'mau_sac' => 'required|string|max:50',
            'dung_luong' => 'required|string|max:50',
            // Giá phải là số và không âm
            'gia' => 'required|numeric|min:0', 
            // Số lượng phải là số nguyên và không âm
            'so_luong_ton' => 'required|integer|min:0', 
            // SKU phải là duy nhất
            'sku' => 'nullable|string|max:100|unique:bien_the_san_pham,sku', 
            'trang_thai' => 'required|boolean',
        ], [
            'mau_sac.required' => 'Màu sắc không được để trống.',
            'dung_luong.required' => 'Dung lượng không được để trống.',
            'gia.required' => 'Giá bán không được để trống.',
            'gia.numeric' => 'Giá bán phải là một số.',
            'so_luong_ton.required' => 'Số lượng tồn không được để trống.',
            'so_luong_ton.integer' => 'Số lượng tồn phải là số nguyên.',
            'sku.unique' => 'Mã SKU này đã tồn tại.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Thêm biến thể thất bại. Vui lòng kiểm tra lại dữ liệu.');
        }
        
        try {
            // 2. Tạo biến thể sản phẩm mới
            BienTheSanPham::create([
                'san_pham_id' => $sanPham->id, // Lấy ID từ Route Model Binding
                'mau_sac' => $request->mau_sac,
                'dung_luong' => $request->dung_luong,
                'gia' => $request->gia,
                'so_luong_ton' => $request->so_luong_ton,
                'sku' => $request->sku,
                'trang_thai' => $request->trang_thai,
            ]);

            return back()->with('success', 'Đã thêm biến thể sản phẩm thành công!');

        } catch (\Exception $e) {
            // Trường hợp lỗi khác (ví dụ: lỗi CSDL)
            return back()->withInput()->with('error', 'Lỗi hệ thống khi thêm biến thể. Chi tiết: ' . $e->getMessage());
        }
    }

    /**
     * Phương thức xử lý cập nhật biến thể sản phẩm (Sửa - PUT/PATCH)
     * $bienThe: Đối tượng BienTheSanPham được tự động load nhờ Route Model Binding.
     */
    public function updateVariant(Request $request, SanPham $sanPham, BienTheSanPham $bienThe)
    {
        // Tùy chọn: Đảm bảo biến thể thực sự thuộc về sản phẩm này (an toàn hơn)
        if ($bienThe->san_pham_id !== $sanPham->id) {
            return back()->with('error', 'Biến thể không thuộc về sản phẩm này.');
        }
        
        // 1. Validation dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'mau_sac' => 'required|string|max:50',
            'dung_luong' => 'required|string|max:50',
            'gia' => 'required|numeric|min:0',
            'so_luong_ton' => 'required|integer|min:0',
            // Rule::unique cho phép bỏ qua chính record hiện tại khi kiểm tra SKU duy nhất
            'sku' => [
                'nullable', 
                'string', 
                'max:100', 
                Rule::unique('bien_the_san_pham', 'sku')->ignore($bienThe->id)
            ],
            'trang_thai' => 'required|boolean',
        ], [
            'sku.unique' => 'Mã SKU này đã tồn tại trong hệ thống.',
            // ... thêm các thông báo lỗi khác nếu cần
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Cập nhật biến thể thất bại. Vui lòng kiểm tra lại dữ liệu.');
        }

        try {
            // 2. Cập nhật biến thể
            $bienThe->update([
                'mau_sac' => $request->mau_sac,
                'dung_luong' => $request->dung_luong,
                'gia' => $request->gia,
                'so_luong_ton' => $request->so_luong_ton,
                'sku' => $request->sku,
                'trang_thai' => $request->trang_thai,
            ]);

            return back()->with('success', 'Đã cập nhật biến thể sản phẩm thành công!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Lỗi hệ thống khi cập nhật biến thể: ' . $e->getMessage());
        }
    }


    /**
     * Phương thức xử lý xóa biến thể sản phẩm (DELETE)
     * $bienThe: Đối tượng BienTheSanPham được tự động load nhờ Route Model Binding.
     */
    public function destroyVariant(SanPham $sanPham, BienTheSanPham $bienThe)
    {
        // Tùy chọn: Đảm bảo biến thể thực sự thuộc về sản phẩm này (an toàn hơn)
        if ($bienThe->san_pham_id !== $sanPham->id) {
            return back()->with('error', 'Biến thể không thuộc về sản phẩm này.');
        }
        
        try {
            // 1. Xóa biến thể
            $bienThe->delete();

            return back()->with('success', 'Đã xóa biến thể sản phẩm thành công!');

        } catch (\Exception $e) {
            // Trường hợp lỗi CSDL (ví dụ: đang có đơn hàng tham chiếu đến biến thể này)
            return back()->with('error', 'Lỗi hệ thống khi xóa biến thể: ' . $e->getMessage());
        }
    }
}