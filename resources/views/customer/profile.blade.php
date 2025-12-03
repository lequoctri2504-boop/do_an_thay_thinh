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
        {{-- Bắt đầu bố cục hai cột: account-layout --}}
        <div class="account-layout"> 
            
            {{-- 1. Sidebar bên trái --}}
            @include('customer.sidebar') 

            {{-- 2. Nội dung bên phải: account-content --}}
            <div class="account-content">
                {{-- Header Nội dung --}}
                <div class="account-header-info">
                    <h2><i class="fas fa-user"></i> Thông tin cá nhân</h2>
                    {{-- Thẻ trạng thái/Vai trò người dùng --}}
                    @php
                        $role = 'Khách hàng';
                        if (Auth::check()) {
                            if (Auth::user()->is_admin) {
                                $role = 'Quản trị viên (Admin)';
                            } elseif (Auth::user()->is_staff) {
                                $role = 'Nhân viên (Staff)';
                            }
                        }
                    @endphp
                    <div class="account-status">
                        Trạng thái: 
                        <span class="status-badge status-{{ strtolower(str_replace([' ', '(', ')'], ['-', '', ''], $role)) }}">
                            {{ $role }}
                        </span>
                    </div>
                </div>

                {{-- Hiển thị thông báo và lỗi --}}
                @if(session('success')) 
                    <div class="alert alert-success" style="padding: 10px; margin-bottom: 20px;">{{ session('success') }}</div> 
                @endif
                @if(session('error')) 
                    <div class="alert alert-danger" style="padding: 10px; margin-bottom: 20px;">{{ session('error') }}</div> 
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger" style="padding: 10px; margin-bottom: 20px;">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                {{-- Form chính: Cập nhật thông tin cơ bản và Đổi mật khẩu --}}
                {{-- $user được truyền vào từ Controller --}}
                <form action="{{ route('customer.update') }}" method="POST" class="profile-form">
                    @csrf
                    @method('PUT')
                    
                    <h3 class="form-section-title">Thông tin cơ bản</h3>

                    <div class="form-group">
                        <label>Họ và tên <span class="required">*</span></label>
                        <input type="text" name="ten" class="form-control" value="{{ old('ten', $user->ho_ten) }}" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Số điện thoại</label>
                            <input type="tel" name="sdt" class="form-control" value="{{ old('sdt', $user->sdt) }}">
                        </div>
                        <div class="form-group">
                            <label>Email <span class="required">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Địa chỉ</label>
                        <textarea name="dia_chi" class="form-control" rows="2">{{ old('dia_chi', $user->dia_chi) }}</textarea>
                    </div>
                    
                    <hr class="form-divider">

                    <h3 class="form-section-title">Đổi mật khẩu (Chỉ nhập nếu muốn đổi)</h3>
                    
                    <div class="form-group">
                        <label>Mật khẩu hiện tại</label>
                        <input type="password" name="mat_khau_cu" class="form-control">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Mật khẩu mới</label>
                            <input type="password" name="mat_khau_moi" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Xác nhận mật khẩu mới</label>
                            <input type="password" name="mat_khau_moi_confirmation" class="form-control">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-large mt-4">
                        <i class="fas fa-save"></i> Cập nhật thông tin
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection