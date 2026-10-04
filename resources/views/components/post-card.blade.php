@props(['post'])

<article {{ $attributes->merge(['class' => 'group flex flex-col justify-between border-2 border-line-strong bg-surface transition-all duration-150 hover:shadow-brutal']) }}>
    <div>
        <!-- Card Thumbnail -->
        <a href="{{ route('news.show', $post->slug) }}" class="relative block aspect-video w-full overflow-hidden border-b border-line-strong bg-paper">
            @if ($post->thumbnail)
                <img src="{{ asset('storage/' . $post->thumbnail) }}"
                     alt="{{ $post->title }}"
                     class="size-full object-cover transition-transform duration-200 group-hover:scale-105"
                     loading="lazy">
            @else
                <div class="flex size-full items-center justify-center bg-paper text-ink-light">
                    <svg class="size-10 text-ink-muted transition-transform group-hover:scale-110 duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                </div>
            @endif

            <!-- Category Badge (Mono / Neo-Brutalist) -->
            @if ($post->category)
                <span class="absolute left-2.5 top-2.5 border border-line-strong bg-paper px-2 py-0.5 text-[10px] font-bold font-mono uppercase tracking-wider text-ink shadow-2xs group-hover:bg-lime transition-colors">
                    {{ $post->category->name }}
                </span>
            @endif
        </a>

        <!-- Content details -->
        <div class="space-y-2 p-4">
            <h2 class="text-sm sm:text-base font-black leading-snug tracking-tight text-ink transition-colors group-hover:underline line-clamp-2">
                <a href="{{ route('news.show', $post->slug) }}">
                    {{ $post->title }}
                </a>
            </h2>

            @if ($post->summary)
                <p class="text-xs text-ink-muted line-clamp-2 leading-relaxed">
                    {{ $post->summary }}
                </p>
            @endif
        </div>
    </div>

    <!-- Card Metadata Footer -->
    <div class="flex items-center justify-between border-t border-line bg-paper-light px-4 py-2.5 text-[11px] font-mono text-ink-muted">
        <div class="flex items-center gap-2 min-w-0">
            @if ($post->author)
                <a href="{{ route('authors.show', $post->author) }}" class="flex items-center gap-1.5 hover:text-ink hover:underline transition-colors truncate">
                    <span class="grid size-4 shrink-0 place-items-center bg-ink text-[9px] font-bold font-mono text-lime">
                        {{ mb_strtoupper(mb_substr($post->author->name, 0, 1)) }}
                    </span>
                    <span class="truncate font-semibold">{{ $post->author->name }}</span>
                </a>
            @else
                <span class="font-semibold">Ban biên tập</span>
            @endif
            <span>&middot;</span>
            <span class="shrink-0">{{ $post->published_at?->format('d/m/Y') ?? '—' }}</span>
        </div>

        <span class="shrink-0 tabular-nums flex items-center gap-1 text-ink-muted">
            <svg class="size-3 text-ink-muted" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10 12.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" />
                <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 010-1.186A10.004 10.004 0 0110 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0110 17c-4.257 0-7.893-2.66-9.336-6.41zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
            </svg>
            {{ number_format($post->view_count) }}
        </span>
    </div>
</article>
