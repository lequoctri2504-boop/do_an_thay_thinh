<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GioHang extends Model
{
    protected $table = 'gio_hang';
    
    protected $fillable = ['nguoi_dung_id'];
    
    public function nguoiDung()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }
    
    public function chiTiet()
    {
        return $this->hasMany(GioHangChiTiet::class, 'gio_hang_id');
    }
}