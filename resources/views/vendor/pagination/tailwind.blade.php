@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between">
        <span class="text-xs text-slate-500 font-medium">
            Page {{ $paginator->currentPage() }} sur {{ $paginator->lastPage() }}
        </span>
        <div class="flex items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="px-3 py-1.5 text-xs font-semibold text-slate-300 cursor-default">Précédent</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   class="px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                    Précédent
                </a>
            @endif
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   class="px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                    Suivant
                </a>
            @else
                <span class="px-3 py-1.5 text-xs font-semibold text-slate-300 cursor-default">Suivant</span>
            @endif
        </div>
    </nav>
@endif
