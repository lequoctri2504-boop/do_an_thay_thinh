<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;

// class DonHangChiTiet extends Model
// {
//     // Khai báo tên bảng (nếu không theo chuẩn số nhiều của Laravel)
//     protected $table = 'don_hang_chi_tiet';

//     // Tắt timestamps vì bảng này trong db.sql của bạn không có created_at/updated_at
//     public $timestamps = false; 

//     protected $fillable = [
//         'don_hang_id',
//         'san_pham_id',
//         'bien_the_id',
//         'ten_sp_ghi_nhan',
//         'sku_ghi_nhan',
//         'gia',
//         'so_luong',
//         'thanh_tien',
//     ];

//     // Thiết lập quan hệ ngược về Đơn hàng
//     public function donHang()
//     {
//         return $this->belongsTo(DonHang::class, 'don_hang_id');
//     }

//     // Thiết lập quan hệ với Sản phẩm (để sau này lấy ảnh hiển thị lại lịch sử mua)
//     public function sanPham()
//     {
//         return $this->belongsTo(SanPham::class, 'san_pham_id');
//     }

//     // Thiết lập quan hệ với Biến thể
//     public function bienThe()
//     {
//         return $this->belongsTo(BienTheSanPham::class, 'bien_the_id');
//     }
// }

use App\Models\BienTheSanPham;
use App\Models\DonHang;
use App\Models\SanPham;
use Illuminate\Database\Eloquent\Model;

class DonHangChiTiet extends Model
{
    protected $table = 'don_hang_chi_tiet';
    public $timestamps = false;

    protected $fillable = [
        'don_hang_id',
        'san_pham_id',
        'bien_the_id',
        'ten_sp_ghi_nhan',
        'sku_ghi_nhan',
        'gia',
        'so_luong',
        'thanh_tien'
    ];

    protected $casts = [
        'gia' => 'decimal:2',
        'thanh_tien' => 'decimal:2',
        'so_luong' => 'integer',
    ];

    public function donHang()
    {
        return $this->belongsTo(DonHang::class, 'don_hang_id');
    }

    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'san_pham_id');
    }

    public function bienThe()
    {
        return $this->belongsTo(BienTheSanPham::class, 'bien_the_id');
    }
}
