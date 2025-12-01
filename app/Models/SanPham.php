<?php

namespace App\Models;

use BinhLuan;
use DanhGia;
use DonHangChiTiet;
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
        'mo_ta_ngan',
        'mo_ta_day_du',
        'hinh_anh_mac_dinh',
        'hien_thi'
    ];

    protected $casts = [
        'hien_thi' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relationship: Sản phẩm thuộc về một thương hiệu
     */
    public function thuongHieu()
    {
        return $this->belongsTo(ThuongHieu::class, 'thuong_hieu_id');
    }

    /**
     * Relationship: Sản phẩm có nhiều biến thể
     */
    public function bienThe()
    {
        return $this->hasMany(BienTheSanPham::class, 'san_pham_id');
    }

    /**
     * Relationship: Sản phẩm có nhiều ảnh
     */
    public function anh()
    {
        return $this->hasMany(SanPhamAnh::class, 'san_pham_id')->orderBy('thu_tu');
    }

    /**
     * Relationship: Sản phẩm thuộc nhiều danh mục
     */
    public function danhMuc()
    {
        return $this->belongsToMany(
            DanhMuc::class,
            'san_pham_danh_muc',
            'san_pham_id',
            'danh_muc_id'
        );
    }

    /**
     * Relationship: Sản phẩm có nhiều đánh giá
     */
    public function danhGia()
    {
        return $this->hasMany(DanhGia::class, 'san_pham_id');
    }

    /**
     * Relationship: Sản phẩm có nhiều bình luận
     */
    public function binhLuan()
    {
        return $this->hasMany(BinhLuan::class, 'san_pham_id');
    }

    /**
     * Relationship: Sản phẩm có trong nhiều đơn hàng chi tiết
     */
    public function donHangChiTiet()
    {
        return $this->hasMany(DonHangChiTiet::class, 'san_pham_id');
    }

    /**
     * Scope: Lấy sản phẩm đang hiển thị
     */
    public function scopeHienThi($query)
    {
        return $query->where('hien_thi', true);
    }

    /**
     * Scope: Lấy sản phẩm mới nhất
     */
    public function scopeMoiNhat($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Accessor: Lấy giá thấp nhất của sản phẩm
     */
    public function getGiaThapNhatAttribute()
    {
        return $this->bienThe()->where('dang_ban', true)->min('gia') ?? 0;
    }

    /**
     * Accessor: Lấy giá cao nhất của sản phẩm
     */
    public function getGiaCaoNhatAttribute()
    {
        return $this->bienThe()->where('dang_ban', true)->max('gia') ?? 0;
    }

    /**
     * Accessor: Lấy điểm đánh giá trung bình
     */
    public function getDiemTrungBinhAttribute()
    {
        return round($this->danhGia()->where('duyet', true)->avg('so_sao') ?? 0, 1);
    }

    /**
     * Accessor: Kiểm tra còn hàng
     */
    public function getConHangAttribute()
    {
        return $this->bienThe()->where('dang_ban', true)->where('ton_kho', '>', 0)->exists();
    }

    /**
     * Accessor: URL hình ảnh đầy đủ
     */
    public function getAnhUrlAttribute()
    {
        return $this->hinh_anh_mac_dinh 
            ? asset('storage/products/' . $this->hinh_anh_mac_dinh) 
            : asset('images/no-image.png');
    }
}