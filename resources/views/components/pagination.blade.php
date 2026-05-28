@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="bg-white border border-stone-100 rounded-2xl shadow-sm p-3">
        <div class="flex items-center justify-between gap-3 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center gap-1.5 px-4 py-3 rounded-xl bg-stone-50 text-stone-300 text-sm font-semibold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Prev
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center gap-1.5 px-4 py-3 rounded-xl bg-stone-50 hover:bg-stone-100 text-stone-700 text-sm font-semibold transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Prev
                </a>
            @endif

            <div class="text-center min-w-0">
                <div class="text-sm font-bold text-stone-700">{{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</div>
                <div class="text-xs text-stone-400">{{ $paginator->total() }} data</div>
            </div>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center gap-1.5 px-4 py-3 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-semibold transition-colors">
                    Next
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            @else
                <span class="inline-flex items-center gap-1.5 px-4 py-3 rounded-xl bg-stone-50 text-stone-300 text-sm font-semibold">
                    Next
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </span>
            @endif
        </div>

        <div class="hidden sm:flex sm:items-center sm:justify-between sm:gap-4">
            <div class="text-sm text-stone-500 shrink-0">
                Menampilkan
                <span class="font-semibold text-stone-700">{{ $paginator->firstItem() }}</span>
                -
                <span class="font-semibold text-stone-700">{{ $paginator->lastItem() }}</span>
                dari
                <span class="font-semibold text-stone-700">{{ $paginator->total() }}</span>
                data
            </div>

            <div class="flex items-center gap-2 min-w-0">
                @if ($paginator->onFirstPage())
                    <span class="inline-flex items-center gap-1.5 px-4 py-3 rounded-xl bg-stone-50 text-stone-300 text-sm font-semibold shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Prev
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center gap-1.5 px-4 py-3 rounded-xl bg-stone-50 hover:bg-stone-100 text-stone-700 text-sm font-semibold transition-colors shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Prev
                    </a>
                @endif

                <div class="flex items-center gap-2 overflow-x-auto max-w-[52vw] px-1 py-1">
                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <span class="min-w-11 h-11 inline-flex items-center justify-center rounded-xl text-sm text-stone-400 shrink-0">{{ $element }}</span>
                        @endif

                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span class="min-w-11 h-11 px-4 inline-flex items-center justify-center rounded-xl bg-emerald-700 text-white text-sm font-bold shadow-sm shrink-0">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="min-w-11 h-11 px-4 inline-flex items-center justify-center rounded-xl bg-stone-50 hover:bg-stone-100 text-stone-700 text-sm font-semibold transition-colors shrink-0">{{ $page }}</a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </div>

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center gap-1.5 px-4 py-3 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-semibold transition-colors shrink-0">
                        Next
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @else
                    <span class="inline-flex items-center gap-1.5 px-4 py-3 rounded-xl bg-stone-50 text-stone-300 text-sm font-semibold shrink-0">
                        Next
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif
