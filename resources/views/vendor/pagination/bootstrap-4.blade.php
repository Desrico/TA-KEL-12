@if ($paginator->hasPages())
    <style>
        /* Pagination spacing and interaction styles to match Sesi Konseling */
        .sesi-pagination-shell {
            margin-bottom: 36px;
        }
        .sesi-pagination-pages a.sesi-pagination-page,
        .sesi-pagination-pages span.sesi-pagination-page {
            transition: background .12s ease, color .12s ease, transform .06s ease;
            cursor: pointer;
        }
        .sesi-pagination-pages a.sesi-pagination-page:hover,
        .sesi-pagination-pages a.sesi-pagination-page:focus {
            background: #065f46;
            color: #fff !important;
            border-color: #065f46 !important;
            text-decoration: none;
        }
        .sesi-pagination-pages a.sesi-pagination-page:active {
            background: #054f3f;
            color: #fff !important;
            transform: translateY(1px);
        }
        /* make active look consistent */
        .sesi-pagination-pages span.sesi-pagination-page.is-active {
            box-shadow: none;
        }
    </style>

    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="sesi-pagination-shell">
        <div class="sesi-pagination-inline">
            <div class="sesi-pagination-pages">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="sesi-pagination-ellipsis">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                    <span class="sesi-pagination-page is-active" aria-current="page" style="display:inline-block; min-width:44px; height:40px; line-height:40px; text-align:center; border-radius:10px; background:#065f46; color:#ffffff; font-weight:700; margin:0 6px; font-size:15px;">{{ $page }}</span>
                                @else
                                    <a class="sesi-pagination-page" href="{{ $url }}" style="display:inline-block; min-width:44px; height:40px; line-height:40px; text-align:center; border-radius:10px; border:1px solid #e6f0ea; color:#0f172a; margin:0 6px; font-size:15px;">{{ $page }}</a>
                                @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- previous/next hidden to show only page numbers --}}
        </div>
    </nav>
@endif
