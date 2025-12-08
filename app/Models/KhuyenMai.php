<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class KhuyenMai extends Model
{
    protected $table = 'khuyen_mai'; // Bảng mà Model này tương tác
    
    protected $fillable = [
        'ten', 'ma', 'gia_tri', 'ngay_bat_dau', 'ngay_ket_thuc'
    ];
    
    // FIX QUAN TRỌNG: Dùng $casts để đảm bảo các cột này là đối tượng Carbon/DateTime
    protected $casts = [
        'ngay_bat_dau' => 'datetime',
        'ngay_ket_thuc' => 'datetime',
    ];
    
    // Tùy chỉnh hiển thị trạng thái động (Attribute Accessor)
    public function getCurrentStatusAttribute()
    {
        // Nhờ $casts, $this->ngay_bat_dau và $this->ngay_ket_thuc đã là đối tượng Carbon
        if (Carbon::now()->isBefore($this->ngay_bat_dau)) {
            return 'Chưa bắt đầu';
        } elseif (Carbon::now()->isAfter($this->ngay_ket_thuc)) {
            return 'Đã kết thúc';
        }
        return 'Đang diễn ra';
    }
}