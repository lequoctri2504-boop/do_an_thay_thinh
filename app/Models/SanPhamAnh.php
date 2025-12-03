<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SanPhamAnh extends Model
{
    protected $table = 'san_pham_anh';
    
    protected $fillable = ['san_pham_id', 'url', 'thu_tu'];
    
    public $timestamps = false;
    
    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'san_pham_id');
    }
}
