<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SanPham extends Model
{
    protected $table = 'san_pham';

    protected $fillable = [
        'ten',
        'slug',
        'thuong_hieu_id',
        'mo_ta_ngan',
        'mo_ta_day_du',
        'hinh_anh_mac_dinh',
        'hien_thi',
    ];

    public $timestamps = true; // vì bảng có created_at, updated_at

    // sau này có thể thêm quan hệ thương hiệu, danh mục...
    // Quan hệ với biến thể
    public function bienThe()
    {
        return $this->hasMany(BienTheSanPham::class, 'san_pham_id');
    }

    // Lấy biến thể đang bán
    public function bienTheDangBan()
    {
        return $this->hasMany(BienTheSanPham::class, 'san_pham_id')
            ->where('dang_ban', 1)
            ->where('ton_kho', '>', 0);
    }

    // Lấy giá thấp nhất
    public function getGiaThapNhatAttribute()
    {
        return $this->bienTheDangBan()->min('gia');
    }

    // Lấy giá cao nhất
    public function getGiaCaoNhatAttribute()
    {
        return $this->bienTheDangBan()->max('gia');
    }
}
