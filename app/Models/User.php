<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'nguoi_dung';  // ← Trỏ đến bảng nguoi_dung
    
    protected $fillable = [
        'ho_ten',
        'email',
        'mat_khau',
        'sdt',
        'dia_chi',
        'vai_tro',
        'trang_thai',
        'google_id',
        'facebook_id'
    ];

    protected $hidden = [
        'mat_khau',
        'remember_token',
    ];

    // Sử dụng cột mat_khau thay vì password
    public function getAuthPassword()
    {
        return $this->mat_khau;
    }

    // Relationships
    public function gioHang()
    {
        return $this->hasOne(GioHang::class, 'nguoi_dung_id');
    }

    public function donHang()
    {
        return $this->hasMany(DonHang::class, 'nguoi_dung_id');
    }

    public function danhGia()
    {
        return $this->hasMany(DanhGia::class, 'nguoi_dung_id');
    }

    public function binhLuan()
    {
        return $this->hasMany(BinhLuan::class, 'nguoi_dung_id');
    }
}