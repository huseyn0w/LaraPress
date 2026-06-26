@props([
    'icon'     => null,
    'headline',
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center text-center gap-4 py-12 px-6']) }}>
    @if($icon)
        <x-icon :name="$icon" class="text-subtle" width="40" height="40" />
    @endif

    <p class="font-serif text-xl text-fg">{{ $headline }}</p>

    @if($slot->isNotEmpty())
        <p class="text-muted text-sm max-w-sm">{{ $slot }}</p>
    @endif

    @isset($cta)
        <div class="mt-2">
            {{ $cta }}
        </div>
    @endisset
</div>
