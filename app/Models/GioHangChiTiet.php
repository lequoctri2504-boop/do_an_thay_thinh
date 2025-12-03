<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GioHangChiTiet extends Model
{
    protected $table = 'gio_hang_chi_tiet';
    
    // Đã thêm 'gia_tai_thoi_diem' vào $fillable
    protected $fillable = ['gio_hang_id', 'bien_the_id', 'so_luong', 'gia_tai_thoi_diem']; 
    
    public $timestamps = false;
    
    public function gioHang()
    {
        return $this->belongsTo(GioHang::class, 'gio_hang_id');
    }
    
    public function bienThe()
    {
        return $this->belongsTo(BienTheSanPham::class, 'bien_the_id');
    }
}