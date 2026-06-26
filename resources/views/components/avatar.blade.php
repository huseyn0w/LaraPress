@props([
    'user' => null,
    'src'  => null,
    'name' => null,
    'size' => 'md',
])

@php
// Resolve name and image from user model if provided
$resolvedName = $name;
$resolvedSrc  = $src;

if ($user !== null) {
    if ($resolvedName === null) {
        $resolvedName = $user->name ?? null;
    }
    if ($resolvedSrc === null) {
        // User model uses `avatar` field; fall back gracefully
        $resolvedSrc = $user->avatar ?? $user->image ?? null;
    }
}

// Compute initials (first letter of name)
$initials = $resolvedName ? strtoupper(mb_substr(trim($resolvedName), 0, 1)) : '?';

// Size map: sm=24, md=32, lg=48
$sizeMap = [
    'sm' => ['px' => '24', 'text' => 'text-[10px]'],
    'md' => ['px' => '32', 'text' => 'text-xs'],
    'lg' => ['px' => '48', 'text' => 'text-sm'],
];
$sizeConfig = $sizeMap[$size] ?? $sizeMap['md'];
$dimension  = $sizeConfig['px'];
$textClass  = $sizeConfig['text'];
@endphp

@if($resolvedSrc)
    <img
        src="{{ $resolvedSrc }}"
        alt="{{ $resolvedName ?? '' }}"
        width="{{ $dimension }}"
        height="{{ $dimension }}"
        {{ $attributes->merge(['class' => 'rounded-full object-cover shrink-0']) }}
        style="width: {{ $dimension }}px; height: {{ $dimension }}px;"
    />
@else
    <span
        aria-label="{{ $resolvedName ?? 'Avatar' }}"
        role="img"
        {{ $attributes->merge(['class' => 'rounded-full bg-surface-2 text-muted inline-flex items-center justify-center shrink-0 font-mono select-none ' . $textClass]) }}
        style="width: {{ $dimension }}px; height: {{ $dimension }}px;"
    >{{ $initials }}</span>
@endif
