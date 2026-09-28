@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}">
        <ul class="pagination" style="display: flex; list-style: none; padding: 0; margin: 0; gap: 2px;">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="disabled" style="padding: 4px 8px; color: #6c757d; background: #fff; border: 1px solid #dee2e6; border-radius: 4px; cursor: not-allowed;">
                    <span aria-hidden="true">&laquo;</span>
                </li>
            @else
                <li style="padding: 4px 8px; color: #0d6efd; background: #fff; border: 1px solid #dee2e6; border-radius: 4px;">
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}" style="color: #0d6efd; text-decoration: none;">&laquo;</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="disabled" style="padding: 4px 8px; color: #6c757d; background: #fff; border: 1px solid #dee2e6; border-radius: 4px;">
                        <span>{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="active" style="padding: 4px 8px; color: #fff; background: #0d6efd; border: 1px solid #0d6efd; border-radius: 4px;">
                                <span>{{ $page }}</span>
                            </li>
                        @else
                            <li style="padding: 4px 8px; color: #0d6efd; background: #fff; border: 1px solid #dee2e6; border-radius: 4px;">
                                <a href="{{ $url }}" style="color: #0d6efd; text-decoration: none;">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li style="padding: 4px 8px; color: #0d6efd; background: #fff; border: 1px solid #dee2e6; border-radius: 4px;">
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}" style="color: #0d6efd; text-decoration: none;">&raquo;</a>
                </li>
            @else
                <li class="disabled" style="padding: 4px 8px; color: #6c757d; background: #fff; border: 1px solid #dee2e6; border-radius: 4px; cursor: not-allowed;">
                    <span aria-hidden="true">&raquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
