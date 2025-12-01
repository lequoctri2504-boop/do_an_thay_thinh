<?php

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiaChi extends Model
{
    use SoftDeletes;

    protected $table = 'dia_chi';

    protected $fillable = [
        'nguoi_dung_id',
        'ten_nguoi_nhan',
        'sdt',
        'dia_chi',
        'tinh_thanh',
        'quan_huyen',
        'phuong_xa',
        'mac_dinh'
    ];

    protected $casts = [
        'mac_dinh' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function nguoiDung()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }
}
