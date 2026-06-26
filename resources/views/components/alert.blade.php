@props([
    'variant' => 'info',
    'dismissible' => false,
])

@php
[$bgClass, $textClass, $borderClass, $iconName, $role] = match($variant) {
    'success' => ['bg-success-bg', 'text-success', 'border-success', 'check-circle', 'status'],
    'warning' => ['bg-warning-bg', 'text-warning', 'border-warning', 'alert-triangle', 'status'],
    'error'   => ['bg-error-bg',   'text-error',   'border-error',   'x-circle',      'alert'],
    default   => ['bg-surface-2',  'text-muted',   'border-border',  'info',           'status'],
};
@endphp

<div
    role="{{ $role }}"
    @if($dismissible) x-data="{ show: true }" x-show="show" @endif
    {{ $attributes->merge(['class' => "relative flex gap-3 rounded-md border p-4 {$bgClass} {$textClass} {$borderClass}"]) }}
>
    <x-icon :name="$iconName" class="shrink-0 mt-0.5" width="18" height="18" />

    <div class="flex-1 min-w-0">
        @if(isset($title))
            <p class="font-medium font-sans text-sm mb-1">{{ $title }}</p>
        @endif
        <div class="text-sm font-sans">{{ $slot }}</div>
    </div>

    @if($dismissible)
        <button
            @click="show = false"
            type="button"
            aria-label="Dismiss"
            class="shrink-0 self-start rounded focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
        >
            <x-icon name="x" width="16" height="16" />
        </button>
    @endif
</div>
