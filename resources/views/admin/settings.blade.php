@extends('layouts.admin')

@section('title', 'Cài đặt hệ thống')

@section('content')
<section class="dashboard-section active">
    <div class="section-header">
        <h1>Cài đặt hệ thống</h1>
    </div>

    <div class="dashboard-card">
        <form class="settings-form">
            @csrf
            <h3>Thông tin website</h3>
            
            <div class="form-group">
                <label>Tên website</label>
                <input type="text" class="form-control" value="PhoneShop">
            </div>

            <div class="form-group">
                <label>Email liên hệ</label>
                <input type="email" class="form-control" value="support@phoneshop.com">
            </div>

            <div class="form-group">
                <label>Số điện thoại</label>
                <input type="text" class="form-control" value="0962371176">
            </div>

            <button type="submit" class="btn btn-primary">Lưu cài đặt</button>
        </form>
    </div>
</section>
@endsection