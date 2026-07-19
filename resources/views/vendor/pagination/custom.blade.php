@if ($paginator->hasPages())
    <div class="custom-pagination">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="page-link disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                <i class="ti ti-chevron-left"></i>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="page-link" rel="prev" aria-label="@lang('pagination.previous')">
                <i class="ti ti-chevron-left"></i>
            </a>
        @endif

        {{-- Hanya nomor halaman aktif yang ditampilkan agar pagination tetap ringkas. --}}
        <span class="page-link active" aria-current="page" aria-label="Halaman {{ $paginator->currentPage() }}">
            {{ $paginator->currentPage() }}
        </span>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="page-link" rel="next" aria-label="@lang('pagination.next')">
                <i class="ti ti-chevron-right"></i>
            </a>
        @else
            <span class="page-link disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                <i class="ti ti-chevron-right"></i>
            </span>
        @endif
    </div>
@endif
