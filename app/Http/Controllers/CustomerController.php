<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DonHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Hiển thị thông tin tài khoản
     */
    public function profile()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
        }
        
        $user = Auth::user();
        
        // Thống kê đơn hàng
        $stats = [
            'total_orders' => DonHang::where('nguoi_dung_id', $user->id)->count(),
            'pending_orders' => DonHang::where('nguoi_dung_id', $user->id)
                ->where('trang_thai', 'DANG_XU_LY')
                ->count(),
            'shipping_orders' => DonHang::where('nguoi_dung_id', $user->id)
                ->where('trang_thai', 'DANG_GIAO')
                ->count(),
            'completed_orders' => DonHang::where('nguoi_dung_id', $user->id)
                ->where('trang_thai', 'HOAN_THANH')
                ->count(),
            'total_spent' => DonHang::where('nguoi_dung_id', $user->id)
                ->where('trang_thai', 'HOAN_THANH')
                ->sum('thanh_tien')
        ];
        
        return view('customer.profile', compact('user', 'stats'));
    }
    
    /**
     * Cập nhật thông tin tài khoản
     */
    public function updateProfile(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
        }
        
        $user = User::findOrFail(Auth::id());
        
        $request->validate([
            'ho_ten' => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:nguoi_dung,email,' . $user->id,
            'sdt' => 'nullable|string|max:32',
            'dia_chi' => 'nullable|string|max:500',
            'mat_khau_cu' => 'nullable|string',
            'mat_khau_moi' => 'nullable|string|min:6|confirmed',
        ], [
            'ho_ten.required' => 'Vui lòng nhập tên',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email đã được sử dụng',
            'mat_khau_moi.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'mat_khau_moi.confirmed' => 'Xác nhận mật khẩu không khớp'
        ]);
        
        try {
            // Cập nhật thông tin cơ bản
            $user->ho_ten = $request->ho_ten;
            $user->email = $request->email;
            $user->sdt = $request->sdt;
            $user->dia_chi = $request->dia_chi;
            
            // Đổi mật khẩu nếu có
            if ($request->filled('mat_khau_moi')) {
                // Kiểm tra mật khẩu cũ
                if (!$request->filled('mat_khau_cu')) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Vui lòng nhập mật khẩu cũ!');
                }
                
                if (!Hash::check($request->mat_khau_cu, $user->mat_khau)) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Mật khẩu cũ không đúng!');
                }
                
                $user->mat_khau = Hash::make($request->mat_khau_moi);
            }
            
            $user->save();
            
            return redirect()->back()->with('success', 'Cập nhật thông tin thành công!');
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    /**
     * Hiển thị danh sách đơn hàng
     */
    // public function orders(Request $request)
    // {
    //     if (!Auth::check()) {
    //         return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
    //     }
        
    //     $query = DonHang::where('nguoi_dung_id', Auth::id());
        
    //     // Lọc theo trạng thái
    //     if ($request->has('status') && $request->status != '') {
    //         $query->where('trang_thai', $request->status);
    //     }
        
    //     // Lọc theo khoảng thời gian
    //     if ($request->has('from_date') && $request->from_date != '') {
    //         $query->whereDate('ngay_dat', '>=', $request->from_date);
    //     }
        
    //     if ($request->has('to_date') && $request->to_date != '') {
    //         $query->whereDate('ngay_dat', '<=', $request->to_date);
    //     }
        
    //     // Tìm kiếm theo mã đơn hàng
    //     if ($request->has('search') && $request->search != '') {
    //         $query->where('ma', 'LIKE', '%' . $request->search . '%');
    //     }
        
    //     $orders = $query->orderBy('ngay_dat', 'desc')->paginate(10);
        
    //     // Đếm số lượng đơn hàng theo trạng thái
    //     $statusCounts = [
    //         'all' => DonHang::where('nguoi_dung_id', Auth::id())->count(),
    //         'DANG_XU_LY' => DonHang::where('nguoi_dung_id', Auth::id())
    //             ->where('trang_thai', 'DANG_XU_LY')->count(),
    //         'DANG_GIAO' => DonHang::where('nguoi_dung_id', Auth::id())
    //             ->where('trang_thai', 'DANG_GIAO')->count(),
    //         'HOAN_THANH' => DonHang::where('nguoi_dung_id', Auth::id())
    //             ->where('trang_thai', 'HOAN_THANH')->count(),
    //         'HUY' => DonHang::where('nguoi_dung_id', Auth::id())
    //             ->where('trang_thai', 'HUY')->count()
    //     ];
        
    //     return view('customer.orders', compact('orders', 'statusCounts'));
    // }
    public function orders(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
        }
        
        // Bắt đầu truy vấn và Eager Load chi tiết sản phẩm
        $query = DonHang::where('nguoi_dung_id', Auth::id())
                        ->with('chiTiet.bienThe.sanPham'); // <-- Bổ sung eager loading
        
        // Lọc theo trạng thái
        $currentStatus = $request->input('status', 'all'); // Lấy trạng thái hiện tại
        if ($currentStatus != 'all') {
            $query->where('trang_thai', $currentStatus);
        }
        
        // Lọc theo khoảng thời gian (tùy chọn, giữ nguyên logic cũ nếu có)
        if ($request->has('from_date') && $request->from_date != '') {
            $query->whereDate('ngay_dat', '>=', $request->from_date);
        }
        
        if ($request->has('to_date') && $request->to_date != '') {
            $query->whereDate('ngay_dat', '<=', $request->to_date);
        }
        
        // Tìm kiếm theo mã đơn hàng
        if ($request->has('search') && $request->search != '') {
            $query->where('ma', 'LIKE', '%' . $request->search . '%');
        }
        
        $orders = $query->orderBy('ngay_dat', 'desc')->paginate(10);
        $orders->appends($request->all()); // Giữ lại tham số lọc khi phân trang
        
        // Đếm số lượng đơn hàng theo trạng thái
        $statusCounts = [
            'all' => DonHang::where('nguoi_dung_id', Auth::id())->count(),
            'DANG_XU_LY' => DonHang::where('nguoi_dung_id', Auth::id())
                ->where('trang_thai', 'DANG_XU_LY')->count(),
            'DANG_GIAO' => DonHang::where('nguoi_dung_id', Auth::id())
                ->where('trang_thai', 'DANG_GIAO')->count(),
            'HOAN_THANH' => DonHang::where('nguoi_dung_id', Auth::id())
                ->where('trang_thai', 'HOAN_THANH')->count(),
            'HUY' => DonHang::where('nguoi_dung_id', Auth::id())
                ->where('trang_thai', 'HUY')->count()
        ];
        
        return view('customer.orders', compact('orders', 'statusCounts', 'currentStatus'));
    }
    
    /**
     * Hiển thị danh sách yêu thích
     */
    // public function wishlist()
    // {
    //     if (!Auth::check()) {
    //         return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
    //     }
        
    //     $wishlistItems = DB::table('yeu_thich')
    //         ->join('san_pham', 'yeu_thich.san_pham_id', '=', 'san_pham.id')
    //         ->join('thuong_hieu', 'san_pham.thuong_hieu_id', '=', 'thuong_hieu.id')
    //         ->leftJoin('bien_the_san_pham', function($join) {
    //             $join->on('san_pham.id', '=', 'bien_the_san_pham.san_pham_id')
    //                  ->where('bien_the_san_pham.dang_ban', '=', 1)
    //                  ->whereRaw('bien_the_san_pham.id = (SELECT MIN(id) FROM bien_the_san_pham WHERE san_pham_id = san_pham.id AND dang_ban = 1)');
    //         })
    //         ->where('yeu_thich.nguoi_dung_id', Auth::id())
    //         ->whereNull('san_pham.deleted_at')
    //         ->select(
    //             'yeu_thich.id as wishlist_id',
    //             'san_pham.id',
    //             'san_pham.ten',
    //             'san_pham.slug',
    //             'san_pham.anh_chinh',
    //             'thuong_hieu.ten as thuong_hieu',
    //             'bien_the_san_pham.id as variant_id',
    //             'bien_the_san_pham.gia',
    //             'bien_the_san_pham.gia_so_sanh',
    //             'bien_the_san_pham.ton_kho',
    //             'yeu_thich.created_at'
    //         )
    //         ->orderBy('yeu_thich.created_at', 'desc')
    //         ->paginate(12);
        
    //     return view('customer.wishlist', compact('wishlistItems'));
    // }
    public function wishlist()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
        }
        
        $wishlistItems = DB::table('yeu_thich')
            ->join('san_pham', 'yeu_thich.san_pham_id', '=', 'san_pham.id')
            ->join('thuong_hieu', 'san_pham.thuong_hieu_id', '=', 'thuong_hieu.id')
            ->leftJoin('bien_the_san_pham', function($join) {
                $join->on('san_pham.id', '=', 'bien_the_san_pham.san_pham_id')
                     ->where('bien_the_san_pham.dang_ban', '=', 1)
                     ->whereRaw('bien_the_san_pham.id = (SELECT MIN(id) FROM bien_the_san_pham WHERE san_pham_id = san_pham.id AND dang_ban = 1)');
            })
            ->where('yeu_thich.nguoi_dung_id', Auth::id())
            ->whereNull('san_pham.deleted_at')
            ->select(
                'yeu_thich.id as wishlist_id',
                'san_pham.id',
                'san_pham.ten',
                'san_pham.slug',
                'san_pham.hinh_anh_mac_dinh as anh_chinh', // ĐÃ SỬA TÊN CỘT
                'thuong_hieu.ten as thuong_hieu',
                'bien_the_san_pham.id as variant_id',
                'bien_the_san_pham.gia',
                'bien_the_san_pham.gia_so_sanh',
                'bien_the_san_pham.ton_kho',
                'yeu_thich.created_at'
            )
            ->orderBy('yeu_thich.created_at', 'desc')
            ->paginate(12);
        
        return view('customer.wishlist', compact('wishlistItems'));
    }
    /**
     * Hiển thị địa chỉ giao hàng
     */
    public function addresses()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
        }
        
        $addresses = DB::table('dia_chi_giao_hang')
            ->where('nguoi_dung_id', Auth::id())
            ->orderBy('mac_dinh', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('customer.addresses', compact('addresses'));
    }
    
    /**
     * Thêm địa chỉ giao hàng
     */
    public function addAddress(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
        }
        
        $request->validate([
            'ten_nguoi_nhan' => 'required|string|max:191',
            'sdt' => 'required|string|max:32',
            'dia_chi' => 'required|string|max:500',
            'mac_dinh' => 'nullable|boolean'
        ]);
        
        DB::beginTransaction();
        try {
            // Nếu set làm mặc định, bỏ mặc định các địa chỉ khác
            if ($request->mac_dinh) {
                DB::table('dia_chi_giao_hang')
                    ->where('nguoi_dung_id', Auth::id())
                    ->update(['mac_dinh' => 0]);
            }
            
            // Thêm địa chỉ mới
            DB::table('dia_chi_giao_hang')->insert([
                'nguoi_dung_id' => Auth::id(),
                'ten_nguoi_nhan' => $request->ten_nguoi_nhan,
                'sdt' => $request->sdt,
                'dia_chi' => $request->dia_chi,
                'mac_dinh' => $request->mac_dinh ?? 0,
                'created_at' => now()
            ]);
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Thêm địa chỉ thành công!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    /**
     * Cập nhật địa chỉ giao hàng
     */
    public function updateAddress(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
        }
        
        $request->validate([
            'ten_nguoi_nhan' => 'required|string|max:191',
            'sdt' => 'required|string|max:32',
            'dia_chi' => 'required|string|max:500',
            'mac_dinh' => 'nullable|boolean'
        ]);
        
        DB::beginTransaction();
        try {
            // Kiểm tra địa chỉ có thuộc user không
            $address = DB::table('dia_chi_giao_hang')
                ->where('id', $id)
                ->where('nguoi_dung_id', Auth::id())
                ->first();
            
            if (!$address) {
                throw new \Exception('Địa chỉ không tồn tại!');
            }
            
            // Nếu set làm mặc định, bỏ mặc định các địa chỉ khác
            if ($request->mac_dinh) {
                DB::table('dia_chi_giao_hang')
                    ->where('nguoi_dung_id', Auth::id())
                    ->where('id', '!=', $id)
                    ->update(['mac_dinh' => 0]);
            }
            
            // Cập nhật địa chỉ
            DB::table('dia_chi_giao_hang')
                ->where('id', $id)
                ->update([
                    'ten_nguoi_nhan' => $request->ten_nguoi_nhan,
                    'sdt' => $request->sdt,
                    'dia_chi' => $request->dia_chi,
                    'mac_dinh' => $request->mac_dinh ?? 0
                ]);
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Cập nhật địa chỉ thành công!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    /**
     * Xóa địa chỉ giao hàng
     */
    public function deleteAddress($id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
        }
        
        try {
            $deleted = DB::table('dia_chi_giao_hang')
                ->where('id', $id)
                ->where('nguoi_dung_id', Auth::id())
                ->delete();
            
            if (!$deleted) {
                throw new \Exception('Địa chỉ không tồn tại!');
            }
            
            return redirect()->back()->with('success', 'Xóa địa chỉ thành công!');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}