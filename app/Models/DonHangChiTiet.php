<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonHangChiTiet extends Model
{
    protected $table = 'don_hang_chi_tiet';
    
    protected $fillable = [
        'don_hang_id', 'san_pham_id', 'bien_the_id',
        'ten_sp_ghi_nhan', 'sku_ghi_nhan', 'gia', 'so_luong', 'thanh_tien'
    ];
    
    public $timestamps = false;
    
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