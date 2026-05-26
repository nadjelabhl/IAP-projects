@php
if (! isset($scrollTo)) {
    $scrollTo = 'body';
}
$scrollIntoViewJsSnippet = ($scrollTo !== false)
    ? <<<JS
       (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
    JS
    : '';
@endphp

<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
            <span class="text-xs text-slate-500 font-medium">
                Page {{ $paginator->currentPage() }} sur {{ $paginator->lastPage() }}
            </span>
            <div class="flex items-center gap-1">
                @if ($paginator->onFirstPage())
                    <span class="px-3 py-1.5 text-xs font-semibold text-slate-300 cursor-default">Précédent</span>
                @else
                    <button type="button"
                        wire:click="previousPage('{{ $paginator->getPageName() }}')"
                        x-on:click="{{ $scrollIntoViewJsSnippet }}"
                        wire:loading.attr="disabled"
                        class="px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                        Précédent
                    </button>
                @endif
                @if ($paginator->hasMorePages())
                    <button type="button"
                        wire:click="nextPage('{{ $paginator->getPageName() }}')"
                        x-on:click="{{ $scrollIntoViewJsSnippet }}"
                        wire:loading.attr="disabled"
                        class="px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                        Suivant
                    </button>
                @else
                    <span class="px-3 py-1.5 text-xs font-semibold text-slate-300 cursor-default">Suivant</span>
                @endif
            </div>
        </nav>
    @endif
</div>
