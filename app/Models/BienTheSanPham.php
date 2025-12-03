<?php

// File: BienTheSanPham.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BienTheSanPham extends Model
{
    use SoftDeletes;
    protected $table = 'bien_the_san_pham';
    
    protected $fillable = [
        'san_pham_id', 'sku', 'mau_sac', 'dung_luong_gb',
        'gia', 'gia_so_sanh', 'ton_kho', 'dang_ban'
    ];
    
    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'san_pham_id');
    }
}