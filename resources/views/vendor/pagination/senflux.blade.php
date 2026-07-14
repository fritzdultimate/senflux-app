@if ($paginator->hasPages())
    <nav class="ff-pager" role="navigation" aria-label="Pagination Navigation">
        <div class="ff-pager__info">
            Showing {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ $paginator->total() }}
        </div>

        <div class="ff-pager__controls">
            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span class="ff-pager__btn ff-pager__btn--disabled" aria-disabled="true">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
                </span>
            @else
                <button wire:click="previousPage" wire:loading.attr="disabled" class="ff-pager__btn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
                </button>
            @endif

            {{-- Page numbers (elided for large ranges) --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="ff-pager__ellipsis">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="ff-pager__btn ff-pager__btn--active">{{ $page }}</span>
                        @else
                            <button wire:click="gotoPage({{ $page }})" class="ff-pager__btn">{{ $page }}</button>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <button wire:click="nextPage" wire:loading.attr="disabled" class="ff-pager__btn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
                </button>
            @else
                <span class="ff-pager__btn ff-pager__btn--disabled" aria-disabled="true">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
                </span>
            @endif
        </div>
    </nav>
@endif