@extends('layouts.app', ['title' => 'NewsHub - Báo điện tử đa phương tiện, Tin tức cập nhật 24/7'])

@section('content')
<div class="space-y-10 sm:space-y-12">
    <!-- 1. Breaking / Trending News Ticker Bar -->
    @if ($featuredPosts->isNotEmpty() || $latestPosts->isNotEmpty())
        @php
            $tickerPost = $featuredPosts->first() ?? $latestPosts->first();
        @endphp
        <aside class="flex flex-col sm:flex-row sm:items-center gap-3 border-2 border-line-strong bg-surface px-4 py-2.5 shadow-brutal-sm rounded-none" aria-label="Tin nóng 24/7">
            <div class="flex items-center gap-2 shrink-0">
                <span class="inline-flex items-center gap-1.5 border border-line-strong bg-lime px-2.5 py-0.5 text-[10px] font-bold font-mono uppercase tracking-wider text-ink">
                    <span class="size-2 bg-ink animate-pulse"></span>
                    Tiêu điểm 24/7
                </span>
            </div>
            <div class="flex items-center justify-between gap-4 min-w-0 flex-1">
                <a href="{{ route('news.show', $tickerPost->slug) }}" class="truncate text-xs font-bold text-ink hover:underline transition-colors">
                    <span class="font-mono text-ink-muted">[{{ $tickerPost->category?->name ?? 'Tin nóng' }}]</span>
                    {{ $tickerPost->title }}
                </a>
                <span class="hidden md:inline shrink-0 text-[11px] font-mono text-ink-muted">
                    {{ $tickerPost->published_at?->diffForHumans() }}
                </span>
            </div>
        </aside>
    @endif

    <!-- 2. Hero Editorial Section: Lead Story (7 cols) + 2 Sub-features (5 cols) -->
    @if ($featuredPosts->isNotEmpty() || $latestPosts->isNotEmpty())
        @php
            $leadPost = $featuredPosts->first() ?? $latestPosts->first();
            $subFeatures = $featuredPosts->count() > 1
                ? $featuredPosts->slice(1, 2)
                : $latestPosts->where('id', '!=', $leadPost?->id)->take(2);
        @endphp
        <section aria-labelledby="featured-headline" class="grid gap-6 lg:grid-cols-12">
            <h2 id="featured-headline" class="sr-only">Tin tức tiêu điểm</h2>

            <!-- Lead Story (7 or 8 columns on large screens) -->
            <article class="group lg:col-span-7 xl:col-span-8 flex flex-col justify-between border-2 border-line-strong bg-surface shadow-brutal transition-all duration-150">
                <a href="{{ route('news.show', $leadPost->slug) }}" class="relative block aspect-[16/9] w-full overflow-hidden border-b-2 border-line-strong bg-paper">
                    @if ($leadPost->thumbnail)
                        <img src="{{ asset('storage/' . $leadPost->thumbnail) }}"
                             alt="{{ $leadPost->title }}"
                             class="size-full object-cover transition-transform duration-300 group-hover:scale-105"
                             loading="eager">
                    @else
                        <div class="flex size-full items-center justify-center bg-paper text-ink-light">
                            <svg class="size-16 text-ink-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                        </div>
                    @endif

                    @if ($leadPost->category)
                        <span class="absolute left-3.5 top-3.5 border border-line-strong bg-paper px-3 py-1 text-xs font-bold font-mono uppercase tracking-wider text-ink shadow-2xs group-hover:bg-lime transition-colors">
                            {{ $leadPost->category->name }}
                        </span>
                    @endif
                </a>

                <div class="p-5 sm:p-6 space-y-3">
                    <h3 class="text-xl sm:text-2xl lg:text-3xl font-black tracking-tight text-ink leading-tight group-hover:underline transition-colors">
                        <a href="{{ route('news.show', $leadPost->slug) }}">
                            {{ $leadPost->title }}
                        </a>
                    </h3>

                    @if ($leadPost->summary)
                        <p class="text-xs sm:text-sm text-ink-muted leading-relaxed line-clamp-3">
                            {{ $leadPost->summary }}
                        </p>
                    @endif

                    <div class="flex items-center gap-3 pt-3 text-xs font-mono text-ink-muted border-t border-line">
                        <span class="font-bold text-ink">{{ $leadPost->author?->name ?? 'Tác giả' }}</span>
                        <span>&middot;</span>
                        <span>{{ $leadPost->published_at?->format('d/m/Y H:i') ?? '—' }}</span>
                        <span>&middot;</span>
                        <span class="tabular-nums">{{ number_format($leadPost->view_count) }} xem</span>
                    </div>
                </div>
            </article>

            <!-- Sub-featured List (5 or 4 columns on large screens) -->
            <div class="lg:col-span-5 xl:col-span-4 flex flex-col gap-6">
                @foreach ($subFeatures as $subPost)
                    <article class="group flex-1 flex flex-col justify-between border-2 border-line-strong bg-surface shadow-brutal transition-all duration-150">
                        <a href="{{ route('news.show', $subPost->slug) }}" class="relative block aspect-[16/9] w-full overflow-hidden border-b border-line-strong bg-paper">
                            @if ($subPost->thumbnail)
                                <img src="{{ asset('storage/' . $subPost->thumbnail) }}"
                                     alt="{{ $subPost->title }}"
                                     class="size-full object-cover transition-transform duration-300 group-hover:scale-105"
                                     loading="lazy">
                            @else
                                <div class="flex size-full items-center justify-center bg-paper text-ink-light">
                                    <svg class="size-8 text-ink-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                    </svg>
                                </div>
                            @endif

                            @if ($subPost->category)
                                <span class="absolute left-2.5 top-2.5 border border-line-strong bg-paper px-2.5 py-0.5 text-[10px] font-bold font-mono uppercase tracking-wider text-ink shadow-2xs group-hover:bg-lime transition-colors">
                                    {{ $subPost->category->name }}
                                </span>
                            @endif
                        </a>

                        <div class="p-4 space-y-2">
                            <h3 class="text-sm sm:text-base font-bold text-ink leading-snug group-hover:underline transition-colors line-clamp-2">
                                <a href="{{ route('news.show', $subPost->slug) }}">
                                    {{ $subPost->title }}
                                </a>
                            </h3>

                            <div class="flex items-center gap-2 text-[11px] font-mono text-ink-muted pt-1 border-t border-line">
                                <span class="font-bold text-ink">{{ $subPost->author?->name ?? 'Tác giả' }}</span>
                                <span>&middot;</span>
                                <span>{{ $subPost->published_at?->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <!-- 3. Main Content Grid: Latest News (8 cols) & Sidebar (Popular Top 5 + Author Callout) (4 cols) -->
    <div class="grid gap-8 lg:grid-cols-12">
        <!-- Left: Latest News Stream (8 cols) -->
        <section aria-labelledby="latest-news-title" class="lg:col-span-8 space-y-6">
            <div class="flex items-center justify-between border-b-2 border-line-strong pb-3">
                <div class="flex items-center gap-2">
                    <span class="inline-block size-3 bg-lime border border-ink"></span>
                    <h2 id="latest-news-title" class="text-lg sm:text-xl font-black uppercase tracking-tight text-ink font-mono">
                        Dòng tin mới nhất
                    </h2>
                </div>
                <a href="{{ route('news.index', ['sort' => 'latest']) }}" class="inline-flex items-center gap-1 text-xs font-bold font-mono text-ink hover:underline transition-colors">
                    <span>Xem tất cả</span>
                    <span aria-hidden="true">&rarr;</span>
                </a>
            </div>

            <!-- Card Grid: 2 columns on tablet/desktop -->
            <div class="grid gap-5 sm:grid-cols-2">
                @foreach ($latestPosts->take(6) as $post)
                    <x-post-card :post="$post" />
                @endforeach
            </div>

            @if ($latestPosts->count() > 6)
                <div class="pt-2 text-center">
                    <a href="{{ route('news.index') }}" class="inline-flex min-h-10 items-center justify-center border-2 border-line-strong bg-surface px-6 py-2 text-xs font-bold font-mono text-ink shadow-brutal-sm hover:bg-paper transition-colors">
                        Khám phá thêm các bài viết khác &rarr;
                    </a>
                </div>
            @endif
        </section>

        <!-- Right: Ranked Top 5 Popular & Editorial Recruitment (4 cols) -->
        <aside class="lg:col-span-4 space-y-8" aria-label="Nội dung nổi bật">
            <!-- Top 5 Ranked Articles -->
            <div class="border-2 border-line-strong bg-surface p-5 shadow-brutal">
                <div class="flex items-center justify-between border-b border-line pb-3">
                    <h2 class="text-base font-black tracking-tight text-ink flex items-center gap-2 font-mono uppercase">
                        <span class="size-2.5 bg-lime border border-ink"></span>
                        <span>Đọc nhiều nhất</span>
                    </h2>
                    <span class="border border-line-strong bg-paper px-2 py-0.5 text-[10px] font-bold font-mono uppercase tracking-wider text-ink">
                        Top 5
                    </span>
                </div>

                <div class="mt-4 divide-y divide-line">
                    @forelse ($popularPosts as $index => $item)
                        @php
                            $rankColor = match($index) {
                                0 => 'bg-lime text-ink border-ink',
                                1 => 'bg-pink text-ink border-ink',
                                2 => 'bg-paper text-ink border-line-strong',
                                default => 'bg-paper-light text-ink-muted border-line',
                            };
                        @endphp
                        <article class="group py-3.5 first:pt-1 last:pb-1 flex items-start gap-3">
                            <span class="grid size-7 shrink-0 place-items-center border-2 text-xs font-black font-mono tabular-nums {{ $rankColor }} shadow-2xs">
                                {{ sprintf('%02d', $index + 1) }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <h3 class="text-xs sm:text-sm font-bold leading-snug text-ink group-hover:underline transition-colors line-clamp-2">
                                    <a href="{{ route('news.show', $item->slug) }}">
                                        {{ $item->title }}
                                    </a>
                                </h3>
                                <div class="mt-1 flex items-center gap-2 text-[11px] font-mono text-ink-muted">
                                    <span class="font-bold text-ink">{{ $item->category?->name }}</span>
                                    <span>&middot;</span>
                                    <span class="tabular-nums">{{ number_format($item->view_count) }} xem</span>
                                </div>
                            </div>
                        </article>
                    @empty
                        <p class="py-4 text-center text-xs font-mono text-ink-muted">Chưa có bài viết.</p>
                    @endforelse
                </div>
            </div>

            <!-- Categories Directory by Parent (Smooth Collapsible Accordion) -->
            <div class="border-2 border-line-strong bg-surface p-5 shadow-brutal">
                <div class="flex items-center justify-between border-b border-line pb-3">
                    <h2 class="text-base font-black tracking-tight text-ink font-mono uppercase">
                        Chuyên mục chủ đề
                    </h2>
                    <a href="{{ route('news.index') }}" class="text-xs font-bold font-mono text-ink hover:underline">
                        Tất cả &rarr;
                    </a>
                </div>
                <div class="mt-3.5 space-y-2">
                    @php
                        $parentCats = $categories->whereNull('parent_id')->values();
                    @endphp
                    @foreach ($parentCats as $idx => $parent)
                        @php
                            $children = $categories->where('parent_id', $parent->id)->values();
                            $hasChildren = $children->isNotEmpty();
                            $isDefaultOpen = $idx === 0 && $hasChildren;
                        @endphp
                        <div class="border border-line bg-paper overflow-hidden" data-home-cat-card>
                            <div class="flex items-center justify-between px-3 py-2 text-xs font-bold text-ink transition-colors">
                                <a href="{{ route('news.index', ['category' => $parent->slug]) }}"
                                   class="inline-flex items-center gap-1.5 hover:underline transition-colors flex-1">
                                    <span class="size-1.5 bg-ink"></span>
                                    <span>{{ $parent->name }}</span>
                                    @if ($hasChildren)
                                        <span class="text-[10px] text-ink-muted font-mono font-normal">({{ $children->count() }})</span>
                                    @endif
                                </a>
                                @if ($hasChildren)
                                    <button type="button"
                                            data-home-cat-toggle
                                            aria-expanded="{{ $isDefaultOpen ? 'true' : 'false' }}"
                                            class="p-1 text-ink hover:bg-surface transition-colors cursor-pointer"
                                            aria-label="Đóng mở chuyên mục con của {{ $parent->name }}">
                                        <svg class="size-3.5 transition-transform duration-200 {{ $isDefaultOpen ? 'rotate-180' : '' }}" data-home-cat-chevron viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                @endif
                            </div>
                            @if ($hasChildren)
                                <div data-home-cat-drawer
                                     class="grid transition-[grid-template-rows] duration-200 ease-in-out {{ $isDefaultOpen ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]' }}">
                                    <div class="overflow-hidden">
                                        <div class="flex flex-wrap gap-1.5 px-3 pb-2.5 pt-1 border-t border-line font-sans">
                                            <a href="{{ route('news.index', ['category' => $parent->slug]) }}"
                                               class="inline-flex items-center border border-line-strong bg-lime px-2 py-0.5 text-[11px] font-bold text-ink shadow-2xs">
                                                <span>Xem tất cả &rarr;</span>
                                            </a>
                                            @foreach ($children as $child)
                                                <a href="{{ route('news.index', ['category' => $child->slug]) }}"
                                                   class="inline-flex items-center border border-line bg-surface px-2 py-0.5 text-[11px] text-ink hover:border-line-strong hover:bg-paper">
                                                    <span>{{ $child->name }}</span>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Editorial Contributor Recruitment CTA -->
            <div class="border-2 border-line-strong bg-lime/20 p-5 shadow-brutal">
                <div class="flex items-center gap-2 text-ink font-bold text-xs uppercase tracking-wider font-mono">
                    <svg class="size-4 text-ink" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-11.25a.75.75 0 00-1.5 0v2.5h-2.5a.75.75 0 000 1.5h2.5v2.5a.75.75 0 001.5 0v-2.5h2.5a.75.75 0 000-1.5h-2.5v-2.5z" clip-rule="evenodd" />
                    </svg>
                    <span>Cộng tác cùng NewsHub</span>
                </div>
                <h3 class="mt-2 text-base font-black text-ink tracking-tight">
                    Gia nhập đội ngũ Tác giả & Biên tập
                </h3>
                <p class="mt-1 text-xs text-ink-muted leading-relaxed">
                    Bạn có câu chuyện, phân tích chuyên môn hay muốn chia sẻ thông tin giá trị? Hãy nộp đơn ứng tuyển làm Tác giả chính thức trên NewsHub.
                </p>
                <div class="mt-4">
                    <a href="{{ route('dashboard') }}" class="inline-flex min-h-9 items-center justify-center border-2 border-line-strong bg-ink px-4 py-1.5 text-xs font-bold font-mono text-lime shadow-brutal-sm hover:bg-lime hover:text-ink transition-colors">
                        Đăng ký làm Tác giả ngay &rarr;
                    </a>
                </div>
            </div>
        </aside>
    </div>

    <!-- 4. Category-specific Showcase Sections -->
    @foreach ($categorySections as $category)
        @if ($category->posts->isNotEmpty())
            <section aria-labelledby="cat-sec-{{ $category->id }}" class="space-y-5 border-t-2 border-line-strong pt-8">
                <div class="flex items-center justify-between border-b border-line pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="inline-block size-3 bg-lime border border-ink"></span>
                        <h2 id="cat-sec-{{ $category->id }}" class="text-lg sm:text-xl font-black uppercase tracking-tight text-ink font-mono">
                            {{ $category->name }}
                        </h2>
                    </div>
                    <a href="{{ route('news.index', ['category' => $category->slug]) }}" class="inline-flex items-center gap-1 text-xs font-bold font-mono text-ink hover:underline transition-colors">
                        <span>Xem chuyên mục</span>
                        <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>

                <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($category->posts as $post)
                        <x-post-card :post="$post" />
                    @endforeach
                </div>
            </section>
        @endif
    @endforeach
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-home-cat-card]').forEach((card) => {
            const toggle = card.querySelector('[data-home-cat-toggle]');
            const drawer = card.querySelector('[data-home-cat-drawer]');
            const chevron = card.querySelector('[data-home-cat-chevron]');

            toggle?.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const isOpen = drawer?.classList.contains('grid-rows-[1fr]');
                if (isOpen) {
                    drawer?.classList.remove('grid-rows-[1fr]');
                    drawer?.classList.add('grid-rows-[0fr]');
                    chevron?.classList.remove('rotate-180');
                    toggle.setAttribute('aria-expanded', 'false');
                } else {
                    drawer?.classList.remove('grid-rows-[0fr]');
                    drawer?.classList.add('grid-rows-[1fr]');
                    chevron?.classList.add('rotate-180');
                    toggle.setAttribute('aria-expanded', 'true');
                }
            });
        });
    });
</script>
@endsection
