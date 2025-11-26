<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\DonHang;

class CustomerController extends Controller
{
    protected function ensureCustomer()
    {
        $user = Auth::user();
        
        if (!$user || $user->vai_tro !== 'KHACH_HANG') {
            return redirect()->route('home')
                ->with('error', 'Bạn không có quyền truy cập.');
        }
        
        return null;
    }
    
    public function profile()
    {
        if ($redirect = $this->ensureCustomer()) {
            return $redirect;
        }
        
        $user = Auth::user();
        return view('customer.profile', compact('user'));
    }
    
    public function orders()
    {
        if ($redirect = $this->ensureCustomer()) {
            return $redirect;
        }
        
        $orders = DonHang::where('nguoi_dung_id', Auth::id())
            ->orderBy('ngay_dat', 'desc')
            ->paginate(10);
            
        return view('customer.orders', compact('orders'));
    }
    
    public function wishlist()
    {
        if ($redirect = $this->ensureCustomer()) {
            return $redirect;
        }
        
        return view('customer.wishlist');
    }
    
    public function reviews()
    {
        if ($redirect = $this->ensureCustomer()) {
            return $redirect;
        }
        
        return view('customer.reviews');
    }
}