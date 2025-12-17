<?php

namespace App\Http\Controllers;

use App\Exports\StaffReportExport;
use App\Models\BaiViet;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\SanPham;
use App\Models\DonHang;
use App\Models\BienTheSanPham;
use App\Models\ThuongHieu;
use App\Models\DanhGia; // Cần cho tính rating trung bình (nếu dùng)
use App\Models\SanPhamAnh;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str; 

use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use app\Exports\StaffRevenueExport;
use App\Models\DanhMuc;
use App\Models\DonHangChiTiet;

class StaffController extends Controller
{
    /**
     * Hàm kiểm tra quyền Nhân viên
     */
    protected function ensureStaff()
    {
        $user = Auth::user();

        if (!$user || $user->vai_tro !== 'NHAN_VIEN') {
            return redirect()->route('home')
                ->with('error', 'Bạn không có quyền truy cập trang nhân viên.');
        }

        return null;
    }

    // DASHBOARD NHÂN VIÊN
    public function index(Request $request)
    {
        if ($redirect = $this->ensureStaff()) { return $redirect; }
        // ... (Logic Dashboard giữ nguyên)
        $currentDate = Carbon::now();
        $queryStart = null;
        $queryEnd = null;
        $selectedQuick = $request->input('quick_select', 'this_month'); 
        switch ($selectedQuick) {
            case 'today': $queryStart = $currentDate->copy()->startOfDay(); $queryEnd = $currentDate->copy()->endOfDay(); break;
            case '7_days': $queryStart = $currentDate->copy()->subDays(6)->startOfDay(); $queryEnd = $currentDate->copy()->endOfDay(); break;
            case '30_days': $queryStart = $currentDate->copy()->subDays(29)->startOfDay(); $queryEnd = $currentDate->copy()->endOfDay(); break;
            case 'this_month': $queryStart = $currentDate->copy()->startOfMonth(); $queryEnd = $currentDate->copy()->endOfDay(); break;
            case 'this_year': $queryStart = $currentDate->copy()->startOfYear(); $queryEnd = $currentDate->copy()->endOfDay(); break;
            case 'custom':
                if ($request->has('start_date') && $request->has('end_date') && $request->start_date && $request->end_date) {
                    $queryStart = Carbon::parse($request->start_date)->startOfDay();
                    $queryEnd = Carbon::parse($request->end_date)->endOfDay();
                } else { $queryStart = $currentDate->copy()->startOfMonth(); $queryEnd = $currentDate->copy()->endOfDay(); $selectedQuick = 'this_month'; } break;
            default: $queryStart = $currentDate->copy()->startOfMonth(); $queryEnd = $currentDate->copy()->endOfDay(); $selectedQuick = 'this_month'; break;
        }
        if (is_null($queryStart) || is_null($queryEnd)) { $queryStart = $currentDate->copy()->startOfMonth(); $queryEnd = $currentDate->copy()->endOfDay(); }
        $tongDonHang = DonHang::whereBetween('ngay_dat', [$queryStart, $queryEnd])->count();
        $donMoi = DonHang::whereBetween('ngay_dat', [$queryStart, $queryEnd])->where('trang_thai', 'DANG_XU_LY')->count();
        $donDangXuLy = DonHang::whereBetween('ngay_dat', [$queryStart, $queryEnd])->where('trang_thai', 'DANG_GIAO')->count();
        $donHoanThanh = DonHang::whereBetween('ngay_dat', [$queryStart, $queryEnd])->where('trang_thai', 'HOAN_THANH')->count();
        $donDaHuy = DonHang::whereBetween('ngay_dat', [$queryStart, $queryEnd])->where('trang_thai', 'HUY')->count();
        $donHangGanDay = DonHang::with('nguoiDung')->whereBetween('ngay_dat', [$queryStart, $queryEnd])->orderBy('ngay_dat', 'desc')->limit(5)->get();
        $sanPhamSapHet = BienTheSanPham::where('ton_kho', '<', 10)->where('ton_kho', '>', 0)->where('dang_ban', 1)->with('sanPham')->limit(5)->get()->map(function($bienThe) {
            return (object) ['ten' => ($bienThe->sanPham->ten ?? 'SP đã xóa') . ($bienThe->mau_sac ? ' - ' . $bienThe->mau_sac : '') . ($bienThe->dung_luong_gb ? ' (' . $bienThe->dung_luong_gb . 'GB)' : ''), 'so_luong' => $bienThe->ton_kho, 'sku' => $bienThe->sku, ];
        });

        return view('staff.dashboard', compact(
            'tongDonHang', 'donMoi', 'donDangXuLy', 'donHoanThanh', 'donDaHuy', 'donHangGanDay', 'sanPhamSapHet', 'selectedQuick'
        ));
    }

    // QUẢN LÝ ĐƠN HÀNG
    public function orders(Request $request)
    {
        if ($redirect = $this->ensureStaff()) return $redirect;
        $query = DonHang::with('nguoiDung')->whereNull('deleted_at');
        $trangThai = $request->input('trang_thai');
        if ($trangThai) { $query->where('trang_thai', $trangThai); }
        $keyword = $request->input('keyword');
        if ($keyword) { $query->where(function ($q) use ($keyword) { $q->where('ma', 'like', "%{$keyword}%")->orWhere('ten_nguoi_nhan', 'like', "%{$keyword}%"); }); }
        $sortBy = $request->input('sort_by', 'ngay_dat'); $sortOrder = $request->input('sort_order', 'desc');
        if (!in_array($sortBy, ['ma', 'thanh_tien', 'trang_thai', 'ngay_dat'])) { $sortBy = 'ngay_dat'; }
        $orders = $query->orderBy($sortBy, $sortOrder)->paginate(10);
        $orders->appends($request->all());
        return view('staff.orders', compact('orders', 'trangThai', 'sortBy', 'sortOrder', 'keyword'));
    }

    public function editOrder($id)
    {
        if ($redirect = $this->ensureStaff()) return $redirect;
        $order = DonHang::with(['chiTiet.bienThe.sanPham', 'nguoiDung'])->whereNull('deleted_at')->findOrFail($id);
        return view('staff.orders.edit', compact('order')); 
    }

    public function updateOrder(Request $request, $id)
    {
        if ($redirect = $this->ensureStaff()) return $redirect;
        $order = DonHang::whereNull('deleted_at')->findOrFail($id);
        $request->validate(['trang_thai' => 'required|in:DANG_XU_LY,DANG_GIAO,HOAN_THANH,HUY', 'trang_thai_tt' => 'required|in:CHUA_TT,DA_TT', 'ten_nguoi_nhan' => 'required|string|max:191', 'sdt_nguoi_nhan' => 'required|string|max:32', 'dia_chi_giao' => 'required|string', 'ghi_chu' => 'nullable|string|max:500', ]);
        DB::beginTransaction();
        try {
            $oldStatus = $order->trang_thai; $order->trang_thai = $request->trang_thai; $order->trang_thai_tt = $request->trang_thai_tt; $order->ten_nguoi_nhan = $request->ten_nguoi_nhan; $order->sdt_nguoi_nhan = $request->sdt_nguoi_nhan; $order->dia_chi_giao = $request->dia_chi_giao; $order->ghi_chu = $request->ghi_chu; $order->updated_at = now();
            if ($oldStatus != 'HUY' && $request->trang_thai == 'HUY') {
                foreach ($order->chiTiet as $item) { if ($item->bienThe) { $item->bienThe->ton_kho = $item->bienThe->ton_kho + $item->so_luong; $item->bienThe->save(); } }
            }
            $order->save(); DB::commit();
            return redirect()->route('staff.orders')->with('success', 'Cập nhật đơn hàng #' . $order->ma . ' thành công!');
        } catch (\Exception $e) { DB::rollBack(); return back()->with('error', 'Lỗi cập nhật: ' . $e->getMessage()); }
    }

    public function deleteOrder($id)
    {
        if ($redirect = $this->ensureStaff()) return $redirect;
        DB::beginTransaction();
        try {
            $order = DonHang::whereNull('deleted_at')->findOrFail($id); $orderCode = $order->ma;
            if ($order->trang_thai != 'HUY') {
                $order->trang_thai = 'HUY';
                foreach ($order->chiTiet as $item) { if ($item->bienThe) { $item->bienThe->ton_kho = $item->bienThe->ton_kho + $item->so_luong; $item->bienThe->save(); } }
                $order->save();
            }
            $order->delete(); 
            DB::commit();
            return redirect()->route('staff.orders')->with('success', 'Đã xóa (soft delete) đơn hàng #' . $orderCode);
        } catch (\Exception $e) { DB::rollBack(); return back()->with('error', 'Lỗi xóa đơn hàng: ' . $e->getMessage()); }
    }
    
    // QUẢN LÝ SẢN PHẨM (STAFF)
    public function products(Request $request)
    {
        if ($redirect = $this->ensureStaff()) { return $redirect; }
        $query = SanPham::with(['thuongHieu', 'bienTheSanPham'])->whereNull('deleted_at')->select('san_pham.*'); 
        $keyword = $request->input('keyword');
        if ($keyword) { $query->where(function ($q) use ($keyword) { $q->where('ten', 'like', "%{$keyword}%")->orWhereHas('bienTheSanPham', function ($q2) use ($keyword) { $q2->where('sku', 'like', "%{$keyword}%"); }); }); }
        $brandId = $request->input('thuong_hieu_id');
        if ($brandId) { $query->where('thuong_hieu_id', $brandId); }
        $sortBy = $request->input('sort_by', 'created_at'); $sortOrder = $request->input('sort_order', 'desc');
        if ($sortBy == 'gia') { $query->join('bien_the_san_pham', 'san_pham.id', '=', 'bien_the_san_pham.san_pham_id')->whereNull('bien_the_san_pham.deleted_at')->groupBy('san_pham.id')->orderBy(DB::raw('MIN(bien_the_san_pham.gia)'), $sortOrder); } else { $query->orderBy($sortBy, $sortOrder); }
        $products = $query->with(['bienTheSanPham' => function($q) { $q->orderBy('gia', 'asc'); }])->paginate(10); 
        $products->getCollection()->transform(function ($p) {
            $p->min_price = $p->bienTheSanPham->min('gia'); $p->total_stock = $p->bienTheSanPham->sum('ton_kho'); $p->first_variant_sku = $p->bienTheSanPham->first()->sku ?? 'N/A'; return $p;
        });
        $thuongHieu = ThuongHieu::all();
        return view('staff.products', compact('products', 'thuongHieu', 'keyword', 'brandId', 'sortBy', 'sortOrder'));
    }
    
    public function editProduct($id)
    {
        if ($redirect = $this->ensureStaff()) return $redirect;
        
        $product = SanPham::with(['bienTheSanPham' => function($q) { $q->whereNull('deleted_at'); }, 'sanPhamAnh'])->whereNull('deleted_at')->findOrFail($id);
        $thuongHieu = ThuongHieu::all(); 
            
        return view('staff.products.edit', compact('product', 'thuongHieu')); 
    }

    // public function updateProduct(Request $request, $id)
    // {
    //     if ($redirect = $this->ensureStaff()) return $redirect;
    //     $product = SanPham::whereNull('deleted_at')->findOrFail($id);
    //     $request->validate(['ten' => 'required|max:191', 'mo_ta_ngan' => 'nullable|string', 'mo_ta_day_du' => 'nullable|string', 'hien_thi' => 'required|boolean', 'hinh_anh_mac_dinh' => 'nullable|image|max:2048', 'new_images.*' => 'nullable|image|max:2048', 'variants' => 'array', 'variants.*.id' => 'nullable|exists:bien_the_san_pham,id', 'variants.*.sku' => 'required|max:64', 'variants.*.gia' => 'required|numeric|min:0', 'variants.*.ton_kho' => 'required|integer|min:0', 'new_variants' => 'array', 'new_variants.*.sku' => 'required|unique:bien_the_san_pham,sku|max:64', ]);
        
    //     DB::beginTransaction();
    //     try {
    //         $product->ten = $request->ten; $product->slug = Str::slug($request->ten) . '-' . $product->id; $product->mo_ta_ngan = $request->mo_ta_ngan; $product->mo_ta_day_du = $request->mo_ta_day_du; $product->hien_thi = $request->hien_thi;
    //         if ($request->hasFile('hinh_anh_mac_dinh')) {
    //             $file = $request->file('hinh_anh_mac_dinh'); $filename = time() . '_' . $file->getClientOriginalName(); $file->move(public_path('uploads'), $filename); $product->hinh_anh_mac_dinh = $filename;
    //         }
    //         $product->save();

    //         // 3. Xử lý Biến thể (Cập nhật/Xóa/Thêm mới)
    //         $submittedVariantIds = collect($request->variants)->pluck('id')->filter()->toArray();
    //         BienTheSanPham::where('san_pham_id', $product->id)->whereNotIn('id', $submittedVariantIds)->delete(); 

    //         if ($request->has('variants')) {
    //             foreach ($request->variants as $variantData) {
    //                 if (isset($variantData['id'])) {
    //                      $variant = BienTheSanPham::findOrFail($variantData['id']);
    //                      $variant->update([ 'sku' => $variantData['sku'], 'gia' => $variantData['gia'], 'gia_so_sanh' => $variantData['gia_so_sanh'] ?? null, 'ton_kho' => $variantData['ton_kho'], 'mau_sac' => $variantData['mau_sac'] ?? null, 'dung_luong_gb' => $variantData['dung_luong_gb'] ?? null, ]);
    //                 }
    //             }
    //         }

    //         if ($request->has('new_variants')) {
    //              foreach ($request->new_variants as $newVariantData) {
    //                 BienTheSanPham::create([ 'san_pham_id' => $product->id, 'sku' => $newVariantData['sku'], 'gia' => $newVariantData['gia'], 'ton_kho' => $newVariantData['ton_kho'], 'mau_sac' => $newVariantData['mau_sac'] ?? null, 'dung_luong_gb' => $newVariantData['dung_luong_gb'] ?? null, 'dang_ban' => 1 ]);
    //              }
    //         }
            
    //         // 4. Xử lý Hình ảnh phụ (Thêm mới và Xóa)
    //         if ($request->has('delete_images')) { SanPhamAnh::whereIn('id', $request->delete_images)->delete(); }
    //         if ($request->hasFile('new_images')) {
    //             foreach ($request->file('new_images') as $file) {
    //                 $filename = time() . '_' . $file->getClientOriginalName(); $file->move(public_path('uploads'), $filename); SanPhamAnh::create(['san_pham_id' => $product->id, 'url' => $filename]);
    //             }
    //         }

    //         DB::commit();
    //         return redirect()->route('staff.products')->with('success', 'Cập nhật sản phẩm: ' . $product->ten . ' thành công!');

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return back()->with('error', 'Lỗi cập nhật sản phẩm: ' . $e->getMessage())->withInput();
    //     }
    // }
//     public function updateProduct(Request $request, $id)
// {
//     if ($redirect = $this->ensureStaff()) return $redirect;
    
//     $product = SanPham::whereNull('deleted_at')->findOrFail($id);

//     $request->validate([
//         'ten' => 'required|max:255',
//         'mo_ta_ngan' => 'nullable|string', // << CORRECTED NAME
//         'mo_ta_day_du' => 'nullable|string', // << CORRECTED NAME
//         'hien_thi' => 'required|boolean',
//         'hinh_anh_mac_dinh' => 'nullable|image|max:2048', 
//         'new_images.*' => 'nullable|image|max:2048',       
        
//         'variants' => 'array',
//         'variants.*.id' => 'nullable|exists:bien_the_san_pham,id',
//         'variants.*.sku' => 'required|max:64',
//         'variants.*.gia' => 'required|numeric|min:0',
//         'variants.*.ton_kho' => 'required|integer|min:0',
        
//         'new_variants' => 'array',
//         'new_variants.*.sku' => 'required|unique:bien_the_san_pham,sku|max:64',
        
//         'la_flash_sale' => 'nullable|boolean',
//         'la_noi_bat' => 'nullable|boolean',
//     ]);
    
//     DB::beginTransaction();
//     try {
        
//         // 1. Cập nhật thông tin chung của sản phẩm
//         $product->ten = $request->ten;
//         $product->slug = Str::slug($request->ten) . '-' . $product->id; 
        
//         // FIX LỖI: Sửa lỗi cột mo_ta -> mo_ta_ngan và mo_ta_chi_tiet -> mo_ta_day_du
//         $product->mo_ta_ngan = $request->mo_ta_ngan; 
//         $product->mo_ta_day_du = $request->mo_ta_day_du; 
        
//         $product->hien_thi = $request->hien_thi;
        
//         // LƯU CÁC CỜ MỚI
//         $product->la_flash_sale = $request->has('la_flash_sale') ? 1 : 0;
//         $product->la_noi_bat = $request->has('la_noi_bat') ? 1 : 0;
        
//         if ($request->hasFile('hinh_anh_mac_dinh')) {
//             $file = $request->file('hinh_anh_mac_dinh');
//             $filename = time() . '_' . $file->getClientOriginalName();
//             $file->move(public_path('uploads'), $filename);
//             $product->hinh_anh_mac_dinh = $filename;
//         }
//         $product->save();

//         // 2. Xử lý Biến thể (Cập nhật/Xóa/Thêm mới)
//         $submittedVariantIds = collect($request->variants)->pluck('id')->filter()->toArray();
        
//         BienTheSanPham::where('san_pham_id', $product->id)
//             ->whereNotIn('id', $submittedVariantIds)
//             ->delete(); 

//         if ($request->has('variants')) {
//             foreach ($request->variants as $variantData) {
//                 if (isset($variantData['id'])) {
//                      $variant = BienTheSanPham::findOrFail($variantData['id']);
//                      $variant->update([
//                         'sku' => $variantData['sku'],
//                         'gia' => $variantData['gia'],
//                         'gia_so_sanh' => $variantData['gia_so_sanh'] ?? null,
//                         'ton_kho' => $variantData['ton_kho'],
//                         'mau_sac' => $variantData['mau_sac'] ?? null,
//                         'dung_luong_gb' => $variantData['dung_luong_gb'] ?? null,
//                      ]);
//                 }
//             }
//         }

//         if ($request->has('new_variants')) {
//              foreach ($request->new_variants as $newVariantData) {
//                 BienTheSanPham::create([ 
//                     'san_pham_id' => $product->id, 'sku' => $newVariantData['sku'], 'gia' => $newVariantData['gia'], 'ton_kho' => $newVariantData['ton_kho'], 
//                     'mau_sac' => $newVariantData['mau_sac'] ?? null, 'dung_luong_gb' => $newVariantData['dung_luong_gb'] ?? null, 'dang_ban' => 1 
//                 ]);
//              }
//         }
        
//         // 3. Xử lý Hình ảnh phụ
//         if ($request->has('delete_images')) { SanPhamAnh::whereIn('id', $request->delete_images)->delete(); }
//         if ($request->hasFile('new_images')) {
//             foreach ($request->file('new_images') as $file) {
//                 $filename = time() . '_' . $file->getClientOriginalName(); $file->move(public_path('uploads'), $filename); SanPhamAnh::create(['san_pham_id' => $product->id, 'url' => $filename]);
//             }
//         }

//         DB::commit();

//         return redirect()->route('staff.products')->with('success', 'Cập nhật sản phẩm: ' . $product->ten . ' thành công!');

//     } catch (\Exception $e) {
//         DB::rollBack();
//         return back()->with('error', 'Lỗi cập nhật sản phẩm: ' . $e->getMessage())->withInput();
//     }
// }
// app/Http/Controllers/StaffController.php



public function updateProduct(Request $request, $id)
{
    if ($redirect = $this->ensureStaff()) return $redirect;

    $product = SanPham::whereNull('deleted_at')->findOrFail($id);

    $request->validate([
        'ten' => 'required|max:191',
        'thuong_hieu_id' => 'required|exists:thuong_hieu,id',
        'mo_ta_ngan' => 'nullable|string',
        'mo_ta_day_du' => 'nullable|string',
        'hien_thi' => 'required|boolean',
        'la_flash_sale' => 'nullable|in:0,1',
        'la_noi_bat' => 'nullable|in:0,1',
        
        'hinh_anh_mac_dinh' => 'nullable|image|max:2048', 
        'new_images.*' => 'nullable|image|max:2048',       
        
        // Validation cho Biến thể hiện có (variants)
        'variants' => 'nullable|array',
        'variants.*.id' => 'required|exists:bien_the_san_pham,id',
        'variants.*.sku' => 'required|max:64',
        'variants.*.gia' => 'required|numeric|min:0',
        'variants.*.ton_kho' => 'required|integer|min:0',
        'variants.*.mau_sac' => 'nullable|string|max:64',
        'variants.*.dung_luong_gb' => 'nullable|integer', 
        
        // Validation cho Biến thể mới (new_variants)
        'new_variants' => 'nullable|array',
        'new_variants.*.sku' => 'required|unique:bien_the_san_pham,sku|max:64',
        'new_variants.*.gia' => 'required|numeric|min:0',
        'new_variants.*.ton_kho' => 'required|integer|min:0',
        'new_variants.*.mau_sac' => 'nullable|string|max:64',
        'new_variants.*.dung_luong_gb' => 'nullable|integer', 

        // Validation cho xóa ảnh phụ
        'delete_images' => 'nullable|array',
        'delete_images.*' => 'exists:san_pham_anh,id',
    ]);

    DB::beginTransaction();
    try {
        
        // 1. Cập nhật thông tin chung của sản phẩm
        $product->ten = $request->ten;
        $product->slug = Str::slug($request->ten) . '-' . $product->id; 
        $product->mo_ta_ngan = $request->mo_ta_ngan;
        $product->mo_ta_day_du = $request->mo_ta_day_du;
        $product->hien_thi = $request->hien_thi;
        
        // Cập nhật cờ đặc biệt
        $product->la_flash_sale = $request->has('la_flash_sale') ? 1 : 0;
        $product->la_noi_bat = $request->has('la_noi_bat') ? 1 : 0;
        
        // Upload ảnh chính
        if ($request->hasFile('hinh_anh_mac_dinh')) {
            $file = $request->file('hinh_anh_mac_dinh');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $filename);
            $product->hinh_anh_mac_dinh = $filename;
        }
        $product->save();

        // 2. Xử lý Biến thể (Cập nhật/Xóa/Thêm mới)
        $submittedVariantIds = collect($request->variants)->pluck('id')->filter()->toArray();
        
        // A. Xóa (Soft delete) các biến thể KHÔNG còn trong form
        BienTheSanPham::where('san_pham_id', $product->id)
            ->whereNotIn('id', $submittedVariantIds)
            ->delete(); 

        // B. Cập nhật các biến thể hiện có
        if ($request->has('variants')) {
            foreach ($request->variants as $variantData) {
                if (isset($variantData['id'])) {
                     $variant = BienTheSanPham::findOrFail($variantData['id']);
                     $variant->update([
                        'sku' => $variantData['sku'],
                        'gia' => $variantData['gia'],
                        'gia_so_sanh' => $variantData['gia_so_sanh'] ?? null,
                        'ton_kho' => $variantData['ton_kho'],
                        'mau_sac' => $variantData['mau_sac'] ?? null,
                        'dung_luong_gb' => $variantData['dung_luong_gb'] ?? null,
                     ]);
                }
            }
        }

        // C. Thêm biến thể mới
        if ($request->has('new_variants')) {
             foreach ($request->new_variants as $newVariantData) {
                BienTheSanPham::create([ 
                    'san_pham_id' => $product->id, 
                    'sku' => $newVariantData['sku'], 
                    'gia' => $newVariantData['gia'], 
                    'ton_kho' => $newVariantData['ton_kho'], 
                    'mau_sac' => $newVariantData['mau_sac'] ?? null, 
                    'dung_luong_gb' => $newVariantData['dung_luong_gb'] ?? null, 
                    'dang_ban' => 1 
                ]);
             }
        }
        
        // 3. Xử lý Hình ảnh phụ (Thêm mới và Xóa)
        
        // Xóa ảnh phụ cũ
        if ($request->has('delete_images')) { 
            \App\Models\SanPhamAnh::whereIn('id', $request->delete_images)->delete(); 
        }
        
        // Thêm ảnh phụ mới
        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName(); 
                $file->move(public_path('uploads'), $filename); 
                \App\Models\SanPhamAnh::create(['san_pham_id' => $product->id, 'url' => $filename]);
            }
        }

        DB::commit();

        return redirect()->route('staff.products')->with('success', 'Cập nhật sản phẩm: ' . $product->ten . ' thành công!');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Lỗi cập nhật sản phẩm: ' . $e->getMessage())->withInput();
    }
}
    
    // BÁO CÁO - THỐNG KÊ (STAFF)
    // public function reports(Request $request)
    // {
    //     if ($redirect = $this->ensureStaff()) {
    //         return $redirect;
    //     }
        
    //     $currentDate = Carbon::now();
    //     $queryStart = null;
    //     $queryEnd = null;
    //     $selectedQuick = $request->input('quick_select', 'this_month');
    //     $queryStartFormatted = $request->input('start_date');
    //     $queryEndFormatted = $request->input('end_date');

    //     // --- 1. Xử lý Lọc theo Ngày/Tháng/Năm ---
    //     switch ($selectedQuick) {
    //         case 'today': $queryStart = $currentDate->copy()->startOfDay(); $queryEnd = $currentDate->copy()->endOfDay(); break;
    //         case '7_days': $queryStart = $currentDate->copy()->subDays(6)->startOfDay(); $queryEnd = $currentDate->copy()->endOfDay(); break;
    //         case '30_days': $queryStart = $currentDate->copy()->subDays(29)->startOfDay(); $queryEnd = $currentDate->copy()->endOfDay(); break;
    //         case 'this_month': $queryStart = $currentDate->copy()->startOfMonth(); $queryEnd = $currentDate->copy()->endOfDay(); break;
    //         case 'this_year': $queryStart = $currentDate->copy()->startOfYear(); $queryEnd = $currentDate->copy()->endOfDay(); break;
    //         case 'custom':
    //             if ($request->filled('start_date') && $request->filled('end_date')) {
    //                 $queryStart = Carbon::parse($request->start_date)->startOfDay();
    //                 $queryEnd = Carbon::parse($request->end_date)->endOfDay();
    //                 $queryStartFormatted = $request->start_date;
    //                 $queryEndFormatted = $request->end_date;
    //             }
    //             break;
    //     }

    //     if (is_null($queryStart) || is_null($queryEnd)) {
    //         $queryStart = $currentDate->copy()->startOfMonth();
    //         $queryEnd = $currentDate->copy()->endOfDay();
    //         $selectedQuick = 'this_month';
    //     }
        
    //     $queryStartFormatted = $queryStart->format('Y-m-d');
    //     $queryEndFormatted = $queryEnd->format('Y-m-d');


    //     // --- 2. Tính toán thống kê ---
    //     $tongDoanhThu = DonHang::where('trang_thai', 'HOAN_THANH')->whereBetween('ngay_dat', [$queryStart, $queryEnd])->sum('thanh_tien');
    //     $tongDonHang = DonHang::whereBetween('ngay_dat', [$queryStart, $queryEnd])->count();
    //     $khachHangMoi = User::where('vai_tro', 'KHACH_HANG')->whereBetween('created_at', [$queryStart, $queryEnd])->count();
    //     $donDangXuLy = DonHang::where('trang_thai', 'DANG_XU_LY')->whereBetween('ngay_dat', [$queryStart, $queryEnd])->count();

    //     $topSellingProducts = \App\Models\DonHangChiTiet::select('san_pham_id', DB::raw('SUM(so_luong) as tong_so_luong_ban'), DB::raw('SUM(don_hang_chi_tiet.thanh_tien) as tong_doanh_thu'))
    //         ->join('don_hang', 'don_hang_chi_tiet.don_hang_id', '=', 'don_hang.id')
    //         ->where('don_hang.trang_thai', 'HOAN_THANH')->whereBetween('don_hang.ngay_dat', [$queryStart, $queryEnd])
    //         ->groupBy('san_pham_id')->orderBy('tong_so_luong_ban', 'desc')->with('sanPham')->limit(5)->get()->map(function($item) {
    //             $item->ten = $item->sanPham->ten ?? 'Sản phẩm đã xóa';
    //             return $item;
    //         });

    //     $recentOrders = DonHang::with('nguoiDung')->whereBetween('ngay_dat', [$queryStart, $queryEnd])->orderBy('ngay_dat', 'desc')->limit(5)->get();
        
    //     return view('staff.reports', compact(
    //         'tongDoanhThu', 'tongDonHang', 'khachHangMoi', 'donDangXuLy', 'topSellingProducts', 'recentOrders',
    //         'selectedQuick', 'queryStartFormatted', 'queryEndFormatted'
    //     ));
    // }
    
//    public function reports(Request $request)
//     {
//         // Giữ lại logic kiểm tra Staff (nếu bạn có)
//         // if ($redirect = $this->ensureStaff()) {
//         //     return $redirect;
//         // }
        
//         $currentDate = Carbon::now();
//         $queryStart = null;
//         $queryEnd = null;
//         $selectedQuick = $request->input('quick_select', 'this_month');
//         $queryStartFormatted = $request->input('start_date');
//         $queryEndFormatted = $request->input('end_date');

//         // --- 1. Xử lý Lọc theo Ngày/Tháng/Năm ---
//         switch ($selectedQuick) {
//             case 'today': $queryStart = $currentDate->copy()->startOfDay(); $queryEnd = $currentDate->copy()->endOfDay(); break;
//             case '7_days': $queryStart = $currentDate->copy()->subDays(6)->startOfDay(); $queryEnd = $currentDate->copy()->endOfDay(); break;
//             case '30_days': $queryStart = $currentDate->copy()->subDays(29)->startOfDay(); $queryEnd = $currentDate->copy()->endOfDay(); break;
//             case 'this_month': $queryStart = $currentDate->copy()->startOfMonth(); $queryEnd = $currentDate->copy()->endOfDay(); break;
//             case 'this_year': $queryStart = $currentDate->copy()->startOfYear(); $queryEnd = $currentDate->copy()->endOfDay(); break;
//             case 'custom':
//                 if ($request->filled('start_date') && $request->filled('end_date')) {
//                     $queryStart = Carbon::parse($request->start_date)->startOfDay();
//                     $queryEnd = Carbon::parse($request->end_date)->endOfDay();
//                     $queryStartFormatted = $request->start_date;
//                     $queryEndFormatted = $request->end_date;
//                 }
//                 break;
//         }

//         if (is_null($queryStart) || is_null($queryEnd)) {
//             $queryStart = $currentDate->copy()->startOfMonth();
//             $queryEnd = $currentDate->copy()->endOfDay();
//             $selectedQuick = 'this_month';
//         }
        
//         $queryStartFormatted = $queryStart->format('Y-m-d');
//         $queryEndFormatted = $queryEnd->format('Y-m-d');


//         // --- 2. Tính toán thống kê Tổng quát ---
//         $tongDonHangHoanThanh = \App\Models\DonHang::where('trang_thai', 'HOAN_THANH')
//                                       ->whereBetween('ngay_dat', [$queryStart, $queryEnd])
//                                       ->count();
                                      
//         $tongDoanhThu = \App\Models\DonHang::where('trang_thai', 'HOAN_THANH')
//                               ->whereBetween('ngay_dat', [$queryStart, $queryEnd])
//                               ->sum('thanh_tien');
        
//         $tongDonHang = \App\Models\DonHang::whereBetween('ngay_dat', [$queryStart, $queryEnd])->count();
        
//         $khachHangMoi = \App\Models\User::where('vai_tro', 'KHACH_HANG') // Dùng NguoiDung nếu User không phải là Model của bạn
//                             ->whereBetween('created_at', [$queryStart, $queryEnd])
//                             ->count();
        
//         $donDangXuLy = \App\Models\DonHang::where('trang_thai', 'DANG_XU_LY')
//                             ->whereBetween('ngay_dat', [$queryStart, $queryEnd])
//                             ->count();

//         // --- 3. Thống kê Chuyên sâu ---
//         $completedOrderIds = \App\Models\DonHang::where('trang_thai', 'HOAN_THANH')
//                                     ->whereBetween('ngay_dat', [$queryStart, $queryEnd])
//                                     ->pluck('id');

//         // BÁN CHẠY & BÁN CHẬM (Lấy Top 5 và Bottom 5)
//         $productSales = \App\Models\DonHangChiTiet::select(
//             'san_pham_id', 
//             DB::raw('SUM(so_luong) as tong_so_luong_ban'), 
//             DB::raw('SUM(don_hang_chi_tiet.thanh_tien) as tong_doanh_thu')
//         )
//             ->whereIn('don_hang_id', $completedOrderIds)
//             ->groupBy('san_pham_id')
//             ->orderBy('tong_so_luong_ban', 'desc')
//             ->with(['sanPham' => function($q) { $q->withTrashed(); }]) // Giả định quan hệ sanPham() tồn tại
//             ->get()
//             ->map(function($item) {
//                 $item->ten = $item->sanPham->ten ?? 'Sản phẩm đã xóa/Không rõ';
//                 return $item;
//             });

//         $topSellingProducts = $productSales->take(5);
//         $bottomSellingProducts = $productSales->count() > 5 ? $productSales->sortBy('tong_so_luong_ban')->take(5) : collect();

//         // DOANH THU THEO DÒNG MÁY (DANH MỤC) - FIX: SỬ DỤNG BẢNG TRUNG GIAN san_pham_danh_muc
//         $revenueByCategory = \App\Models\DonHangChiTiet::select(
//             'danh_muc.ten AS ten_danh_muc', 
//             DB::raw('SUM(don_hang_chi_tiet.thanh_tien) as tong_doanh_thu')
//         )
//             ->join('san_pham_danh_muc', 'don_hang_chi_tiet.san_pham_id', '=', 'san_pham_danh_muc.san_pham_id')
//             ->join('danh_muc', 'san_pham_danh_muc.danh_muc_id', '=', 'danh_muc.id')
//             ->whereIn('don_hang_id', $completedOrderIds)
//             // Phải Group by danh_muc.ten (chỉ lấy 1 danh mục/sản phẩm nếu có nhiều)
//             ->groupBy('danh_muc.ten') 
//             ->orderBy('tong_doanh_thu', 'desc')
//             ->get();
            
//         // SẢN PHẨM TỒN KHO NHIỀU NHẤT (Biến thể)
//         $topStockProducts = \App\Models\BienTheSanPham::select(
//             'bien_the_san_pham.id', 
//             'bien_the_san_pham.sku', 
//             'bien_the_san_pham.ton_kho', 
//             'san_pham.ten AS ten_san_pham',
//             'bien_the_san_pham.mau_sac',
//             'bien_the_san_pham.dung_luong_gb'
//         )
//             ->join('san_pham', 'bien_the_san_pham.san_pham_id', '=', 'san_pham.id')
//             ->orderBy('bien_the_san_pham.ton_kho', 'desc')
//             ->limit(5)
//             ->get();

//         // SỐ LƯỢNG BÁN THEO TỪNG MẪU MÁY (BIẾN THỂ)
//         $salesByVariant = \App\Models\DonHangChiTiet::select(
//             'don_hang_chi_tiet.bien_the_id', // Thay bien_the_san_pham_id bằng bien_the_id
//             DB::raw('SUM(don_hang_chi_tiet.so_luong) as tong_so_luong_ban'), 
//             'san_pham.ten AS ten_san_pham',
//             'bien_the_san_pham.sku',
//             'bien_the_san_pham.mau_sac',
//             'bien_the_san_pham.dung_luong_gb'
//         )
//             ->join('bien_the_san_pham', 'don_hang_chi_tiet.bien_the_id', '=', 'bien_the_san_pham.id') // Sửa join key
//             ->join('san_pham', 'bien_the_san_pham.san_pham_id', '=', 'san_pham.id')
//             ->whereIn('don_hang_id', $completedOrderIds)
//             ->groupBy('don_hang_chi_tiet.bien_the_id', 'san_pham.ten', 'bien_the_san_pham.sku', 'bien_the_san_pham.mau_sac', 'bien_the_san_pham.dung_luong_gb')
//             ->orderBy('tong_so_luong_ban', 'desc')
//             ->limit(10) // Lấy top 10 biến thể bán chạy nhất
//             ->get();


//         return view('staff.reports', compact(
//             'tongDoanhThu', 'tongDonHang', 'khachHangMoi', 'donDangXuLy', 'tongDonHangHoanThanh',
//             'topSellingProducts', 'bottomSellingProducts', 'revenueByCategory', 'topStockProducts', 'salesByVariant',
//             'selectedQuick', 'queryStartFormatted', 'queryEndFormatted'
//         ));
//     }
 // app/Http/Controllers/StaffController.php

// app/Http/Controllers/StaffController.php

public function reports(Request $request)
{
    // Kiểm tra quyền nhân viên (Thay thế ensureAdminOrStaff bị lỗi)
    if (!auth()->check() || (auth()->user()->vai_tro !== 'QUAN_TRI' && auth()->user()->vai_tro !== 'NHAN_VIEN')) {
        return redirect()->route('login');
    }

    $startDate = $request->start_date ?? now()->startOfMonth()->format('Y-m-d');
    $endDate = $request->end_date ?? now()->format('Y-m-d');
    $reportType = $request->report_type ?? 'doanh_thu'; // Loại báo cáo mặc định

    $data = collect();

    // Xử lý lấy dữ liệu dựa trên loại báo cáo được chọn
    switch ($reportType) {
        case 'doanh_thu':
            $data = \App\Models\DonHang::whereBetween('ngay_dat', [$startDate, $endDate])
                ->where('trang_thai', 'HOAN_THANH')->get();
            break;
        case 'ban_chay':
            $data = \App\Models\DonHangChiTiet::select('ten_sp_ghi_nhan', \DB::raw('SUM(so_luong) as total_qty'))
                ->join('don_hang', 'don_hang_chi_tiet.don_hang_id', '=', 'don_hang.id')
                ->whereBetween('don_hang.ngay_dat', [$startDate, $endDate])
                ->groupBy('ten_sp_ghi_nhan')->orderByDesc('total_qty')->take(5)->get();
            break;
        case 'ban_cham':
            $threeMonthsAgo = now()->subMonths(3);
            $data = \App\Models\SanPham::withSum(['chiTietDonHang as total_sold' => function($q) use ($threeMonthsAgo) {
                $q->join('don_hang', 'don_hang_chi_tiet.don_hang_id', '=', 'don_hang.id')
                  ->where('don_hang.ngay_dat', '>=', $threeMonthsAgo);
            }], 'so_luong')->having('total_sold', '<', 5)->orHavingNull('total_sold')->take(5)->get();
            break;
        case 'ton_kho':
            $data = \App\Models\BienTheSanPham::with('sanPham')->orderByDesc('ton_kho')->take(10)->get();
            break;
    }

    return view('staff.reports.index', compact('data', 'startDate', 'endDate', 'reportType'));
}
 public function exportReport(Request $request)
{
    $startDate = $request->start_date ?? now()->startOfMonth()->format('Y-m-d');
    $endDate = $request->end_date ?? now()->format('Y-m-d');
    $reportType = $request->report_type ?? 'doanh_thu';
    $format = $request->format ?? 'excel';

    // 1. Lấy dữ liệu Tổng quan (Dùng chung cho cả Excel và PDF)
    $doanhThu = \App\Models\DonHang::whereBetween('ngay_dat', [$startDate, $endDate])
                ->where('trang_thai', 'HOAN_THANH')->sum('thanh_tien');
    $tongDonHang = \App\Models\DonHang::whereBetween('ngay_dat', [$startDate, $endDate])->count();
    $donDangXuLy = \App\Models\DonHang::where('trang_thai', 'DANG_XU_LY')->count();
    $tongKhachHang = \App\Models\User::where('vai_tro', 'KHACH_HANG')->count();

    // 2. Lấy dữ liệu chi tiết và gán vào đúng biến để View PDF không bị lỗi Undefined
    $dataDetails = collect();
    $banChay = collect();
    $banCham = collect();
    $tonKhoNhieu = collect();

    if ($reportType == 'doanh_thu') {
        $dataDetails = \App\Models\DonHang::whereBetween('ngay_dat', [$startDate, $endDate])
            ->where('trang_thai', 'HOAN_THANH')->get();
    } elseif ($reportType == 'ban_chay') {
        $banChay = \App\Models\DonHangChiTiet::select('ten_sp_ghi_nhan', \DB::raw('SUM(so_luong) as total_qty'))
            ->join('don_hang', 'don_hang_chi_tiet.don_hang_id', '=', 'don_hang.id')
            ->whereBetween('don_hang.ngay_dat', [$startDate, $endDate])
            ->groupBy('ten_sp_ghi_nhan')->orderByDesc('total_qty')->take(5)->get();
        $dataDetails = $banChay;
    } elseif ($reportType == 'ban_cham') {
        $threeMonthsAgo = now()->subMonths(3);
        $banCham = \App\Models\SanPham::withSum(['chiTietDonHang as total_sold' => function($q) use ($threeMonthsAgo) {
            $q->join('don_hang', 'don_hang_chi_tiet.don_hang_id', '=', 'don_hang.id')
              ->where('don_hang.ngay_dat', '>=', $threeMonthsAgo);
        }], 'so_luong')->having('total_sold', '<', 5)->orHavingNull('total_sold')->take(5)->get();
        $dataDetails = $banCham;
    } elseif ($reportType == 'ton_kho') {
        $tonKhoNhieu = \App\Models\BienTheSanPham::with('sanPham')->orderByDesc('ton_kho')->take(10)->get();
        $dataDetails = $tonKhoNhieu;
    }

    // Gộp tất cả vào mảng truyền ra View
    $allData = [
        'startDate' => $startDate,
        'endDate' => $endDate,
        'reportType' => $reportType,
        'doanhThu' => $doanhThu,
        'tongDonHang' => $tongDonHang,
        'donDangXuLy' => $donDangXuLy,
        'tongKhachHang' => $tongKhachHang,
        'dataDetails' => $dataDetails, // Dùng cho Excel
        'banChay' => $banChay,         // Dùng cho PDF
        'banCham' => $banCham,         // Dùng cho PDF
        'tonKhoNhieu' => $tonKhoNhieu   // Dùng cho PDF
    ];

    // 3. Xuất file
    if ($format == 'excel') {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\StaffReportExport($allData), 
            "Bao_cao_nhan_vien_{$startDate}.xlsx"
        );
    }

    if ($format == 'pdf') {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('staff.reports.export_pdf', $allData)
        ->setPaper('a4', 'portrait')
        ->setOption('isHtml5ParserEnabled', true)
        ->setOption('isRemoteEnabled', true)
        ->setOption('defaultFont', 'DejaVu Sans'); // Ép buộc sử dụng font Unicode
    
    return $pdf->download("Bao_cao_nhan_vien_{$startDate}.pdf");
    }
}
    // =========================================================
    // QUẢN LÝ TIN CÔNG NGHỆ (MỚI)
    // =========================================================

    public function news(Request $request)
    {
        if ($redirect = $this->ensureStaff()) return $redirect;

        $keyword = $request->input('keyword');
        $query = BaiViet::with('nguoiDung')->whereNull('deleted_at');

        if ($keyword) {
            $query->where('tieu_de', 'like', "%{$keyword}%")->orWhere('noi_dung', 'like', "%{$keyword}%");
        }

        $news = $query->orderBy('created_at', 'desc')->paginate(10);
        $news->appends($request->all());

        return view('staff.news.index', compact('news', 'keyword'));
    }

    public function createNews()
    {
        if ($redirect = $this->ensureStaff()) return $redirect;
        return view('staff.news.create');
    }

    public function storeNews(Request $request)
    {
        if ($redirect = $this->ensureStaff()) return $redirect;
        
        $request->validate([
            'tieu_de' => 'required|max:255',
            'noi_dung' => 'required',
            'hinh_anh_chinh' => 'nullable|image|max:2048',
            'trang_thai' => 'required|in:NHAP,XUAT_BAN',
        ]);

        DB::beginTransaction();
        try {
            $baiViet = new BaiViet();
            $baiViet->tieu_de = $request->tieu_de;
            $baiViet->slug = Str::slug($request->tieu_de) . '-' . time();
            $baiViet->mo_ta_ngan = $request->mo_ta_ngan;
            $baiViet->noi_dung = $request->noi_dung;
            $baiViet->nguoi_dung_id = Auth::id(); // Tác giả là nhân viên đang đăng nhập
            $baiViet->trang_thai = $request->trang_thai;
            
            if ($request->hasFile('hinh_anh_chinh')) {
                $file = $request->file('hinh_anh_chinh');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads'), $filename);
                $baiViet->hinh_anh_chinh = $filename;
            }
            $baiViet->save();
            
            DB::commit();
            return redirect()->route('staff.news')->with('success', 'Đã thêm bài viết mới!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function editNews($id)
    {
        if ($redirect = $this->ensureStaff()) return $redirect;
        $news = BaiViet::whereNull('deleted_at')->findOrFail($id);
        return view('staff.news.edit', compact('news'));
    }

    public function updateNews(Request $request, $id)
    {
        if ($redirect = $this->ensureStaff()) return $redirect;
        $baiViet = BaiViet::whereNull('deleted_at')->findOrFail($id);
        
        $request->validate([
            'tieu_de' => 'required|max:255',
            'noi_dung' => 'required',
            'hinh_anh_chinh_moi' => 'nullable|image|max:2048',
            'trang_thai' => 'required|in:NHAP,XUAT_BAN',
        ]);
        
        DB::beginTransaction();
        try {
            $baiViet->tieu_de = $request->tieu_de;
            $baiViet->slug = Str::slug($request->tieu_de) . '-' . $baiViet->id;
            $baiViet->mo_ta_ngan = $request->mo_ta_ngan;
            $baiViet->noi_dung = $request->noi_dung;
            $baiViet->trang_thai = $request->trang_thai;

            if ($request->hasFile('hinh_anh_chinh_moi')) {
                // Xóa ảnh cũ (tùy chọn)
                // if ($baiViet->hinh_anh_chinh) { unlink(public_path('uploads/' . $baiViet->hinh_anh_chinh)); }
                $file = $request->file('hinh_anh_chinh_moi');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads'), $filename);
                $baiViet->hinh_anh_chinh = $filename;
            }
            $baiViet->save();
            
            DB::commit();
            return redirect()->route('staff.news')->with('success', 'Đã cập nhật bài viết!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi cập nhật: ' . $e->getMessage());
        }
    }

    public function deleteNews($id)
    {
        if ($redirect = $this->ensureStaff()) return $redirect;
        
        try {
            BaiViet::whereNull('deleted_at')->findOrFail($id)->delete();
            return redirect()->route('staff.news')->with('success', 'Đã xóa (soft delete) bài viết!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi xóa: ' . $e->getMessage());
        }
    }
    public function toggleProductFlag(Request $request, $id)
    {
        if ($redirect = $this->ensureStaff()) {
            return $redirect;
        }

        $request->validate([
            'flag' => 'required|in:la_flash_sale,la_noi_bat',
            'value' => 'required|boolean',
        ]);

        $product = SanPham::whereNull('deleted_at')->findOrFail($id);
        $flag = $request->flag;
        $value = $request->value;
        
        try {
            // Cập nhật cờ
            $product->$flag = $value;
            $product->save();
            
            $messageType = ($flag == 'la_flash_sale' ? 'Flash Sale' : 'Nổi bật');

            return response()->json([
                'success' => true,
                'message' => 'Đã ' . ($value ? 'BẬT' : 'TẮT') . ' cờ ' . $messageType . ' thành công.',
                'new_value' => $value
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: Không thể cập nhật cờ sản phẩm. ' . $e->getMessage()
            ], 500);
        }
    }

    public function customers(Request $request)
    {
        if ($redirect = $this->ensureStaff()) {
            return $redirect;
        }

        $query = User::where('vai_tro', 'KHACH_HANG');
        
        // BỔ SUNG: Logic Tìm kiếm theo keyword
        $keyword = $request->input('keyword');
        if ($keyword) {
            $query->where(function($q) use ($keyword) {
                $q->where('ho_ten', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%")
                  ->orWhere('sdt', 'like', "%{$keyword}%");
            });
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Giữ lại tham số tìm kiếm khi phân trang
        $customers->appends($request->all());

        return view('staff.customers', compact('customers', 'keyword'));
    }

    public function profile()
    {
        // Chức năng kiểm tra quyền nhân viên
        if ($redirect = $this->ensureStaff()) {
            return $redirect;
        }

        $user = Auth::user();
        return view('staff.profile', compact('user'));
    }

    /**
     * Xử lý cập nhật thông tin cá nhân của Nhân viên (Có địa chỉ)
     */
    public function updateProfile(Request $request)
    {
        if ($redirect = $this->ensureStaff()) {
            return $redirect;
        }

        $user = Auth::user();
        
        $rules = [
            'ho_ten' => 'required|string|max:191',
            'sdt' => 'nullable|string|max:32',
            'email' => 'required|email|max:191|unique:nguoi_dung,email,' . $user->id,
            'dia_chi' => 'nullable|string|max:255',
            'password_new' => 'nullable|min:6|confirmed',
        ];
        
        $request->validate($rules);
        
        DB::beginTransaction();
        try {
            $user->ho_ten = $request->ho_ten;
            $user->sdt = $request->sdt;
            $user->email = $request->email;
            $user->dia_chi = $request->dia_chi; // Cập nhật địa chỉ

            // Cập nhật mật khẩu nếu có nhập mật khẩu mới
            if ($request->filled('password_new')) {
                $user->password = Hash::make($request->password_new);
            }
            
            $user->save();
            DB::commit();

            return redirect()->route('staff.profile')->with('success', 'Cập nhật thông tin cá nhân thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi cập nhật: ' . $e->getMessage());
        }
    }

    // public function exportReport(Request $request)
    // {
    //     if ($redirect = $this->ensureStaff()) {
    //         return $redirect;
    //     }

    //     // Tái sử dụng logic lọc thời gian
    //     $currentDate = Carbon::now();
    //     $queryStart = null;
    //     $queryEnd = null;
    //     $selectedQuick = $request->input('quick_select', 'this_month');
        
    //     switch ($selectedQuick) {
    //         case 'today': $queryStart = $currentDate->copy()->startOfDay(); $queryEnd = $currentDate->copy()->endOfDay(); break;
    //         case '7_days': $queryStart = $currentDate->copy()->subDays(6)->startOfDay(); $queryEnd = $currentDate->copy()->endOfDay(); break;
    //         case '30_days': $queryStart = $currentDate->copy()->subDays(29)->startOfDay(); $queryEnd = $currentDate->copy()->endOfDay(); break;
    //         case 'this_month': $queryStart = $currentDate->copy()->startOfMonth(); $queryEnd = $currentDate->copy()->endOfDay(); break;
    //         case 'this_year': $queryStart = $currentDate->copy()->startOfYear(); $queryEnd = $currentDate->copy()->endOfDay(); break;
    //         case 'custom':
    //             if ($request->filled('start_date') && $request->filled('end_date')) {
    //                 $queryStart = Carbon::parse($request->start_date)->startOfDay();
    //                 $queryEnd = Carbon::parse($request->end_date)->endOfDay();
    //             }
    //             break;
    //         default: $queryStart = $currentDate->copy()->startOfMonth(); $queryEnd = $currentDate->copy()->endOfDay(); break;
    //     }

    //     if (is_null($queryStart) || is_null($queryEnd)) {
    //         $queryStart = $currentDate->copy()->startOfMonth();
    //         $queryEnd = $currentDate->copy()->endOfDay();
    //     }
        
    //     // --- 1. LẤY DỮ LIỆU ĐỂ XUẤT ---
        
    //     // Lấy tất cả đơn hàng HOÀN THÀNH trong kỳ (dữ liệu chính cho báo cáo)
    //     $ordersToReport = DonHang::with(['nguoiDung', 'chiTiet'])
    //                             ->where('trang_thai', 'HOAN_THANH')
    //                             ->whereBetween('ngay_dat', [$queryStart, $queryEnd])
    //                             ->get();
                                
    //     // Lấy dữ liệu Top Selling (nếu cần xuất)
    //     $topSellingProducts = \App\Models\DonHangChiTiet::select(
    //         DB::raw('san_pham_id, SUM(so_luong) as tong_so_luong_ban, SUM(don_hang_chi_tiet.thanh_tien) as tong_doanh_thu')
    //     )
    //         ->join('don_hang', 'don_hang_chi_tiet.don_hang_id', '=', 'don_hang.id')
    //         ->where('don_hang.trang_thai', 'HOAN_THANH')
    //         ->whereBetween('don_hang.ngay_dat', [$queryStart, $queryEnd])
    //         ->groupBy('san_pham_id')
    //         ->orderBy('tong_so_luong_ban', 'desc')
    //         ->with('sanPham')
    //         ->get();


    //     $dateRange = $queryStart->format('Ymd') . '_to_' . $queryEnd->format('Ymd');
    //     $fileName = 'BaoCaoDoanhThu_' . $dateRange . '.xlsx'; // Chọn định dạng .xlsx

    //     // --- 2. LOGIC XUẤT FILE (PLACEHOLDER) ---
        
    //     // BƯỚC CẦN THIẾT: Bạn cần cài đặt thư viện Maatwebsite/Laravel Excel.
    //     // Chạy lệnh: composer require maatwebsite/excel
        
    //     // Ví dụ xuất Excel (Maatwebsite/Laravel Excel):
    //     // return Excel::download(new \App\Exports\RevenueReportExport($ordersToReport, $topSellingProducts), $fileName);
        
    //     // Tạm thời, chúng ta sẽ trả về một thông báo và dữ liệu thô:
    //     return response()->json([
    //         'success' => false,
    //         'message' => 'Tính năng xuất file đang chờ thư viện Excel được cài đặt (Maatwebsite/Excel).',
    //         'orders_count' => $ordersToReport->count(),
    //         'start_date' => $queryStart->format('Y-m-d'),
    //         'end_date' => $queryEnd->format('Y-m-d'),
    //         // 'data_sample' => $ordersToReport->take(2) // Có thể tạm thời comment lại để tránh lỗi đệ quy JSON
    //     ]);
    // }
        /**
     * Xử lý xuất báo cáo ra file Excel hoặc PDF.
     */
    

    /**
     * Hiển thị form thêm biến thể mới (Và ảnh phụ cho biến thể đó)
     */
    public function createVariant($id)
    {
        if ($redirect = $this->ensureStaff()) return $redirect;
        
        $product = SanPham::whereNull('deleted_at')->findOrFail($id);

        return view('staff.products.variants.create', compact('product'));
    }

    /**
     * Xử lý lưu biến thể mới và ảnh phụ (Nếu có)
     */
    public function storeVariant(Request $request, $id)
    {
        if ($redirect = $this->ensureStaff()) return $redirect;
        
        $product = SanPham::whereNull('deleted_at')->findOrFail($id);

        $request->validate([
            'sku' => 'required|unique:bien_the_san_pham,sku|max:64',
            'gia' => 'required|numeric|min:0',
            'ton_kho' => 'required|integer|min:0',
            'mau_sac' => 'nullable|string|max:64',
            'dung_luong_gb' => 'nullable|integer',
            'new_images.*' => 'nullable|image|max:2048', // Ảnh phụ cho biến thể này
        ]);
        
        DB::beginTransaction();
        try {
            // 1. Tạo Biến thể mới
            $variant = BienTheSanPham::create([ 
                'san_pham_id' => $product->id, 
                'sku' => $request->sku, 
                'gia' => $request->gia, 
                'gia_so_sanh' => $request->gia_so_sanh ?? null,
                'ton_kho' => $request->ton_kho, 
                'mau_sac' => $request->mau_sac ?? null, 
                'dung_luong_gb' => $request->dung_luong_gb ?? null, 
                'dang_ban' => 1 
            ]);
            
            // 2. Xử lý Ảnh phụ (Tạm thời lưu vào bảng SanPhamAnh như ảnh chung)
            if ($request->hasFile('new_images')) {
                 $thuTu = $product->sanPhamAnh->max('thu_tu') + 1; // Lấy thứ tự tiếp theo
                 foreach ($request->file('new_images') as $file) {
                    $filename = time() . '_' . $file->getClientOriginalName(); 
                    $file->move(public_path('uploads'), $filename); 
                    \App\Models\SanPhamAnh::create([
                        'san_pham_id' => $product->id, 
                        'url' => $filename,
                        'thu_tu' => $thuTu++ 
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('staff.products.edit', $product->id)
                             ->with('success', 'Đã thêm biến thể SKU: ' . $variant->sku . ' thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi khi thêm biến thể: ' . $e->getMessage())->withInput();
        }
    }

    public function confirmExport(Request $request)
    {
        if ($redirect = $this->ensureStaff()) {
            return $redirect;
        }

        $currentDate = Carbon::now();
        $queryStart = null;
        $queryEnd = null;
        $selectedQuick = $request->input('quick_select', 'this_month');
        
        // --- Tái sử dụng logic tính toán ngày ---
        switch ($selectedQuick) {
            case 'today': $queryStart = $currentDate->copy()->startOfDay(); $queryEnd = $currentDate->copy()->endOfDay(); break;
            case '7_days': $queryStart = $currentDate->copy()->subDays(6)->startOfDay(); $queryEnd = $currentDate->copy()->endOfDay(); break;
            case '30_days': $queryStart = $currentDate->copy()->subDays(29)->startOfDay(); $queryEnd = $currentDate->copy()->endOfDay(); break;
            case 'this_month': $queryStart = $currentDate->copy()->startOfMonth(); $queryEnd = $currentDate->copy()->endOfDay(); break;
            case 'this_year': $queryStart = $currentDate->copy()->startOfYear(); $queryEnd = $currentDate->copy()->endOfDay(); break;
            case 'custom':
                if ($request->filled('start_date') && $request->filled('end_date')) {
                    $queryStart = Carbon::parse($request->start_date)->startOfDay();
                    $queryEnd = Carbon::parse($request->end_date)->endOfDay();
                }
                break;
            default: $queryStart = $currentDate->copy()->startOfMonth(); $queryEnd = $currentDate->copy()->endOfDay(); break;
        }
        
        if (is_null($queryStart) || is_null($queryEnd)) {
            $queryStart = $currentDate->copy()->startOfMonth();
            $queryEnd = $currentDate->copy()->endOfDay();
            $selectedQuick = 'this_month';
        }
        
        $ordersCount = \App\Models\DonHang::where('trang_thai', 'HOAN_THANH')
                                        ->whereBetween('ngay_dat', [$queryStart, $queryEnd])
                                        ->count();

        return view('staff.reports.confirm-export', compact(
            'queryStart', 
            'queryEnd', 
            'selectedQuick', 
            'ordersCount',
            'request' // Truyền request gốc để giữ lại các tham số ẩn
        ));
    }

    /**
     * Xử lý xuất báo cáo ra file Excel hoặc PDF.
     */
    // public function exportReport(Request $request)
    // {
    //     if ($redirect = $this->ensureStaff()) {
    //         return $redirect;
    //     }

    //     // 1. Tái sử dụng logic lọc thời gian
    //     $currentDate = Carbon::now();
    //     $queryStart = null;
    //     $queryEnd = null;
    //     $selectedQuick = $request->input('quick_select', 'this_month');
        
    //     // --- Logic Tính Toán Ngày ---
    //     switch ($selectedQuick) {
    //         case 'today': $queryStart = $currentDate->copy()->startOfDay(); $queryEnd = $currentDate->copy()->endOfDay(); break;
    //         case '7_days': $queryStart = $currentDate->copy()->subDays(6)->startOfDay(); $queryEnd = $currentDate->copy()->endOfDay(); break;
    //         case '30_days': $queryStart = $currentDate->copy()->subDays(29)->startOfDay(); $queryEnd = $currentDate->copy()->endOfDay(); break;
    //         case 'this_month': $queryStart = $currentDate->copy()->startOfMonth(); $queryEnd = $currentDate->copy()->endOfDay(); break;
    //         case 'this_year': $queryStart = $currentDate->copy()->startOfYear(); $queryEnd = $currentDate->copy()->endOfDay(); break;
    //         case 'custom':
    //             if ($request->filled('start_date') && $request->filled('end_date')) {
    //                 $queryStart = Carbon::parse($request->start_date)->startOfDay();
    //                 $queryEnd = Carbon::parse($request->end_date)->endOfDay();
    //             }
    //             break;
    //         default: $queryStart = $currentDate->copy()->startOfMonth(); $queryEnd = $currentDate->copy()->endOfDay(); break;
    //     }

    //     if (is_null($queryStart) || is_null($queryEnd)) {
    //         $queryStart = $currentDate->copy()->startOfMonth();
    //         $queryEnd = $currentDate->copy()->endOfDay();
    //     }
        
    //     // 2. LẤY DỮ LIỆU ĐỂ XUẤT (Chỉ đơn hàng HOÀN THÀNH)
    //     $ordersToReport = DonHang::with(['nguoiDung', 'chiTiet.sanPham'])
    //                             ->where('trang_thai', 'HOAN_THANH')
    //                             ->whereBetween('ngay_dat', [$queryStart, $queryEnd])
    //                             ->get();
                                
    //     // 3. XỬ LÝ EXPORT
    //     $dateRange = $queryStart->format('Ymd') . '_to_' . $queryEnd->format('Ymd');
    //     $exportType = $request->input('type', 'excel'); 
        
    //     if ($exportType === 'excel') {
    //         $fileName = 'BaoCaoDoanhThu_' . $dateRange . '.xlsx';
            
    //         // SỬ DỤNG LỚP EXPORT VỪA TẠO
    //         return \Maatwebsite\Excel\Facades\Excel::download(
    //             new \App\Exports\StaffRevenueExport($ordersToReport, $queryStart, $queryEnd), 
    //             $fileName
    //         );
    //     } elseif ($exportType === 'pdf') {
    //         $fileName = 'BaoCaoDoanhThu_' . $dateRange . '.pdf';
            
    //         // SỬ DỤNG LỚP PDF VỪA TẠO
    //         $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.staff_revenue_pdf', [
    //             'ordersToReport' => $ordersToReport,
    //             'startDate' => $queryStart,
    //             'endDate' => $queryEnd,
    //         ]);
    //         // Nếu có lỗi, thử setting font cho PDF
    //         // $pdf->setOptions(['defaultFont' => 'sans-serif']); 
    //         return $pdf->download($fileName);
    //     }

    //     return back()->with('error', 'Định dạng xuất file không hợp lệ.');
    // }
}