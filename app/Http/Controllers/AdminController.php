<?php

namespace App\Http\Controllers;

use App\Exports\AdminOrderExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\AdminReportExport;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon; // <-- Đã thêm Carbon để xử lý ngày tháng

use App\Models\User;
use App\Models\SanPham;
use App\Models\DonHang;
use App\Models\ThuongHieu;
use App\Models\BienTheSanPham;
use App\Models\DanhMuc;
use App\Models\DanhGia; // <-- ĐÃ THÊM
use App\Models\DonHangChiTiet;
use App\Models\KhuyenMai;
use Illuminate\Support\Facades\Hash;

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
    public function index(Request $request) // Đã cập nhật để nhận Request cho bộ lọc
    {
        if ($redirect = $this->ensureAdminOrStaff()) {
            return $redirect;
        }

        $currentDate = Carbon::now();
        $queryStart = null;
        $queryEnd = null;
        $selectedQuick = null; // Biến để theo dõi lựa chọn nhanh

        // --- 1. Xử lý Lọc theo Ngày/Tháng/Năm ---
        if ($request->has('quick_select') && $request->quick_select != '') {
            $selectedQuick = $request->quick_select;
            switch ($selectedQuick) {
                case 'today':
                    $queryStart = $currentDate->copy()->startOfDay();
                    $queryEnd = $currentDate->copy()->endOfDay();
                    break;
                case 'this_month':
                    $queryStart = $currentDate->copy()->startOfMonth();
                    $queryEnd = $currentDate->copy()->endOfDay();
                    break;
                case 'this_year':
                    $queryStart = $currentDate->copy()->startOfYear();
                    $queryEnd = $currentDate->copy()->endOfDay();
                    break;
                case 'custom':
                    // Custom logic được xử lý bên dưới bằng start_date/end_date
                    if ($request->has('start_date') && $request->has('end_date') && $request->start_date && $request->end_date) {
                        $queryStart = Carbon::parse($request->start_date)->startOfDay();
                        $queryEnd = Carbon::parse($request->end_date)->endOfDay();
                    }
                    break;
            }
        } 
        
        // Đặt giá trị mặc định nếu chưa được đặt (trường hợp lần đầu truy cập hoặc custom chưa có ngày)
        if (is_null($queryStart) || is_null($queryEnd)) {
            $queryStart = $currentDate->copy()->startOfMonth();
            $queryEnd = $currentDate->copy()->endOfDay();
            $selectedQuick = $selectedQuick ?? 'this_month';
        }
        
        // --- 2. Thống kê theo điều kiện lọc ---
        // Thống kê không lọc theo ngày
        $tongKhachHang = User::where('vai_tro', 'KHACH_HANG')->count();
        $tongSanPham    = SanPham::whereNull('deleted_at')->count();

        // Query cho Đơn hàng trong khoảng thời gian
        $tongDonHang = DonHang::query()
            ->whereBetween('ngay_dat', [$queryStart, $queryEnd])
            ->count();
        
        // Tính lại Doanh thu (ĐH HOAN_THANH trong khoảng thời gian)
        $tongDoanhThu = DonHang::query()
            ->whereBetween('ngay_dat', [$queryStart, $queryEnd])
            ->where('trang_thai', 'HOAN_THANH')
            ->sum('thanh_tien');

        // Thống kê Đơn hàng theo trạng thái
        $donMoi = DonHang::query()
            ->whereBetween('ngay_dat', [$queryStart, $queryEnd])
            ->where('trang_thai', 'DANG_XU_LY')
            ->count();
            
        $donHoanThanh = DonHang::query()
            ->whereBetween('ngay_dat', [$queryStart, $queryEnd])
            ->where('trang_thai', 'HOAN_THANH')
            ->count();
            
        // Gán lại các biến ngày tháng (sử dụng request data nếu có, nếu không thì dùng giá trị đã tính)
        $selectedStartDate = $request->start_date ?? $queryStart->format('Y-m-d');
        $selectedEndDate = $request->end_date ?? $queryEnd->format('Y-m-d');


        return view('admin.dashboard', compact(
            'tongKhachHang',
            'tongSanPham',
            'tongDonHang',
            'tongDoanhThu',
            'donMoi',
            'donHoanThanh',
            'selectedStartDate',
            'selectedEndDate',
            'selectedQuick'
        ));
    }

    // QUẢN LÝ TÀI KHOẢN
    public function accounts(Request $request)
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;

        $query = User::query();

        // --- 1. Lọc theo Vai trò ---
        $vaiTro = $request->input('vai_tro');
        if ($vaiTro && in_array($vaiTro, ['ADMIN', 'NHAN_VIEN', 'KHACH_HANG'])) {
            $query->where('vai_tro', $vaiTro);
        }
        
        // --- 2. Tìm kiếm theo ho_ten, email, sdt ---
        if ($request->has('keyword') && $request->keyword != '') {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('ho_ten', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%")
                  ->orWhere('sdt', 'like', "%{$keyword}%");
            });
        }
        
        // --- 3. Sắp xếp ---
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        
        if (!in_array($sortBy, ['id', 'ho_ten', 'email', 'vai_tro', 'created_at'])) {
            $sortBy = 'created_at';
        }
        if (!in_array(strtolower($sortOrder), ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        $users = $query->orderBy($sortBy, $sortOrder)->paginate(10);
        $users->appends($request->all());

        return view('admin.accounts', compact('users', 'vaiTro', 'sortBy', 'sortOrder'));
    }

    // 2. FORM THÊM
    public function createAccount()
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;
        return view('admin.accounts.create');
    }

    // 3. XỬ LÝ LƯU (STORE)
    public function storeAccount(Request $request)
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;

        $request->validate([
            'ho_ten' => 'required|string|max:255', // Input name="ho_ten"
            'email' => 'required|email|unique:nguoi_dung,email', // Check trùng bảng nguoi_dung
            'mat_khau' => 'required|min:6',
            'vai_tro' => 'required|in:ADMIN,NHAN_VIEN,KHACH_HANG',
        ], [
            'email.unique' => 'Email này đã được sử dụng.',
            'mat_khau.min' => 'Mật khẩu phải từ 6 ký tự trở lên.'
        ]);

        try {
            $user = new User();
            $user->ho_ten = $request->ho_ten;
            $user->email = $request->email;
            $user->mat_khau = Hash::make($request->mat_khau); // Lưu cột mat_khau
            $user->vai_tro = $request->vai_tro;
            $user->sdt = $request->sdt; // Lưu cột sdt

            $user->save();

            return redirect()->route('admin.accounts')->with('success', 'Tạo tài khoản thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage())->withInput();
        }
    }

    // 4. FORM SỬA
    public function editAccount($id)
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;
        $user = User::findOrFail($id);
        return view('admin.accounts.edit', compact('user'));
    }

    // 5. XỬ LÝ CẬP NHẬT (UPDATE)
    public function updateAccount(Request $request, $id)
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;
        $user = User::findOrFail($id);

        $request->validate([
            'ho_ten' => 'required|string|max:255',
            'email' => 'required|email|unique:nguoi_dung,email,' . $user->id,
            'vai_tro' => 'required|in:ADMIN,NHAN_VIEN,KHACH_HANG',
            'mat_khau' => 'nullable|min:6',
        ]);

        try {
            $user->ho_ten = $request->ho_ten;
            $user->email = $request->email;
            $user->vai_tro = $request->vai_tro;
            $user->sdt = $request->sdt;
            
            if ($request->filled('mat_khau')) {
                $user->mat_khau = Hash::make($request->mat_khau);
            }

            $user->save();

            return redirect()->route('admin.accounts')->with('success', 'Cập nhật tài khoản thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    // 6. XÓA
    public function deleteAccount($id)
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;

        try {
            if (Auth::id() == $id) {
                return back()->with('error', 'Bạn không thể xóa tài khoản của chính mình!');
            }
            $user = User::findOrFail($id);
            $user->delete();
            return redirect()->route('admin.accounts')->with('success', 'Đã xóa tài khoản!');
        } catch (\Exception $e) {
            return back()->with('error', 'Xóa thất bại: ' . $e->getMessage());
        }
    }



    // QUẢN LÝ SẢN PHẨM
    public function products(Request $request)
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;

        $query = SanPham::with('thuongHieu')->select('san_pham.*');
        
        $keyword = $request->input('keyword');
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('ten', 'like', "%{$keyword}%")
                  ->orWhereHas('bienTheSanPham', function ($q2) use ($keyword) {
                      $q2->where('sku', 'like', "%{$keyword}%");
                  });
            });
        }

        $brandId = $request->input('thuong_hieu_id');
        if ($brandId) {
            $query->where('thuong_hieu_id', $brandId);
        }
        
        $hienThi = $request->input('hien_thi');
        if (in_array($hienThi, ['1', '0'])) {
            $query->where('hien_thi', (int)$hienThi);
        }

        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        if ($sortBy == 'gia' || $sortBy == 'ton_kho') {
            $query->join('bien_the_san_pham', 'san_pham.id', '=', 'bien_the_san_pham.san_pham_id')
                  ->whereNull('bien_the_san_pham.deleted_at')
                  ->where(function($q) {
                      $q->whereRaw('bien_the_san_pham.id = (SELECT MIN(id) FROM bien_the_san_pham WHERE san_pham_id = san_pham.id)');
                  })
                  ->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }
        
        if ($sortBy == 'gia' || $sortBy == 'ton_kho') {
            $products = $query->distinct()->with('bienTheSanPham')->paginate(10);
        } else {
            $products = $query->with('bienTheSanPham')->paginate(10);
        }
        
        $thuongHieu = ThuongHieu::all();
        $products->appends($request->all());

        return view('admin.products', compact(
            'products', 
            'thuongHieu',
            'keyword',
            'brandId',
            'hienThi',
            'sortBy',
            'sortOrder'
        ));
    }

    /**
     * 2. Form thêm sản phẩm mới
     */
    public function createProduct()
    {
        // if ($redirect = $this->ensureAdminOrStaff()) return $redirect;
        // $thuongHieu = ThuongHieu::all(); // Lấy danh sách thương hiệu
        // return view('admin.products.create', compact('thuongHieu'));
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;
        $thuongHieu = ThuongHieu::all();
        $danhMuc = DanhMuc::whereNull('deleted_at')->get(); // <<< LẤY DANH MỤC >>>
        // Pass danhMuc list to the view
        return view('admin.products.create', compact('thuongHieu', 'danhMuc'));
    }

    /**
     * 3. Xử lý lưu sản phẩm mới
     */
    // public function storeProduct(Request $request)
    // {
    //     if ($redirect = $this->ensureAdminOrStaff()) return $redirect;

    //     // Validate dữ liệu
    //     $request->validate([
    //         'ten' => 'required|max:191',
    //         'sku' => 'required|unique:bien_the_san_pham,sku',
    //         'gia' => 'required|numeric|min:0',
    //         'hinh_anh' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    //     ]);

    //     try {
    //         DB::beginTransaction();

    //         // Tạo Sản Phẩm
    //         $sanPham = new SanPham();
    //         $sanPham->ten = $request->ten;
    //         $sanPham->slug = Str::slug($request->ten) . '-' . Str::random(4);
    //         $sanPham->thuong_hieu_id = $request->thuong_hieu_id;
    //         $sanPham->mo_ta_ngan = $request->mo_ta_ngan;
    //         $sanPham->mo_ta_day_du = $request->mo_ta_day_du;
    //         $sanPham->hien_thi = $request->has('hien_thi') ? 1 : 0;

    //         // Upload ảnh
    //         if ($request->hasFile('hinh_anh')) {
    //             $file = $request->file('hinh_anh');
    //             $filename = time() . '_' . $file->getClientOriginalName();
    //             $file->move(public_path('uploads'), $filename);
    //             $sanPham->hinh_anh_mac_dinh = $filename;
    //         }

    //         $sanPham->save();

    //         // Tạo Biến Thể (Giá/Kho/SKU)
    //         $bienThe = new BienTheSanPham();
    //         $bienThe->san_pham_id = $sanPham->id;
    //         $bienThe->sku = $request->sku;
    //         $bienThe->gia = $request->gia;
    //         $bienThe->gia_so_sanh = $request->gia_so_sanh;
    //         $bienThe->ton_kho = $request->ton_kho ?? 0;
    //         $bienThe->dang_ban = 1; // Mặc định bán ngay
    //         $bienThe->save();

    //         DB::commit();

    //         return redirect()->route('admin.products')->with('success', 'Thêm sản phẩm thành công!');

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return back()->with('error', 'Lỗi: ' . $e->getMessage())->withInput();
    //     }
    // }

    /**
     * 4. Form sửa sản phẩm
     */
    // public function editProduct($id)
    // {
    //     if ($redirect = $this->ensureAdminOrStaff()) return $redirect;

    //     // FIX LỖI: Dùng 'bienTheSanPham' thay vì 'bienThe'
    //     $product = SanPham::with('bienTheSanPham')->findOrFail($id);
    //     $thuongHieu = ThuongHieu::all();
        
    //     // SỬ DỤNG TÊN MỐI QUAN HỆ ĐÚNG
    //     $firstVariant = $product->bienTheSanPham->first();

    //     return view('admin.products.edit', compact('product', 'thuongHieu', 'firstVariant'));
    // }
    /**
     * 5. Xử lý cập nhật sản phẩm
     */
    // public function updateProduct(Request $request, $id)
    // {
    //     if ($redirect = $this->ensureAdminOrStaff()) return $redirect;

    //     $product = SanPham::findOrFail($id);
        
    //     // Lấy biến thể chính cần sửa
    //     $variant = $product->bienTheSanPham()->first(); 
    //     $variantId = $variant ? $variant->id : null;

    //     $request->validate([
    //         'ten' => 'required|max:191',
    //         'sku' => 'required|unique:bien_the_san_pham,sku,' . $variantId,
    //         'gia' => 'required|numeric|min:0',
    //         'hinh_anh' => 'nullable|image|max:2048',
    //     ]);

    //     try {
    //         DB::beginTransaction();

    //         // Cập nhật thông tin chung
    //         $product->ten = $request->ten;
    //         $product->thuong_hieu_id = $request->thuong_hieu_id;
    //         $product->mo_ta_ngan = $request->mo_ta_ngan;
    //         $product->mo_ta_day_du = $request->mo_ta_day_du;
    //         $product->hien_thi = $request->has('hien_thi') ? 1 : 0;

    //         // Xử lý ảnh mới nếu có
    //         if ($request->hasFile('hinh_anh')) {
    //             // (Tùy chọn: Xóa ảnh cũ ở đây nếu muốn tiết kiệm dung lượng)
    //             $file = $request->file('hinh_anh');
    //             $filename = time() . '_' . $file->getClientOriginalName();
    //             $file->move(public_path('uploads'), $filename);
    //             $product->hinh_anh_mac_dinh = $filename;
    //         }
    //         $product->save();

    //         // Cập nhật hoặc Tạo biến thể
    //         if ($variant) {
    //             $variant->sku = $request->sku;
    //             $variant->gia = $request->gia;
    //             $variant->gia_so_sanh = $request->gia_so_sanh;
    //             $variant->ton_kho = $request->ton_kho;
    //             $variant->save();
    //         } else {
    //             // Trường hợp dữ liệu cũ bị thiếu biến thể, tạo mới để fix lỗi
    //             BienTheSanPham::create([
    //                 'san_pham_id' => $product->id,
    //                 'sku' => $request->sku,
    //                 'gia' => $request->gia,
    //                 'ton_kho' => $request->ton_kho,
    //                 'dang_ban' => 1
    //             ]);
    //         }

    //         DB::commit();
    //         return redirect()->route('admin.products')->with('success', 'Cập nhật thành công!');

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return back()->with('error', 'Lỗi: ' . $e->getMessage());
    //     }
    // }
    // 3. Xử lý lưu sản phẩm mới
public function storeProduct(Request $request)
{
    if ($redirect = $this->ensureAdminOrStaff()) return $redirect;

    // Validate dữ liệu
    $request->validate([
        'ten' => 'required|max:191',
        'sku' => 'required|unique:bien_the_san_pham,sku',
        'gia' => 'required|numeric|min:0',
        'hinh_anh' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'danh_muc_ids' => 'required|array', // <<< YÊU CẦU CHỌN DANH MỤC >>>
        'danh_muc_ids.*' => 'exists:danh_muc,id',
    ]);

    try {
        DB::beginTransaction();

        // 1. Tạo Sản Phẩm
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
        
        // <<< LƯU MỐI QUAN HỆ DANH MỤC (ATTACH) >>>
        $sanPham->danhMuc()->attach($request->danh_muc_ids); 

        // 2. Tạo Biến Thể (Giá/Kho/SKU)
        $bienThe = new BienTheSanPham();
        $bienThe->san_pham_id = $sanPham->id;
        $bienThe->sku = $request->sku;
        $bienThe->gia = $request->gia;
        $bienThe->gia_so_sanh = $request->gia_so_sanh;
        $bienThe->ton_kho = $request->ton_kho ?? 0;
        $bienThe->dang_ban = 1; 
        $bienThe->save();

        DB::commit();

        return redirect()->route('admin.products')->with('success', 'Thêm sản phẩm thành công!');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Lỗi: ' . $e->getMessage())->withInput();
    }
}

// 4. Form sửa sản phẩm
public function editProduct($id)
{
    if ($redirect = $this->ensureAdminOrStaff()) return $redirect;

    // Load thêm mối quan hệ danhMuc
    $product = SanPham::with('bienTheSanPham', 'danhMuc')->findOrFail($id); 
    $thuongHieu = ThuongHieu::all();
    $danhMuc = DanhMuc::whereNull('deleted_at')->get(); // <<< LẤY DANH MỤC >>>
    
    $firstVariant = $product->bienTheSanPham->first();

    // Pass danhMuc list to the view
    return view('admin.products.edit', compact('product', 'thuongHieu', 'firstVariant', 'danhMuc'));
}

// 5. Xử lý cập nhật sản phẩm
// app.zip/Http/Controllers/AdminController.php - Phương thức updateProduct

public function updateProduct(Request $request, $id)
{
    if ($redirect = $this->ensureAdminOrStaff()) return $redirect;

    $product = SanPham::findOrFail($id);
    // Lấy biến thể đầu tiên để kiểm tra ID khi validate unique SKU
    $variant = $product->bienTheSanPham()->first(); 
    $variantId = $variant ? $variant->id : null;

    $request->validate([
        'ten' => 'required|max:191',
        'sku' => 'required|unique:bien_the_san_pham,sku,' . $variantId . ',id', // Thêm validation unique SKU
        'gia' => 'required|numeric|min:0',
        'hinh_anh' => 'nullable|image|max:2048',
        'danh_muc_ids' => 'required|array', 
        'danh_muc_ids.*' => 'exists:danh_muc,id',
    ]);

    DB::beginTransaction();
    try {
        // --- GHI DỮ LIỆU SẢN PHẨM CHÍNH ---
        
        $product->ten = $request->ten;
        $product->slug = Str::slug($request->ten) . '-' . $product->id; 
        $product->thuong_hieu_id = $request->thuong_hieu_id;
        $product->mo_ta_ngan = $request->mo_ta_ngan;
        $product->mo_ta_day_du = $request->mo_ta_day_du;
        $product->hien_thi = $request->has('hien_thi') ? 1 : 0;
        
        // Cập nhật cờ đặc biệt (Flash Sale/Nổi bật)
        $product->la_flash_sale = $request->has('la_flash_sale') ? 1 : 0;
        $product->la_noi_bat = $request->has('la_noi_bat') ? 1 : 0;

        // Xử lý Upload ảnh chính (Giữ nguyên logic cũ)
        if ($request->hasFile('hinh_anh')) {
            $file = $request->file('hinh_anh');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $filename);
            $product->hinh_anh_mac_dinh = $filename;
        }

        $product->save();
        
        // --- GHI MỐI QUAN HỆ DANH MỤC (Many-to-Many) ---
        $product->danhMuc()->sync($request->danh_muc_ids); 
        
        // --- GHI DỮ LIỆU BIẾN THỂ ĐẦU TIÊN (Giả định sản phẩm đơn giản chỉ có 1 biến thể) ---
        if ($variant) {
            $variant->sku = $request->sku;
            $variant->gia = $request->gia;
            $variant->gia_so_sanh = $request->gia_so_sanh;
            $variant->ton_kho = $request->ton_kho ?? 0;
            $variant->mau_sac = $request->mau_sac; // Nếu form có field này
            $variant->dung_luong_gb = $request->dung_luong_gb; // Nếu form có field này
            $variant->dang_ban = 1;
            $variant->save();
        }
        
        DB::commit();
        return redirect()->route('admin.products')->with('success', 'Cập nhật thành công!');

    } catch (\Exception $e) {
        DB::rollBack();
        // Dòng này rất quan trọng để xem lỗi SQL hoặc lỗi code
        // dd($e); 
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
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }


    
    // QUẢN LÝ DANH MỤC
    public function categories(Request $request)
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;
        
        $query = DanhMuc::with('parent')->whereNull('deleted_at');
        
        $keyword = $request->input('keyword');
        if ($keyword) {
            $query->where('ten', 'like', "%{$keyword}%");
        }
        
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        
        if (!in_array($sortBy, ['id', 'ten', 'slug', 'created_at'])) {
            $sortBy = 'created_at';
        }

        $categories = $query->orderBy($sortBy, $sortOrder)->paginate(10);
        $categories->appends($request->all());

        return view('admin.categories.index', compact('categories', 'sortBy', 'sortOrder', 'keyword'));
    }

    public function createCategory()
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;
        // Lấy danh sách để chọn danh mục cha (chỉ lấy danh mục gốc cha_id = null)
        $parentCategories = DanhMuc::whereNull('cha_id')->get();
        return view('admin.categories.create', compact('parentCategories'));
    }

    public function storeCategory(Request $request)
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;
        $request->validate([
            'ten' => 'required|max:191|unique:danh_muc,ten',
        ]);

        try {
            DanhMuc::create([
                'ten' => $request->ten,
                'slug' => Str::slug($request->ten),
                'cha_id' => $request->cha_id, // Có thể null
            ]);
            return redirect()->route('admin.categories')->with('success', 'Thêm danh mục thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function editCategory($id)
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;
        $category = DanhMuc::findOrFail($id);
        // Lấy danh mục cha, loại trừ chính nó để tránh vòng lặp
        $parentCategories = DanhMuc::whereNull('cha_id')->where('id', '!=', $id)->get();
        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function updateCategory(Request $request, $id)
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;
        $category = DanhMuc::findOrFail($id);
        $request->validate([
            'ten' => 'required|max:191|unique:danh_muc,ten,' . $id,
        ]);

        try {
            $category->update([
                'ten' => $request->ten,
                'slug' => Str::slug($request->ten),
                'cha_id' => $request->cha_id,
            ]);
            return redirect()->route('admin.categories')->with('success', 'Cập nhật danh mục thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function deleteCategory($id)
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;
        try {
            $category = DanhMuc::findOrFail($id);
            // Kiểm tra xem có danh mục con hoặc sản phẩm không trước khi xóa (tùy chọn)
            $category->delete();
            return redirect()->route('admin.categories')->with('success', 'Xóa danh mục thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }


    // QUẢN LÝ ĐƠN HÀNG
    public function orders(Request $request)
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;
        
        $query = DonHang::with('nguoiDung')->whereNull('deleted_at');

        $trangThai = $request->input('trang_thai');
        if ($trangThai) {
            $query->where('trang_thai', $trangThai);
        }

        $keyword = $request->input('keyword');
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('ma', 'like', "%{$keyword}%")
                  ->orWhere('ten_nguoi_nhan', 'like', "%{$keyword}%");
            });
        }
        
        $sortBy = $request->input('sort_by', 'ngay_dat');
        $sortOrder = $request->input('sort_order', 'desc');

        if (!in_array($sortBy, ['ma', 'tong_tien', 'trang_thai', 'ngay_dat'])) {
            $sortBy = 'ngay_dat';
        }
        
        $orders = $query->orderBy($sortBy, $sortOrder)->paginate(10);
        $orders->appends($request->all());

        return view('admin.orders', compact('orders', 'trangThai', 'sortBy', 'sortOrder', 'keyword'));
    }

    public function editOrder($id)
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;
        
        $order = DonHang::with(['chiTiet.bienThe.sanPham', 'nguoiDung'])
            ->whereNull('deleted_at')
            ->findOrFail($id);
            
        // Trả về view để Admin xem chi tiết và chỉnh sửa
        return view('admin.orders.edit', compact('order'));
    }

    // 2. XỬ LÝ CẬP NHẬT (UPDATE)
    public function updateOrder(Request $request, $id)
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;
        
        $order = DonHang::whereNull('deleted_at')->findOrFail($id);

        $request->validate([
            'trang_thai' => 'required|in:DANG_XU_LY,DANG_GIAO,HOAN_THANH,HUY',
            'trang_thai_tt' => 'required|in:CHUA_TT,DA_TT',
            'ten_nguoi_nhan' => 'required|string|max:191',
            'sdt_nguoi_nhan' => 'required|string|max:32',
            'dia_chi_giao' => 'required|string',
            'ghi_chu' => 'nullable|string|max:500',
        ]);
        
        DB::beginTransaction();
        try {
            $oldStatus = $order->trang_thai;

            $order->trang_thai = $request->trang_thai;
            $order->trang_thai_tt = $request->trang_thai_tt;
            $order->ten_nguoi_nhan = $request->ten_nguoi_nhan;
            $order->sdt_nguoi_nhan = $request->sdt_nguoi_nhan;
            $order->dia_chi_giao = $request->dia_chi_giao;
            $order->ghi_chu = $request->ghi_chu;
            $order->updated_at = now();
            
            // Logic hoàn trả tồn kho nếu chuyển sang HUY
            if ($oldStatus != 'HUY' && $request->trang_thai == 'HUY') {
                 // Hoàn lại tồn kho
                foreach ($order->chiTiet as $item) {
                    if ($item->bienThe) {
                        $item->bienThe->ton_kho = $item->bienThe->ton_kho + $item->so_luong;
                        $item->bienThe->save();
                    }
                }
            }
            
            $order->save();
            DB::commit();

            return redirect()->route('admin.orders')->with('success', 'Cập nhật đơn hàng #' . $order->ma . ' thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi cập nhật: ' . $e->getMessage());
        }
    }

    // 3. XÓA (DELETE) - Soft delete
    public function deleteOrder($id)
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;
        
        DB::beginTransaction();
        try {
            $order = DonHang::whereNull('deleted_at')->findOrFail($id);
            $orderCode = $order->ma;

            // Xử lý hoàn kho và chuyển trạng thái trước khi soft delete
            if ($order->trang_thai != 'HUY') {
                $order->trang_thai = 'HUY';
                 // Hoàn lại tồn kho
                foreach ($order->chiTiet as $item) {
                    if ($item->bienThe) {
                        $item->bienThe->ton_kho = $item->bienThe->ton_kho + $item->so_luong;
                        $item->bienThe->save();
                    }
                }
                $order->save();
            }
            
            $order->delete(); // Soft delete
            
            DB::commit();

            return redirect()->route('admin.orders')->with('success', 'Đã xóa (soft delete) đơn hàng #' . $orderCode);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi xóa đơn hàng: ' . $e->getMessage());
        }
    }
    
    // QUẢN LÝ THƯƠNG HIỆU
    public function brands(Request $request)
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;
        
        $query = ThuongHieu::whereNull('deleted_at');
        
        $keyword = $request->input('keyword');
        if ($keyword) {
            $query->where('ten', 'like', "%{$keyword}%");
        }
        
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        
        if (!in_array($sortBy, ['id', 'ten', 'slug', 'created_at'])) {
            $sortBy = 'created_at';
        }
        
        $brands = $query->orderBy($sortBy, $sortOrder)->paginate(10);
        $brands->appends($request->all());

        return view('admin.brands.index', compact('brands', 'sortBy', 'sortOrder', 'keyword'));
    }

    public function createBrand()
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;
        return view('admin.brands.create');
    }

    public function storeBrand(Request $request)
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;

        $request->validate([
            'ten' => 'required|max:191|unique:thuong_hieu,ten',
            'hinh_anh' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // BẮT BUỘC có ảnh khi tạo
        ]);

        try {
            $hinhAnhPath = null;
            if ($request->hasFile('hinh_anh')) {
                $file = $request->file('hinh_anh');
                // Đường dẫn lưu file: public/images/brands
                $destinationPath = public_path('images/brands');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move($destinationPath, $filename);
                $hinhAnhPath = $filename;
            }

            ThuongHieu::create([
                'ten' => $request->ten,
                'slug' => Str::slug($request->ten),
                'hinh_anh' => $hinhAnhPath, // LƯU TÊN FILE
            ]);

            return redirect()->route('admin.brands')->with('success', 'Thêm thương hiệu thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage())->withInput();
        }
    }

    public function editBrand($id)
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;
        $brand = ThuongHieu::findOrFail($id);
        return view('admin.brands.edit', compact('brand'));
    }

    public function updateBrand(Request $request, $id)
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;

        $brand = ThuongHieu::findOrFail($id);

        $request->validate([
            'ten' => 'required|max:191|unique:thuong_hieu,ten,' . $brand->id,
            'hinh_anh_moi' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Ảnh mới là tùy chọn
        ]);

        try {
            $brand->ten = $request->ten;
            $brand->slug = Str::slug($request->ten);

            if ($request->hasFile('hinh_anh_moi')) {
                // Tùy chọn: Xóa ảnh cũ
                // if ($brand->hinh_anh && file_exists(public_path('images/brands/' . $brand->hinh_anh))) {
                //     unlink(public_path('images/brands/' . $brand->hinh_anh));
                // }
                
                $file = $request->file('hinh_anh_moi');
                $destinationPath = public_path('images/brands');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move($destinationPath, $filename);
                $brand->hinh_anh = $filename; // LƯU TÊN FILE MỚI
            }

            $brand->save();

            return redirect()->route('admin.brands')->with('success', 'Cập nhật thương hiệu thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage())->withInput();
        }
    }

    public function deleteBrand($id)
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;
        try {
            ThuongHieu::findOrFail($id)->delete();
            return redirect()->route('admin.brands')->with('success', 'Xóa thương hiệu thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function reviews()
    {
        if ($redirect = $this->ensureAdminOrStaff()) {
            return $redirect;
        }

        return view('admin.reviews');
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


    public function profile()// trang thông tin cá nhân 
    {
        if ($redirect = $this->ensureAdminOrStaff()) {
            return $redirect;
        }

        // Lấy thông tin người dùng đang đăng nhập
        $user = Auth::user();

        return view('admin.profile', compact('user'));
    }
    // Xử lý cập nhật thông tin cá nhân
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // 1. Validate dữ liệu
        $request->validate([
            'ho_ten' => 'required|string|max:255',
            'sdt' => 'nullable|string|max:20', // Sửa validate theo tên input mới
            'password' => 'nullable|string|min:6|confirmed', 
            // Lưu ý: Input mật khẩu trên form vẫn nên để name="password" để tận dụng tính năng "confirmed" của Laravel
        ], [
            'ho_ten.required' => 'Vui lòng nhập họ tên.',
            'password.min' => 'Mật khẩu phải từ 6 ký tự trở lên.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ]);

        // 2. Cập nhật thông tin cơ bản
        $user->ho_ten = $request->ho_ten;
        $user->sdt = $request->sdt; // <-- Sửa: lưu vào cột 'sdt'

        // 3. Nếu người dùng có nhập mật khẩu mới
        if ($request->filled('password')) {
            // <-- Sửa: lưu vào cột 'mat_khau'
            $user->mat_khau = Hash::make($request->password); 
        }

        /** @var \App\Models\User $user */
        $user->save();

        return back()->with('success', 'Đã cập nhật hồ sơ thành công!');
    }
    
    // QUẢN LÝ KHUYẾN MÃI (REAL DB)
    public function promotions(Request $request)
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;
        
        $query = KhuyenMai::query();
        
        // --- 1. Tìm kiếm (theo Tên và Mã) ---
        $keyword = $request->input('keyword');
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('ten', 'like', "%{$keyword}%")
                  ->orWhere('ma', 'like', "%{$keyword}%");
            });
        }

        // --- 2. Sắp xếp ---
        $sortBy = $request->input('sort_by', 'ngay_bat_dau'); 
        $sortOrder = $request->input('sort_order', 'desc');

        if (!in_array($sortBy, ['ten', 'ma', 'ngay_bat_dau'])) {
            $sortBy = 'ngay_bat_dau';
        }
        
        $promotions = $query->orderBy($sortBy, $sortOrder)->paginate(10);
        $promotions->appends($request->all());

        return view('admin.promotions', compact('promotions', 'keyword', 'sortBy', 'sortOrder'));
    }

    public function createPromotion()
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;
        return view('admin.promotions.create');
    }

    public function storePromotion(Request $request)
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;

        $request->validate([
            'name' => 'required|max:191',
            'code' => 'required|unique:khuyen_mai,ma', // Kiểm tra mã duy nhất
            'discount_value' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
        
        try {
            KhuyenMai::create([
                'ten' => $request->name,
                'ma' => $request->code,
                'gia_tri' => $request->discount_value,
                'ngay_bat_dau' => $request->start_date,
                'ngay_ket_thuc' => $request->end_date,
            ]);

            return redirect()->route('admin.promotions')->with('success', 'Tạo khuyến mãi thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi DB: ' . $e->getMessage())->withInput();
        }
    }

    public function editPromotion($id)
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;
        
        $promo = KhuyenMai::findOrFail($id);
        
        return view('admin.promotions.edit', compact('promo'));
    }

    public function updatePromotion(Request $request, $id)
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;

        $request->validate([
            'name' => 'required|max:191',
            'discount_value' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        try {
            $promo = KhuyenMai::findOrFail($id);
            
            $promo->update([
                'ten' => $request->name,
                'gia_tri' => $request->discount_value,
                'ngay_bat_dau' => $request->start_date,
                'ngay_ket_thuc' => $request->end_date,
            ]);

            return redirect()->route('admin.promotions')->with('success', 'Cập nhật khuyến mãi thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi DB: ' . $e->getMessage())->withInput();
        }
    }

    public function deletePromotion($id)
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;
        
        try {
            KhuyenMai::findOrFail($id)->delete();
            return redirect()->route('admin.promotions')->with('success', 'Đã xóa khuyến mãi!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi xóa: ' . $e->getMessage());
        }
    }
    
    // BÁO CÁO TỔNG HỢP (ĐÃ CẬP NHẬT)
    // public function reports(Request $request)
    // {
    //     if ($redirect = $this->ensureAdminOrStaff()) return $redirect;

    //     $currentDate = Carbon::now();
    //     $queryStart = null;
    //     $queryEnd = null;
    //     $selectedQuick = null;

    //     // --- 1. Xử lý Lọc theo Ngày/Tháng/Năm (Tương tự Dashboard) ---
    //     if ($request->has('quick_select') && $request->quick_select != '') {
    //         $selectedQuick = $request->quick_select;
    //         switch ($selectedQuick) {
    //             case 'today':
    //                 $queryStart = $currentDate->copy()->startOfDay();
    //                 $queryEnd = $currentDate->copy()->endOfDay();
    //                 break;
    //             case 'this_month':
    //                 $queryStart = $currentDate->copy()->startOfMonth();
    //                 $queryEnd = $currentDate->copy()->endOfDay();
    //                 break;
    //             case 'this_year':
    //                 $queryStart = $currentDate->copy()->startOfYear();
    //                 $queryEnd = $currentDate->copy()->endOfDay();
    //                 break;
    //             case 'custom':
    //                 if ($request->has('start_date') && $request->has('end_date') && $request->start_date && $request->end_date) {
    //                     $queryStart = Carbon::parse($request->start_date)->startOfDay();
    //                     $queryEnd = Carbon::parse($request->end_date)->endOfDay();
    //                 }
    //                 break;
    //         }
    //     } 
        
    //     if (is_null($queryStart) || is_null($queryEnd)) {
    //         $queryStart = $currentDate->copy()->startOfMonth();
    //         $queryEnd = $currentDate->copy()->endOfDay();
    //         $selectedQuick = $selectedQuick ?? 'this_month';
    //     }

    //     // --- 2. Tính toán thống kê ---
        
    //     // Doanh thu và Đơn hàng
    //     $tongDoanhThu = DonHang::where('trang_thai', 'HOAN_THANH')
    //         ->whereBetween('ngay_dat', [$queryStart, $queryEnd])
    //         ->sum('thanh_tien');

    //     $tongDonHang = DonHang::whereBetween('ngay_dat', [$queryStart, $queryEnd])->count();
        
    //     // Khách hàng mới (Vai trò KHACH_HANG)
    //     $khachHangMoi = User::where('vai_tro', 'KHACH_HANG')
    //         ->whereBetween('created_at', [$queryStart, $queryEnd])
    //         ->count();

    //     // Đánh giá trung bình (FIXED: DanhGia::query())
    //     $avgRating = DanhGia::query()->where('duyet', 1)->avg('so_sao') ?? 0;
        
    //     // Top Sản phẩm bán chạy nhất (ĐÃ SỬA LỖI AMBIGUOUS)
    //     $topSellingProducts = \App\Models\DonHangChiTiet::select('san_pham_id', DB::raw('SUM(so_luong) as tong_so_luong_ban'), DB::raw('SUM(don_hang_chi_tiet.thanh_tien) as tong_doanh_thu'))
    //         ->join('don_hang', 'don_hang_chi_tiet.don_hang_id', '=', 'don_hang.id')
    //         ->where('don_hang.trang_thai', 'HOAN_THANH')
    //         ->whereBetween('don_hang.ngay_dat', [$queryStart, $queryEnd])
    //         ->groupBy('san_pham_id')
    //         ->orderBy('tong_so_luong_ban', 'desc')
    //         ->with('sanPham') 
    //         ->limit(5)
    //         ->get()
    //         ->map(function($item) {
    //             $item->ten = $item->sanPham->ten ?? 'Sản phẩm đã xóa';
    //             return $item;
    //         });

    //     // Đơn hàng gần đây
    //     $recentOrders = DonHang::with('nguoiDung')
    //         ->whereBetween('ngay_dat', [$queryStart, $queryEnd])
    //         ->orderBy('ngay_dat', 'desc')
    //         ->limit(5)
    //         ->get();
        
    //     // Truyền các biến ngày tháng
    //     $queryStartFormatted = $queryStart->format('Y-m-d');
    //     $queryEndFormatted = $queryEnd->format('Y-m-d');

    //     return view('admin.reports', compact(
    //         'tongDoanhThu',
    //         'tongDonHang',
    //         'khachHangMoi',
    //         'avgRating',
    //         'topSellingProducts',
    //         'recentOrders',
    //         'selectedQuick', 
    //         'queryStartFormatted', 
    //         'queryEndFormatted'
    //     ));
    // }
    public function reports(Request $request) {
    $type = $request->input('report_type', 'doanh_thu');
    $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
    $endDate = $request->input('end_date', Carbon::now()->endOfDay()->toDateString());

    $data = $this->getReportData($type, $startDate, $endDate);

    return view('admin.reports.index', compact('data', 'type', 'startDate', 'endDate'));
}

private function getReportData($type, $start, $end) {
    $query = match($type) {
        'don_hang' => DonHang::whereBetween('ngay_dat', [$start, $end]),
        'doanh_thu' => DonHang::where('trang_thai', 'HOAN_THANH')->whereBetween('ngay_dat', [$start, $end]),
        'san_pham' => DonHangChiTiet::join('don_hang', 'don_hang_chi_tiet.don_hang_id', '=', 'don_hang.id')
                        ->whereBetween('don_hang.ngay_dat', [$start, $end])
                        ->selectRaw('san_pham_id, ten_sp_ghi_nhan, SUM(so_luong) as total_qty, SUM(don_hang_chi_tiet.thanh_tien) as total_amount')
                        ->groupBy('san_pham_id', 'ten_sp_ghi_nhan'),
        'khach_hang' => User::where('vai_tro', 'KHACH_HANG')->whereBetween('created_at', [$start, $end]),
        default => DonHang::query(),
    };
    return $query->get();
}

public function exportReports(Request $request) {
    $type = $request->input('report_type');
    $format = $request->input('format'); // excel hoặc pdf
    $start = $request->input('start_date');
    $end = $request->input('end_date');
    $data = $this->getReportData($type, $start, $end);

    if ($format == 'pdf') {
    $pdf = Pdf::loadView("admin.reports.pdf_$type", compact('data', 'start', 'end'))
              ->setPaper('a4', 'portrait')
              ->setOption('defaultFont', 'DejaVu Sans'); // Đảm bảo dùng font này để hiển thị tiếng Việt
    return $pdf->download("baocao_{$type}_{$start}_to_{$end}.pdf");
}

    return Excel::download(new AdminReportExport($data, $type), "baocao_{$type}.xlsx");
}
    
    // ... (các hàm CRUD systems giữ nguyên)


    public function exportOrders(Request $request) 
{
    // Lấy dữ liệu (có thể thêm lọc theo ngày tương tự trang nhân viên)
    $orders = DonHang::all(); 
    $type = $request->query('type', 'excel');

    if ($type == 'pdf') {
        $pdf = Pdf::loadView('admin.exports.orders_excel', ['orders' => $orders]);
        return $pdf->download('danh-sach-don-hang.pdf');
    }

    return Excel::download(new AdminOrderExport($orders), 'danh-sach-don-hang.xlsx');
}
}