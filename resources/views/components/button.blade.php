@props([
    'variant' => 'primary',
    'size' => 'md',
    'as' => 'button',
    'href' => null,
    'loading' => false,
    'icon' => null,
])

@php
$tag = $href ? 'a' : ($as === 'a' ? 'a' : 'button');

$variantClasses = match($variant) {
    'primary'     => 'bg-primary text-primary-contrast hover:bg-primary-hover',
    'secondary'   => 'bg-surface-2 text-fg hover:bg-border',
    'outline'     => 'border border-strong bg-transparent text-fg hover:bg-surface-2',
    'ghost'       => 'bg-transparent text-fg hover:bg-surface-2',
    'destructive' => 'bg-error text-primary-contrast hover:opacity-90',
    default       => 'bg-primary text-primary-contrast hover:bg-primary-hover',
};

$sizeClasses = match($size) {
    'sm' => 'h-8 px-3 text-sm',
    'md' => 'h-10 px-4 text-base',
    'lg' => 'h-12 px-6 text-lg',
    default => 'h-10 px-4 text-base',
};

$baseClasses = 'inline-flex items-center justify-center gap-2 font-sans font-medium rounded-md transition-colors duration-[var(--dur-fast)] ease-[var(--ease-out)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 active:scale-[0.98] motion-reduce:active:scale-100 disabled:cursor-not-allowed disabled:opacity-50 disabled:pointer-events-none';

$classes = $baseClasses . ' ' . $variantClasses . ' ' . $sizeClasses;
@endphp

<{{ $tag }}
    @if($tag === 'a') href="{{ $href }}" @endif
    @if($tag === 'button') type="{{ $attributes->get('type', 'button') }}" @endif
    @if($loading) aria-busy="true" @endif
    {{ $attributes->except(['type'])->merge(['class' => $classes]) }}
>
    @if($loading)
        <svg class="animate-spin shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
    @elseif($icon)
        <x-icon :name="$icon" width="16" height="16" />
    @endif

    {{ $slot }}
</{{ $tag }}>
