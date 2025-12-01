<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;

// class DonHang extends Model
// {
//     protected $table = 'don_hang';

//     protected $fillable = [
//         'ma',
//         'nguoi_dung_id',
//         'trang_thai',
//         'tong_tien',
//         'giam_gia',
//         'phi_van_chuyen',
//         'thanh_tien',
//         'ten_nguoi_nhan',
//         'sdt_nguoi_nhan',
//         'dia_chi_giao',
//         'phuong_thuc_tt',
//         'trang_thai_tt',
//         'ngay_dat',
//     ];

//     public $timestamps = true;

//     public function khachHang()
//     {
//         return $this->belongsTo(User::class, 'nguoi_dung_id');
//     }
//     public function chiTiet()
//     {
//         return $this->hasMany(DonHangChiTiet::class, 'don_hang_id');
//     }
// }

namespace App\Models;
use App\Models\User;
use DonHangChiTiet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DonHang extends Model
{
    use SoftDeletes;

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
        'ngay_dat'
    ];

    protected $casts = [
        'tong_tien' => 'decimal:2',
        'giam_gia' => 'decimal:2',
        'phi_van_chuyen' => 'decimal:2',
        'thanh_tien' => 'decimal:2',
        'ngay_dat' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function nguoiDung()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    public function chiTiet()
    {
        return $this->hasMany(DonHangChiTiet::class, 'don_hang_id');
    }
}

