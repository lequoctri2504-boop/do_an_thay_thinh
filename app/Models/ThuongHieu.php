<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Sử dụng SoftDeletes vì trong DB có cột deleted_at

class ThuongHieu extends Model
{
    use SoftDeletes;

    protected $table = 'thuong_hieu';

    protected $fillable = [
        'ten',
        'slug',
    ];

    public $timestamps = true;

    // Quan hệ: Một thương hiệu có nhiều sản phẩm
    public function sanPhams()
    {
        return $this->hasMany(SanPham::class, 'thuong_hieu_id');
    }
}