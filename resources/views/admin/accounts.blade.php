@extends('layouts.admin')

@section('title', 'Quản lý tài khoản - PhoneShop')

@section('content')
<section class="dashboard-section active" id="accounts">
    <div class="section-header">
        <h1>Quản lý tài khoản</h1>
        <div class="header-actions">
            <input type="text" class="form-control" placeholder="Tìm kiếm tài khoản...">
            <button class="btn btn-primary"><i class="fas fa-user-plus"></i> Thêm tài khoản</button>
        </div>
    </div>

    <div class="account-tabs">
        <button class="tab-btn active">Tất cả ({{ $users->total() }})</button>
        <button class="tab-btn">Admin ({{ $users->where('vai_tro', 'ADMIN')->count() }})</button>
        <button class="tab-btn">Nhân viên ({{ $users->where('vai_tro', 'NHAN_VIEN')->count() }})</button>
        <button class="tab-btn">Khách hàng ({{ $users->where('vai_tro', 'KHACH_HANG')->count() }})</button>
    </div>

    <div class="dashboard-card">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                <tr>
                    <th><input type="checkbox"></th>
                    <th>Tên</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Vai trò</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse($users as $user)
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><strong>{{ $user->ho_ten }}</strong></td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->sdt }}</td>
                        <td>
                            @if($user->vai_tro === 'ADMIN')
                                <span class="role-badge admin">Admin</span>
                            @elseif($user->vai_tro === 'NHAN_VIEN')
                                <span class="role-badge staff">Nhân viên</span>
                            @else
                                <span class="role-badge customer">Khách hàng</span>
                            @endif
                        </td>
                        <td>
                            @if($user->bi_chan)
                                <span class="status-badge inactive">Bị khóa</span>
                            @else
                                <span class="status-badge active">Hoạt động</span>
                            @endif
                        </td>
                        <td>{{ $user->created_at?->format('d/m/Y') }}</td>
                        <td>
                            <button class="btn btn-sm btn-primary" title="Xem / Sửa">
                                <i class="fas fa-edit"></i>
                            </button>
                            @if(!$user->bi_chan)
                                <button class="btn btn-sm btn-danger" title="Khóa">
                                    <i class="fas fa-lock"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Chưa có tài khoản nào.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $users->links() }}
        </div>
    </div>
</section>
@endsection
