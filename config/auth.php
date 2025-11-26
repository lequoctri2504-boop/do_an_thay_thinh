<?php

return [

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'nguoi_dung',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'nguoi_dung',    // ← sửa thành nguoi_dung
        ],
    ],

    'providers' => [
        // XÓA HOẶC COMMENT DÒNG users CŨ ĐI
        // 'users' => [
        //     'driver' => 'eloquent',
        //     'model' => App\Models\User::class,
        // ],

        // THÊM MỚI CÁI NÀY – CHÍNH LÀ BẢNG CỦA BẠN
        'nguoi_dung' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
    ],

    'passwords' => [
        'nguoi_dung' => [
            'provider' => 'nguoi_dung',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

];