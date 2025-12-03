<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanhGia extends Model
{
    protected $table = 'danh_gia';
    
    protected $fillable = [
        'san_pham_id', 'nguoi_dung_id', 'so_sao',
        'tieu_de', 'noi_dung', 'duyet'
    ];
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;
    
    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'san_pham_id');
    }
    
    public function nguoiDung()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }
}
