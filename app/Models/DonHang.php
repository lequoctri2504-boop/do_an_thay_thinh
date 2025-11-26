<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonHang extends Model
{
    protected $table = 'don_hang';

    protected $fillable = [
        'ma',
        'nguoi_dung_id',
        'trang_thai',
        'tong_tien',
        'giam_gia',
        'phi_van_chuyen',
        'thanh_tien',
        'ten_nguoi_nhan',
        'sdt_nguoi_nhan',
        'dia_chi_giao',
        'phuong_thuc_tt',
        'trang_thai_tt',
        'ngay_dat',
    ];

    public $timestamps = true;

    public function khachHang()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }
}
