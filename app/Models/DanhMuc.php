<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DanhMuc extends Model
{
    use SoftDeletes;
    protected $table = 'danh_muc';
    
    protected $fillable = ['ten', 'slug', 'cha_id'];
    
    public function sanPham()
    {
        return $this->belongsToMany(SanPham::class, 'san_pham_danh_muc', 'danh_muc_id', 'san_pham_id');
    }
    
    public function parent()
    {
        return $this->belongsTo(DanhMuc::class, 'cha_id');
    }
    
    public function children()
    {
        return $this->hasMany(DanhMuc::class, 'cha_id');
    }
}