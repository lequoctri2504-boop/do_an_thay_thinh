<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BienTheSanPham extends Model
{
    protected $table = 'bien_the_san_pham';

    protected $fillable = [
        'san_pham_id',
        'sku',
        'mau_sac',
        'dung_luong_gb',
        'gia',
        'gia_so_sanh',
        'ton_kho',
        'dang_ban',
    ];

    public $timestamps = true;

    // Quan hệ với sản phẩm
    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'san_pham_id');
    }
}