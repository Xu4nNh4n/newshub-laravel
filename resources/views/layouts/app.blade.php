<!DOCTYPE html>
<html lang="vi" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'NewsHub - Báo điện tử đa phương tiện' }}</title>
    <meta name="description" content="{{ $metaDescription ?? 'Tin tức mới nhất, chính xác, cập nhật liên tục 24/7 trên mọi lĩnh vực.' }}">
    <meta property="og:title" content="{{ $title ?? 'NewsHub - Tin tức mới nhất' }}">
    <meta property="og:description" content="{{ $metaDescription ?? 'Tin tức mới nhất, chính xác và đáng tin cậy.' }}">
    @isset($ogImage)<meta property="og:image" content="{{ $ogImage }}">@endisset
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,600&family=Merriweather:ital,wght@0,400;0,700;0,900;1,400;1,700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    @stack('head')
</head>
<body class="min-h-dvh flex flex-col bg-paper font-sans text-ink antialiased selection:bg-lime selection:text-ink">
    <!-- Top Masthead Bar: Date, Edition & Quick Info -->
    <div class="border-b border-line bg-paper-light py-1.5 text-xs text-ink-muted font-mono uppercase tracking-wider">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 text-ink font-semibold">
                    <svg class="size-3.5 text-ink" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.75 2a.75.75 0 01.75.75V4h7V2.75a.75.75 0 011.5 0V4h.25A2.75 2.75 0 0118 6.75v8.5A2.75 2.75 0 0115.25 18H4.75A2.75 2.75 0 012 15.25v-8.5A2.75 2.75 0 014.75 4H5V2.75A.75.75 0 015.75 2zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75z" clip-rule="evenodd" />
                    </svg>
                    {{ now()->translatedFormat('l, d/m/Y') }}
                </span>
                <span class="hidden sm:inline text-line">|</span>
                <span class="hidden sm:inline-flex items-center gap-1.5 text-ink font-medium">
                    <span class="size-2 bg-lime border border-ink"></span>
                    Cập nhật liên tục 24/7
                </span>
            </div>

            <div class="flex items-center gap-4 text-xs font-mono">
                <a href="{{ route('news.index', ['sort' => 'popular']) }}" class="hidden md:inline text-ink-muted hover:text-ink hover:underline transition-colors">
                    Xu hướng đọc
                </a>
                @auth
                    @if(auth()->user()->role === \App\Enums\UserRole::User)
                        <a href="{{ route('dashboard') }}" class="hidden sm:inline font-bold text-ink hover:text-[#587dae] transition-colors">
                            Ứng tuyển Tác giả &rarr;
                        </a>
                    @endif
                @else
                    <a href="{{ route('register') }}" class="hidden sm:inline text-ink-muted hover:text-ink hover:underline transition-colors">
                        Đăng ký thành viên
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Main Header (Logo, Search, User Menu) -->
    <header class="sticky top-0 z-40 border-b border-line-strong bg-surface/95 backdrop-blur-md">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
            <!-- Brand Logo -->
            <div class="flex items-center gap-3">
                <button type="button"
                        id="mobile-menu-open-btn"
                        class="grid size-9 shrink-0 place-items-center rounded-none border border-line-strong text-ink hover:bg-paper focus-visible:outline-2 focus-visible:outline-ink lg:hidden cursor-pointer"
                        aria-label="Mở menu chuyên mục">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <a href="{{ route('home') }}" class="group flex items-center gap-2.5">
                    <span class="grid size-9 shrink-0 place-items-center bg-lime text-white border border-line-strong font-serif font-black text-lg shadow-brutal-sm transition-transform group-hover:scale-105 select-none">
                        N
                    </span>
                    <div class="leading-none">
                        <span class="block text-xl font-serif font-bold tracking-tight text-[#1D4ED8]">
                            NewsHub
                        </span>
                        <span class="hidden sm:block text-[9px] font-sans font-bold uppercase tracking-widest text-[#6B7280] mt-1">
                            Editorial Newsroom
                        </span>
                    </div>
                </a>
            </div>

            <!-- Search Bar in Header -->
            <form action="{{ route('news.index') }}" method="GET" class="hidden md:flex flex-1 max-w-md items-center relative">
                <input type="text"
                       name="q"
                       value="{{ request('q') }}"
                       placeholder="Tìm kiếm tin tức, sự kiện, chủ đề..."
                       class="w-full rounded-none border border-line bg-paper py-1.5 pl-9 pr-4 text-xs text-ink placeholder:text-ink-muted outline-none transition-all focus:border-line-strong focus:shadow-brutal-sm">
                <svg class="pointer-events-none absolute left-3 size-4 text-ink-muted" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                </svg>
            </form>

            <!-- User / Auth Actions -->
            <div class="flex items-center gap-2 sm:gap-3">
                @auth
                    @if(auth()->user()->hasVerifiedEmail() && in_array(auth()->user()->role, [\App\Enums\UserRole::Author, \App\Enums\UserRole::Admin], true))
                        <!-- Quick Post Creation for Authors/Admins -->
                        <a href="{{ route('author.posts.create') }}"
                           class="hidden sm:inline-flex min-h-9 items-center gap-1.5 rounded-none bg-lime border border-line-strong px-3 py-1.5 text-xs font-bold text-white shadow-brutal-sm hover:bg-ink hover:text-white transition-colors">
                            <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                            </svg>
                            <span>Viết bài</span>
                        </a>
                    @endif

                    <!-- User Profile Dropdown Menu -->
                    <div class="relative" data-user-menu>
                        <button type="button"
                                data-user-menu-trigger
                                aria-expanded="false"
                                aria-label="Mở menu người dùng"
                                class="group flex items-center gap-2 rounded-none border border-line bg-surface py-1 pl-1 pr-2.5 text-xs text-ink transition-all hover:border-line-strong hover:bg-paper hover:shadow-brutal-sm focus-visible:outline-2 focus-visible:outline-ink cursor-pointer">
                            <span class="grid size-7 place-items-center rounded-full bg-[#1D4ED8] text-xs font-bold font-mono text-white select-none">
                                {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                            </span>
                            <span class="hidden md:inline-block max-w-[120px] truncate font-bold text-ink">
                                {{ auth()->user()->name }}
                            </span>
                            <svg class="size-3 text-ink-muted transition-transform duration-200 group-hover:text-ink" data-user-menu-chevron viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <!-- Floating Dropdown Panel -->
                        <div data-user-menu-panel
                             class="pointer-events-none opacity-0 invisible translate-y-1 transition-all duration-150 ease-out absolute right-0 top-full pt-2 z-50 w-64 max-w-[90vw]">
                            <div class="rounded-none border-2 border-line-strong bg-surface p-2 shadow-brutal divide-y divide-line">
                                <!-- User Header Identity -->
                                <div class="px-2.5 py-2">
                                    <div class="flex items-center gap-2.5">
                                        <span class="grid size-9 place-items-center rounded-full bg-[#1D4ED8] text-xs font-bold font-mono text-white select-none">
                                            {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                                        </span>
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-xs font-bold text-ink">{{ auth()->user()->name }}</p>
                                            <p class="truncate text-[11px] font-mono text-ink-muted">{{ auth()->user()->email }}</p>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        @if (auth()->user()->role === \App\Enums\UserRole::Admin)
                                            <span class="inline-flex items-center gap-1 border border-line-strong bg-pink px-2 py-0.5 text-[10px] font-bold font-mono uppercase tracking-wider text-ink shadow-2xs">
                                                Quản trị viên (Admin)
                                            </span>
                                        @elseif (auth()->user()->role === \App\Enums\UserRole::Author)
                                            <span class="inline-flex items-center gap-1 border border-line-strong bg-lime px-2 py-0.5 text-[10px] font-bold font-mono uppercase tracking-wider text-ink shadow-2xs">
                                                Tác giả (Author)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 border border-line bg-paper px-2 py-0.5 text-[10px] font-medium font-mono text-ink-muted">
                                                Độc giả
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Workspace / Dashboard Links -->
                                <div class="py-1">
                                    <a href="{{ route('dashboard') }}"
                                       class="flex items-center gap-2.5 px-2.5 py-1.5 text-xs font-medium text-ink hover:bg-paper transition-colors">
                                        <svg class="size-4 text-ink" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/>
                                        </svg>
                                        <span>Bảng điều khiển (Dashboard)</span>
                                    </a>

                                    @if(auth()->user()->role === \App\Enums\UserRole::Admin)
                                        <a href="{{ route('admin.post-reviews.index') }}"
                                           class="flex items-center justify-between px-2.5 py-1.5 text-xs font-medium text-ink hover:bg-paper transition-colors">
                                            <span class="flex items-center gap-2.5">
                                                <svg class="size-4 text-ink" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                    <path d="m5 12 4 4L19 6"/><path d="M21 12a9 9 0 1 1-5.3-8.2"/>
                                                </svg>
                                                <span>Duyệt bài viết</span>
                                            </span>
                                        </a>
                                        <a href="{{ route('admin.posts.index') }}"
                                           class="flex items-center gap-2.5 px-2.5 py-1.5 text-xs font-medium text-ink hover:bg-paper transition-colors">
                                            <svg class="size-4 text-ink-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <path d="M4 6h16M4 12h16M4 18h10"/>
                                            </svg>
                                            <span>Quản lý bài viết</span>
                                        </a>
                                        <a href="{{ route('admin.categories.index') }}"
                                           class="flex items-center gap-2.5 px-2.5 py-1.5 text-xs font-medium text-ink hover:bg-paper transition-colors">
                                            <svg class="size-4 text-ink-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <path d="M3 7h7l2 2h9v10H3z"/>
                                            </svg>
                                            <span>Quản lý chuyên mục</span>
                                        </a>
                                    @elseif(auth()->user()->role === \App\Enums\UserRole::Author)
                                        <a href="{{ route('author.posts.create') }}"
                                           class="flex items-center gap-2.5 px-2.5 py-1.5 text-xs font-medium text-ink hover:bg-paper transition-colors">
                                            <svg class="size-4 text-ink" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <path d="M12 5v14M5 12h14"/>
                                            </svg>
                                            <span>Viết bài mới</span>
                                        </a>
                                        <a href="{{ route('author.posts.index') }}"
                                           class="flex items-center gap-2.5 px-2.5 py-1.5 text-xs font-medium text-ink hover:bg-paper transition-colors">
                                            <svg class="size-4 text-ink-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <path d="M4 6h16M4 12h16M4 18h10"/>
                                            </svg>
                                            <span>Bài viết của tôi</span>
                                        </a>
                                    @endif
                                </div>

                                <!-- Personal Links -->
                                <div class="py-1">
                                    <a href="{{ route('profile.edit') }}"
                                       class="flex items-center gap-2.5 px-2.5 py-1.5 text-xs font-medium text-ink hover:bg-paper transition-colors">
                                        <svg class="size-4 text-ink-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                                        </svg>
                                        <span>Hồ sơ & Mật khẩu</span>
                                    </a>

                                    <a href="{{ route('profile.comments') }}"
                                       class="flex items-center gap-2.5 px-2.5 py-1.5 text-xs font-medium text-ink hover:bg-paper transition-colors">
                                        <svg class="size-4 text-ink-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                        </svg>
                                        <span>Bình luận của tôi</span>
                                    </a>
                                </div>

                                <!-- Logout Action -->
                                <div class="pt-1">
                                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                                        @csrf
                                        <button type="submit"
                                                class="w-full flex items-center gap-2.5 px-2.5 py-2 text-xs font-bold text-danger hover:bg-rose-50 transition-colors cursor-pointer text-left">
                                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path d="M10 17l5-5-5-5M15 12H3M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                                            </svg>
                                            <span>Đăng xuất tài khoản</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="inline-flex min-h-9 items-center justify-center border-2 border-line-strong bg-surface px-3.5 py-1.5 text-xs font-bold text-ink shadow-brutal-sm transition-all hover:bg-ink hover:text-white focus-visible:outline-2 focus-visible:outline-ink">
                        Đăng nhập
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex min-h-9 items-center justify-center border-2 border-line-strong bg-lime px-4 py-1.5 text-xs font-bold text-white shadow-brutal-sm transition-all hover:bg-ink hover:text-white focus-visible:outline-2 focus-visible:outline-ink">
                        Đăng ký
                    </a>
                @endauth
            </div>
        </div>

        <!-- Category Navigation Subheader -->
        @php
            $allActiveCats = \App\Models\Category::query()
                ->where('status', \App\Enums\CategoryStatus::Active)
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'description']);

            if (method_exists(\App\Models\Category::class, 'children')) {
                $dbParents = \App\Models\Category::query()
                    ->where('status', \App\Enums\CategoryStatus::Active)
                    ->whereNull('parent_id')
                    ->with(['children' => fn ($q) => $q->where('status', \App\Enums\CategoryStatus::Active)->orderBy('name')])
                    ->orderBy('name')
                    ->get();
                $navTree = $dbParents->map(fn ($parent) => [
                    'id' => $parent->id,
                    'name' => $parent->name,
                    'slug' => $parent->slug,
                    'description' => $parent->description,
                    'children' => $parent->children->map(fn ($c) => [
                        'id' => $c->id,
                        'name' => $c->name,
                        'slug' => $c->slug,
                        'description' => $c->description,
                    ])->all(),
                ]);
            } else {
                $catsBySlug = $allActiveCats->keyBy('slug');
                $editorialGroups = [
                    'cong-nghe' => [
                        'tri-tue-nhan-tao', 'an-ninh-mang', 'phan-mem', 'phan-cung',
                        'dien-toan-dam-may', 'du-lieu', 'chuyen-doi-so',
                    ],
                    'kinh-doanh' => [
                        'khoi-nghiep-cong-nghe',
                    ],
                ];

                $childSlugList = collect($editorialGroups)->flatten()->all();
                $navTree = collect();

                foreach ($allActiveCats as $cat) {
                    if (in_array($cat->slug, $childSlugList, true)) {
                        continue;
                    }

                    $childSlugs = $editorialGroups[$cat->slug] ?? [];
                    $children = collect($childSlugs)
                        ->map(fn ($slug) => $catsBySlug->get($slug))
                        ->filter()
                        ->values();

                    $navTree->push([
                        'id' => $cat->id,
                        'name' => $cat->name,
                        'slug' => $cat->slug,
                        'description' => $cat->description,
                        'children' => $children->map(fn ($c) => [
                            'id' => $c->id,
                            'name' => $c->name,
                            'slug' => $c->slug,
                            'description' => $c->description,
                        ])->all(),
                    ]);
                }
            }
        @endphp
        <nav class="border-t border-line bg-paper relative z-30" aria-label="Chuyên mục tin tức">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-1 px-4 py-1 sm:px-6 lg:px-8 text-xs font-mono font-medium">
                <!-- Left Nav Items -->
                <div class="flex items-center gap-1 sm:gap-2 py-0.5 overflow-x-auto sm:overflow-visible scrollbar-none">
                    <a href="{{ route('home') }}" @class([
                        'flex items-center gap-1.5 px-3 py-1.5 transition-colors shrink-0 uppercase tracking-wider pb-1.5 -mb-px border-b-2',
                        'border-lime text-lime font-bold' => request()->routeIs('home'),
                        'border-transparent text-ink-muted hover:text-lime font-semibold' => ! request()->routeIs('home'),
                    ])>
                        <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.293 2.293a1 1 0 011.414 0l7 7A1 1 0 0117 11h-1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-3a1 1 0 00-1-1H9a1 1 0 00-1 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-6H3a1 1 0 01-.707-1.707l7-7z" clip-rule="evenodd" />
                        </svg>
                        <span>Trang chủ</span>
                    </a>

                    <a href="{{ route('news.index', ['sort' => 'latest']) }}" @class([
                        'px-3 py-1.5 transition-colors shrink-0 uppercase tracking-wider pb-1.5 -mb-px border-b-2',
                        'border-lime text-lime font-bold' => request()->fullUrl() === route('news.index', ['sort' => 'latest']),
                        'border-transparent text-ink-muted hover:text-lime font-semibold' => request()->fullUrl() !== route('news.index', ['sort' => 'latest']),
                    ])>
                        Mới nhất
                    </a>

                    @foreach ($navTree as $item)
                        @php
                            $hasChildren = !empty($item['children']);
                            $isChildSelected = collect($item['children'])->contains(fn ($c) => request('category') === $c['slug']);
                            $isDirectSelected = request()->routeIs('news.*') && request('category') === $item['slug'];
                            $isParentActive = $isDirectSelected || $isChildSelected;
                        @endphp

                        @if ($hasChildren)
                            <!-- Dropdown Parent Item -->
                            <div class="relative group/nav shrink-0" data-nav-dropdown>
                                <button type="button"
                                        data-dropdown-trigger
                                        aria-expanded="false"
                                        aria-haspopup="true"
                                        aria-label="Chuyên mục {{ $item['name'] }} và danh mục con"
                                        @class([
                                            'flex items-center gap-1.5 px-3 py-1.5 transition-colors cursor-pointer select-none uppercase tracking-wider pb-1.5 -mb-px border-b-2 font-mono',
                                            'border-lime text-lime font-bold' => $isParentActive,
                                            'border-transparent text-ink-muted hover:text-lime font-semibold' => ! $isParentActive,
                                        ])>
                                    <span>{{ $item['name'] }}</span>
                                    @if ($isChildSelected)
                                        <span class="size-1.5 bg-lime rounded-full"></span>
                                    @endif
                                    <svg class="size-3 text-ink-muted transition-transform duration-200 group-hover/nav:text-lime" data-dropdown-chevron viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <!-- Floating Dropdown Panel -->
                                <div data-dropdown-panel
                                     class="pointer-events-none opacity-0 invisible -translate-y-1 transition-all duration-150 ease-out group-hover/nav:pointer-events-auto group-hover/nav:opacity-100 group-hover/nav:visible group-hover/nav:translate-y-0 group-focus-within/nav:pointer-events-auto group-focus-within/nav:opacity-100 group-focus-within/nav:visible group-focus-within/nav:translate-y-0 absolute left-0 top-full pt-1.5 z-50 min-w-[320px] sm:min-w-[360px]">
                                    <div class="rounded-none border-2 border-line-strong bg-surface p-3.5 shadow-brutal">
                                        <div class="flex items-center justify-between border-b border-line pb-2 px-1 mb-2.5">
                                            <div class="flex items-center gap-2">
                                                <span class="size-2 bg-lime border border-ink"></span>
                                                <span class="text-xs font-bold uppercase tracking-wider font-mono text-ink">
                                                    {{ $item['name'] }}
                                                </span>
                                            </div>
                                            <a href="{{ route('news.index', ['category' => $item['slug']]) }}"
                                               class="text-[11px] font-bold text-lime hover:underline flex items-center gap-1 font-mono">
                                                <span>Xem tất cả tin</span>
                                                <span>&rarr;</span>
                                            </a>
                                        </div>

                                        <div @class([
                                            'grid gap-1.5',
                                            'grid-cols-2' => count($item['children']) > 3,
                                            'grid-cols-1' => count($item['children']) <= 3,
                                        ])>
                                            @foreach ($item['children'] as $child)
                                                @php
                                                    $isChildActive = request()->routeIs('news.*') && request('category') === $child['slug'];
                                                @endphp
                                                <a href="{{ route('news.index', ['category' => $child['slug']]) }}"
                                                   @class([
                                                       'flex items-center justify-between border px-2.5 py-1.5 text-xs transition-all font-sans',
                                                       'border-line-strong bg-lime text-white font-bold shadow-2xs' => $isChildActive,
                                                       'border-line-light text-ink hover:border-line-strong hover:bg-lime hover:text-white' => ! $isChildActive,
                                                   ])>
                                                    <span class="truncate">{{ $child['name'] }}</span>
                                                    <span class="text-[10px] shrink-0 ml-1 font-mono">&rsaquo;</span>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('news.index', ['category' => $item['slug']]) }}"
                               @class([
                                   'px-3 py-1.5 transition-colors shrink-0 uppercase tracking-wider pb-1.5 -mb-px border-b-2 font-mono',
                                   'border-lime text-lime font-bold' => $isParentActive,
                                   'border-transparent text-ink-muted hover:text-lime font-semibold' => ! $isParentActive,
                               ])>
                                {{ $item['name'] }}
                            </a>
                        @endif
                    @endforeach
                </div>

                <!-- Right Nav Items: Mega Menu Trigger -->
                <div class="relative group/mega shrink-0 hidden md:block" data-mega-menu>
                    <button type="button"
                            data-mega-trigger
                            aria-expanded="false"
                            class="flex items-center gap-1.5 rounded-none border border-line-strong bg-surface px-2.5 py-1 text-xs font-bold font-mono uppercase tracking-wider text-ink hover:bg-ink hover:text-white transition-colors cursor-pointer select-none">
                        <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 4.25A2.25 2.25 0 015.25 2h1.5A2.25 2.25 0 019 4.25v1.5A2.25 2.25 0 016.75 8h-1.5A2.25 2.25 0 013 5.75v-1.5zm0 8.5A2.25 2.25 0 015.25 10.5h1.5A2.25 2.25 0 019 12.75v1.5A2.25 2.25 0 016.75 16.5h-1.5A2.25 2.25 0 013 14.25v-1.5zm8.5-8.5A2.25 2.25 0 0113.75 2h1.5A2.25 2.25 0 0117.5 4.25v1.5A2.25 2.25 0 0115.25 8h-1.5A2.25 2.25 0 0111.5 5.75v-1.5zm0 8.5a2.25 2.25 0 012.25-2.25h1.5a2.25 2.25 0 012.25 2.25v1.5a2.25 2.25 0 01-2.25 2.25h-1.5a2.25 2.25 0 01-2.25-2.25v-1.5z" clip-rule="evenodd" />
                        </svg>
                        <span>Sơ đồ chuyên mục</span>
                        <svg class="size-3 text-ink transition-transform duration-200 group-hover/mega:rotate-180" data-mega-chevron viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <!-- Mega Menu Flyout Panel -->
                    <div data-mega-panel
                         class="pointer-events-none opacity-0 invisible -translate-y-1 transition-all duration-150 ease-out group-hover/mega:pointer-events-auto group-hover/mega:opacity-100 group-hover/mega:visible group-hover/mega:translate-y-0 group-focus-within/mega:pointer-events-auto group-focus-within/mega:opacity-100 group-focus-within/mega:visible group-focus-within/mega:translate-y-0 absolute right-0 top-full pt-1.5 z-50 w-[480px] max-w-[90vw]">
                        <div class="rounded-none border-2 border-line-strong bg-surface p-4 shadow-brutal">
                            <div class="flex items-center justify-between border-b border-line pb-2.5 mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="size-2 bg-lime border border-ink"></span>
                                    <span class="text-xs font-bold uppercase tracking-wider font-mono text-ink">Toàn bộ chuyên mục</span>
                                </div>
                                <a href="{{ route('news.index') }}" class="text-xs font-bold font-mono text-ink hover:underline">
                                    Tất cả tin tức &rarr;
                                </a>
                            </div>

                            <div class="grid grid-cols-2 gap-4 max-h-[60vh] overflow-y-auto pr-1 font-sans">
                                @foreach ($navTree as $treeItem)
                                    <div class="space-y-1.5">
                                        <a href="{{ route('news.index', ['category' => $treeItem['slug']]) }}"
                                           class="text-xs font-bold text-ink hover:underline transition-colors flex items-center gap-1">
                                            <span>{{ $treeItem['name'] }}</span>
                                            <span class="text-ink-muted text-[10px] font-mono">&rsaquo;</span>
                                        </a>
                                        @if (!empty($treeItem['children']))
                                            <ul class="space-y-1 border-l-2 border-line pl-2.5 text-[11px]">
                                                @foreach ($treeItem['children'] as $treeChild)
                                                    <li>
                                                        <a href="{{ route('news.index', ['category' => $treeChild['slug']]) }}"
                                                           class="text-ink-muted hover:text-ink hover:underline transition-colors block truncate">
                                                            {{ $treeChild['name'] }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Mobile Navigation Drawer -->
    <div id="mobile-menu-overlay" class="fixed inset-0 z-50 hidden bg-ink/70 backdrop-blur-xs transition-opacity lg:hidden" aria-hidden="true"></div>
    <div id="mobile-menu-drawer" class="fixed inset-y-0 left-0 z-50 w-72 -translate-x-full border-r-2 border-line-strong bg-paper p-5 transition-transform duration-200 ease-in-out lg:hidden flex flex-col justify-between overflow-y-auto">
        <div class="space-y-5">
            <div class="flex items-center justify-between border-b border-line pb-4">
                <span class="text-lg font-black tracking-tight text-ink">
                    News<span class="underline decoration-lime decoration-2">Hub</span>
                </span>
                <button type="button" id="mobile-menu-close-btn" class="grid size-8 place-items-center text-ink hover:bg-surface border border-line" aria-label="Đóng menu">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Mobile Search -->
            <form action="{{ route('news.index') }}" method="GET" class="relative">
                <input type="text"
                       name="q"
                       value="{{ request('q') }}"
                       placeholder="Tìm kiếm tin tức..."
                       class="w-full rounded-none border border-line bg-surface py-2 pl-9 pr-3 text-xs text-ink placeholder:text-ink-muted outline-none focus:border-line-strong">
                <svg class="pointer-events-none absolute left-3 top-2.5 size-4 text-ink-muted" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                </svg>
            </form>

            <!-- Mobile Categories -->
            <div class="space-y-1">
                <p class="px-2 text-[10px] font-mono font-bold uppercase tracking-widest text-ink-muted">Chuyên mục</p>
                <div class="mt-1 space-y-1">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 px-2.5 py-1.5 text-xs font-bold text-ink hover:bg-surface">
                        Trang chủ
                    </a>
                    <a href="{{ route('news.index', ['sort' => 'latest']) }}" class="flex items-center gap-2 px-2.5 py-1.5 text-xs font-bold text-ink hover:bg-surface">
                        Mới nhất
                    </a>

                    @foreach ($navTree as $parentItem)
                        @php
                            $hasChildren = !empty($parentItem['children']);
                            $isChildSelected = collect($parentItem['children'])->contains(fn ($c) => request('category') === $c['slug']);
                            $isDirectSelected = request()->routeIs('news.*') && request('category') === $parentItem['slug'];
                            $isParentActive = $isDirectSelected || $isChildSelected;
                        @endphp
                        <div class="space-y-0.5" data-mobile-accordion>
                            <div @class([
                                'flex items-center justify-between px-2.5 py-1.5 text-xs font-medium transition-colors border',
                                'border-line-strong bg-lime text-white font-bold shadow-2xs' => $isParentActive,
                                'border-transparent text-ink hover:bg-surface' => ! $isParentActive,
                            ])>
                                <a href="{{ route('news.index', ['category' => $parentItem['slug']]) }}" class="flex-1 flex items-center gap-1.5">
                                    <span>{{ $parentItem['name'] }}</span>
                                    @if ($isChildSelected)
                                        <span class="size-1.5 bg-white"></span>
                                    @endif
                                </a>

                                @if ($hasChildren)
                                    <button type="button"
                                            data-accordion-toggle
                                            aria-label="Mở danh mục con của {{ $parentItem['name'] }}"
                                            class="p-1 text-ink hover:bg-paper cursor-pointer">
                                        <svg class="size-3.5 transition-transform duration-200 {{ $isParentActive ? 'rotate-180' : '' }}" data-accordion-icon viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                @endif
                            </div>

                            @if ($hasChildren)
                                <div data-accordion-content class="grid transition-[grid-template-rows] duration-200 ease-in-out {{ $isParentActive ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]' }}">
                                    <div class="overflow-hidden">
                                        <div class="pl-3 pr-1 py-1 space-y-0.5 border-l-2 border-line ml-3 mt-0.5 font-sans">
                                            <a href="{{ route('news.index', ['category' => $parentItem['slug']]) }}"
                                               class="block px-2 py-1 text-[11px] font-medium text-ink hover:underline">
                                                &bull; Xem tất cả {{ $parentItem['name'] }}
                                            </a>
                                            @foreach ($parentItem['children'] as $child)
                                                @php
                                                    $isChildActive = request()->routeIs('news.*') && request('category') === $child['slug'];
                                                @endphp
                                                <a href="{{ route('news.index', ['category' => $child['slug']]) }}"
                                                   @class([
                                                       'flex items-center justify-between px-2 py-1 text-[11px] font-medium transition-colors',
                                                       'text-ink font-bold underline decoration-lime decoration-2' => $isChildActive,
                                                       'text-ink-muted hover:text-ink' => ! $isChildActive,
                                                   ])>
                                                    <span>{{ $child['name'] }}</span>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach

                    <a href="{{ route('news.index') }}" class="flex items-center gap-2 px-2.5 py-2 text-xs font-bold font-mono text-ink hover:underline">
                        Tất cả tin tức &rarr;
                    </a>
                </div>
            </div>
        </div>

        <div class="border-t border-line pt-4 space-y-2">
            @auth
                <div class="flex items-center gap-3 px-1 pb-3 border-b border-line mb-2">
                    <span class="grid size-9 place-items-center rounded-full bg-[#1D4ED8] text-xs font-bold font-mono text-white select-none">
                        {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-xs font-bold text-ink">{{ auth()->user()->name }}</p>
                        <p class="truncate text-[11px] font-mono text-ink-muted">{{ auth()->user()->email }}</p>
                        <div class="mt-0.5">
                            @if (auth()->user()->role === \App\Enums\UserRole::Admin)
                                <span class="text-[10px] font-bold font-mono text-ink bg-pink px-1">ADMIN</span>
                            @elseif (auth()->user()->role === \App\Enums\UserRole::Author)
                                <span class="text-[10px] font-bold font-mono text-ink bg-lime px-1">TÁC GIẢ</span>
                            @else
                                <span class="text-[10px] font-medium font-mono text-ink-muted">ĐỘC GIẢ</span>
                            @endif
                        </div>
                    </div>
                </div>

                <a href="{{ route('dashboard') }}" class="flex w-full items-center justify-center gap-2 border border-line-strong bg-lime py-2 text-xs font-bold text-ink shadow-brutal-sm">
                    Bảng điều khiển
                </a>
                <a href="{{ route('profile.edit') }}" class="flex w-full items-center justify-center gap-2 border border-line bg-surface py-2 text-xs font-medium text-ink hover:bg-paper">
                    Hồ sơ cá nhân & Mật khẩu
                </a>
                <a href="{{ route('profile.comments') }}" class="flex w-full items-center justify-center gap-2 border border-line bg-surface py-2 text-xs font-medium text-ink hover:bg-paper">
                    Bình luận của tôi
                </a>
                <form method="POST" action="{{ route('logout') }}" class="w-full pt-1">
                    @csrf
                    <button type="submit" class="flex w-full items-center justify-center gap-2 border border-line-strong bg-rose-100 py-2 text-xs font-bold text-danger hover:bg-rose-200 transition-colors">
                        Đăng xuất
                    </button>
                </form>
            @else
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('login') }}" class="flex items-center justify-center border border-line-strong bg-surface py-2 text-xs font-bold text-ink hover:bg-paper">
                        Đăng nhập
                    </a>
                    <a href="{{ route('register') }}" class="flex items-center justify-center border border-line-strong bg-lime py-2 text-xs font-bold text-ink shadow-brutal-sm">
                        Đăng ký
                    </a>
                </div>
            @endauth
        </div>
    </div>

    <!-- Main Body Container -->
    <main class="flex-1 mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        @if (session('status'))
            <div role="status" class="mb-6 flex items-start gap-3 border-2 border-line-strong bg-lime/20 p-3.5 text-xs font-medium text-ink shadow-brutal-sm">
                <svg class="size-4 shrink-0 text-ink mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div role="alert" class="mb-6 border-2 border-line-strong bg-rose-50 p-4 text-xs text-danger shadow-brutal-sm">
                <p class="font-bold text-danger flex items-center gap-1.5 font-mono uppercase tracking-wider">
                    <svg class="size-4 text-danger" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>
                    Vui lòng kiểm tra lại:
                </p>
                <ul class="mt-2 list-inside list-disc space-y-1 text-ink">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Professional Editorial News Portal Footer -->
    <footer class="mt-auto border-t border-line bg-paper-light text-ink-muted text-xs">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Col 1: Editorial Info & Masthead -->
                <div class="space-y-3">
                    <div class="flex items-center gap-2.5">
                        <span class="grid size-8 shrink-0 place-items-center bg-lime text-white border border-line-strong font-serif font-black text-base shadow-brutal-sm select-none">
                            N
                        </span>
                        <div class="leading-none">
                            <span class="block text-base font-serif font-bold tracking-tight text-[#1D4ED8]">
                                NewsHub
                            </span>
                            <span class="block text-[8px] font-sans font-bold uppercase tracking-widest text-[#6B7280] mt-0.5">
                                Editorial Newsroom
                            </span>
                        </div>
                    </div>
                    <p class="text-xs text-ink-muted leading-relaxed">
                        Báo điện tử đa phương tiện cung cấp thông tin thời sự, công nghệ, kinh tế và đời sống chính xác, nhanh chóng và khách quan.
                    </p>
                    <div class="space-y-1 text-[11px] text-ink-light pt-1 font-mono">
                        <p>Giấy phép xuất bản số: <strong class="text-ink">128/GP-BTTTT</strong></p>
                        <p>Tổng biên tập: Ban biên tập NewsHub</p>
                        <p>Hotline: <strong class="text-ink font-bold">1900 8888</strong></p>
                        <p>Email: <a href="mailto:toasoan@newshub.vn" class="text-ink hover:underline">toasoan@newshub.vn</a></p>
                    </div>
                </div>

                <!-- Col 2: Chuyên mục chính -->
                <div class="space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider font-mono text-ink">Chuyên mục tin tức</h3>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('home') }}" class="hover:text-ink hover:underline transition-colors">Trang chủ</a></li>
                        <li><a href="{{ route('news.index', ['sort' => 'latest']) }}" class="hover:text-ink hover:underline transition-colors">Tin mới 24/7</a></li>
                        <li><a href="{{ route('news.index', ['sort' => 'popular']) }}" class="hover:text-ink hover:underline transition-colors">Tin đọc nhiều nhất</a></li>
                        @foreach ($navTree as $navParent)
                            <li><a href="{{ route('news.index', ['category' => $navParent['slug']]) }}" class="hover:text-ink hover:underline transition-colors">{{ $navParent['name'] }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <!-- Col 3: Dành cho độc giả & Tác giả -->
                <div class="space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider font-mono text-ink">Độc giả & Tác giả</h3>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('profile.comments') }}" class="hover:text-ink hover:underline transition-colors">Lịch sử bình luận</a></li>
                        <li><a href="{{ route('dashboard') }}" class="hover:text-ink hover:underline transition-colors">Đăng ký làm Tác giả / CTV</a></li>
                        <li><a href="{{ route('profile.edit') }}" class="hover:text-ink hover:underline transition-colors">Tài khoản & Thiết lập</a></li>
                        @guest
                            <li><a href="{{ route('login') }}" class="hover:text-ink hover:underline transition-colors">Đăng nhập tài khoản</a></li>
                        @endguest
                    </ul>
                </div>

                <!-- Col 4: Pháp lý & Tiêu chuẩn xuất bản -->
                <div class="space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider font-mono text-ink">Tiêu chuẩn & Pháp lý</h3>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('pages.privacy') }}" class="hover:text-ink hover:underline transition-colors">Chính sách bảo mật thông tin</a></li>
                        <li><a href="{{ route('pages.terms') }}" class="hover:text-ink hover:underline transition-colors">Điều khoản dịch vụ độc giả</a></li>
                        <li><a href="{{ route('pages.moderation-policy') }}" class="hover:text-ink hover:underline transition-colors">Quy chế kiểm duyệt nội dung</a></li>
                        <li><a href="{{ route('pages.contact') }}" class="hover:text-ink hover:underline transition-colors">Liên hệ quảng cáo & Tòa soạn</a></li>
                    </ul>
                    <div class="mt-4 border border-line bg-surface p-3 text-[11px] leading-relaxed text-ink-muted">
                        Nội dung trên NewsHub được bảo vệ bản quyền. Nghiêm cấm sao chép dưới mọi hình thức khi chưa có sự đồng ý bằng văn bản.
                    </div>
                </div>
            </div>

            <!-- Bottom Copyright & Back to Top -->
            <div class="mt-8 flex flex-col items-center justify-between gap-4 border-t border-line pt-6 text-[11px] font-mono text-ink-muted sm:flex-row">
                <p>© {{ date('Y') }} NewsHub. Toàn bộ bản quyền thuộc về Tòa soạn Báo điện tử NewsHub.</p>
                <div class="flex items-center gap-4">
                    <button type="button" onclick="window.scrollTo({top: 0, behavior: 'smooth'})" class="inline-flex items-center gap-1 hover:text-ink hover:underline transition-colors cursor-pointer">
                        <span>Lên đầu trang</span>
                        <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 17a.75.75 0 01-.75-.75V5.612L5.29 9.77a.75.75 0 01-1.08-1.04l5.25-5.5a.75.75 0 011.08 0l5.25 5.5a.75.75 0 11-1.08 1.04l-3.96-4.158V16.25A.75.75 0 0110 17z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </footer>

    <!-- Universal Custom Confirmation Modal (Neo-Brutalist) -->
    <div id="universal-confirm-modal" class="fixed inset-0 z-50 hidden bg-ink/60 backdrop-blur-xs flex items-center justify-center p-4" aria-modal="true" role="dialog" aria-labelledby="confirm-modal-title">
        <div class="w-full max-w-md border-2 border-line-strong bg-surface p-6 shadow-brutal-lg animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-start gap-3">
                <div id="confirm-modal-icon-container" class="grid size-10 shrink-0 place-items-center border-2 border-line-strong bg-lime text-white">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M12 9v4m0 4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                    </svg>
                </div>
                <div class="flex-1 space-y-1">
                    <h3 id="confirm-modal-title" class="text-base font-black text-ink tracking-tight">Xác nhận thao tác</h3>
                    <p id="confirm-modal-message" class="text-xs text-ink-muted leading-relaxed">Bạn có chắc chắn muốn thực hiện thao tác này không?</p>
                </div>
            </div>
            <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-line">
                <button type="button" id="confirm-modal-cancel-btn" class="border border-line-strong bg-surface px-4 py-2 text-xs font-bold text-ink hover:bg-paper cursor-pointer transition-colors">
                    Hủy bỏ
                </button>
                <button type="button" id="confirm-modal-confirm-btn" class="border border-line-strong bg-lime px-4 py-2 text-xs font-bold text-white shadow-brutal-sm hover:bg-ink hover:text-white cursor-pointer transition-colors">
                    Đồng ý
                </button>
            </div>
        </div>
    </div>

    <!-- Client-side Scripts -->
    <script>
        // Custom Confirmation Modal Controller
        window.customConfirm = function(message, options = {}) {
            return new Promise((resolve) => {
                const modal = document.getElementById('universal-confirm-modal');
                const titleEl = document.getElementById('confirm-modal-title');
                const msgEl = document.getElementById('confirm-modal-message');
                const confirmBtn = document.getElementById('confirm-modal-confirm-btn');
                const cancelBtn = document.getElementById('confirm-modal-cancel-btn');
                const iconContainer = document.getElementById('confirm-modal-icon-container');

                if (!modal) {
                    resolve(window.confirm(message));
                    return;
                }

                titleEl.textContent = options.title || 'Xác nhận thao tác';
                msgEl.textContent = message || 'Bạn có chắc muốn thực hiện thao tác này?';
                confirmBtn.textContent = options.confirmText || 'Đồng ý';
                cancelBtn.textContent = options.cancelText || 'Hủy bỏ';

                if (options.isDanger) {
                    confirmBtn.className = 'border border-line-strong bg-danger text-white px-4 py-2 text-xs font-bold shadow-brutal-sm hover:opacity-90 cursor-pointer transition-colors';
                    iconContainer.className = 'grid size-10 shrink-0 place-items-center border-2 border-line-strong bg-rose-100 text-danger';
                } else {
                    confirmBtn.className = 'border border-line-strong bg-lime text-white px-4 py-2 text-xs font-bold shadow-brutal-sm hover:bg-ink hover:text-white cursor-pointer transition-colors';
                    iconContainer.className = 'grid size-10 shrink-0 place-items-center border-2 border-line-strong bg-lime text-white';
                }

                modal.classList.remove('hidden');

                const cleanup = () => {
                    modal.classList.add('hidden');
                    confirmBtn.removeEventListener('click', onConfirm);
                    cancelBtn.removeEventListener('click', onCancel);
                    document.removeEventListener('keydown', onKey);
                };

                const onConfirm = () => {
                    cleanup();
                    resolve(true);
                };

                const onCancel = () => {
                    cleanup();
                    resolve(false);
                };

                const onKey = (e) => {
                    if (e.key === 'Escape') onCancel();
                };

                confirmBtn.addEventListener('click', onConfirm);
                cancelBtn.addEventListener('click', onCancel);
                document.addEventListener('keydown', onKey);
            });
        };

        // Mobile drawer navigation
        const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
        const mobileMenuDrawer = document.getElementById('mobile-menu-drawer');
        const mobileMenuOpenBtn = document.getElementById('mobile-menu-open-btn');
        const mobileMenuCloseBtn = document.getElementById('mobile-menu-close-btn');

        const setMobileMenuOpen = (open) => {
            mobileMenuDrawer.classList.toggle('-translate-x-full', !open);
            mobileMenuOverlay.classList.toggle('hidden', !open);
            document.body.classList.toggle('overflow-hidden', open);
        };

        mobileMenuOpenBtn?.addEventListener('click', () => setMobileMenuOpen(true));
        mobileMenuCloseBtn?.addEventListener('click', () => setMobileMenuOpen(false));
        mobileMenuOverlay?.addEventListener('click', () => setMobileMenuOpen(false));

        // Mobile drawer accordion toggling
        document.querySelectorAll('[data-accordion-toggle]').forEach((btn) => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const container = btn.closest('[data-mobile-accordion]');
                const content = container?.querySelector('[data-accordion-content]');
                const icon = btn.querySelector('[data-accordion-icon]');
                if (content) {
                    const isOpen = content.classList.contains('grid-rows-[1fr]');
                    if (isOpen) {
                        content.classList.remove('grid-rows-[1fr]');
                        content.classList.add('grid-rows-[0fr]');
                        icon?.classList.remove('rotate-180');
                    } else {
                        content.classList.remove('grid-rows-[0fr]');
                        content.classList.add('grid-rows-[1fr]');
                        icon?.classList.add('rotate-180');
                    }
                }
            });
        });

        // Category dropdown
        document.querySelectorAll('[data-nav-dropdown]').forEach((dropdown) => {
            const trigger = dropdown.querySelector('[data-dropdown-trigger]');
            const panel = dropdown.querySelector('[data-dropdown-panel]');
            const chevron = dropdown.querySelector('[data-dropdown-chevron]');

            const openDropdown = () => {
                panel?.classList.remove('pointer-events-none', 'opacity-0', 'invisible', '-translate-y-1');
                panel?.classList.add('pointer-events-auto', 'opacity-100', 'visible', 'translate-y-0');
                trigger?.setAttribute('aria-expanded', 'true');
                chevron?.classList.add('rotate-180');
            };

            const closeDropdown = () => {
                panel?.classList.add('pointer-events-none', 'opacity-0', 'invisible', '-translate-y-1');
                panel?.classList.remove('pointer-events-auto', 'opacity-100', 'visible', 'translate-y-0');
                trigger?.setAttribute('aria-expanded', 'false');
                chevron?.classList.remove('rotate-180');
            };

            trigger?.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const isOpen = trigger.getAttribute('aria-expanded') === 'true';
                document.querySelectorAll('[data-nav-dropdown]').forEach(other => {
                    if (other !== dropdown) {
                        const oPanel = other.querySelector('[data-dropdown-panel]');
                        const oTrigger = other.querySelector('[data-dropdown-trigger]');
                        const oChevron = other.querySelector('[data-dropdown-chevron]');
                        oPanel?.classList.add('pointer-events-none', 'opacity-0', 'invisible', '-translate-y-1');
                        oPanel?.classList.remove('pointer-events-auto', 'opacity-100', 'visible', 'translate-y-0');
                        oTrigger?.setAttribute('aria-expanded', 'false');
                        oChevron?.classList.remove('rotate-180');
                    }
                });
                if (isOpen) {
                    closeDropdown();
                } else {
                    openDropdown();
                }
            });

            dropdown.addEventListener('mouseenter', openDropdown);
            dropdown.addEventListener('mouseleave', closeDropdown);
        });

        // Mega Menu controller
        document.querySelectorAll('[data-mega-menu]').forEach((mega) => {
            const trigger = mega.querySelector('[data-mega-trigger]');
            const panel = mega.querySelector('[data-mega-panel]');
            const chevron = mega.querySelector('[data-mega-chevron]');

            const openMega = () => {
                panel?.classList.remove('pointer-events-none', 'opacity-0', 'invisible', '-translate-y-1');
                panel?.classList.add('pointer-events-auto', 'opacity-100', 'visible', 'translate-y-0');
                trigger?.setAttribute('aria-expanded', 'true');
                chevron?.classList.add('rotate-180');
            };

            const closeMega = () => {
                panel?.classList.add('pointer-events-none', 'opacity-0', 'invisible', '-translate-y-1');
                panel?.classList.remove('pointer-events-auto', 'opacity-100', 'visible', 'translate-y-0');
                trigger?.setAttribute('aria-expanded', 'false');
                chevron?.classList.remove('rotate-180');
            };

            trigger?.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const isOpen = trigger.getAttribute('aria-expanded') === 'true';
                if (isOpen) {
                    closeMega();
                } else {
                    openMega();
                }
            });

            mega.addEventListener('mouseenter', openMega);
            mega.addEventListener('mouseleave', closeMega);
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('[data-nav-dropdown]')) {
                document.querySelectorAll('[data-nav-dropdown]').forEach((dropdown) => {
                    const panel = dropdown.querySelector('[data-dropdown-panel]');
                    const trigger = dropdown.querySelector('[data-dropdown-trigger]');
                    const chevron = dropdown.querySelector('[data-dropdown-chevron]');
                    panel?.classList.add('pointer-events-none', 'opacity-0', 'invisible', '-translate-y-1');
                    panel?.classList.remove('pointer-events-auto', 'opacity-100', 'visible', 'translate-y-0');
                    trigger?.setAttribute('aria-expanded', 'false');
                    chevron?.classList.remove('rotate-180');
                });
            }
            if (!e.target.closest('[data-mega-menu]')) {
                document.querySelectorAll('[data-mega-menu]').forEach((mega) => {
                    const panel = mega.querySelector('[data-mega-panel]');
                    const trigger = mega.querySelector('[data-mega-trigger]');
                    const chevron = mega.querySelector('[data-mega-chevron]');
                    panel?.classList.add('pointer-events-none', 'opacity-0', 'invisible', '-translate-y-1');
                    panel?.classList.remove('pointer-events-auto', 'opacity-100', 'visible', 'translate-y-0');
                    trigger?.setAttribute('aria-expanded', 'false');
                    chevron?.classList.remove('rotate-180');
                });
            }
        });

        // User Profile Dropdown Controller
        document.querySelectorAll('[data-user-menu]').forEach((menu) => {
            const trigger = menu.querySelector('[data-user-menu-trigger]');
            const panel = menu.querySelector('[data-user-menu-panel]');
            const chevron = menu.querySelector('[data-user-menu-chevron]');

            const openMenu = () => {
                panel?.classList.remove('pointer-events-none', 'opacity-0', 'invisible', 'translate-y-1');
                panel?.classList.add('pointer-events-auto', 'opacity-100', 'visible', 'translate-y-0');
                trigger?.setAttribute('aria-expanded', 'true');
                chevron?.classList.add('rotate-180');
            };

            const closeMenu = () => {
                panel?.classList.add('pointer-events-none', 'opacity-0', 'invisible', 'translate-y-1');
                panel?.classList.remove('pointer-events-auto', 'opacity-100', 'visible', 'translate-y-0');
                trigger?.setAttribute('aria-expanded', 'false');
                chevron?.classList.remove('rotate-180');
            };

            trigger?.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const isOpen = trigger.getAttribute('aria-expanded') === 'true';
                if (isOpen) {
                    closeMenu();
                } else {
                    openMenu();
                }
            });

            menu.addEventListener('mouseenter', openMenu);
            menu.addEventListener('mouseleave', closeMenu);
        });

        document.addEventListener('click', (e) => {
            if (!e.target.closest('[data-user-menu]')) {
                document.querySelectorAll('[data-user-menu]').forEach((menu) => {
                    const panel = menu.querySelector('[data-user-menu-panel]');
                    const trigger = menu.querySelector('[data-user-menu-trigger]');
                    const chevron = menu.querySelector('[data-user-menu-chevron]');
                    panel?.classList.add('pointer-events-none', 'opacity-0', 'invisible', 'translate-y-1');
                    panel?.classList.remove('pointer-events-auto', 'opacity-100', 'visible', 'translate-y-0');
                    trigger?.setAttribute('aria-expanded', 'false');
                    chevron?.classList.remove('rotate-180');
                });
            }
        });

        // Custom Client-Side Inline Form Validation (Prevents ugly browser HTML5 popups)
        document.addEventListener('invalid', (e) => {
            // Prevent default browser tooltip bubble ("Please fill out this field")
            e.preventDefault();
            renderFieldInlineError(e.target);
        }, true);

        function getFieldLabelText(field) {
            if (field.id) {
                const label = document.querySelector(`label[for="${field.id}"]`);
                if (label) {
                    return label.textContent.replace(/[*:\n\r]/g, '').trim();
                }
            }
            const parentLabel = field.closest('label');
            if (parentLabel) {
                return parentLabel.textContent.replace(/[*:\n\r]/g, '').trim();
            }
            return '';
        }

        function getSmartFieldErrorMessage(field) {
            const v = field.validity;
            const label = getFieldLabelText(field);

            if (v.valueMissing) {
                if (field.name === 'password_confirmation') {
                    return 'Vui lòng xác nhận lại mật khẩu.';
                }
                if (field.type === 'password' || field.name === 'password') {
                    return 'Vui lòng nhập mật khẩu.';
                }
                if (field.type === 'email' || field.name === 'email') {
                    return 'Vui lòng nhập địa chỉ email.';
                }
                if (field.tagName === 'SELECT') {
                    return label ? `Vui lòng chọn ${label.toLowerCase()}.` : 'Vui lòng chọn một mục trong danh sách.';
                }
                if (field.type === 'checkbox') {
                    return 'Vui lòng đánh dấu chọn ô này để tiếp tục.';
                }
                if (field.tagName === 'TEXTAREA' || field.name === 'content') {
                    return 'Vui lòng nhập nội dung.';
                }
                if (label) {
                    return `Vui lòng nhập ${label.toLowerCase()}.`;
                }
                return 'Vui lòng không để trống trường này.';
            }

            if (v.typeMismatch) {
                if (field.type === 'email') {
                    return 'Địa chỉ email không hợp lệ (ví dụ: name@example.com).';
                }
                if (field.type === 'url') {
                    return 'Vui lòng nhập đúng định dạng liên kết URL (ví dụ: https://example.com).';
                }
            }

            if (v.tooShort) {
                return `Vui lòng nhập tối thiểu ${field.minLength} ký tự (hiện có ${field.value.length} ký tự).`;
            }

            if (v.tooLong) {
                return `Vui lòng không nhập quá ${field.maxLength} ký tự.`;
            }

            if (v.patternMismatch) {
                return 'Dữ liệu nhập vào chưa đúng định dạng yêu cầu.';
            }

            return field.validationMessage || 'Dữ liệu không hợp lệ.';
        }

        function renderFieldInlineError(field) {
            clearFieldInlineError(field);

            field.classList.add('!border-danger', '!ring-1', '!ring-danger', 'bg-rose-50/20');

            const msg = getSmartFieldErrorMessage(field);
            const errorP = document.createElement('p');
            errorP.className = 'custom-field-error mt-1.5 flex items-center gap-1.5 text-xs text-danger font-sans font-medium';
            errorP.setAttribute('role', 'alert');
            errorP.innerHTML = `
                <svg class="size-3.5 shrink-0 text-danger" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                </svg>
                <span>${msg}</span>
            `;

            // Insert after input wrapper (e.g., .relative with icon) or the field itself
            const anchor = field.closest('.relative') || field;
            anchor.insertAdjacentElement('afterend', errorP);

            const onInputFix = () => {
                if (field.checkValidity()) {
                    clearFieldInlineError(field);
                    field.removeEventListener('input', onInputFix);
                    field.removeEventListener('change', onInputFix);
                }
            };
            field.addEventListener('input', onInputFix);
            field.addEventListener('change', onInputFix);
        }

        function clearFieldInlineError(field) {
            field.classList.remove('!border-danger', '!ring-1', '!ring-danger', 'bg-rose-50/20');
            const anchor = field.closest('.relative') || field;
            const nextEl = anchor.nextElementSibling;
            if (nextEl && nextEl.classList.contains('custom-field-error')) {
                nextEl.remove();
            }
        }

        // Intercept submit clicks to show custom inline validation before native bubbles
        document.addEventListener('click', (event) => {
            const submitBtn = event.target.closest('button[type="submit"], input[type="submit"]');
            if (!submitBtn) return;
            const form = submitBtn.form;
            if (!form) return;

            const fields = Array.from(form.querySelectorAll('input, select, textarea'));
            let firstInvalid = null;
            let hasError = false;

            form.querySelectorAll('.custom-field-error').forEach(el => el.remove());
            fields.forEach(field => clearFieldInlineError(field));

            for (const field of fields) {
                if (field.disabled || field.type === 'hidden' || field.type === 'submit' || field.type === 'button') {
                    continue;
                }
                if (!field.checkValidity()) {
                    hasError = true;
                    if (!firstInvalid) {
                        firstInvalid = field;
                    }
                    renderFieldInlineError(field);
                }
            }

            if (hasError) {
                event.preventDefault();
                event.stopPropagation();
                if (firstInvalid) {
                    firstInvalid.focus();
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        }, true);

        // Form Submit & Double-Submit Guard
        document.addEventListener('submit', (event) => {
            const form = event.target;
            if (!(form instanceof HTMLFormElement)) {
                return;
            }

            const fields = Array.from(form.querySelectorAll('input, select, textarea'));
            let firstInvalid = null;
            let hasError = false;

            for (const field of fields) {
                if (field.disabled || field.type === 'hidden' || field.type === 'submit' || field.type === 'button') {
                    continue;
                }
                if (!field.checkValidity()) {
                    hasError = true;
                    if (!firstInvalid) {
                        firstInvalid = field;
                    }
                    renderFieldInlineError(field);
                }
            }

            if (hasError) {
                event.preventDefault();
                event.stopPropagation();
                if (firstInvalid) {
                    firstInvalid.focus();
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return;
            }

            // Double submit prevention when all fields are valid
            if (form.dataset.submitting === 'true') {
                event.preventDefault();
                return;
            }
            form.dataset.submitting = 'true';
            if (event.submitter instanceof HTMLButtonElement) {
                event.submitter.setAttribute('aria-busy', 'true');
                event.submitter.classList.add('pointer-events-none', 'opacity-70');
            }
        });
    </script>
</body>
</html>
