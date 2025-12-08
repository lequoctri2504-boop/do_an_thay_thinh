@extends('layouts.admin')

@section('title', 'Cập nhật thông tin cá nhân')

@section('content')
<section class="dashboard-section active">
    <div class="section-header">
        <h1>Hồ sơ cá nhân</h1>
    </div>

    @if(session('success'))
    <div class="alert alert-success" style="padding: 10px; background: #d4edda; color: #155724; margin-bottom: 20px;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger" style="padding: 10px; background: #f8d7da; color: #721c24; margin-bottom: 20px;">
        <ul class="mb-0" style="list-style-type: none; padding-left: 0;">
            @foreach($errors->all() as $error)
            <li>- {{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="dashboard-card" style="max-width: 800px; margin: 0 auto; padding: 30px;">
        <form method="POST" action="{{ route('admin.profile.update') }}">
            @csrf
            @method('PUT')

            <h3 style="font-size: 1.25rem; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">
                Thông tin tài khoản
            </h3>

            <div class="form-group" style="margin-bottom: 15px;">
                <label class="small mb-1 fw-bold">Email (Tên đăng nhập)</label>
                <input class="form-control" type="text" value="{{ $user->email }}" readonly disabled style="background-color: #f5f5f5;">
            </div>

            <div class="form-row" style="display: flex; gap: 15px;">
                <div class="form-group" style="margin-bottom: 15px; flex: 1;">
                    <label class="small mb-1 fw-bold">Họ và tên <span class="text-danger">*</span></label>
                    <input class="form-control" name="ho_ten" type="text"
                        value="{{ old('ho_ten', $user->ho_ten) }}" required>
                </div>
                <div class="form-group" style="margin-bottom: 15px; flex: 1;">
                    <label class="small mb-1 fw-bold">Số điện thoại</label>
                    <input class="form-control" name="sdt" type="text"
                        value="{{ old('sdt', $user->sdt) }}">
                </div>
            </div>

            <hr style="margin: 30px 0;">
            <h3 style="font-size: 1.25rem; color: var(--primary-color); border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">
                <i class="fas fa-key"></i> Đổi mật khẩu (Bỏ trống nếu không đổi)
            </h3>

            <div class="form-row" style="display: flex; gap: 15px;">
                <div class="form-group" style="margin-bottom: 15px; flex: 1;">
                    <label class="small mb-1 fw-bold">Mật khẩu mới</label>
                    <input class="form-control" name="password" type="password" placeholder="Nhập mật khẩu mới...">
                </div>
                <div class="form-group" style="margin-bottom: 15px; flex: 1;">
                    <label class="small mb-1 fw-bold">Xác nhận mật khẩu</label>
                    <input class="form-control" name="password_confirmation" type="password" placeholder="Nhập lại mật khẩu mới...">
                </div>
            </div>

            <div style="text-align: right; margin-top: 30px;">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-save me-1"></i> Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</section>
@endsection