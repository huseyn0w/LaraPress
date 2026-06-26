@props(['variant' => 'neutral'])

@php
$variantClasses = match($variant) {
    'neutral'     => 'bg-surface-2 text-muted',
    'primary'     => 'bg-primary text-primary-contrast',
    'success'     => 'bg-success-bg text-success',
    'warning'     => 'bg-warning-bg text-warning',
    'error'       => 'bg-error-bg text-error',
    default       => 'bg-surface-2 text-muted',
};
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 h-[22px] text-xs font-medium font-sans ' . $variantClasses]) }}>{{ $slot }}</span>
