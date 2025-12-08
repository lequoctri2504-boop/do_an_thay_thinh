<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaiViet extends Model
{
    use SoftDeletes;
    
    protected $table = 'bai_viet';
    
    protected $fillable = [
        'tieu_de', 'slug', 'mo_ta_ngan', 'noi_dung', 
        'hinh_anh_chinh', 'nguoi_dung_id', 'trang_thai', 'luot_xem'
    ];
    
    public function nguoiDung()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }
}