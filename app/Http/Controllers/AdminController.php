<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\SanPham;
use App\Models\DonHang;
use App\Models\ThuongHieu;
use App\Models\BienTheSanPham;

class AdminController extends Controller
{
    /**
     * Hàm dùng chung để kiểm tra quyền ADMIN / NHAN_VIEN
     */
    protected function ensureAdminOrStaff()
    {
        $user = Auth::user();

        if (!$user || !in_array($user->vai_tro, ['ADMIN'])) {
            // Không đủ quyền → quay lại trang chủ
            return redirect()->route('home')
                ->with('error', 'Bạn không có quyền truy cập trang quản trị.');
        }

        // Nếu ok thì return null
        return null;
    }

    // DASHBOARD
    public function index()
    {
        if ($redirect = $this->ensureAdminOrStaff()) {
            return $redirect;
        }

        // Một số thống kê đơn giản từ DB
        $tongKhachHang = User::where('vai_tro', 'KHACH_HANG')->count();
        $tongNhanVien   = User::where('vai_tro', 'NHAN_VIEN')->count();
        $tongAdmin      = User::where('vai_tro', 'ADMIN')->count();

        $tongSanPham    = SanPham::count();

        $tongDonHang    = DonHang::count();
        $tongDoanhThu   = DonHang::where('trang_thai', 'HOAN_THANH')->sum('thanh_tien');

        return view('admin.dashboard', compact(
            'tongKhachHang',
            'tongNhanVien',
            'tongAdmin',
            'tongSanPham',
            'tongDonHang',
            'tongDoanhThu'
        ));
    }

    // QUẢN LÝ TÀI KHOẢN
    public function accounts()
    {
        if ($redirect = $this->ensureAdminOrStaff()) {
            return $redirect;
        }

        $users = User::orderBy('created_at', 'desc')->paginate(10);

        return view('admin.accounts', compact('users'));
    }

    // QUẢN LÝ SẢN PHẨM
    // public function products()
    // {
    //     if ($redirect = $this->ensureAdminOrStaff()) {
    //         return $redirect;
    //     }

    //     $products = SanPham::orderBy('created_at', 'desc')->paginate(10);

    //     return view('admin.products', compact('products'));
    // }



    // =========================================================================
    // QUẢN LÝ SẢN PHẨM (FULL CRUD)
    // =========================================================================

    /**
     * 1. Hiển thị danh sách sản phẩm
     */
    public function products()
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;

        // Eager load 'bienThe' để lấy giá hiển thị
        $products = SanPham::with('bienThe')->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.products', compact('products'));
    }

    /**
     * 2. Form thêm sản phẩm mới
     */
    public function createProduct()
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;

        $thuongHieu = ThuongHieu::all(); // Lấy danh sách thương hiệu
        return view('admin.products.create', compact('thuongHieu'));
    }

    /**
     * 3. Xử lý lưu sản phẩm mới
     */
    public function storeProduct(Request $request)
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;

        // Validate dữ liệu
        $request->validate([
            'ten' => 'required|max:191',
            'sku' => 'required|unique:bien_the_san_pham,sku',
            'gia' => 'required|numeric|min:0',
            'hinh_anh' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // Tạo Sản Phẩm
            $sanPham = new SanPham();
            $sanPham->ten = $request->ten;
            $sanPham->slug = Str::slug($request->ten) . '-' . Str::random(4);
            $sanPham->thuong_hieu_id = $request->thuong_hieu_id;
            $sanPham->mo_ta_ngan = $request->mo_ta_ngan;
            $sanPham->mo_ta_day_du = $request->mo_ta_day_du;
            $sanPham->hien_thi = $request->has('hien_thi') ? 1 : 0;

            // Upload ảnh
            if ($request->hasFile('hinh_anh')) {
                $file = $request->file('hinh_anh');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads'), $filename);
                $sanPham->hinh_anh_mac_dinh = $filename;
            }

            $sanPham->save();

            // Tạo Biến Thể (Giá/Kho/SKU)
            $bienThe = new BienTheSanPham();
            $bienThe->san_pham_id = $sanPham->id;
            $bienThe->sku = $request->sku;
            $bienThe->gia = $request->gia;
            $bienThe->gia_so_sanh = $request->gia_so_sanh;
            $bienThe->ton_kho = $request->ton_kho ?? 0;
            $bienThe->dang_ban = 1; // Mặc định bán ngay
            $bienThe->save();

            DB::commit();

            return redirect()->route('admin.products')->with('success', 'Thêm sản phẩm thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * 4. Form sửa sản phẩm
     */
    public function editProduct($id)
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;

        $product = SanPham::with('bienThe')->findOrFail($id);
        $thuongHieu = ThuongHieu::all();
        
        // Lấy biến thể đầu tiên để hiển thị thông tin giá/sku
        $firstVariant = $product->bienThe->first();

        return view('admin.products.edit', compact('product', 'thuongHieu', 'firstVariant'));
    }

    /**
     * 5. Xử lý cập nhật sản phẩm
     */
    public function updateProduct(Request $request, $id)
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;

        $product = SanPham::findOrFail($id);
        
        // Lấy biến thể chính cần sửa
        $variant = $product->bienThe()->first(); 
        $variantId = $variant ? $variant->id : null;

        $request->validate([
            'ten' => 'required|max:191',
            'sku' => 'required|unique:bien_the_san_pham,sku,' . $variantId,
            'gia' => 'required|numeric|min:0',
            'hinh_anh' => 'nullable|image|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // Cập nhật thông tin chung
            $product->ten = $request->ten;
            $product->thuong_hieu_id = $request->thuong_hieu_id;
            $product->mo_ta_ngan = $request->mo_ta_ngan;
            $product->mo_ta_day_du = $request->mo_ta_day_du;
            $product->hien_thi = $request->has('hien_thi') ? 1 : 0;

            // Xử lý ảnh mới nếu có
            if ($request->hasFile('hinh_anh')) {
                // (Tùy chọn: Xóa ảnh cũ ở đây nếu muốn tiết kiệm dung lượng)
                $file = $request->file('hinh_anh');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads'), $filename);
                $product->hinh_anh_mac_dinh = $filename;
            }
            $product->save();

            // Cập nhật hoặc Tạo biến thể
            if ($variant) {
                $variant->sku = $request->sku;
                $variant->gia = $request->gia;
                $variant->gia_so_sanh = $request->gia_so_sanh;
                $variant->ton_kho = $request->ton_kho;
                $variant->save();
            } else {
                // Trường hợp dữ liệu cũ bị thiếu biến thể, tạo mới để fix lỗi
                BienTheSanPham::create([
                    'san_pham_id' => $product->id,
                    'sku' => $request->sku,
                    'gia' => $request->gia,
                    'ton_kho' => $request->ton_kho,
                    'dang_ban' => 1
                ]);
            }

            DB::commit();
            return redirect()->route('admin.products')->with('success', 'Cập nhật thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    /**
     * 6. Xóa sản phẩm
     */
    public function deleteProduct($id)
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;

        try {
            $product = SanPham::findOrFail($id);
            // Ảnh cũ vẫn giữ lại hoặc bạn có thể code thêm đoạn xóa file trong public/uploads
            $product->delete(); // Xóa cascade biến thể (do setup DB hoặc Model)
            
            return redirect()->route('admin.products')->with('success', 'Đã xóa sản phẩm!');
        } catch (\Exception $e) {
            return back()->with('error', 'Xóa thất bại: ' . $e->getMessage());
        }
    }


    
    public function categories()
    {
        if ($redirect = $this->ensureAdminOrStaff()) {
            return $redirect;
        }

        return view('admin.categories');
    }

    public function orders()
    {
        if ($redirect = $this->ensureAdminOrStaff()) {
            return $redirect;
        }

        $orders = DonHang::orderBy('ngay_dat', 'desc')->paginate(10);

        return view('admin.orders', compact('orders'));
    }

    public function promotions()
    {
        if ($redirect = $this->ensureAdminOrStaff()) {
            return $redirect;
        }

        return view('admin.promotions');
    }

    public function reviews()
    {
        if ($redirect = $this->ensureAdminOrStaff()) {
            return $redirect;
        }

        return view('admin.reviews');
    }

    public function reports()
    {
        if ($redirect = $this->ensureAdminOrStaff()) {
            return $redirect;
        }

        return view('admin.reports');
    }

    public function backup()
    {
        if ($redirect = $this->ensureAdminOrStaff()) {
            return $redirect;
        }

        return view('admin.backup');
    }

    public function settings()
    {
        if ($redirect = $this->ensureAdminOrStaff()) {
            return $redirect;
        }

        return view('admin.settings');
    }



    
}
