<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DanhMuc extends Model
{
    use SoftDeletes; // Sử dụng xóa mềm

    protected $table = 'danh_muc';

    protected $fillable = [
        'ten',
        'slug',
        'cha_id', // ID danh mục cha (nếu có)
    ];

    // Quan hệ: Danh mục cha
    public function danhMucCha()
    {
        return $this->belongsTo(DanhMuc::class, 'cha_id');
    }

    // Quan hệ: Danh mục con
    public function danhMucCon()
    {
        return $this->hasMany(DanhMuc::class, 'cha_id');
    }

    // Quan hệ: Sản phẩm thuộc danh mục
    public function sanPhams()
    {
        return $this->belongsToMany(SanPham::class, 'san_pham_danh_muc', 'danh_muc_id', 'san_pham_id');
    }
}