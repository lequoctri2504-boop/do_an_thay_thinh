@extends('layouts.app')
@section('title', 'Tin tức Công nghệ')
@section('content')
<div class="breadcrumb">
    <div class="container">
        <a href="{{ route('home') }}">Trang chủ</a>
        <i class="fas fa-chevron-right"></i>
        <span>Tin tức</span>
    </div>
</div>
<section class="products-page">
    <div class="container">
        <h1 style="margin-bottom: 30px;"><i class="fas fa-newspaper"></i> Tất cả Tin tức Công nghệ</h1>
        
        {{-- Thanh Tìm kiếm và Sắp xếp --}}
        <div class="products-toolbar" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; padding: 15px;">
            <form action="{{ route('news.index') }}" method="GET" style="display: flex; gap: 15px; align-items: center;">
                <input type="text" name="q" placeholder="Tìm kiếm tin tức..." value="{{ $keyword ?? '' }}" class="form-control" style="width: 300px;">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Tìm</button>
                @if(isset($keyword) && $keyword)
                    <a href="{{ route('news.index') }}" class="btn btn-secondary btn-sm">Hủy</a>
                @endif
            </form>
            
            <div class="toolbar-right" style="display: flex; align-items: center; gap: 10px;">
                <span>Sắp xếp theo:</span>
                <select class="sort-select" onchange="window.location.href = '{{ route('news.index') }}?q={{ $keyword ?? '' }}&sort=' + this.value">
                    <option value="newest" {{ ($sortBy ?? 'newest') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                    <option value="oldest" {{ ($sortBy ?? '') == 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                    <option value="popular" {{ ($sortBy ?? '') == 'popular' ? 'selected' : '' }}>Phổ biến nhất</option>
                </select>
            </div>
        </div>
        
        <div class="news-grid">
            @forelse($newsArticles as $article)
                @php $imagePath = $article->hinh_anh_chinh ? asset('uploads/' . $article->hinh_anh_chinh) : 'https://via.placeholder.com/400x250'; @endphp
                <article class="news-card">
                    <img src="{{ $imagePath }}" alt="{{ $article->tieu_de }}">
                    <div class="news-content">
                        <span class="news-date"><i class="far fa-calendar"></i> {{ \Carbon\Carbon::parse($article->created_at)->format('d/m/Y') }}</span>
                        <h3><a href="{{ route('news.show', $article->slug) }}">{{ $article->tieu_de }}</a></h3>
                        <p>{{ \Illuminate\Support\Str::limit(strip_tags($article->noi_dung), 100) }}</p>
                        <a href="{{ route('news.show', $article->slug) }}" class="read-more">Đọc thêm <i class="fas fa-arrow-right"></i></a>
                    </div>
                </article>
            @empty
                <p class="text-center" style="grid-column: 1 / -1; padding: 50px;">Không tìm thấy tin tức nào khớp với tìm kiếm.</p>
            @endforelse
        </div>
        <div class="pagination-wrapper" style="margin-top: 30px;">
             {{ $newsArticles->links() }}
        </div>
    </div>
</section>
@endsection