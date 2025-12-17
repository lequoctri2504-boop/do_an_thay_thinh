<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class StaffRevenueExport implements FromView, WithTitle, ShouldAutoSize
{
    private $orders;
    private $startDate;
    private $endDate;

    public function __construct($orders, $startDate, $endDate)
    {
        $this->orders = $orders;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function view(): View
    {
        // Trỏ đến view Blade sẽ được render thành Excel
        return view('exports.staff_revenue_excel', [
            'ordersToReport' => $this->orders,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }

    public function title(): string
    {
        return 'Báo cáo Doanh thu';
    }
}