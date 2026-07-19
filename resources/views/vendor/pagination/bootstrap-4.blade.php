@if ($paginator->hasPages())
    <style>
        .sesi-pagination-shell {
            display: flex;
            justify-content: center;
            width: 100%;
            margin: .75rem 0 1rem;
        }

        .sesi-pagination-inline {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
        }

        .sesi-pagination-link,
        .sesi-pagination-page {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            min-width: 38px;
            height: 38px;
            padding: 0;
            border: 1px solid #dbe7df;
            border-radius: 10px;
            background: #ffffff;
            color: #065f46;
            font-size: .86rem;
            font-weight: 800;
            line-height: 1;
            text-decoration: none;
            box-sizing: border-box;
            transition: background .16s ease, color .16s ease, border-color .16s ease;
        }

        .sesi-pagination-link {
            font-size: 1.25rem;
        }

        .sesi-pagination-page.is-active {
            border-color: #065f46;
            background: #065f46;
            color: #ffffff;
        }

        a.sesi-pagination-link:hover,
        a.sesi-pagination-link:focus-visible {
            border-color: #065f46;
            background: #ecfdf5;
            color: #065f46;
        }

        .sesi-pagination-link.is-disabled {
            cursor: not-allowed;
            opacity: .42;
            background: #f8fafc;
            color: #94a3b8;
        }

        @media (max-width: 576px) {
            .sesi-pagination-shell {
                margin-block: .6rem .8rem;
            }

            .sesi-pagination-link,
            .sesi-pagination-page {
                width: 36px;
                min-width: 36px;
                height: 36px;
            }
        }
    </style>

    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="sesi-pagination-shell">
        <div class="sesi-pagination-inline">
            @if ($paginator->onFirstPage())
                <span class="sesi-pagination-link is-disabled" aria-disabled="true" aria-label="{{ __('pagination.previous') }}">&lsaquo;</span>
            @else
                <a class="sesi-pagination-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}">&lsaquo;</a>
            @endif

            <span class="sesi-pagination-page is-active" aria-current="page" aria-label="Halaman {{ $paginator->currentPage() }}">
                {{ $paginator->currentPage() }}
            </span>

            @if ($paginator->hasMorePages())
                <a class="sesi-pagination-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}">&rsaquo;</a>
            @else
                <span class="sesi-pagination-link is-disabled" aria-disabled="true" aria-label="{{ __('pagination.next') }}">&rsaquo;</span>
            @endif
        </div>
    </nav>
@endif
