@extends('layouts.staff')

@section('title', 'Quản lý khách hàng')

@section('content')
<section class="dashboard-section active">

    <div class="section-header">
        <h1>Quản lý khách hàng</h1>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    
    <div class="dashboard-card">
        
        {{-- Search Bar (Thêm chức năng tìm kiếm) --}}
        <div class="search-box" style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
            <form action="{{ route('staff.customers') }}" method="GET" style="display: flex; gap: 15px; align-items: center;">
                <input type="text" name="keyword" class="form-control" 
                       placeholder="Tìm theo tên, email hoặc SĐT..." 
                       value="{{ request('keyword') ?? '' }}" style="width: 350px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Tìm</button>
                @if(request('keyword'))
                    <a href="{{ route('staff.customers') }}" class="btn btn-secondary">Hủy tìm kiếm</a>
                @endif
            </form>
        </div>

        <div class="table-responsive">

            <table class="data-table">
                <thead>
                    <tr>
                        {{-- Không cần sắp xếp phức tạp, chỉ cần tiêu đề --}}
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>SĐT</th>
                        <th>Ngày tạo</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($customers as $c)
                    <tr>
                        <td><strong>{{ $c->ho_ten }}</strong></td>
                        <td>{{ $c->email }}</td>
                        <td>{{ $c->sdt ?? '---' }}</td>
                        <td>{{ \Carbon\Carbon::parse($c->created_at)->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center">Không có khách hàng</td></tr>
                    @endforelse
                </tbody>
            </table>

        </div>

        {{-- Phân trang sử dụng template custom --}}
        <div class="pagination-wrapper">
            {{ $customers->appends(request()->all())->links('vendor.pagination.admin-custom-pagination') }}
        </div>
    </div>

</section>
@endsection