@extends('layouts.staff')

@section('title', 'Chi tiết & Cập nhật đơn hàng #' . $order->ma)

@section('content')
<section class="dashboard-section active">
    <div class="section-header">
        <h1>Chi tiết & Cập nhật đơn hàng #{{ $order->ma }}</h1>
        <a href="{{ route('staff.orders') }}" class="btn btn-secondary">Quay lại danh sách</a>
    </div>

    {{-- Hiển thị thông báo và lỗi --}}
    @if(session('success'))
    <div class="alert alert-success" style="padding: 10px; background: #d4edda; color: #155724; margin-bottom: 20px;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger" style="padding: 10px; background: #f8d7da; color: #721c24; margin-bottom: 20px;">
        <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
    </div>
    @endif
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
    </div>
    @endif

    <div class="dashboard-row" style="gap: 20px;">

        {{-- Cột Trái (col-8): Chi tiết sản phẩm và Tổng tiền --}}
        <div class="col-8">
            {{-- Card 1: Danh sách sản phẩm và Tổng tiền --}}
            <div class="dashboard-card" style="margin-bottom: 20px;">
                <h3 class="card-header"><i class="fas fa-box-open"></i> Danh sách sản phẩm</h3>

                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Biến thể</th>
                                <th>Giá</th>
                                <th>SL</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->chiTiet as $item)
                            <tr>
                                <td>{{ $item->ten_sp_ghi_nhan }}</td>
                                <td>
                                    @if($item->bienThe)
                                    {{ $item->bienThe->mau_sac ?? 'N/A' }} - {{ $item->bienThe->dung_luong_gb ?? 'N/A' }}GB
                                    @else
                                    N/A
                                    @endif
                                </td>
                                <td>{{ number_format($item->gia) }}₫</td>
                                <td>{{ $item->so_luong }}</td>
                                <td>{{ number_format($item->thanh_tien) }}₫</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Tổng tiền chi tiết --}}
<div style="clear: both; margin-top: 20px; display: flex; justify-content: flex-end;">
    {{-- Container mới sử dụng border và padding để tạo hộp chứa rõ ràng --}}
    <div class="order-totals" style="max-width: 400px; padding: 15px; border: 1px solid var(--border-color); border-radius: 8px; background: var(--secondary-color);">
        
        <div class="total-row" style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px;">
            <span>Tạm tính:</span> 
            <span>{{ number_format($order->tong_tien) }}₫</span>
        </div>
        
        <div class="total-row" style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px;">
            <span>Giảm giá:</span> 
            <span class="text-danger">-{{ number_format($order->giam_gia) }}₫</span>
        </div>
        
        <div class="total-row" style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px;">
            <span>Phí vận chuyển:</span> 
            <span>{{ number_format($order->phi_van_chuyen) }}₫</span>
        </div>
        
        {{-- Tổng Thanh toán (Grand Total) --}}
        <div class="total-row grand-total" style="display: flex; justify-content: space-between; border-top: 2px solid var(--border-color); padding-top: 10px; margin-top: 10px;">
            <span style="font-weight: bold; font-size: 16px;">TỔNG THANH TOÁN:</span> 
            <span class="price-final" style="color: var(--primary-color); font-size: 1.5em; font-weight: bold;">{{ number_format($order->thanh_tien) }}₫</span>
        </div>
    </div>
</div>
            </div>

            {{-- Card 2: Thông tin Khách hàng --}}
            <div class="dashboard-card">
                <h3 class="card-header"><i class="fas fa-user-circle"></i> Thông tin Khách hàng & Đặt hàng</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; font-size: 14px;">
                    <div>
                        <p><strong>Mã đơn:</strong> #{{ $order->ma }}</p>
                        <p><strong>Ngày đặt:</strong> {{ \Carbon\Carbon::parse($order->ngay_dat)->format('d/m/Y H:i') }}</p>
                        <p><strong>Khách hàng:</strong> {{ $order->nguoiDung->ho_ten ?? 'Khách lẻ' }}</p>
                        <p><strong>Email:</strong> {{ $order->nguoiDung->email ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p><strong>Trạng thái TT:</strong> <span class="status-badge payment-{{ $order->trang_thai_tt == 'DA_TT' ? 'approved' : 'pending' }}">{{ $order->trang_thai_tt }}</span></p>
                        <p><strong>Người nhận:</strong> {{ $order->ten_nguoi_nhan }}</p>
                        <p><strong>SĐT ĐH:</strong> {{ $order->sdt_nguoi_nhan }}</p>
                        <p><strong>Phương thức TT:</strong> {{ $order->phuong_thuc_tt }}</p>
                    </div>
                </div>
                @if($order->ghi_chu)
                <h4 style="margin-top: 20px; border-top: 1px dashed #eee; padding-top: 10px;">Ghi chú khách hàng:</h4>
                <p class="text-muted">{{ $order->ghi_chu }}</p>
                @endif
            </div>
        </div>

        {{-- Cột Phải (col-4): Form Cập nhật trạng thái và Thao tác --}}
        <div class="col-4">
            {{-- Card 3: Cập nhật trạng thái --}}
            <div class="dashboard-card" style="margin-bottom: 20px;">
                <h3 class="card-header"><i class="fas fa-sync-alt"></i> Cập nhật Trạng thái</h3>
                <form action="{{ route('staff.orders.update', $order->id) }}" method="POST">
                    @csrf @method('PUT')

                    {{-- FIX LỖI: Thêm các hidden input để thỏa mãn validation của Controller (vì 2 form update độc lập) --}}
                    <input type="hidden" name="ten_nguoi_nhan" value="{{ $order->ten_nguoi_nhan }}">
                    <input type="hidden" name="sdt_nguoi_nhan" value="{{ $order->sdt_nguoi_nhan }}">
                    <input type="hidden" name="dia_chi_giao" value="{{ $order->dia_chi_giao }}">
                    <input type="hidden" name="ghi_chu" value="{{ $order->ghi_chu }}">

                    {{-- Trạng thái Đơn hàng --}}
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Trạng thái Đơn hàng</label>
                        <select name="trang_thai" class="form-control" required>
                            <option value="DANG_XU_LY" {{ $order->trang_thai == 'DANG_XU_LY' ? 'selected' : '' }}>DANG_XU_LY (Đang chờ)</option>
                            <option value="DANG_GIAO" {{ $order->trang_thai == 'DANG_GIAO' ? 'selected' : '' }}>DANG_GIAO (Đang giao)</option>
                            <option value="HOAN_THANH" {{ $order->trang_thai == 'HOAN_THANH' ? 'selected' : '' }}>HOAN_THANH (Hoàn thành)</option>
                            <option value="HUY" {{ $order->trang_thai == 'HUY' ? 'selected' : '' }}>HUY (Đã hủy)</option>
                        </select>
                    </div>

                    {{-- Trạng thái Thanh toán --}}
                    <div class="form-group" style="margin-bottom: 25px;">
                        <label>Trạng thái Thanh toán</label>
                        <select name="trang_thai_tt" class="form-control" required>
                            <option value="CHUA_TT" {{ $order->trang_thai_tt == 'CHUA_TT' ? 'selected' : '' }}>CHUA_TT (Chưa thanh toán)</option>
                            <option value="DA_TT" {{ $order->trang_thai_tt == 'DA_TT' ? 'selected' : '' }}>DA_TT (Đã thanh toán)</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-save"></i> Cập nhật trạng thái</button>
                </form>
            </div>

            {{-- Card 4: Cập nhật Địa chỉ --}}
            <div class="dashboard-card" style="margin-top: 20px;">
                <h3 class="card-header"><i class="fas fa-map-marker-alt"></i> Cập nhật Địa chỉ</h3>
                <form action="{{ route('staff.orders.update', $order->id) }}" method="POST">
                    @csrf @method('PUT')
                    {{-- Gửi kèm trạng thái hiện tại (hidden) --}}
                    <input type="hidden" name="trang_thai" value="{{ $order->trang_thai }}">
                    <input type="hidden" name="trang_thai_tt" value="{{ $order->trang_thai_tt }}">

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Tên người nhận</label>
                        <input type="text" name="ten_nguoi_nhan" class="form-control" value="{{ old('ten_nguoi_nhan', $order->ten_nguoi_nhan) }}" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>SĐT</label>
                        <input type="text" name="sdt_nguoi_nhan" class="form-control" value="{{ old('sdt_nguoi_nhan', $order->sdt_nguoi_nhan) }}" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Địa chỉ Giao</label>
                        <textarea name="dia_chi_giao" class="form-control" rows="2" required>{{ old('dia_chi_giao', $order->dia_chi_giao) }}</textarea>
                    </div>
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Ghi chú</label>
                        <textarea name="ghi_chu" class="form-control" rows="2">{{ old('ghi_chu', $order->ghi_chu) }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-secondary btn-block"><i class="fas fa-map-pin"></i> Cập nhật địa chỉ</button>
                </form>
            </div>

            {{-- Nút Xóa (Soft Delete) --}}
            <form action="{{ route('staff.orders.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn XÓA VĨNH VIỄN (soft delete) đơn hàng này? Việc này sẽ hoàn lại tồn kho nếu đơn hàng chưa hủy.');" style="margin-top: 20px;">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-block"><i class="fas fa-trash-alt"></i> Xóa đơn hàng (Soft Delete)</button>
            </form>
        </div>
    </div>
</section>
@endsection