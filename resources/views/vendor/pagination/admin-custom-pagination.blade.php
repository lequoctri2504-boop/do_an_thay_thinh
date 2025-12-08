<div class="table-pagination" style="display: flex; justify-content: space-between; align-items: center; padding-top: 15px;">
    
    <div class="pagination-info">
        Hiển thị {{ $paginator->firstItem() }} đến {{ $paginator->lastItem() }} trong tổng số {{ $paginator->total() }} kết quả
    </div>

    <span class="pagination">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="page-btn disabled">
                <i class="fas fa-chevron-left"></i>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="page-btn">
                <i class="fas fa-chevron-left"></i>
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page" class="page-btn active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="page-btn">
                <i class="fas fa-chevron-right"></i>
            </a>
        @else
            <span class="page-btn disabled">
                <i class="fas fa-chevron-right"></i>
            </span>
        @endif
    </span>

</div>