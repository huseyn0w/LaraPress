@props(['paginator'])

@php
$prevUrl = $paginator->previousPageUrl();
$nextUrl = $paginator->nextPageUrl();
$currentPage = $paginator->currentPage();
$lastPage = $paginator->lastPage();
@endphp

<nav aria-label="Pagination" {{ $attributes->merge(['class' => 'flex items-center justify-between gap-4 font-mono text-xs']) }}>
    @if($prevUrl)
        <a href="{{ $prevUrl }}" class="text-muted hover:text-fg transition-colors" rel="prev">← Previous</a>
    @else
        <span class="text-subtle pointer-events-none">← Previous</span>
    @endif

    <span class="text-muted">
        Page {{ $currentPage }} of {{ $lastPage }}
    </span>

    @if($nextUrl)
        <a href="{{ $nextUrl }}" class="text-muted hover:text-fg transition-colors" rel="next">Next →</a>
    @else
        <span class="text-subtle pointer-events-none">Next →</span>
    @endif
</nav>
