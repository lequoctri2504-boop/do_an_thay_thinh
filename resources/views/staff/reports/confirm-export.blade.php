@extends('layouts.staff')

@section('title', 'Xác nhận xuất Báo cáo')

@section('content')
<section class="dashboard-section active">
    <div class="section-header">
        <h1>Xác nhận Xuất Báo cáo Doanh thu</h1>
        <a href="{{ route('staff.reports') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại Báo cáo
        </a>
    </div>

    <div class="dashboard-card" style="max-width: 700px; margin: 0 auto; padding: 30px;">
        <h3 class="card-header" style="margin-bottom: 30px; font-size: 24px;">Thông tin Xuất File</h3>

        {{-- HIỂN THỊ KHOẢNG THỜI GIAN LỌC --}}
        <div style="margin-bottom: 25px; padding: 15px; border: 1px dashed #007bff; border-radius: 8px; background: #f0f8ff;">
            <p style="font-size: 16px; margin-bottom: 5px;">
                <i class="fas fa-calendar-check" style="color: #007bff;"></i> **Khoảng thời gian:** Từ {{ $queryStart->format('d/m/Y') }} đến {{ $queryEnd->format('d/m/Y') }}
            </p>
            <p style="font-size: 16px; margin-bottom: 0;">
                <i class="fas fa-file-invoice" style="color: #007bff;"></i> **Số đơn hàng hoàn thành:** {{ number_format($ordersCount) }} đơn
            </p>
        </div>

        <form action="{{ route('staff.reports.export') }}" method="GET" id="exportForm">
            
            {{-- CHUYỂN CÁC THAM SỐ LỌC ẨN SANG TRANG EXPORT --}}
            <input type="hidden" name="quick_select" value="{{ $selectedQuick }}">
            <input type="hidden" name="start_date" value="{{ $queryStart->format('Y-m-d') }}">
            <input type="hidden" name="end_date" value="{{ $queryEnd->format('Y-m-d') }}">

            {{-- TÙY CHỌN ĐỊNH DẠNG --}}
            <h4 style="margin-top: 30px; margin-bottom: 15px;">1. Chọn định dạng file (*):</h4>
            <div class="form-group" style="margin-bottom: 20px;">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="type" id="export_excel" value="excel" checked>
                    <label class="form-check-label" for="export_excel">
                        <i class="fas fa-file-excel" style="color: green;"></i> Excel (.xlsx)
                    </label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="type" id="export_pdf" value="pdf">
                    <label class="form-check-label" for="export_pdf">
                        <i class="fas fa-file-pdf" style="color: red;"></i> PDF (.pdf)
                    </label>
                </div>
            </div>

            {{-- NỘI DUNG XUẤT (Tùy chọn nâng cao) --}}
            <h4 style="margin-top: 20px; margin-bottom: 15px;">2. Nội dung báo cáo:</h4>
            <div class="form-group" style="margin-bottom: 30px;">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="include_details" value="1" id="include_details" checked>
                    <label class="form-check-label" for="include_details">
                        Bao gồm chi tiết sản phẩm trong từng đơn hàng (Chỉ áp dụng cho Excel)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="include_customer" value="1" id="include_customer">
                    <label class="form-check-label" for="include_customer">
                        Bao gồm thông tin liên hệ đầy đủ của Khách hàng
                    </label>
                </div>
            </div>


            <div style="text-align: center; margin-top: 40px;">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-cloud-download-alt"></i> Tải về Báo cáo
                </button>
            </div>
        </form>
    </div>
</section>
@endsection