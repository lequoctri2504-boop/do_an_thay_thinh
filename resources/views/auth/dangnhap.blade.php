<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - PhoneShop</title>
    <link rel="stylesheet" href="{{ 'css/style.css' }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

    <!-- Header -->
    <header class="header">
        <div class="header-top">
            <div class="container">
                <div class="header-top-content">
                    <div class="header-left">
                        <a href="#"><i class="fas fa-phone"></i> hotline: 0962371176</a>
                        <a href="#"><i class="fas fa-map-marker-alt"></i> Tìm cửa hàng</a>
                    </div>
                    <div class="header-right">
                        {{-- link sang trang đăng ký --}}
                        <a href="{{ route('register') }}"><i class="fas fa-user-plus"></i> Đăng ký</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="header-main">
            <div class="container">
                <div class="header-main-content">
                    <div class="logo">
                        <a href="{{ route('home') }}">
                            <span><img src="logo_LQT1.png" alt="" width="70px"></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <div class="container">
            <a href="{{ route('home') }}">Trang chủ</a>
            <i class="fas fa-chevron-right"></i>
            <span>Đăng nhập</span>
        </div>
    </div>

    <!-- Login Form -->
    <section class="login-form">
        <div class="container">
            <h1>Đăng Nhập Tài Khoản</h1>

            {{-- Hiển thị lỗi --}}
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('login.post') }}" method="post">
                @csrf
                <div class="form-group">
                    <label for="email">Email</label>
                    <input value="{{ old('email') }}" type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>


                <button type="submit" class="btn btn-primary btn-block">Đăng Nhập</button>
            </form>
            <div class="form-group d-flex justify-content-between align-items-center">
                <div></div>
                <a href="{{ route('password.request') }}">Quên mật khẩu?</a>
            </div>

            <p>Chưa có tài khoản?
                <a href="{{ route('register') }}">Đăng ký ngay</a>
            </p>

            <br>
            <!-- <div class="divider">
                <span>HOẶC</span>
            </div> -->

            <!-- Google and Facebook Login Buttons (hiện để trang trí) -->
            <!-- <div class="social-login-buttons">
                <button class="btn btn-google" type="button">
                    <i class="fab fa-google"></i> Đăng nhập với Google
                </button>
                <button class="btn btn-facebook" type="button">
                    <i class="fab fa-facebook"></i> Đăng nhập với Facebook
                </button>
            </div> -->

            <div class="text-center mt-4">
                <p class="text-muted">Hoặc đăng nhập nhanh bằng</p>

                <!-- <a href="{{ route('social.login', 'google') }}" class="btn btn-danger btn-sm">
                    Google
                </a>
                <a href="{{ route('social.login', 'facebook') }}" class="btn btn-primary btn-sm ms-2">
                    Facebook
                </a> -->
                <a href="{{ route('social.login', 'google') }}" class="btn btn-danger w-100 mb-2">
                    <i class="fab fa-google"></i> Đăng nhập bằng Google
                </a>

                <!-- Nếu muốn thêm Facebook -->
                <a href="{{ route('social.login', 'facebook') }}" class="btn btn-primary w-100">
                    <i class="fab fa-facebook"></i> Đăng nhập bằng Facebook
                </a>
            </div>

        </div>
    </section>

</body>

</html>