@extends('layouts.app')

@section('title', 'Thông tin cá nhân')

@section('content')
<div class="breadcrumb">
    <div class="container">
        <a href="{{ route('home') }}">Trang chủ</a>
        <i class="fas fa-chevron-right"></i>
        <span>Tài khoản</span>
    </div>
</div>

<section class="account-page">
    <div class="container">
        <div class="account-layout">
            @include('customer.sidebar')

            <div class="account-content">
                <h2>Thông tin cá nhân</h2>
                
                <form class="profile-form">
                    @csrf
                    <div class="form-group">
                        <label>Họ và tên</label>
                        <input type="text" class="form-control" value="{{ $user->ho_ten }}">
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" value="{{ $user->email }}" readonly>
                    </div>

                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <input type="text" class="form-control" value="{{ $user->sdt }}">
                    </div>

                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection