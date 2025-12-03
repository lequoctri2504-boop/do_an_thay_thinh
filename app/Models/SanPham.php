<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SanPham extends Model
{
    use SoftDeletes;

    protected $table = 'san_pham';
    
    protected $fillable = [
        'ten',
        'slug',
        'thuong_hieu_id',
        'mo_ta',
        'mo_ta_chi_tiet',
        'anh_chinh',
        'dang_ban'
    ];
    
    protected $dates = ['deleted_at'];
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    
    /**
     * Quan hệ với Thương Hiệu
     */
    public function thuongHieu()
    {
        return $this->belongsTo(ThuongHieu::class, 'thuong_hieu_id');
    }
    
    /**
     * Quan hệ với Biến Thể Sản Phẩm
     */
    public function bienTheSanPham()
    {
        return $this->hasMany(BienTheSanPham::class, 'san_pham_id');
    }
    
    /**
     * Quan hệ với Danh Mục (many-to-many)
     */
    public function danhMuc()
    {
        return $this->belongsToMany(DanhMuc::class, 'san_pham_danh_muc', 'san_pham_id', 'danh_muc_id');
    }
    
    /**
     * Quan hệ với Ảnh Sản Phẩm
     */
    public function sanPhamAnh()
    {
        return $this->hasMany(SanPhamAnh::class, 'san_pham_id')->orderBy('thu_tu');
    }
    
    /**
     * Quan hệ với Đánh Giá
     */
    public function danhGia()
    {
        return $this->hasMany(DanhGia::class, 'san_pham_id')->where('duyet', 1);
    }
    
    /**
     * Quan hệ với Bình Luận
     */
    public function binhLuan()
    {
        return $this->hasMany(BinhLuan::class, 'san_pham_id')->where('duyet', 1);
    }
}