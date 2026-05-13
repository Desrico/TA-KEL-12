@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="sesi-pagination-shell">
        <div class="sesi-pagination-inline">
            @if ($paginator->onFirstPage())
                <span class="sesi-pagination-link is-disabled">@lang('pagination.previous')</span>
            @else
                <a class="sesi-pagination-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">@lang('pagination.previous')</a>
            @endif

            <div class="sesi-pagination-pages">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="sesi-pagination-ellipsis">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="sesi-pagination-page is-active" aria-current="page">{{ $page }}</span>
                            @else
                                <a class="sesi-pagination-page" href="{{ $url }}">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            @if ($paginator->hasMorePages())
                <a class="sesi-pagination-link" href="{{ $paginator->nextPageUrl() }}" rel="next">@lang('pagination.next')</a>
            @else
                <span class="sesi-pagination-link is-disabled">@lang('pagination.next')</span>
            @endif
        </div>
    </nav>
@endif
