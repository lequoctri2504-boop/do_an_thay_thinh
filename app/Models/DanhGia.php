<?php

use App\Models\SanPham;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class DanhGia extends Model
{
    protected $table = 'danh_gia';
    public $timestamps = false;

    protected $fillable = [
        'san_pham_id',
        'nguoi_dung_id',
        'so_sao',
        'tieu_de',
        'noi_dung',
        'duyet'
    ];

    protected $casts = [
        'so_sao' => 'integer',
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
}
