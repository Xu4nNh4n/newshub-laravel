@props([
    'size' => 'md',
    'iconOnly' => false,
])

@php
    $iconSize = match($size) {
        'sm' => 'size-8 text-base border border-line-strong',
        'lg' => 'size-12 text-2xl border-2 border-line-strong',
        'xl' => 'size-14 text-3xl border-2 border-line-strong',
        default => 'size-9 text-lg border border-line-strong',
    };
    $titleSize = match($size) {
        'sm' => 'text-lg',
        'lg' => 'text-2xl',
        'xl' => 'text-3xl',
        default => 'text-xl',
    };
    $subtitleSize = match($size) {
        'sm' => 'text-[8px]',
        'lg' => 'text-[10px]',
        'xl' => 'text-[11px]',
        default => 'text-[9px]',
    };
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5']) }}>
    <!-- Brand Icon: Sharp Blue Square with White Serif N -->
    <span class="grid {{ $iconSize }} shrink-0 place-items-center bg-lime text-white font-serif font-black shadow-brutal-sm select-none">
        N
    </span>

    @unless($iconOnly)
        <div class="leading-none">
            <span class="block font-serif font-bold tracking-tight text-[#1D4ED8] {{ $titleSize }}">
                NewsHub
            </span>
            <span class="block font-sans font-bold uppercase tracking-widest text-[#6B7280] {{ $subtitleSize }} mt-1">
                Editorial Newsroom
            </span>
        </div>
    @endunless
</div>
