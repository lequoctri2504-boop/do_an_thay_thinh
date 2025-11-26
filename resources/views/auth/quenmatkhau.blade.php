<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - PhoneShop</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<header class="header">
    <div class="header-top">
        <div class="container">
            <div class="header-top-content">
                <div class="header-left">
                    <a href="#"><i class="fas fa-phone"></i> hotline: 0962371176</a>
                    <a href="#"><i class="fas fa-map-marker-alt"></i> Tìm cửa hàng</a>
                </div>
                <div class="header-right">
                    <a href="{{ route('login') }}"><i class="fas fa-sign-in-alt"></i> Đăng nhập</a>
                </div>
            </div>
        </div>
    </div>
</header>

<div class="breadcrumb">
    <div class="container">
        <a href="{{ route('home') }}">Trang chủ</a>
        <i class="fas fa-chevron-right"></i>
        <span>Quên mật khẩu</span>
    </div>
</div>

<section class="login-form">
    <div class="container">
        <h1>Quên mật khẩu</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('password.update.simple') }}" method="post">
            @csrf
            <div class="form-group">
                <label for="email">Email đăng ký</label>
                <input type="email" id="email" name="email"
                       class="form-control" value="{{ old('email') }}" required>
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu mới</label>
                <input type="password" id="password" name="password"
                       class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password_confirmation">Xác nhận mật khẩu mới</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Đổi mật khẩu</button>
        </form>
    </div>
</section>

</body>
</html>
