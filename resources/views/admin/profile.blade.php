@extends('layouts.admin')

@section('title', 'Cập nhật thông tin cá nhân')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Hồ sơ cá nhân</h1>
    <!-- <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Hồ sơ</li>
    </ol> -->

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0 pl-3">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <!-- <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-image me-1"></i> Ảnh đại diện
                </div>
                <div class="card-body text-center">
                    <img class="img-account-profile rounded-circle mb-2"
                        src="{{ asset('img/logo_LQT1.png') }}"
                        alt="Avatar" style="width: 150px; height: 150px; object-fit: cover;">
                    <div class="small font-italic text-muted mb-4">Ảnh đại diện mặc định</div>
                    <h5 class="font-weight-bold">{{ $user->ho_ten }}</h5>
                    <div class="badge bg-primary">{{ $user->vai_tro }}</div>
                </div>
            </div>
        </div> -->

        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user-edit me-1"></i> Chỉnh sửa thông tin
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="small mb-1 fw-bold">Email (Tên đăng nhập)</label>
                            <input class="form-control" type="text" value="{{ $user->email }}" readonly disabled style="background-color: #e9ecef;">
                        </div>

                        <div class="row gx-3 mb-3">
                            <div class="col-md-6">
                                <label class="small mb-1 fw-bold">Họ và tên <span class="text-danger">*</span></label>
                                <input class="form-control" name="ho_ten" type="text"
                                    value="{{ old('ho_ten', $user->ho_ten) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="small mb-1 fw-bold">Số điện thoại</label>
                                <input class="form-control" name="sdt" type="text"
                                    value="{{ old('sdt', $user->sdt) }}">
                            </div>
                        </div>

                        <hr class="my-4">
                        <h5 class="text-primary"><i class="fas fa-key"></i> Đổi mật khẩu (Bỏ trống nếu không đổi)</h5>

                        <div class="row gx-3 mb-3">
                            <div class="col-md-6">
                                <label class="small mb-1 fw-bold">Mật khẩu mới</label>
                                <input class="form-control" name="password" type="password" placeholder="Nhập mật khẩu mới...">
                            </div>
                            <div class="col-md-6">
                                <label class="small mb-1 fw-bold">Xác nhận mật khẩu</label>
                                <input class="form-control" name="password_confirmation" type="password" placeholder="Nhập lại mật khẩu mới...">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-save me-1"></i> Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection