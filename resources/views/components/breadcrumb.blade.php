@props([])

<nav aria-label="Breadcrumb" {{ $attributes }}>
    <ol class="flex items-center gap-2 font-mono text-xs text-muted">
        {{ $slot }}
    </ol>
</nav>
