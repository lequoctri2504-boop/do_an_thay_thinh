<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class AdminReportExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $data;
    protected $type;

    /**
     * Khởi tạo class với dữ liệu và loại báo cáo
     */
    public function __construct($data, $type)
    {
        $this->data = $data;
        $this->type = $type;
    }

    /**
     * Trả về view để Excel render thành file
     */
    public function view(): View
    {
        // Sử dụng chung một template hoặc tách riêng tùy ý
        // Ở đây tôi dùng chung template và truyền biến $type vào để xử lý trong Blade
        return view('admin.reports.export_excel', [
            'data' => $this->data,
            'type' => $this->type
        ]);
    }

    /**
     * Đặt tên cho Sheet trong file Excel
     */
    public function title(): string
    {
        return match($this->type) {
            'doanh_thu'  => 'Báo cáo Doanh thu',
            'don_hang'   => 'Danh sách Đơn hàng',
            'san_pham'   => 'Thống kê Sản phẩm',
            'khach_hang' => 'Danh sách Khách hàng',
            default      => 'Báo cáo Thống kê',
        };
    }
}