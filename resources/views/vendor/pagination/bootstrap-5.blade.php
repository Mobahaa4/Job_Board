@if ($paginator->hasPages())
    <nav class="pagination-custom" aria-label="Pagination">
        <ul class="pagination pagination-sm justify-content-center align-items-center gap-1 mb-0">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link page-arrow"><i class="bi bi-chevron-left"></i></span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link page-arrow" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous"><i class="bi bi-chevron-left"></i></a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true"><span class="page-link page-ellipsis">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link page-arrow" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next"><i class="bi bi-chevron-right"></i></a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link page-arrow"><i class="bi bi-chevron-right"></i></span>
                </li>
            @endif
        </ul>
    </nav>
    <div class="text-center text-muted small mt-2">
        Showing <span class="fw-semibold text-dark">{{ $paginator->firstItem() }}</span> to
        <span class="fw-semibold text-dark">{{ $paginator->lastItem() }}</span> of
        <span class="fw-semibold text-dark">{{ $paginator->total() }}</span> results
    </div>
@endif
