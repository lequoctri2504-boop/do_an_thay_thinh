@extends('layouts.admin')

@section('title', 'Quản lý Tài khoản - PhoneShop')

@section('content')
<section class="dashboard-section active" id="accounts">
    <div class="section-header">
        <h1>Quản lý Tài khoản</h1>
        <div class="header-actions" style="gap: 15px;">
            <a href="{{ route('admin.accounts.create') }}" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Thêm tài khoản
            </a>
        </div>
    </div>
    
    {{-- Hiển thị thông báo --}}
    @if (session('success'))
        <div class="alert alert-success" style="padding: 10px; background: #d4edda; color: #155724; margin-bottom: 20px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger" style="padding: 10px; background: #f8d7da; color: #721c24; margin-bottom: 20px;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <div class="dashboard-card">
        {{-- Thanh Lọc và Tìm kiếm --}}
        <div class="search-box" style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
            <form action="{{ route('admin.accounts') }}" method="GET" style="display: flex; gap: 15px; align-items: center;">
                {{-- Lọc Vai trò --}}
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.accounts', array_merge(request()->except(['vai_tro', 'page']), ['vai_tro' => ''])) }}" 
                       class="btn btn-sm {{ empty($vaiTro) ? 'btn-primary' : 'btn-outline' }}">
                        Tất cả
                    </a>
                    <a href="{{ route('admin.accounts', array_merge(request()->except(['vai_tro', 'page']), ['vai_tro' => 'ADMIN'])) }}" 
                       class="btn btn-sm {{ $vaiTro == 'ADMIN' ? 'btn-danger' : 'btn-outline-danger' }}">
                        Admin
                    </a>
                    <a href="{{ route('admin.accounts', array_merge(request()->except(['vai_tro', 'page']), ['vai_tro' => 'NHAN_VIEN'])) }}" 
                       class="btn btn-sm {{ $vaiTro == 'NHAN_VIEN' ? 'btn-info' : 'btn-outline-info' }}">
                        Nhân viên
                    </a>
                    <a href="{{ route('admin.accounts', array_merge(request()->except(['vai_tro', 'page']), ['vai_tro' => 'KHACH_HANG'])) }}" 
                       class="btn btn-sm {{ $vaiTro == 'KHACH_HANG' ? 'btn-success' : 'btn-outline-success' }}">
                        Khách hàng
                    </a>
                </div>
                
                {{-- Tìm kiếm từ khóa --}}
                <input type="text" name="keyword" class="form-control" 
                       placeholder="Tìm theo tên, email hoặc SĐT..." 
                       value="{{ request('keyword') }}" style="width: 300px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Tìm</button>
                @if(request('keyword') || $vaiTro)
                    <a href="{{ route('admin.accounts') }}" class="btn btn-secondary">Hủy lọc</a>
                @endif
            </form>
        </div>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        {{-- Hàm tạo URL sắp xếp --}}
                        @php
                            $getSortUrl = function($column) use ($sortBy, $sortOrder) {
                                $newOrder = ($sortBy == $column && $sortOrder == 'asc') ? 'desc' : 'asc';
                                return route('admin.accounts', array_merge(request()->except(['sort_by', 'sort_order', 'page']), ['sort_by' => $column, 'sort_order' => $newOrder]));
                            };
                            $showSortIcon = function($column) use ($sortBy, $sortOrder) {
                                if ($sortBy != $column) return '';
                                return $sortOrder == 'asc' ? '<i class="fas fa-sort-up"></i>' : '<i class="fas fa-sort-down"></i>';
                            };
                        @endphp

                        <th>
                            <a href="{{ $getSortUrl('id') }}" class="text-decoration-none text-dark">
                                ID {!! $showSortIcon('id') !!}
                            </a>
                        </th>
                        <th>
                            <a href="{{ $getSortUrl('ho_ten') }}" class="text-decoration-none text-dark">
                                Họ tên {!! $showSortIcon('ho_ten') !!}
                            </a>
                        </th>
                        <th>Email</th>
                        <th>SĐT</th>
                        <th>
                            <a href="{{ $getSortUrl('vai_tro') }}" class="text-decoration-none text-dark">
                                Vai trò {!! $showSortIcon('vai_tro') !!}
                            </a>
                        </th>
                        <th>
                            <a href="{{ $getSortUrl('created_at') }}" class="text-decoration-none text-dark">
                                Ngày tạo {!! $showSortIcon('created_at') !!}
                            </a>
                        </th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>#{{ $user->id }}</td>
                            <td><strong>{{ $user->ho_ten }}</strong></td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->sdt ?? '---' }}</td>
                            <td>
                                @if ($user->vai_tro == 'ADMIN')
                                    <span class="status-badge status-cancelled">ADMIN</span>
                                @elseif ($user->vai_tro == 'NHAN_VIEN')
                                    <span class="status-badge status-shipping">Nhân viên</span>
                                @else
                                    <span class="status-badge status-approved">Khách hàng</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($user->created_at)->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('admin.accounts.edit', $user->id) }}" class="btn btn-sm btn-primary" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if (Auth::id() != $user->id)
                                    <form action="{{ route('admin.accounts.destroy', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Xóa tài khoản này?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Không tìm thấy tài khoản nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="pagination-wrapper" >{{ $users->links('vendor.pagination.admin-custom-pagination') }}</div>
    </div>
</section>
@endsection