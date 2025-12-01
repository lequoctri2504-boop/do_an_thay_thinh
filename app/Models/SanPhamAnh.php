<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SanPhamAnh extends Model
{
    protected $table = 'san_pham_anh';
    public $timestamps = false;

    protected $fillable = ['san_pham_id', 'url', 'thu_tu'];

    protected $casts = [
        'thu_tu' => 'integer',
    ];

    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'san_pham_id');
    }

    // URL đầy đủ
    public function getFullUrlAttribute()
    {
        return asset('storage/products/' . $this->url);
    }
}
