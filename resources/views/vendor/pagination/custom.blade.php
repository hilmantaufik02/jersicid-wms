@if ($paginator->hasPages())
<nav class="inline-flex items-center gap-1" role="navigation">
    {{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())
    <span class="pagination-link pagination-disabled" aria-disabled="true">
        ← Prev
    </span>
    @else
    <a href="{{ $paginator->previousPageUrl() }}" class="pagination-link" rel="prev">
        ← Prev
    </a>
    @endif

    {{-- Pagination Elements --}}
    @foreach ($elements as $element)
    {{-- "Three Dots" Separator --}}
    @if (is_string($element))
    <span class="pagination-link pagination-disabled">{{ $element }}</span>
    @endif

    {{-- Array Of Links --}}
    @if (is_array($element))
    @foreach ($element as $page => $url)
    @if ($page == $paginator->currentPage())
    <span class="pagination-link pagination-active" aria-current="page">{{ $page }}</span>
    @else
    <a href="{{ $url }}" class="pagination-link">{{ $page }}</a>
    @endif
    @endforeach
    @endif
    @endforeach

    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
    <a href="{{ $paginator->nextPageUrl() }}" class="pagination-link" rel="next">
        Next →
    </a>
    @else
    <span class="pagination-link pagination-disabled" aria-disabled="true">
        Next →
    </span>
    @endif
</nav>
@endif