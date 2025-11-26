<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký - PhoneShop</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
    .btn-primary {
        background-color: #b40000 !important;
        border-color: #b40000 !important;
        font-weight: 600;
        padding: 10px;
        font-size: 1.1rem;
    }
</style>

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
                        {{-- link sang đăng nhập --}}
                        <a href="{{ route('login') }}"><i class="fas fa-sign-in-alt"></i> Đăng nhập</a>
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
            <span>Đăng ký</span>
        </div>
    </div>

    <!-- Registration Form -->
    <section class="registration-form">
        <div class="container">
            <h1>Đăng Ký Tài Khoản</h1>

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

            <!-- <form action="{{ route('register.post') }}" method="post">
                @csrf
                <div class="form-group">
                    <label for="name">Họ và Tên</label>
                    <input type="text" id="name" name="name"
                        value="{{ old('name') }}" class="form-control" required>
                </div>

                {{-- SĐT mới thêm --}}
                <div class="form-group">
                    <label for="sdt">Số điện thoại</label>
                    <input type="text" id="sdt" name="sdt"
                        value="{{ old('sdt') }}" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email"
                        value="{{ old('email') }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <input type="password" id="password" name="password"
                        class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Xác nhận mật khẩu</label>
                    <input type="password" id="password_confirmation"
                        name="password_confirmation" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Đăng Ký</button>
            </form> -->
            
            <form action="{{ route('register.post') }}" method="post">
                @csrf

                <div class="form-group">
                    <label for="name">Họ và Tên</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control"
                        value="{{ old('name') }}"
                        required>
                </div>

                <div class="form-group">
                    <label for="sdt">Số điện thoại</label>
                    <input
                        type="text"
                        id="sdt"
                        name="sdt"
                        class="form-control"
                        value="{{ old('sdt') }}"
                        required
                        pattern="^0[0-9]{9,10}$"
                        title="Số điện thoại phải bắt đầu bằng 0 và gồm 10 đến 11 chữ số">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        value="{{ old('email') }}"
                        required>
                </div>

                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        required>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Xác nhận mật khẩu</label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="form-control"
                        required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Đăng Ký</button>
            </form>

            <p>Đã có tài khoản?
                <a href="{{ route('login') }}">Đăng nhập ngay</a>
            </p>
        </div>
    </section>

</body>

</html>