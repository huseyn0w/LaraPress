@props(['interactive' => false])

@php
$baseClasses = 'bg-surface border border-border rounded-lg p-6';
$interactiveClasses = $interactive ? ' hover:border-strong hover:shadow-card transition' : '';
@endphp

<div {{ $attributes->merge(['class' => $baseClasses . $interactiveClasses]) }}>
    @isset($header)
        <div class="mb-4 pb-4 border-b border-border">
            {{ $header }}
        </div>
    @endisset

    {{ $slot }}

    @isset($footer)
        <div class="mt-4 pt-4 border-t border-border">
            {{ $footer }}
        </div>
    @endisset
</div>
