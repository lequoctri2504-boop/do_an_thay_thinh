@extends('layouts.staff')

@section('title', 'Thông tin cá nhân')

@section('content')
<section class="dashboard-section active">
    <div class="section-header">
        <h1>Thông tin cá nhân</h1>
    </div>

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
        <div class="alert alert-danger"><ul>@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul></div>
    @endif
    
    <div class="dashboard-card" style="max-width: 700px; margin: 0 auto;">
        
        <form action="{{ route('staff.profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <h3 class="card-header" style="margin-bottom: 20px;"><i class="fas fa-user-edit"></i> Cập nhật hồ sơ</h3>

            <div class="form-group" style="margin-bottom: 15px;">
                <label for="ho_ten">Họ và Tên (*)</label>
                <input type="text" name="ho_ten" id="ho_ten" class="form-control" 
                       value="{{ old('ho_ten', $user->ho_ten) }}" required>
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label for="sdt">Số điện thoại</label>
                <input type="text" name="sdt" id="sdt" class="form-control" 
                       value="{{ old('sdt', $user->sdt) }}">
            </div>
            
            {{-- BỔ SUNG TRƯỜNG ĐỊA CHỈ --}}
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="dia_chi">Địa chỉ</label>
                <input type="text" name="dia_chi" id="dia_chi" class="form-control" 
                       value="{{ old('dia_chi', $user->dia_chi) }}">
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label for="email">Email (*)</label>
                <input type="email" name="email" id="email" class="form-control" 
                       value="{{ old('email', $user->email) }}" required>
            </div>

            <h4 style="margin-top: 30px; border-top: 1px dashed #ddd; padding-top: 15px;">Đổi mật khẩu</h4>

            <div class="form-group" style="margin-bottom: 15px;">
                <label for="password_new">Mật khẩu mới (Để trống nếu không đổi)</label>
                <input type="password" name="password_new" id="password_new" class="form-control">
                @error('password_new') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label for="password_new_confirmation">Xác nhận mật khẩu mới</label>
                <input type="password" name="password_new_confirmation" id="password_new_confirmation" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-save"></i> Lưu thông tin</button>
        </form>
    </div>
</section>
@endsection