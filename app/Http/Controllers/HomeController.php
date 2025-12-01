<?php

namespace App\Http\Controllers;

use App\Models\SanPham;
use App\Models\ThuongHieu;
use App\Models\DanhMuc;
use App\Models\BienTheSanPham;
use BinhLuan;
use DanhGia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Hiển thị trang chủ
     */
    public function index()
    {
        // Lấy sản phẩm nổi bật (top 8 sản phẩm mới nhất đang hiển thị)
        $sanPhamNoiBat = SanPham::with(['thuongHieu', 'bienThe' => function($query) {
            $query->where('dang_ban', 1)->orderBy('gia', 'asc');
        }])
        ->where('hien_thi', 1)
        ->orderBy('created_at', 'desc')
        ->limit(8)
        ->get();

        // Lấy sản phẩm bán chạy (giả sử dựa trên số lượng đơn hàng)
        $sanPhamBanChay = SanPham::with(['thuongHieu', 'bienThe' => function($query) {
            $query->where('dang_ban', 1)->orderBy('gia', 'asc');
        }])
        ->withCount('donHangChiTiet')
        ->where('hien_thi', 1)
        ->orderBy('don_hang_chi_tiet_count', 'desc')
        ->limit(8)
        ->get();

        // Lấy sản phẩm giảm giá (có gia_so_sanh > gia)
        $sanPhamGiamGia = SanPham::with(['thuongHieu', 'bienThe' => function($query) {
            $query->where('dang_ban', 1)
                  ->whereNotNull('gia_so_sanh')
                  ->where('gia_so_sanh', '>', DB::raw('gia'))
                  ->orderBy('gia', 'asc');
        }])
        ->where('hien_thi', 1)
        ->whereHas('bienThe', function($query) {
            $query->where('dang_ban', 1)
                  ->whereNotNull('gia_so_sanh')
                  ->where('gia_so_sanh', '>', DB::raw('gia'));
        })
        ->limit(8)
        ->get();

        // Lấy danh sách thương hiệu
        $thuongHieu = ThuongHieu::withCount('sanPham')->get();

        // Lấy danh mục
        $danhMuc = DanhMuc::whereNull('cha_id')->with('con')->get();

        return view('home.index', compact(
            'sanPhamNoiBat',
            'sanPhamBanChay', 
            'sanPhamGiamGia',
            'thuongHieu',
            'danhMuc'
        ));
    }

    /**
     * Tìm kiếm sản phẩm
     */
    public function search(Request $request)
    {
        $keyword = $request->input('q');
        $thuongHieuId = $request->input('thuong_hieu');
        $danhMucId = $request->input('danh_muc');
        $giaMin = $request->input('gia_min');
        $giaMax = $request->input('gia_max');
        $sapXep = $request->input('sap_xep', 'moi_nhat');

        $query = SanPham::with(['thuongHieu', 'bienThe' => function($query) {
            $query->where('dang_ban', 1)->orderBy('gia', 'asc');
        }])->where('hien_thi', 1);

        // Tìm kiếm theo từ khóa
        if ($keyword) {
            $query->where(function($q) use ($keyword) {
                $q->where('ten', 'LIKE', "%{$keyword}%")
                  ->orWhere('mo_ta_ngan', 'LIKE', "%{$keyword}%");
            });
        }

        // Lọc theo thương hiệu
        if ($thuongHieuId) {
            $query->where('thuong_hieu_id', $thuongHieuId);
        }

        // Lọc theo danh mục
        if ($danhMucId) {
            $query->whereHas('danhMuc', function($q) use ($danhMucId) {
                $q->where('danh_muc_id', $danhMucId);
            });
        }

        // Lọc theo giá
        if ($giaMin || $giaMax) {
            $query->whereHas('bienThe', function($q) use ($giaMin, $giaMax) {
                if ($giaMin) {
                    $q->where('gia', '>=', $giaMin);
                }
                if ($giaMax) {
                    $q->where('gia', '<=', $giaMax);
                }
            });
        }

        // Sắp xếp
        switch ($sapXep) {
            case 'gia_thap':
                $query->join('bien_the_san_pham', 'san_pham.id', '=', 'bien_the_san_pham.san_pham_id')
                      ->where('bien_the_san_pham.dang_ban', 1)
                      ->select('san_pham.*', DB::raw('MIN(bien_the_san_pham.gia) as gia_min'))
                      ->groupBy('san_pham.id')
                      ->orderBy('gia_min', 'asc');
                break;
            case 'gia_cao':
                $query->join('bien_the_san_pham', 'san_pham.id', '=', 'bien_the_san_pham.san_pham_id')
                      ->where('bien_the_san_pham.dang_ban', 1)
                      ->select('san_pham.*', DB::raw('MAX(bien_the_san_pham.gia) as gia_max'))
                      ->groupBy('san_pham.id')
                      ->orderBy('gia_max', 'desc');
                break;
            case 'ten_az':
                $query->orderBy('ten', 'asc');
                break;
            case 'ten_za':
                $query->orderBy('ten', 'desc');
                break;
            case 'ban_chay':
                $query->withCount('donHangChiTiet')->orderBy('don_hang_chi_tiet_count', 'desc');
                break;
            default: // moi_nhat
                $query->orderBy('created_at', 'desc');
        }

        $sanPham = $query->paginate(12);
        $thuongHieu = ThuongHieu::all();
        $danhMuc = DanhMuc::whereNull('cha_id')->get();

        return view('home.search', compact('sanPham', 'thuongHieu', 'danhMuc', 'keyword'));
    }

    /**
     * Chi tiết sản phẩm
     */
    public function chiTiet($slug)
    {
        $sanPham = SanPham::with([
            'thuongHieu',
            'bienThe' => function($query) {
                $query->where('dang_ban', 1);
            },
            'anh',
            'danhMuc',
            'danhGia' => function($query) {
                $query->where('duyet', 1)->with('nguoiDung')->latest()->limit(10);
            },
            'binhLuan' => function($query) {
                $query->where('duyet', 1)->whereNull('parent_id')->with(['nguoiDung', 'replies'])->latest();
            }
        ])->where('slug', $slug)->where('hien_thi', 1)->firstOrFail();

        // Tính điểm đánh giá trung bình
        $danhGiaTrungBinh = $sanPham->danhGia->avg('so_sao');
        $tongDanhGia = $sanPham->danhGia->count();

        // Sản phẩm liên quan (cùng thương hiệu hoặc cùng danh mục)
        $sanPhamLienQuan = SanPham::with(['thuongHieu', 'bienThe' => function($query) {
            $query->where('dang_ban', 1)->orderBy('gia', 'asc');
        }])
        ->where('hien_thi', 1)
        ->where('id', '!=', $sanPham->id)
        ->where(function($query) use ($sanPham) {
            $query->where('thuong_hieu_id', $sanPham->thuong_hieu_id)
                  ->orWhereHas('danhMuc', function($q) use ($sanPham) {
                      $q->whereIn('danh_muc_id', $sanPham->danhMuc->pluck('id'));
                  });
        })
        ->limit(8)
        ->get();

        return view('home.chi-tiet', compact('sanPham', 'danhGiaTrungBinh', 'tongDanhGia', 'sanPhamLienQuan'));
    }

    /**
     * Lọc theo thương hiệu
     */
    public function thuongHieu($slug)
    {
        $thuongHieu = ThuongHieu::where('slug', $slug)->firstOrFail();
        
        $sanPham = SanPham::with(['thuongHieu', 'bienThe' => function($query) {
            $query->where('dang_ban', 1)->orderBy('gia', 'asc');
        }])
        ->where('thuong_hieu_id', $thuongHieu->id)
        ->where('hien_thi', 1)
        ->orderBy('created_at', 'desc')
        ->paginate(12);

        $danhSachThuongHieu = ThuongHieu::all();

        return view('home.thuong-hieu', compact('thuongHieu', 'sanPham', 'danhSachThuongHieu'));
    }

    /**
     * Lọc theo danh mục
     */
    public function danhMuc($slug)
    {
        $danhMuc = DanhMuc::where('slug', $slug)->firstOrFail();
        
        $sanPham = SanPham::with(['thuongHieu', 'bienThe' => function($query) {
            $query->where('dang_ban', 1)->orderBy('gia', 'asc');
        }])
        ->whereHas('danhMuc', function($query) use ($danhMuc) {
            $query->where('danh_muc_id', $danhMuc->id);
        })
        ->where('hien_thi', 1)
        ->orderBy('created_at', 'desc')
        ->paginate(12);

        $danhSachDanhMuc = DanhMuc::whereNull('cha_id')->with('con')->get();

        return view('home.danh-muc', compact('danhMuc', 'sanPham', 'danhSachDanhMuc'));
    }

    /**
     * Thêm bình luận
     */
    // public function themBinhLuan(Request $request)
    // {
    //     $request->validate([
    //         'san_pham_id' => 'required|exists:san_pham,id',
    //         'noi_dung' => 'required|string|max:1000',
    //         'parent_id' => 'nullable|exists:binh_luan,id'
    //     ]);

    //     if (!auth()->check()) {
    //         return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập để bình luận'], 401);
    //     }

    //     $binhLuan = BinhLuan::create([
    //         'san_pham_id' => $request->san_pham_id,
    //         'nguoi_dung_id' => auth()->id(),
    //         'parent_id' => $request->parent_id,
    //         'noi_dung' => $request->noi_dung,
    //         'duyet' => 1
    //     ]);

    //     $binhLuan->load('nguoiDung');

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Thêm bình luận thành công',
    //         'data' => $binhLuan
    //     ]);
    // }

    // /**
    //  * Thêm đánh giá
    //  */
    // public function themDanhGia(Request $request)
    // {
    //     $request->validate([
    //         'san_pham_id' => 'required|exists:san_pham,id',
    //         'so_sao' => 'required|integer|min:1|max:5',
    //         'tieu_de' => 'nullable|string|max:191',
    //         'noi_dung' => 'nullable|string|max:1000'
    //     ]);

    //     if (!auth()->check()) {
    //         return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập để đánh giá'], 401);
    //     }

    //     // Kiểm tra đã đánh giá chưa
    //     $daTonTai = DanhGia::where('san_pham_id', $request->san_pham_id)
    //         ->where('nguoi_dung_id', auth()->id())
    //         ->exists();

    //     if ($daTonTai) {
    //         return response()->json(['success' => false, 'message' => 'Bạn đã đánh giá sản phẩm này rồi'], 400);
    //     }

    //     $danhGia = DanhGia::create([
    //         'san_pham_id' => $request->san_pham_id,
    //         'nguoi_dung_id' => auth()->id(),
    //         'so_sao' => $request->so_sao,
    //         'tieu_de' => $request->tieu_de,
    //         'noi_dung' => $request->noi_dung,
    //         'duyet' => 1
    //     ]);

    //     $danhGia->load('nguoiDung');

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Cảm ơn bạn đã đánh giá!',
    //         'data' => $danhGia
    //     ]);
    // }
}