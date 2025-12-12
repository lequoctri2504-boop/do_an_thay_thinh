<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class KhuyenMai extends Model
{
    protected $table = 'khuyen_mai'; 
    
    protected $fillable = [
        'ten', 'ma', 'gia_tri', 'ngay_bat_dau', 'ngay_ket_thuc' 
    ];
    
    protected $casts = [
        'ngay_bat_dau' => 'datetime',
        'ngay_ket_thuc' => 'datetime',
    ];
    
    // Thuộc tính ảo để lấy Mã giảm giá (Sử dụng cột 'ma' thực tế)
    public function getMaKhuyenMaiAttribute()
    {
        return $this->ma;
    }
    
    // Thuộc tính ảo để phân loại (Giả định: Nếu có % là PHAN_TRAM, còn lại là GIA_TIEN)
    public function getLoaiGiamGiaAttribute()
    {
        $giaTri = trim($this->gia_tri);
        if (str_ends_with($giaTri, '%')) {
            return 'PHAN_TRAM';
        }
        return 'GIA_TIEN';
    }

    public function getCurrentStatusAttribute()
    {
        if (Carbon::now()->isBefore($this->ngay_bat_dau)) {
            return 'Chưa bắt đầu';
        } elseif (Carbon::now()->isAfter($this->ngay_ket_thuc)) {
            return 'Đã kết thúc';
        }
        return 'Đang diễn ra';
    }
}