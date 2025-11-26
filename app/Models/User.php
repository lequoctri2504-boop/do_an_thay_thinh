<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Bắt buộc phải khai báo bảng là nguoi_dung
    protected $table = 'nguoi_dung';

    protected $fillable = [
        'ho_ten',
        'email',
        'mat_khau',
        'sdt',
        'vai_tro',
        'bi_chan',
        'google_id',       // thêm để lưu ID Google
        'facebook_id',     // thêm để lưu ID Facebook (dùng sau)
    ];

    protected $hidden = [
        'mat_khau',
        'remember_token',
    ];

    protected $casts = [
        'bi_chan' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // RẤT QUAN TRỌNG: Laravel tìm cột "password" → phải override lại lấy "mat_khau"
    public function getAuthPassword()
    {
        return $this->mat_khau;
    }
}