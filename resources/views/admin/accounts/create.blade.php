@extends('layouts.admin')
@section('title', 'Thêm tài khoản mới')
@section('content')
<section class="dashboard-section active">
    <div class="section-header">
        <h1>Thêm tài khoản</h1>
        <a href="{{ route('admin.accounts') }}" class="btn btn-secondary">Quay lại</a>
    </div>
    <div class="dashboard-card" style="padding: 30px; max-width: 800px; margin: 0 auto;">
        @if ($errors->any())
            <div class="alert alert-danger"><ul>@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul></div>
        @endif
        <form action="{{ route('admin.accounts.store') }}" method="POST">
            @csrf
            <div class="form-group" style="margin-bottom:15px">
                <label>Họ và tên (*)</label>
                <input type="text" name="ho_ten" class="form-control" value="{{ old('ho_ten') }}" required>
            </div>
            <div class="form-group" style="margin-bottom:15px">
                <label>Email (*)</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>
            <div class="form-group" style="margin-bottom:15px">
                <label>Mật khẩu (*)</label>
                <input type="password" name="mat_khau" class="form-control" required>
            </div>
            <div class="form-group" style="margin-bottom:15px">
                <label>Số điện thoại</label>
                <input type="text" name="sdt" class="form-control" value="{{ old('sdt') }}">
            </div>
            <div class="form-group" style="margin-bottom:15px">
                <label>Vai trò (*)</label>
                <select name="vai_tro" class="form-control">
                    <option value="KHACH_HANG">Khách hàng</option>
                    <option value="NHAN_VIEN">Nhân viên</option>
                    <option value="ADMIN">Quản trị viên</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Lưu tài khoản</button>
        </form>
    </div>
</section>
@endsection