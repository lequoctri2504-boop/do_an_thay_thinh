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
use App\Models\DanhMuc;
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
    // public function accounts()
    // {
    //     if ($redirect = $this->ensureAdminOrStaff()) {
    //         return $redirect;
    //     }

    //     $users = User::orderBy('created_at', 'desc')->paginate(10);

    //     return view('admin.accounts', compact('users'));
    // }

    // // --- 1. HIỂN THỊ DANH SÁCH & TÌM KIẾM TÀI KHOẢN ---
    // public function accounts(Request $request)
    // {
    //     if ($redirect = $this->ensureAdminOrStaff()) return $redirect;

    //     $query = User::query();

    //     // Xử lý tìm kiếm
    //     if ($request->has('keyword') && $request->keyword != '') {
    //         $keyword = $request->keyword;
    //         $query->where(function($q) use ($keyword) {
    //             $q->where('name', 'like', "%{$keyword}%") // Tìm theo tên
    //               ->orWhere('email', 'like', "%{$keyword}%") // Tìm theo email
    //               ->orWhere('so_dien_thoai', 'like', "%{$keyword}%"); // Tìm theo SĐT (nếu có cột này)
    //         });
    //     }

    //     // Sắp xếp mới nhất trước và phân trang
    //     $users = $query->orderBy('created_at', 'desc')->paginate(10);

    //     // Giữ lại tham số tìm kiếm khi chuyển trang (page 1 -> page 2)
    //     $users->appends($request->all());

    //     return view('admin.accounts', compact('users'));
    // }

    // // --- 2. FORM THÊM TÀI KHOẢN ---
    // public function createAccount()
    // {
    //     if ($redirect = $this->ensureAdminOrStaff()) return $redirect;
    //     return view('admin.accounts.create');
    // }

    // // --- 3. XỬ LÝ LƯU TÀI KHOẢN MỚI ---
    // public function storeAccount(Request $request)
    // {
    //     if ($redirect = $this->ensureAdminOrStaff()) return $redirect;

    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email|unique:users,email',
    //         'password' => 'required|min:6',
    //         'vai_tro' => 'required|in:ADMIN,NHAN_VIEN,KHACH_HANG',
    //     ], [
    //         'email.unique' => 'Email này đã được sử dụng.',
    //         'password.min' => 'Mật khẩu phải từ 6 ký tự trở lên.'
    //     ]);

    //     try {
    //         $user = new User();
    //         $user->name = $request->name;
    //         $user->email = $request->email;
    //         $user->password = Hash::make($request->password); // Mã hóa mật khẩu
    //         $user->vai_tro = $request->vai_tro;
            
    //         // Nếu bảng users của bạn có cột so_dien_thoai, dia_chi thì thêm vào đây:
    //         // $user->so_dien_thoai = $request->so_dien_thoai;
    //         // $user->dia_chi = $request->dia_chi;

    //         $user->save();

    //         return redirect()->route('admin.accounts')->with('success', 'Tạo tài khoản thành công!');
    //     } catch (\Exception $e) {
    //         return back()->with('error', 'Lỗi: ' . $e->getMessage())->withInput();
    //     }
    // }

    // // --- 4. FORM SỬA TÀI KHOẢN ---
    // public function editAccount($id)
    // {
    //     if ($redirect = $this->ensureAdminOrStaff()) return $redirect;

    //     $user = User::findOrFail($id);
    //     return view('admin.accounts.edit', compact('user'));
    // }

    // // --- 5. XỬ LÝ CẬP NHẬT TÀI KHOẢN ---
    // public function updateAccount(Request $request, $id)
    // {
    //     if ($redirect = $this->ensureAdminOrStaff()) return $redirect;

    //     $user = User::findOrFail($id);

    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email|unique:users,email,' . $user->id, // Cho phép trùng email của chính mình
    //         'vai_tro' => 'required|in:ADMIN,NHAN_VIEN,KHACH_HANG',
    //         'password' => 'nullable|min:6', // Mật khẩu không bắt buộc khi sửa
    //     ], [
    //         'email.unique' => 'Email này đã được sử dụng bởi tài khoản khác.',
    //     ]);

    //     try {
    //         $user->name = $request->name;
    //         $user->email = $request->email;
    //         $user->vai_tro = $request->vai_tro;
            
    //         // Nếu có nhập mật khẩu mới thì mới cập nhật
    //         if ($request->filled('password')) {
    //             $user->password = Hash::make($request->password);
    //         }

    //         // Cập nhật SĐT nếu có
    //         if ($request->has('so_dien_thoai')) {
    //             $user->so_dien_thoai = $request->so_dien_thoai;
    //         }

    //         $user->save();

    //         return redirect()->route('admin.accounts')->with('success', 'Cập nhật tài khoản thành công!');
    //     } catch (\Exception $e) {
    //         return back()->with('error', 'Lỗi: ' . $e->getMessage());
    //     }
    // }

    // // --- 6. XÓA TÀI KHOẢN ---
    // public function deleteAccount($id)
    // {
    //     if ($redirect = $this->ensureAdminOrStaff()) return $redirect;

    //     try {
    //         // Không cho phép tự xóa chính mình
    //         if (Auth::id() == $id) {
    //             return back()->with('error', 'Bạn không thể xóa tài khoản của chính mình đang đăng nhập!');
    //         }

    //         $user = User::findOrFail($id);
    //         $user->delete();

    //         return redirect()->route('admin.accounts')->with('success', 'Đã xóa tài khoản!');
    //     } catch (\Exception $e) {
    //         return back()->with('error', 'Xóa thất bại: ' . $e->getMessage());
    //     }
    // }

    // 1. HIỂN THỊ & TÌM KIẾM
    public function accounts(Request $request)
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;

        $query = User::query();

        // Tìm kiếm theo ho_ten, email, sdt
        if ($request->has('keyword') && $request->keyword != '') {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('ho_ten', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%")
                  ->orWhere('sdt', 'like', "%{$keyword}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10);
        $users->appends($request->all());

        return view('admin.accounts', compact('users'));
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


    
    // public function categories()
    // {
    //     if ($redirect = $this->ensureAdminOrStaff()) {
    //         return $redirect;
    //     }

    //     return view('admin.categories');
    // }

    // =========================================================================
    // QUẢN LÝ DANH MỤC (CATEGORIES)
    // =========================================================================
    public function categories()
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;
        $categories = DanhMuc::with('danhMucCha')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.categories.index', compact('categories'));
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
    // =========================================================================
    // QUẢN LÝ THƯƠNG HIỆU (BRANDS)
    // =========================================================================
    public function brands()
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;
        $brands = ThuongHieu::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.brands.index', compact('brands'));
    }

    public function createBrand()
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;
        return view('admin.brands.create');
    }

    public function storeBrand(Request $request)
    {
        if ($redirect = $this->ensureAdminOrStaff()) return $redirect;
        $request->validate(['ten' => 'required|max:191|unique:thuong_hieu,ten']);

        try {
            ThuongHieu::create([
                'ten' => $request->ten,
                'slug' => Str::slug($request->ten),
            ]);
            return redirect()->route('admin.brands')->with('success', 'Thêm thương hiệu thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
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
        $request->validate(['ten' => 'required|max:191|unique:thuong_hieu,ten,' . $id]);

        try {
            $brand->update([
                'ten' => $request->ten,
                'slug' => Str::slug($request->ten),
            ]);
            return redirect()->route('admin.brands')->with('success', 'Cập nhật thương hiệu thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
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
    
}
