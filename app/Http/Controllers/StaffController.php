<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\SanPham;
use App\Models\DonHang;

class StaffController extends Controller
{
    /**
     * Chỉ cho phép user có vai_tro = NHAN_VIEN vào khu vực staff
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
    public function index()
    {
        if ($redirect = $this->ensureStaff()) {
            return $redirect;
        }

        // Thống kê cơ bản cho nhân viên
        $tongDonHang    = DonHang::count();
        $donHangMoi     = DonHang::where('trang_thai', 'MOI')->count();
        $donDangXuLy    = DonHang::where('trang_thai', 'DANG_XU_LY')->count();
        $donHoanThanh   = DonHang::where('trang_thai', 'HOAN_THANH')->count();
        $donDaHuy       = DonHang::where('trang_thai', 'HUY')->count();

        $donHangGanDay  = DonHang::with('nguoiDung')
                                ->orderBy('ngay_dat', 'desc')
                                ->limit(5)
                                ->get();

        //$sanPhamSapHet  = SanPham::where('so_luong', '<', 10)->limit(5)->get();
        $sanPhamSapHet  = collect();
        
        return view('staff.dashboard', compact(
            'tongDonHang',
            'donHangMoi',
            'donDangXuLy',
            'donHoanThanh',
            'donDaHuy',
            'donHangGanDay',
            'sanPhamSapHet'
        ));
    }

    public function orders()
    {
        if ($redirect = $this->ensureStaff()) {
            return $redirect;
        }

        $orders = DonHang::with('nguoiDung')
            ->orderBy('ngay_dat', 'desc')
            ->paginate(10);

        return view('staff.orders', compact('orders'));
    }

    public function products()
    {
        if ($redirect = $this->ensureStaff()) {
            return $redirect;
        }

        $products = SanPham::orderBy('created_at', 'desc')->paginate(10);
        return view('staff.products', compact('products'));
    }

    public function customers()
    {
        if ($redirect = $this->ensureStaff()) {
            return $redirect;
        }

        $customers = User::where('vai_tro', 'KHACH_HANG')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('staff.customers', compact('customers'));
    }

    public function reports()
    {
        if ($redirect = $this->ensureStaff()) {
            return $redirect;
        }

        // tạm thời chỉ trả view trống, sau em có thể gắn thống kê chi tiết
        return view('staff.reports');
    }

    public function support()
    {
        if ($redirect = $this->ensureStaff()) {
            return $redirect;
        }

        return view('staff.support');
    }
}
