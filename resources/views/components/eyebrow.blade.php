@props([])

<span {{ $attributes->merge(['class' => 'font-mono text-xs uppercase tracking-[0.08em] text-muted']) }}>{{ $slot }}</span>
