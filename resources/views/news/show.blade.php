@extends('layouts.app')
@section('title', $article->tieu_de)
@section('content')
<div class="breadcrumb">
    <div class="container">
        <a href="{{ route('home') }}">Trang chủ</a>
        <i class="fas fa-chevron-right"></i>
        <a href="{{ route('news.index') }}">Tin tức</a>
        <i class="fas fa-chevron-right"></i>
        <span>{{ $article->tieu_de }}</span>
    </div>
</div>
<section class="products-page">
    <div class="container" style="max-width: 900px; margin: 0 auto;">
        <article class="dashboard-card" style="padding: 40px;">
            <h1 style="font-size: 28px; margin-bottom: 15px;">{{ $article->tieu_de }}</h1>
            <div style="font-size: 13px; color: var(--text-gray); margin-bottom: 25px; border-bottom: 1px solid #eee; padding-bottom: 15px;">
                <i class="fas fa-user"></i> Tác giả: {{ $article->nguoiDung->ho_ten ?? 'Admin' }} 
                | <i class="far fa-calendar"></i> Ngày đăng: {{ \Carbon\Carbon::parse($article->created_at)->format('d/m/Y') }}
                | <i class="fas fa-eye"></i> Lượt xem: {{ number_format($article->luot_xem) }}
            </div>
            
            <div class="article-content" style="line-height: 1.8;">
                @if($article->hinh_anh_chinh)
                    <img src="{{ asset('uploads/' . $article->hinh_anh_chinh) }}" alt="{{ $article->tieu_de }}" style="max-width: 100%; height: auto; margin: 20px 0; border-radius: 8px;">
                @endif
                {!! $article->noi_dung !!}
            </div>
            
            <div style="margin-top: 50px; padding-top: 20px; border-top: 1px solid #eee;">
                 <a href="{{ route('news.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại danh sách</a>
            </div>
        </article>
        
        @if($relatedArticles->isNotEmpty())
        <h2 style="margin-top: 40px; font-size: 24px;"><i class="fas fa-link"></i> Bài viết liên quan</h2>
        <div class="news-grid">
            @foreach($relatedArticles as $related)
                @php $imagePath = $related->hinh_anh_chinh ? asset('uploads/' . $related->hinh_anh_chinh) : 'https://via.placeholder.com/400x250'; @endphp
                <article class="news-card">
                    <img src="{{ $imagePath }}" alt="{{ $related->tieu_de }}">
                    <div class="news-content">
                        <h3><a href="{{ route('news.show', $related->slug) }}">{{ $related->tieu_de }}</a></h3>
                        <a href="{{ route('news.show', $related->slug) }}" class="read-more">Đọc thêm <i class="fas fa-arrow-right"></i></a>
                    </div>
                </article>
            @endforeach
        </div>
        @endif
    </div>
</section>
@endsection