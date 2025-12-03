<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ThuongHieu extends Model
{
    use SoftDeletes;
    protected $table = 'thuong_hieu';
    
    protected $fillable = ['ten', 'slug'];
    
    public function sanPham()
    {
        return $this->hasMany(SanPham::class, 'thuong_hieu_id');
    }
}

