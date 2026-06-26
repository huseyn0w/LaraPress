@props([
    'label' => null,
    'name' => null,
    'error' => null,
    'help' => null,
    'required' => false,
])

@php
$hasError = !empty($error);
$descById = $hasError && $name ? $name . '-error' : null;
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-col gap-y-1.5']) }}>
    @if($label)
        <label
            @if($name) for="{{ $name }}" @endif
            class="font-sans font-medium text-sm text-fg"
        >
            {{ $label }}@if($required)<span class="text-error ml-0.5" aria-hidden="true">*</span><span class="sr-only"> (required)</span>@endif
        </label>
    @endif

    {{ $slot }}

    @if($hasError)
        <p id="{{ $descById }}" class="text-xs text-error" role="alert">{{ $error }}</p>
    @elseif($help)
        <p class="text-xs text-subtle">{{ $help }}</p>
    @endif
</div>
