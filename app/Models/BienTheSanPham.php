<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BienTheSanPham extends Model
{
    use SoftDeletes;

    protected $table = 'bien_the_san_pham';

    protected $fillable = [
        'san_pham_id',
        'sku',
        'mau_sac',
        'dung_luong_gb',
        'gia',
        'gia_so_sanh',
        'ton_kho',
        'dang_ban'
    ];

    protected $casts = [
        'gia' => 'decimal:2',
        'gia_so_sanh' => 'decimal:2',
        'ton_kho' => 'integer',
        'dung_luong_gb' => 'integer',
        'dang_ban' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'san_pham_id');
    }

    // Tính phần trăm giảm giá
    public function getPhanTramGiamGiaAttribute()
    {
        if ($this->gia_so_sanh && $this->gia_so_sanh > $this->gia) {
            return round((($this->gia_so_sanh - $this->gia) / $this->gia_so_sanh) * 100);
        }
        return 0;
    }

    // Kiểm tra còn hàng
    public function getConHangAttribute()
    {
        return $this->ton_kho > 0;
    }
}
