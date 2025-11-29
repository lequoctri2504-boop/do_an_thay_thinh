@extends('layouts.admin')
@section('title', 'Cập nhật tài khoản')
@section('content')
<section class="dashboard-section active">
    <div class="section-header">
        <h1>Cập nhật: {{ $user->ho_ten }}</h1>
        <a href="{{ route('admin.accounts') }}" class="btn btn-secondary">Quay lại</a>
    </div>
    <div class="dashboard-card" style="padding: 30px; max-width: 800px; margin: 0 auto;">
        @if ($errors->any())
            <div class="alert alert-danger"><ul>@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul></div>
        @endif
        <form action="{{ route('admin.accounts.update', $user->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="form-group" style="margin-bottom:15px">
                <label>Họ và tên (*)</label>
                <input type="text" name="ho_ten" class="form-control" value="{{ old('ho_ten', $user->ho_ten) }}" required>
            </div>
            <div class="form-group" style="margin-bottom:15px">
                <label>Email (*)</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            </div>
            <div class="form-group" style="margin-bottom:15px">
                <label>Mật khẩu mới (Để trống nếu không đổi)</label>
                <input type="password" name="mat_khau" class="form-control">
            </div>
            <div class="form-group" style="margin-bottom:15px">
                <label>Số điện thoại</label>
                <input type="text" name="sdt" class="form-control" value="{{ old('sdt', $user->sdt) }}">
            </div>
            <div class="form-group" style="margin-bottom:15px">
                <label>Vai trò (*)</label>
                <select name="vai_tro" class="form-control">
                    <option value="KHACH_HANG" {{ $user->vai_tro == 'KHACH_HANG' ? 'selected' : '' }}>Khách hàng</option>
                    <option value="NHAN_VIEN" {{ $user->vai_tro == 'NHAN_VIEN' ? 'selected' : '' }}>Nhân viên</option>
                    <option value="ADMIN" {{ $user->vai_tro == 'ADMIN' ? 'selected' : '' }}>Quản trị viên</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Cập nhật</button>
        </form>
    </div>
</section>
@endsection