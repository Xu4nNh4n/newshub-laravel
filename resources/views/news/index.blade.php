@extends('layouts.app', ['title' => 'Tin tức & Bài viết - NewsHub'])

@section('content')
@php
    $activeCategoryModel = !empty($filters['category'])
        ? \App\Models\Category::where('slug', $filters['category'])->first()
        : null;
@endphp
<div class="space-y-8">
    <!-- Breadcrumb & Page Header -->
    <div class="space-y-2 border-b-2 border-line-strong pb-6">
        <nav aria-label="Breadcrumb" class="text-xs font-mono text-ink-muted">
            <ol class="flex items-center gap-1.5 flex-wrap">
                <li><a href="{{ route('home') }}" class="hover:text-ink hover:underline transition-colors">Trang chủ</a></li>
                <li class="text-line">/</li>
                @if (!empty($filters['category']))
                    <li><a href="{{ route('news.index') }}" class="hover:text-ink hover:underline transition-colors">Tin tức</a></li>
                    <li class="text-line">/</li>
                    <li class="font-bold text-ink uppercase tracking-wide">
                        Chuyên mục: {{ $activeCategoryModel?->name ?? $filters['category'] }}
                    </li>
                @elseif (!empty($filters['tag']))
                    <li><a href="{{ route('news.index') }}" class="hover:text-ink hover:underline transition-colors">Tin tức</a></li>
                    <li class="text-line">/</li>
                    <li class="font-bold text-ink font-mono">
                        #{{ $filters['tag'] }}
                    </li>
                @else
                    <li class="font-bold text-ink">Tất cả tin tức</li>
                @endif
            </ol>
        </nav>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between pt-2">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-ink font-sans">
                    @if (!empty($filters['q']))
                        Kết quả tìm kiếm: <span class="underline decoration-lime decoration-4">"{{ $filters['q'] }}"</span>
                    @elseif (!empty($filters['category']))
                        Chuyên mục: <span class="underline decoration-lime decoration-4">{{ $activeCategoryModel?->name ?? str_replace('-', ' ', $filters['category']) }}</span>
                    @elseif (!empty($filters['tag']))
                        Chủ đề gắn thẻ: <span class="underline decoration-lime decoration-4 font-mono">#{{ $filters['tag'] }}</span>
                    @else
                        Tin tức & Bài viết mới nhất
                    @endif
                </h1>
                <p class="mt-1 text-xs sm:text-sm text-ink-muted">
                    Cập nhật dòng chảy thông tin thời sự, kinh tế, công nghệ và đời sống.
                </p>
            </div>

            @if ($posts->total() > 0)
                <span class="border-2 border-line-strong bg-surface px-3 py-1 text-xs font-bold font-mono tabular-nums text-ink shadow-2xs shrink-0">
                    {{ number_format($posts->total()) }} BÀI VIẾT
                </span>
            @endif
        </div>
    </div>

    <!-- Filter & Search Controls -->
    <div class="space-y-4">
        <form method="GET" action="{{ route('news.index') }}" class="grid gap-3 border-2 border-line-strong bg-surface p-4 sm:grid-cols-12 items-center shadow-brutal rounded-none font-sans">
            <!-- Keyword Search -->
            <div class="sm:col-span-6 relative">
                <label for="search-q" class="sr-only">Tìm kiếm tin tức</label>
                <input id="search-q"
                       type="text"
                       name="q"
                       value="{{ $filters['q'] ?? '' }}"
                       placeholder="Nhập từ khóa tìm kiếm bài viết..."
                       class="w-full border border-line bg-paper py-2 pl-9 pr-3 text-xs text-ink placeholder:text-ink-muted outline-none focus:border-line-strong focus:shadow-brutal-sm">
                <svg class="pointer-events-none absolute left-3 top-2.5 size-4 text-ink-muted" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                </svg>
            </div>

            <!-- Sort By -->
            <div class="sm:col-span-4">
                <label for="sort-select" class="sr-only">Sắp xếp theo</label>
                <select id="sort-select"
                        name="sort"
                        class="w-full border border-line bg-paper px-3 py-2 text-xs text-ink font-mono outline-none focus:border-line-strong focus:shadow-brutal-sm">
                    <option value="latest" @selected(($filters['sort'] ?? '') !== 'popular')>Mới nhất trước</option>
                    <option value="popular" @selected(($filters['sort'] ?? '') === 'popular')>Lượt xem nhiều nhất</option>
                </select>
            </div>

            <!-- Preserve category & tag if present -->
            @if (!empty($filters['category']))
                <input type="hidden" name="category" value="{{ $filters['category'] }}">
            @endif
            @if (!empty($filters['tag']))
                <input type="hidden" name="tag" value="{{ $filters['tag'] }}">
            @endif

            <!-- Submit Button -->
            <div class="sm:col-span-2">
                <button type="submit"
                        class="flex w-full min-h-9 items-center justify-center gap-1.5 border border-line-strong bg-lime px-4 py-2 text-xs font-bold font-mono text-ink shadow-brutal-sm transition-colors hover:bg-lime-hover active:scale-[0.98] cursor-pointer">
                    <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M2.628 1.601C5.028 1.206 7.49 1 10 1s4.973.206 7.372.601a.75.75 0 01.628.74v2.288a2.25 2.25 0 01-.659 1.59l-4.682 4.683a2.25 2.25 0 00-.659 1.59v3.037c0 .684-.31 1.33-.844 1.757l-1.937 1.55A.75.75 0 018 18.25v-5.757a2.25 2.25 0 00-.659-1.591L2.659 6.22A2.25 2.25 0 012 4.629V2.34a.75.75 0 01.628-.74z" clip-rule="evenodd" />
                    </svg>
                    <span>Lọc tin</span>
                </button>
            </div>
        </form>

        @php
            $currentCatSlug = $filters['category'] ?? null;
            $activeCategoryModel = $currentCatSlug
                ? \App\Models\Category::where('slug', $currentCatSlug)->first()
                : null;

            $parentCategories = \App\Models\Category::query()
                ->where('status', \App\Enums\CategoryStatus::Active)
                ->whereNull('parent_id')
                ->with(['children' => fn ($q) => $q->where('status', \App\Enums\CategoryStatus::Active)->orderBy('name')])
                ->orderBy('name')
                ->get();
        @endphp

        <!-- Active Filter Tags indicator -->
        @if (!empty($filters['q']) || !empty($filters['category']) || !empty($filters['tag']))
            <div class="flex flex-wrap items-center gap-2 pt-1 text-xs font-mono">
                <span class="text-ink-muted">Bộ lọc đang áp dụng:</span>
                @if (!empty($filters['q']))
                    <span class="inline-flex items-center gap-1 border border-line-strong bg-surface px-2.5 py-1 text-ink font-semibold">
                        Từ khóa: <strong>{{ $filters['q'] }}</strong>
                    </span>
                @endif
                @if (!empty($filters['category']))
                    <span class="inline-flex items-center gap-1 border border-line-strong bg-surface px-2.5 py-1 text-ink font-semibold">
                        Chuyên mục: <strong>{{ $activeCategoryModel?->name ?? $filters['category'] }}</strong>
                    </span>
                @endif
                @if (!empty($filters['tag']))
                    <span class="inline-flex items-center gap-1 border border-line-strong bg-surface px-2.5 py-1 text-ink font-bold">
                        #{{ $filters['tag'] }}
                    </span>
                @endif
                <a href="{{ route('news.index') }}" class="text-xs font-bold text-danger hover:underline">
                    Xóa tất cả &times;
                </a>
            </div>
        @endif

        <!-- Category & Topic Filter Strip -->
        <div class="border-2 border-line-strong bg-surface p-4 space-y-3 shadow-brutal rounded-none font-mono">
            <div class="flex items-center justify-between flex-wrap gap-2 text-xs border-b border-line pb-2.5">
                <div class="flex items-center gap-2">
                    <span class="size-2 bg-lime border border-ink"></span>
                    <span class="font-bold uppercase tracking-wider text-ink">
                        @if ($activeCategoryModel)
                            Đang lọc: <span class="underline decoration-lime">{{ $activeCategoryModel->name }}</span>
                            @if ($activeCategoryModel->parent)
                                <span class="text-ink-muted font-normal text-[11px]">(thuộc {{ $activeCategoryModel->parent->name }})</span>
                            @endif
                        @else
                            Khám phá theo chuyên mục
                        @endif
                    </span>
                </div>

                @if (!empty($filters['category']))
                    <a href="{{ route('news.index', array_filter(['q' => $filters['q'] ?? null, 'sort' => $filters['sort'] ?? null])) }}"
                       class="text-xs text-ink-muted hover:text-ink hover:underline transition-colors flex items-center gap-1 font-bold">
                        <span>&larr; Xem tất cả chuyên mục</span>
                    </a>
                @endif
            </div>

            <!-- Parent Category Strip -->
            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                <!-- All News Pill -->
                <a href="{{ route('news.index', array_filter(['q' => $filters['q'] ?? null, 'sort' => $filters['sort'] ?? null])) }}"
                   @class([
                       'inline-flex items-center gap-1.5 border px-3 py-1.5 text-xs font-bold transition-all uppercase tracking-wider',
                       'border-line-strong bg-ink text-lime shadow-2xs' => empty($filters['category']),
                       'border-line bg-paper text-ink hover:border-line-strong hover:bg-surface' => !empty($filters['category']),
                   ])>
                    <span>Tất cả</span>
                </a>

                @foreach ($parentCategories as $pCat)
                    @php
                        $hasChildren = $pCat->children->isNotEmpty();
                        $isParentDirectActive = $currentCatSlug === $pCat->slug;
                        $activeChild = $hasChildren
                            ? $pCat->children->firstWhere('slug', $currentCatSlug)
                            : null;
                        $isChildActive = (bool) $activeChild;
                        $isDrawerOpenOnLoad = $isParentDirectActive || $isChildActive;
                    @endphp

                    @if ($hasChildren)
                        <!-- Parent Category Accordion Toggle Button -->
                        <button type="button"
                                data-cat-toggle="{{ $pCat->slug }}"
                                data-cat-active="{{ $isDrawerOpenOnLoad ? 'true' : 'false' }}"
                                aria-expanded="{{ $isDrawerOpenOnLoad ? 'true' : 'false' }}"
                                aria-controls="cat-drawer-{{ $pCat->slug }}"
                                @class([
                                    'inline-flex items-center gap-1.5 border px-3 py-1.5 text-xs font-bold transition-all cursor-pointer select-none uppercase tracking-wider',
                                    'border-line-strong bg-ink text-lime shadow-2xs' => $isParentDirectActive,
                                    'border-line-strong bg-lime text-ink' => $isChildActive && !$isParentDirectActive,
                                    'border-line bg-paper text-ink hover:border-line-strong hover:bg-surface' => !$isParentDirectActive && !$isChildActive,
                                ])>
                            <span>{{ $pCat->name }}</span>
                            @if ($isChildActive)
                                <span class="bg-ink px-1 text-[10px] font-bold text-lime">
                                    : {{ $activeChild->name }}
                                </span>
                            @endif
                            <svg data-cat-chevron
                                 class="size-3.5 transition-transform duration-200 {{ $isDrawerOpenOnLoad ? 'rotate-180' : '' }}"
                                 viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    @else
                        <!-- Direct Link -->
                        <a href="{{ route('news.index', array_filter(['category' => $pCat->slug, 'q' => $filters['q'] ?? null, 'sort' => $filters['sort'] ?? null])) }}"
                           @class([
                               'inline-flex items-center gap-1 border px-3 py-1.5 text-xs font-bold transition-all uppercase tracking-wider',
                               'border-line-strong bg-ink text-lime shadow-2xs' => $isParentDirectActive,
                               'border-line bg-paper text-ink hover:border-line-strong hover:bg-surface' => !$isParentDirectActive,
                           ])>
                            <span>{{ $pCat->name }}</span>
                        </a>
                    @endif
                @endforeach
            </div>

            <!-- Subcategory Drawers -->
            @foreach ($parentCategories as $pCat)
                @if ($pCat->children->isNotEmpty())
                    @php
                        $isDrawerOpen = $currentCatSlug === $pCat->slug || $pCat->children->contains('slug', $currentCatSlug);
                    @endphp
                    <div id="cat-drawer-{{ $pCat->slug }}"
                         data-cat-drawer="{{ $pCat->slug }}"
                         class="grid transition-[grid-template-rows] duration-200 ease-in-out {{ $isDrawerOpen ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]' }}">
                        <div class="overflow-hidden">
                            <div class="mt-2.5 border-t border-line pt-3 bg-paper p-3">
                                <div class="flex items-center justify-between flex-wrap gap-2 pb-2 mb-2 border-b border-line">
                                    <div class="flex items-center gap-1.5">
                                        <span class="size-1.5 bg-ink"></span>
                                        <span class="text-[11px] font-bold uppercase tracking-wider text-ink font-mono">
                                            Chuyên mục con {{ $pCat->name }}:
                                        </span>
                                    </div>
                                    <a href="{{ route('news.index', array_filter(['category' => $pCat->slug, 'q' => $filters['q'] ?? null, 'sort' => $filters['sort'] ?? null])) }}"
                                       class="text-[11px] font-bold text-ink hover:underline transition-colors flex items-center gap-1 font-mono">
                                        <span>Xem tất cả bài {{ $pCat->name }}</span>
                                        <span>&rarr;</span>
                                    </a>
                                </div>
                                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 font-sans">
                                    <!-- All of this parent pill -->
                                    <a href="{{ route('news.index', array_filter(['category' => $pCat->slug, 'q' => $filters['q'] ?? null, 'sort' => $filters['sort'] ?? null])) }}"
                                       @class([
                                           'inline-flex items-center gap-1 border px-2.5 py-1 text-xs font-bold transition-all',
                                           'border-line-strong bg-ink text-lime shadow-2xs' => $currentCatSlug === $pCat->slug,
                                           'border-line bg-surface text-ink hover:border-line-strong hover:bg-paper' => $currentCatSlug !== $pCat->slug,
                                       ])>
                                        <span>Tất cả {{ $pCat->name }}</span>
                                    </a>

                                    <!-- Child Category Pills -->
                                    @foreach ($pCat->children as $cCat)
                                        @php
                                            $isCurrentSub = $currentCatSlug === $cCat->slug;
                                        @endphp
                                        <a href="{{ route('news.index', array_filter(['category' => $cCat->slug, 'q' => $filters['q'] ?? null, 'sort' => $filters['sort'] ?? null])) }}"
                                           @class([
                                               'inline-flex items-center gap-1 border px-2.5 py-1 text-xs font-bold transition-all',
                                               'border-line-strong bg-lime text-ink shadow-2xs' => $isCurrentSub,
                                               'border-line bg-surface text-ink hover:border-line-strong hover:bg-paper' => !$isCurrentSub,
                                           ])>
                                            <span>{{ $cCat->name }}</span>
                                            @if ($isCurrentSub)
                                                <span class="font-mono text-ink ml-0.5">✓</span>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
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
                <a href="{{ route('news.index') }}" class="mt-4 inline-flex min-h-9 items-center gap-1.5 border border-line-strong bg-lime px-4 py-2 text-xs font-bold font-mono text-ink shadow-brutal-sm hover:bg-lime-hover transition-colors">
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
    document.addEventListener('DOMContentLoaded', () => {
        const toggles = document.querySelectorAll('[data-cat-toggle]');
        toggles.forEach((toggle) => {
            toggle.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const slug = toggle.getAttribute('data-cat-toggle');
                const targetDrawer = document.getElementById(`cat-drawer-${slug}`);
                const chevron = toggle.querySelector('[data-cat-chevron]');
                const isOpen = targetDrawer?.classList.contains('grid-rows-[1fr]');

                document.querySelectorAll('[data-cat-drawer]').forEach((drawer) => {
                    drawer.classList.remove('grid-rows-[1fr]');
                    drawer.classList.add('grid-rows-[0fr]');
                });
                document.querySelectorAll('[data-cat-toggle]').forEach((btn) => {
                    btn.setAttribute('aria-expanded', 'false');
                    btn.querySelector('[data-cat-chevron]')?.classList.remove('rotate-180');
                });

                if (!isOpen && targetDrawer) {
                    targetDrawer.classList.remove('grid-rows-[0fr]');
                    targetDrawer.classList.add('grid-rows-[1fr]');
                    toggle.setAttribute('aria-expanded', 'true');
                    chevron?.classList.add('rotate-180');
                }
            });
        });
    });
</script>
@endsection
