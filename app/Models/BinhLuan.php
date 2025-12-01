<?php

use App\Models\SanPham;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class BinhLuan extends Model
{
    protected $table = 'binh_luan';
    public $timestamps = false;

    protected $fillable = [
        'san_pham_id',
        'nguoi_dung_id',
        'parent_id',
        'noi_dung',
        'duyet'
    ];

    protected $casts = [
        'duyet' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'san_pham_id');
    }

    public function nguoiDung()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    // Bình luận cha
    public function parent()
    {
        return $this->belongsTo(BinhLuan::class, 'parent_id');
    }

    // Bình luận con (replies)
    public function replies()
    {
        return $this->hasMany(BinhLuan::class, 'parent_id')->where('duyet', true)->with('nguoiDung');
    }
}
