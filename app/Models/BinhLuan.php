<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BinhLuan extends Model
{
    protected $table = 'binh_luan';
    
    protected $fillable = [
        'san_pham_id', 'nguoi_dung_id', 'parent_id',
        'noi_dung', 'duyet'
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
    
    public function parent()
    {
        return $this->belongsTo(BinhLuan::class, 'parent_id');
    }
    
    public function replies()
    {
        return $this->hasMany(BinhLuan::class, 'parent_id');
    }
}
