@props([
    'href'    => null,
    'current' => false,
])

<li class="flex items-center gap-2 [&:not(:first-child)]:before:content-['›'] [&:not(:first-child)]:before:text-subtle [&:not(:first-child)]:before:select-none">
    <span aria-hidden="true" class="hidden [li:not(:first-child)_&]:inline text-subtle select-none">›</span>

    @if($current)
        <span aria-current="page" class="text-fg">{{ $slot }}</span>
    @else
        <a href="{{ $href }}" class="text-muted hover:text-fg transition-colors">{{ $slot }}</a>
    @endif
</li>
