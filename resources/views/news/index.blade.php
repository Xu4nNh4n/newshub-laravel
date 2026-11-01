@extends('layouts.app', ['title' => ($selectedCategory ? $selectedCategory->name . ' - ' : '') . 'Tin tức - NewsHub'])

@section('content')
@php
    $currentCatSlug = $filters['category'] ?? null;
    $currentSort = $filters['sort'] ?? 'latest';
    $searchQuery = $filters['q'] ?? null;

    // Determine main section title, parent URL, and horizontal topic list (matching docs/docs/2.png)
    if ($selectedCategory) {
        if ($selectedCategory->parent_id !== null && $selectedCategory->parent) {
            // Viewing a child category (e.g. "Sức khỏe" under "Đời sống")
            $mainTitle = $selectedCategory->parent->name;
            $mainTitleUrl = route('news.index', array_filter(['category' => $selectedCategory->parent->slug, 'q' => $searchQuery, 'sort' => $currentSort]));
            $topicList = $selectedCategory->parent->children;
            $allPillActive = false;
            $allPillUrl = route('news.index', array_filter(['category' => $selectedCategory->parent->slug, 'q' => $searchQuery, 'sort' => $currentSort]));
            $allPillLabel = 'Tất cả ' . $selectedCategory->parent->name;
        } else {
            // Viewing a parent category (e.g. "Đời sống")
            $mainTitle = $selectedCategory->name;
            $mainTitleUrl = route('news.index', array_filter(['category' => $selectedCategory->slug, 'q' => $searchQuery, 'sort' => $currentSort]));
            $topicList = $selectedCategory->children;
            $allPillActive = true;
            $allPillUrl = route('news.index', array_filter(['category' => $selectedCategory->slug, 'q' => $searchQuery, 'sort' => $currentSort]));
            $allPillLabel = 'Tất cả ' . $selectedCategory->name;
        }
    } else {
        // All news or search results
        $mainTitle = $searchQuery ? 'Tìm kiếm' : 'Tin tức mới';
        $mainTitleUrl = route('news.index', array_filter(['q' => $searchQuery, 'sort' => $currentSort]));
        $topicList = $parentCategories ?? collect();
        $allPillActive = empty($currentCatSlug);
        $allPillUrl = route('news.index', array_filter(['q' => $searchQuery, 'sort' => $currentSort]));
        $allPillLabel = 'Tất cả';
    }
@endphp
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav aria-label="Breadcrumb" class="text-xs font-mono text-ink-muted">
        <ol class="flex items-center gap-1.5 flex-wrap">
            <li><a href="{{ route('home') }}" class="hover:text-ink hover:underline transition-colors">Trang chủ</a></li>
            <li class="text-line">/</li>
            <li><a href="{{ route('news.index') }}" class="hover:text-ink hover:underline transition-colors">Tin tức</a></li>
            @if ($selectedCategory)
                @if ($selectedCategory->parent)
                    <li class="text-line">/</li>
                    <li>
                        <a href="{{ route('news.index', ['category' => $selectedCategory->parent->slug]) }}" class="hover:text-ink hover:underline transition-colors">
                            {{ $selectedCategory->parent->name }}
                        </a>
                    </li>
                @endif
                <li class="text-line">/</li>
                <li class="font-bold text-ink uppercase tracking-wide">
                    {{ $selectedCategory->name }}
                </li>
            @endif
        </ol>
    </nav>

    <!-- Category Header Strip (Designed from docs/docs/2.png) -->
    <div class="border-b-2 border-line-strong pb-3">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 sm:gap-4">
            <!-- Left: Prominent Category Title + Subcategories Row -->
            <div class="flex items-center gap-3 sm:gap-6 flex-1 min-w-0">
                <!-- Large Category Title (as in 2.png) -->
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-serif font-black tracking-tight text-lime shrink-0">
                    <a href="{{ $mainTitleUrl }}" class="hover:underline transition-colors">
                        {{ $mainTitle }}
                    </a>
                </h1>

                <!-- Vertical Separator -->
                <div class="hidden sm:block h-6 w-px bg-line shrink-0" aria-hidden="true"></div>

                <!-- Horizontal Subcategories / Topics with Slider Controls (as in docs/docs/3.png) -->
                <div class="relative flex items-center min-w-0 flex-1 overflow-hidden" data-subcat-slider>
                    <!-- Left Scroll Button (appears when scrolled right) -->
                    <button type="button"
                            data-subcat-prev
                            aria-label="Cuộn sang trái"
                            title="Cuộn sang trái"
                            class="hidden absolute left-0 top-1/2 -translate-y-1/2 z-10 h-7 w-7 items-center justify-center border border-line bg-paper text-ink hover:text-lime hover:border-lime hover:bg-lime/5 shadow-2xs transition-all cursor-pointer">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m15 18-6-6 6-6"/>
                        </svg>
                    </button>
                    <!-- Left Fade Mask -->
                    <div data-subcat-fade-left class="hidden absolute left-0 top-0 bottom-0 w-10 pointer-events-none bg-gradient-to-r from-paper via-paper/90 to-transparent z-[5]"></div>

                    <!-- Track of Subcategories -->
                    <div data-subcat-track
                         class="flex items-center gap-3 sm:gap-5 text-xs sm:text-sm overflow-x-auto scrollbar-none scroll-smooth py-1 px-0.5 min-w-0 flex-1 font-sans">
                        <!-- 'All' of this group -->
                        <a href="{{ $allPillUrl }}"
                           @class([
                               'shrink-0 whitespace-nowrap transition-colors pb-0.5',
                               'text-lime font-bold border-b-2 border-lime' => $allPillActive,
                               'text-ink-muted hover:text-lime font-medium' => ! $allPillActive,
                           ])
                           @if($allPillActive) data-active-topic @endif>
                            {{ $allPillLabel }}
                        </a>

                        @foreach ($topicList as $topic)
                            @php
                                $isTopicActive = $currentCatSlug === $topic->slug;
                                $topicUrl = route('news.index', array_filter([
                                    'category' => $topic->slug,
                                    'q' => $searchQuery,
                                    'sort' => $currentSort,
                                ]));
                            @endphp
                            <a href="{{ $topicUrl }}"
                               @class([
                                   'shrink-0 whitespace-nowrap transition-colors pb-0.5',
                                   'text-lime font-bold border-b-2 border-lime' => $isTopicActive,
                                   'text-ink-muted hover:text-lime font-medium' => ! $isTopicActive,
                               ])
                               @if($isTopicActive) data-active-topic @endif>
                                {{ $topic->name }}
                            </a>
                        @endforeach
                    </div>

                    <!-- Right Fade Mask -->
                    <div data-subcat-fade-right class="hidden absolute right-0 top-0 bottom-0 w-12 pointer-events-none bg-gradient-to-l from-paper via-paper/90 to-transparent z-[5]"></div>

                    <!-- Right Scroll Button (as in docs/docs/3.png) -->
                    <button type="button"
                            data-subcat-next
                            aria-label="Xem thêm danh mục con"
                            title="Xem thêm danh mục con"
                            class="hidden absolute right-0 top-1/2 -translate-y-1/2 z-10 h-7 w-7 items-center justify-center border border-line bg-paper text-lime hover:border-lime hover:bg-lime/5 shadow-2xs transition-all cursor-pointer">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m9 18 6-6-6-6"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Right: Sort Pills & Article Count -->
            <div class="flex items-center justify-between sm:justify-end gap-3 shrink-0 text-xs font-mono pt-1 lg:pt-0 border-t lg:border-t-0 border-line">
                <!-- Quick Sort Filter -->
                <div class="inline-flex items-center rounded-none border border-line bg-paper p-0.5 shadow-2xs">
                    <a href="{{ route('news.index', array_filter(['category' => $currentCatSlug, 'q' => $searchQuery, 'sort' => 'latest'])) }}"
                       @class([
                           'px-2.5 py-1 text-xs font-semibold transition-colors',
                           'bg-lime text-white font-bold' => $currentSort === 'latest',
                           'text-ink-muted hover:text-ink' => $currentSort !== 'latest',
                       ])>
                        Mới nhất
                    </a>
                    <a href="{{ route('news.index', array_filter(['category' => $currentCatSlug, 'q' => $searchQuery, 'sort' => 'popular'])) }}"
                       @class([
                           'px-2.5 py-1 text-xs font-semibold transition-colors',
                           'bg-lime text-white font-bold' => $currentSort === 'popular',
                           'text-ink-muted hover:text-ink' => $currentSort !== 'popular',
                       ])>
                        Xem nhiều
                    </a>
                </div>

                @if ($posts->total() > 0)
                    <span class="text-ink-muted font-mono text-[11px] font-semibold whitespace-nowrap">
                        {{ number_format($posts->total()) }} bài viết
                    </span>
                @endif
            </div>
        </div>

        <!-- Search Keyword Active Badge (if searching) -->
        @if (!empty($filters['q']))
            <div class="mt-3 flex items-center gap-2 text-xs font-mono pt-2 border-t border-line">
                <span class="text-ink-muted">Kết quả tìm kiếm cho:</span>
                <span class="inline-flex items-center gap-1 border border-line-strong bg-paper px-2 py-0.5 text-ink font-bold">
                    "{{ $filters['q'] }}"
                </span>
                <a href="{{ route('news.index', array_filter(['category' => $currentCatSlug, 'sort' => $currentSort])) }}"
                   class="text-xs font-bold text-danger hover:underline">
                    Xóa từ khóa &times;
                </a>
            </div>
        @endif
    </div>

    <!-- News Grid -->
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($posts as $post)
            <x-post-card :post="$post" />
        @empty
            <div class="col-span-full flex flex-col items-center justify-center border-2 border-line-strong bg-surface p-12 text-center shadow-brutal rounded-none">
                <span class="grid size-12 place-items-center bg-paper border border-line-strong text-ink mb-3">
                    <svg class="size-6 text-ink" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                </span>
                <h3 class="text-base font-black text-ink tracking-tight">Không tìm thấy bài viết phù hợp</h3>
                <p class="mt-1 max-w-sm text-xs text-ink-muted leading-relaxed">
                    Rất tiếc, không có bài viết nào khớp với tiêu chí tìm kiếm hoặc chuyên mục bạn chọn.
                </p>
                <a href="{{ route('news.index') }}" class="mt-4 inline-flex min-h-9 items-center gap-1.5 border-2 border-line-strong bg-lime px-4 py-2 text-xs font-bold font-mono text-white shadow-brutal-sm hover:bg-ink hover:text-white transition-colors">
                    Xem toàn bộ bài viết
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($posts->hasPages())
        <div class="pt-4 flex justify-center">
            {{ $posts->links() }}
        </div>
    @endif
</div>

<script>
    (function () {
        function initSubcatSlider() {
            const slider = document.querySelector('[data-subcat-slider]');
            if (!slider) return;

            const track = slider.querySelector('[data-subcat-track]');
            const prevBtn = slider.querySelector('[data-subcat-prev]');
            const nextBtn = slider.querySelector('[data-subcat-next]');
            const fadeLeft = slider.querySelector('[data-subcat-fade-left]');
            const fadeRight = slider.querySelector('[data-subcat-fade-right]');

            if (!track) return;

            function update() {
                const scrollLeft = Math.ceil(track.scrollLeft);
                const maxScroll = track.scrollWidth - track.clientWidth;
                const canPrev = scrollLeft > 6;
                const canNext = maxScroll > 6 && scrollLeft < maxScroll - 6;

                if (prevBtn) {
                    prevBtn.classList.toggle('hidden', !canPrev);
                    prevBtn.classList.toggle('flex', canPrev);
                }
                if (fadeLeft) {
                    fadeLeft.classList.toggle('hidden', !canPrev);
                }

                if (nextBtn) {
                    nextBtn.classList.toggle('hidden', !canNext);
                    nextBtn.classList.toggle('flex', canNext);
                }
                if (fadeRight) {
                    fadeRight.classList.toggle('hidden', !canNext);
                }
            }

            prevBtn?.addEventListener('click', (e) => {
                e.preventDefault();
                track.scrollBy({ left: -240, behavior: 'smooth' });
                setTimeout(update, 260);
            });

            nextBtn?.addEventListener('click', (e) => {
                e.preventDefault();
                track.scrollBy({ left: 240, behavior: 'smooth' });
                setTimeout(update, 260);
            });

            track.addEventListener('scroll', update, { passive: true });
            window.addEventListener('resize', update);

            // Auto-scroll active item into visible area on page load
            const activeItem = track.querySelector('[data-active-topic]');
            if (activeItem) {
                const itemLeft = activeItem.offsetLeft;
                const itemWidth = activeItem.offsetWidth;
                const trackWidth = track.clientWidth;
                if (itemLeft + itemWidth > trackWidth || itemLeft < track.scrollLeft) {
                    track.scrollLeft = Math.max(0, itemLeft - (trackWidth / 2) + (itemWidth / 2));
                }
            }

            update();
            setTimeout(update, 100);
            if (document.fonts && document.fonts.ready) {
                document.fonts.ready.then(update);
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initSubcatSlider);
        } else {
            initSubcatSlider();
        }
    })();
</script>
@endsection
