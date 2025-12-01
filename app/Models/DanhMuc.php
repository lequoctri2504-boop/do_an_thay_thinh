<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DanhMuc extends Model
{
    use SoftDeletes;

    protected $table = 'danh_muc';

    protected $fillable = ['ten', 'slug', 'cha_id'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Danh mục con
    public function con()
    {
        return $this->hasMany(DanhMuc::class, 'cha_id');
    }

    // Danh mục cha
    public function cha()
    {
        return $this->belongsTo(DanhMuc::class, 'cha_id');
    }

    // Sản phẩm thuộc danh mục
    public function sanPham()
    {
        return $this->belongsToMany(
            SanPham::class,
            'san_pham_danh_muc',
            'danh_muc_id',
            'san_pham_id'
        );
    }
}
