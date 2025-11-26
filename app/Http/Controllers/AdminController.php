<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\SanPham;
use App\Models\DonHang;

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
    public function products()
    {
        if ($redirect = $this->ensureAdminOrStaff()) {
            return $redirect;
        }

        $products = SanPham::orderBy('created_at', 'desc')->paginate(10);

        return view('admin.products', compact('products'));
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
