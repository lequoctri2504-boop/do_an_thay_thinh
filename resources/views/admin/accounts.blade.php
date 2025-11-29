@extends('layouts.admin')

@section('title', 'Quản lý tài khoản')

@section('content')
<section class="dashboard-section active" id="accounts">
    <div class="section-header">
        <h1>Quản lý tài khoản</h1>
        <a href="{{ route('admin.accounts.create') }}" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Thêm tài khoản
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="padding: 10px; background: #d4edda; color: #155724; margin-bottom: 20px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger" style="padding: 10px; background: #f8d7da; color: #721c24; margin-bottom: 20px;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <div class="dashboard-card">
        <div class="search-box" style="margin-bottom: 20px; padding: 15px; background: #f8f9fa;">
            <form action="{{ route('admin.accounts') }}" method="GET" style="display: flex; gap: 10px;">
                <input type="text" name="keyword" class="form-control" 
                       placeholder="Tìm theo tên, email hoặc SĐT..." 
                       value="{{ request('keyword') }}" style="width: 300px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Tìm</button>
                @if(request('keyword'))
                    <a href="{{ route('admin.accounts') }}" class="btn btn-secondary">Hủy lọc</a>
                @endif
            </form>
        </div>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>SĐT</th>
                        <th>Vai trò</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>#{{ $user->id }}</td>
                        <td>
                            <strong>{{ $user->ho_ten }}</strong> </td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->sdt ?? '---' }}</td> <td>
                            @if($user->vai_tro == 'ADMIN') <span style="color:red;font-weight:bold">Admin</span>
                            @elseif($user->vai_tro == 'NHAN_VIEN') <span style="color:blue">Nhân viên</span>
                            @else <span style="color:green">Khách hàng</span>
                            @endif
                        </td>
                        <td>{{ $user->created_at ? $user->created_at->format('d/m/Y') : '' }}</td>
                        <td>
                            <a href="{{ route('admin.accounts.edit', $user->id) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                            @if(Auth::id() != $user->id) 
                                <form action="{{ route('admin.accounts.destroy', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Xóa tài khoản này?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">Không tìm thấy tài khoản nào.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrapper" style="margin-top:20px;float:right">{{ $users->links() }}</div>
    </div>
</section>
@endsection