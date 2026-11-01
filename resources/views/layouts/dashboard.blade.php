<!DOCTYPE html>
<html lang="vi" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} · {{ config('app.name') }}</title>
    <meta name="description" content="Khu vực quản lý nội dung {{ config('app.name') }}.">
    @vite('resources/css/app.css')
</head>
<body class="min-h-dvh bg-paper font-sans text-ink antialiased selection:bg-lime selection:text-ink">
    <!-- Skip to main content -->
    <a href="#dashboard-content" class="sr-only z-50 rounded-none border border-line-strong bg-lime px-4 py-2 text-sm font-bold text-ink shadow-brutal-sm focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:outline-none">
        Chuyển đến nội dung chính
    </a>

    <!-- Mobile sidebar overlay -->
    <div id="dashboard-overlay" class="fixed inset-0 z-40 hidden bg-ink/70 backdrop-blur-xs transition-opacity lg:hidden" aria-hidden="true"></div>

    <!-- Sidebar -->
    <aside id="dashboard-sidebar" class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col border-r border-line bg-paper transition-transform duration-200 ease-in-out motion-reduce:transition-none lg:translate-x-0" aria-label="Điều hướng bảng điều khiển">
        <!-- Brand / Header -->
        <div class="flex h-16 shrink-0 items-center justify-between border-b border-line px-4 bg-paper-light">
            <a href="{{ route('home') }}"
               title="Về trang chủ tin tức NewsHub"
               class="group flex items-center gap-2.5 rounded-none px-1 py-1 transition-colors focus-visible:outline-2 focus-visible:outline-ink">
                <span class="grid size-8 shrink-0 place-items-center bg-lime text-white font-serif font-black text-base shadow-brutal-sm border border-line-strong select-none">
                    N
                </span>
                <span class="min-w-0 leading-none">
                    <span class="block truncate text-base font-serif font-bold tracking-tight text-ink">
                        NewsHub
                    </span>
                    <span class="block text-[9px] font-mono font-bold uppercase tracking-wider text-ink-muted flex items-center gap-0.5 mt-1">
                        <span>&larr; Trang tin tức</span>
                    </span>
                </span>
            </a>
            <button type="button" data-sidebar-close class="grid size-8 cursor-pointer place-items-center text-ink hover:bg-surface border border-line lg:hidden" aria-label="Đóng menu điều hướng">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-5">
                    <path d="m6 6 12 12M18 6 6 18"/>
                </svg>
            </button>
        </div>

        <!-- Navigation items -->
        <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-4 font-mono text-xs" aria-label="Danh mục điều hướng">
            <!-- Tổng quan -->
            <div>
                <p class="px-2 text-[10px] font-bold uppercase tracking-widest text-ink-muted">Tổng quan</p>
                <div class="mt-1.5 space-y-1">
                    @php
                        $isDashboardActive = request()->routeIs('dashboard', 'admin.dashboard', 'author.dashboard');
                    @endphp
                    <a href="{{ route('dashboard') }}" @class([
                        'group flex min-h-9 items-center gap-2.5 px-2.5 py-1.5 font-bold transition-all',
                        'border-2 border-line-strong bg-ink text-white shadow-brutal-sm' => $isDashboardActive,
                        'border border-transparent text-ink hover:border-line-strong hover:bg-ink hover:text-white' => ! $isDashboardActive,
                    ]) @if ($isDashboardActive) aria-current="page" @endif>
                        <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-4 shrink-0">
                            <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                </div>
            </div>

            <!-- Nội dung cá nhân -->
            <div>
                <p class="px-2 text-[10px] font-bold uppercase tracking-widest text-ink-muted">Nội dung</p>
                <div class="mt-1.5 space-y-1">
                    @php
                        $isMyPostsActive = request()->routeIs('author.posts.index');
                        $isCreateActive = request()->routeIs('author.posts.create');
                        $canWritePosts = in_array(auth()->user()->role, [\App\Enums\UserRole::Author, \App\Enums\UserRole::Admin], true);
                    @endphp

                    @if (auth()->user()->hasVerifiedEmail() && $canWritePosts)
                        <a href="{{ route('author.posts.index') }}" @class([
                            'group flex min-h-9 items-center gap-2.5 px-2.5 py-1.5 font-bold transition-all',
                            'border-2 border-line-strong bg-ink text-white shadow-brutal-sm' => $isMyPostsActive,
                            'border border-transparent text-ink hover:border-line-strong hover:bg-ink hover:text-white' => ! $isMyPostsActive,
                        ]) @if ($isMyPostsActive) aria-current="page" @endif>
                            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-4 shrink-0">
                                <path d="M5 3h11l3 3v15H5z"/><path d="M15 3v4h4M8 11h8M8 15h8"/>
                            </svg>
                            <span>Bài viết của tôi</span>
                        </a>

                        <a href="{{ route('author.posts.create') }}" @class([
                            'group flex min-h-9 items-center gap-2.5 px-2.5 py-1.5 font-bold transition-all',
                            'border-2 border-line-strong bg-ink text-white shadow-brutal-sm' => $isCreateActive,
                            'border border-transparent text-ink hover:border-line-strong hover:bg-ink hover:text-white' => ! $isCreateActive,
                        ]) @if ($isCreateActive) aria-current="page" @endif>
                            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-4 shrink-0">
                                <path d="M12 5v14M5 12h14"/>
                            </svg>
                            <span>Viết bài mới</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Quản trị hệ thống (Admin only) -->
            @if (auth()->user()->role === \App\Enums\UserRole::Admin)
                <div>
                    <p class="px-2 text-[10px] font-bold uppercase tracking-widest text-ink-muted">Quản trị</p>
                    <div class="mt-1.5 space-y-1">
                        @php
                            $isReviewActive = request()->routeIs('admin.post-reviews.*');
                            $isAuthorAppsActive = request()->routeIs('admin.author-applications.*');
                            $isPostRequestsActive = request()->routeIs('admin.post-requests.*');
                            $isPostsActive = request()->routeIs('admin.posts.*') && ! $isReviewActive;
                            $isCatActive = request()->routeIs('admin.categories.*');
                            $isUsersActive = request()->routeIs('admin.users.*');
                            $isCommentsActive = request()->routeIs('admin.comments.*');

                            $pendingPosts = $pending_posts_count ?? $adminStats['pending_posts'] ?? 0;
                            $pendingApplications = $pending_applications_count ?? $adminStats['pending_applications'] ?? 0;
                            $pendingPostRequests = $pending_post_requests_count ?? $adminStats['pending_post_requests'] ?? 0;
                        @endphp

                        <!-- Duyệt bài -->
                        <a href="{{ route('admin.post-reviews.index') }}" @class([
                            'group flex min-h-9 items-center justify-between gap-2.5 px-2.5 py-1.5 font-bold transition-all',
                            'border-2 border-line-strong bg-ink text-white shadow-brutal-sm' => $isReviewActive,
                            'border border-transparent text-ink hover:border-line-strong hover:bg-ink hover:text-white' => ! $isReviewActive,
                        ]) @if ($isReviewActive) aria-current="page" @endif>
                            <span class="flex items-center gap-2.5 min-w-0">
                                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-4 shrink-0">
                                    <path d="m5 12 4 4L19 6"/><path d="M21 12a9 9 0 1 1-5.3-8.2"/>
                                </svg>
                                <span class="truncate">Duyệt bài</span>
                            </span>
                            @if ($pendingPosts > 0)
                                <span @class([
                                    'px-2 py-0.5 text-[10px] font-bold border transition-colors',
                                    'bg-lime text-white border-white' => $isReviewActive,
                                    'bg-surface text-ink border-line-strong group-hover:bg-lime group-hover:text-white group-hover:border-white' => ! $isReviewActive,
                                ])>
                                    {{ $pendingPosts }}
                                </span>
                            @endif
                        </a>

                        <!-- Đơn ứng tuyển CTV -->
                        <a href="{{ route('admin.author-applications.index') }}" @class([
                            'group flex min-h-9 items-center justify-between gap-2.5 px-2.5 py-1.5 font-bold transition-all',
                            'border-2 border-line-strong bg-ink text-white shadow-brutal-sm' => $isAuthorAppsActive,
                            'border border-transparent text-ink hover:border-line-strong hover:bg-ink hover:text-white' => ! $isAuthorAppsActive,
                        ]) @if ($isAuthorAppsActive) aria-current="page" @endif>
                            <span class="flex items-center gap-2.5 min-w-0">
                                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-4 shrink-0">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="m16 11 2 2 4-4"/>
                                </svg>
                                <span class="truncate">Đơn ứng tuyển CTV</span>
                            </span>
                            @if ($pendingApplications > 0)
                                <span @class([
                                    'px-2 py-0.5 text-[10px] font-bold border transition-colors',
                                    'bg-lime text-white border-white' => $isAuthorAppsActive,
                                    'bg-surface text-ink border-line-strong group-hover:bg-lime group-hover:text-white group-hover:border-white' => ! $isAuthorAppsActive,
                                ])>
                                    {{ $pendingApplications }}
                                </span>
                            @endif
                        </a>

                        <!-- Yêu cầu gỡ / sửa bài -->
                        <a href="{{ route('admin.post-requests.index') }}" @class([
                            'group flex min-h-9 items-center justify-between gap-2.5 px-2.5 py-1.5 font-bold transition-all',
                            'border-2 border-line-strong bg-ink text-white shadow-brutal-sm' => $isPostRequestsActive,
                            'border border-transparent text-ink hover:border-line-strong hover:bg-ink hover:text-white' => ! $isPostRequestsActive,
                        ]) @if ($isPostRequestsActive) aria-current="page" @endif>
                            <span class="flex items-center gap-2.5 min-w-0">
                                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-4 shrink-0">
                                    <path d="M12 9v4m0 4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                                </svg>
                                <span class="truncate">Yêu cầu bài viết</span>
                            </span>
                            @if ($pendingPostRequests > 0)
                                <span @class([
                                    'px-2 py-0.5 text-[10px] font-bold border transition-colors',
                                    'bg-lime text-white border-white' => $isPostRequestsActive,
                                    'bg-surface text-ink border-line-strong group-hover:bg-lime group-hover:text-white group-hover:border-white' => ! $isPostRequestsActive,
                                ])>
                                    {{ $pendingPostRequests }}
                                </span>
                            @endif
                        </a>

                        <!-- Tất cả bài viết -->
                        <a href="{{ route('admin.posts.index') }}" @class([
                            'group flex min-h-9 items-center gap-2.5 px-2.5 py-1.5 font-bold transition-all',
                            'border-2 border-line-strong bg-ink text-white shadow-brutal-sm' => $isPostsActive,
                            'border border-transparent text-ink hover:border-line-strong hover:bg-ink hover:text-white' => ! $isPostsActive,
                        ]) @if ($isPostsActive) aria-current="page" @endif>
                            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-4 shrink-0">
                                <path d="M4 6h16M4 12h16M4 18h10"/>
                            </svg>
                            <span>Tất cả bài viết</span>
                        </a>

                        <!-- Danh mục -->
                        <a href="{{ route('admin.categories.index') }}" @class([
                            'group flex min-h-9 items-center gap-2.5 px-2.5 py-1.5 font-bold transition-all',
                            'border-2 border-line-strong bg-ink text-white shadow-brutal-sm' => $isCatActive,
                            'border border-transparent text-ink hover:border-line-strong hover:bg-ink hover:text-white' => ! $isCatActive,
                        ]) @if ($isCatActive) aria-current="page" @endif>
                            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-4 shrink-0">
                                <path d="M3 7h7l2 2h9v10H3z"/><path d="M3 7V5h7l2 2"/>
                            </svg>
                            <span>Danh mục</span>
                        </a>

                        <!-- Người dùng -->
                        <a href="{{ route('admin.users.index') }}" @class([
                            'group flex min-h-9 items-center gap-2.5 px-2.5 py-1.5 font-bold transition-all',
                            'border-2 border-line-strong bg-ink text-white shadow-brutal-sm' => $isUsersActive,
                            'border border-transparent text-ink hover:border-line-strong hover:bg-ink hover:text-white' => ! $isUsersActive,
                        ]) @if ($isUsersActive) aria-current="page" @endif>
                            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-4 shrink-0">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            <span>Người dùng</span>
                        </a>

                        <!-- Bình luận -->
                        <a href="{{ route('admin.comments.index') }}" @class([
                            'group flex min-h-9 items-center gap-2.5 px-2.5 py-1.5 font-bold transition-all',
                            'border-2 border-line-strong bg-ink text-white shadow-brutal-sm' => $isCommentsActive,
                            'border border-transparent text-ink hover:border-line-strong hover:bg-ink hover:text-white' => ! $isCommentsActive,
                        ]) @if ($isCommentsActive) aria-current="page" @endif>
                            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-4 shrink-0">
                                <path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/>
                            </svg>
                            <span>Bình luận</span>
                        </a>
                    </div>
                </div>
            @endif
        </nav>

        <!-- Sidebar footer -->
        <div class="shrink-0 border-t border-line p-3 space-y-1 font-mono text-xs">
            <a href="{{ route('profile.edit') }}" @class([
                'group flex min-h-9 items-center gap-2.5 px-2.5 py-1.5 font-bold transition-all',
                'border-2 border-line-strong bg-ink text-white shadow-brutal-sm' => request()->routeIs('profile.*'),
                'border border-transparent text-ink hover:border-line-strong hover:bg-ink hover:text-white' => ! request()->routeIs('profile.*'),
            ])>
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-4 shrink-0">
                    <circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>
                </svg>
                <span>Hồ sơ & cài đặt</span>
            </a>

            <a href="{{ route('home') }}" class="group flex min-h-9 items-center gap-2.5 px-2.5 py-1.5 font-bold text-ink hover:bg-ink hover:text-white transition-colors border border-transparent hover:border-line-strong">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-4 shrink-0">
                    <path d="M14 3h7v7M10 14 21 3M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5"/>
                </svg>
                <span>Xem trang tin</span>
            </a>

            <form method="POST" action="{{ route('logout') }}" class="pt-1">
                @csrf
                <button type="submit" class="flex min-h-9 w-full cursor-pointer items-center gap-2.5 px-2.5 py-1.5 text-left font-bold text-danger hover:bg-rose-50 transition-colors border border-transparent hover:border-danger/30">
                    <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-4 shrink-0">
                        <path d="M10 17l5-5-5-5M15 12H3M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                    </svg>
                    <span>Đăng xuất</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main content area -->
    <div class="min-h-dvh lg:pl-64 flex flex-col">
        <!-- Sticky Topbar -->
        <header class="sticky top-0 z-30 flex h-16 shrink-0 items-center justify-between border-b border-line bg-surface/95 px-4 backdrop-blur-md sm:px-6 lg:px-8">
            <!-- Left: Mobile toggle + Breadcrumb -->
            <div class="flex min-w-0 items-center gap-3">
                <button type="button" data-sidebar-open class="grid size-9 shrink-0 cursor-pointer place-items-center border border-line-strong text-ink hover:bg-paper focus-visible:outline-2 focus-visible:outline-ink lg:hidden" aria-controls="dashboard-sidebar" aria-expanded="false" aria-label="Mở menu điều hướng">
                    <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-5">
                        <path d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <nav aria-label="Breadcrumb" class="min-w-0 font-mono text-xs">
                    <ol class="flex items-center gap-1.5 text-ink-muted">
                        <li class="hidden sm:inline-block">
                            <span class="text-ink-muted">Workspace</span>
                        </li>
                        @if (!empty($breadcrumbs))
                            @foreach ($breadcrumbs as $crumb)
                                <li class="hidden sm:inline-block text-line" aria-hidden="true">/</li>
                                <li class="truncate">
                                    @if (!empty($crumb['url']))
                                        <a href="{{ $crumb['url'] }}" class="text-ink-muted hover:text-ink hover:underline transition-colors">
                                            {{ $crumb['label'] }}
                                        </a>
                                    @else
                                        <span class="font-bold text-ink">{{ $crumb['label'] }}</span>
                                    @endif
                                </li>
                            @endforeach
                        @else
                            <li class="hidden sm:inline-block text-line" aria-hidden="true">/</li>
                            <li class="truncate font-bold text-ink">
                                {{ $title ?? 'Dashboard' }}
                            </li>
                        @endif
                    </ol>
                </nav>
            </div>

            <!-- Right: Actions & User Info -->
            <div class="flex items-center gap-2.5 sm:gap-3">
                <!-- Nút Xem trang tin -->
                <a href="{{ route('home') }}"
                   title="Xem trang tin tức NewsHub"
                   class="inline-flex min-h-8 items-center gap-1.5 border-2 border-line-strong bg-surface px-3 py-1.5 text-xs font-bold text-ink shadow-brutal-sm transition-colors hover:bg-ink hover:text-white focus-visible:outline-2 focus-visible:outline-ink">
                    <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-3.5">
                        <path d="M14 3h7v7M10 14 21 3M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5"/>
                    </svg>
                    <span class="hidden sm:inline">Trang tin</span>
                </a>

                @if (auth()->user()->hasVerifiedEmail() && in_array(auth()->user()->role, [\App\Enums\UserRole::Author, \App\Enums\UserRole::Admin], true))
                    <a href="{{ route('author.posts.create') }}" class="inline-flex min-h-8 items-center gap-1.5 border-2 border-line-strong bg-lime px-3.5 py-1.5 text-xs font-bold text-white shadow-brutal-sm transition-colors hover:bg-ink hover:text-white focus-visible:outline-2 focus-visible:outline-ink">
                        <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="size-3.5">
                            <path d="M12 5v14M5 12h14"/>
                        </svg>
                        <span>Viết bài</span>
                    </a>
                @endif

                <!-- User Profile Dropdown Menu in Dashboard -->
                <div class="relative border-l border-line pl-3" data-user-menu>
                    <button type="button"
                            data-user-menu-trigger
                            aria-expanded="false"
                            aria-label="Menu cá nhân"
                            class="group flex items-center gap-2.5 border border-transparent py-1 pl-1 pr-2 transition-all hover:border-line-strong hover:bg-paper focus-visible:outline-2 focus-visible:outline-ink cursor-pointer">
                        <span class="grid size-8 shrink-0 place-items-center bg-lime text-xs font-bold font-mono text-white border border-line-strong shadow-brutal-sm select-none" aria-hidden="true">
                            {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                        </span>
                        <div class="hidden min-w-0 text-left sm:block">
                            <p class="max-w-32 truncate text-xs font-bold text-ink leading-tight">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] font-mono font-medium uppercase tracking-wider text-ink-muted">{{ auth()->user()->role->value }}</p>
                        </div>
                        <svg class="size-3 text-ink-muted transition-transform duration-200 group-hover:text-ink" data-user-menu-chevron viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <!-- Dropdown Panel -->
                    <div data-user-menu-panel
                         class="pointer-events-none opacity-0 invisible translate-y-1 transition-all duration-150 ease-out absolute right-0 top-full pt-1.5 z-50 w-60">
                        <div class="rounded-none border-2 border-line-strong bg-surface p-2 shadow-brutal divide-y divide-line">
                            <div class="px-2.5 py-2">
                                <p class="truncate text-xs font-bold text-ink">{{ auth()->user()->name }}</p>
                                <p class="text-[10px] font-mono font-bold uppercase tracking-wider text-ink-muted">{{ auth()->user()->role->value }}</p>
                            </div>

                            <div class="py-1">
                                <a href="{{ route('home') }}"
                                   class="flex items-center gap-2.5 px-2.5 py-1.5 text-xs font-bold text-ink hover:bg-ink hover:text-white transition-colors">
                                    <svg class="size-4 shrink-0 text-current" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                                    </svg>
                                    <span>Về trang chủ tin tức</span>
                                </a>

                                <a href="{{ route('profile.edit') }}"
                                   class="flex items-center gap-2.5 px-2.5 py-1.5 text-xs font-bold text-ink hover:bg-ink hover:text-white transition-colors">
                                    <svg class="size-4 shrink-0 text-current" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                                    </svg>
                                    <span>Hồ sơ cá nhân & Mật khẩu</span>
                                </a>
                            </div>

                            <div class="pt-1">
                                <form method="POST" action="{{ route('logout') }}" class="w-full">
                                    @csrf
                                    <button type="submit"
                                            class="w-full flex items-center gap-2.5 px-2.5 py-2 text-xs font-bold text-danger hover:bg-rose-50 transition-colors cursor-pointer text-left">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path d="M10 17l5-5-5-5M15 12H3M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                                        </svg>
                                        <span>Đăng xuất</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Body -->
        <main id="dashboard-content" tabindex="-1" class="flex-1 mx-auto w-full max-w-7xl px-4 py-6 focus:outline-none sm:px-6 sm:py-8 lg:px-8">
            <!-- Global Flash Messages -->
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
                        Vui lòng kiểm tra lại thông tin:
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
    </div>

    <!-- Global Floating Confirmation Modal Markup (Neo-Brutalist) -->
    <div id="app-confirm-modal" class="fixed inset-0 z-[9999] hidden overflow-y-auto bg-ink/60 backdrop-blur-xs p-4 sm:p-6" aria-modal="true" role="dialog">
        <div class="flex min-h-full items-center justify-center">
            <div id="app-confirm-panel" class="relative w-full max-w-md scale-95 opacity-0 border-2 border-line-strong bg-surface p-6 shadow-brutal-lg transition-all duration-150">
                <div class="flex items-start gap-3">
                    <div id="app-confirm-icon-box" class="grid size-10 shrink-0 place-items-center border-2 border-line-strong">
                        <span id="app-confirm-icon"></span>
                    </div>
                    <div class="flex-1 space-y-1">
                        <h3 id="app-confirm-title" class="text-base font-black text-ink tracking-tight">Xác nhận</h3>
                        <p id="app-confirm-message" class="text-xs text-ink-muted leading-relaxed"></p>
                        <div id="app-confirm-subtext" class="mt-2.5 hidden text-xs font-semibold p-2.5 border text-left line-clamp-2"></div>
                    </div>
                </div>
                <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-line">
                    <button type="button" id="app-confirm-cancel-btn" class="border border-line-strong bg-surface px-4 py-2 text-xs font-bold text-ink hover:bg-paper cursor-pointer transition-colors">
                        Hủy bỏ
                    </button>
                    <button type="button" id="app-confirm-ok-btn" class="border border-line-strong px-4 py-2 text-xs font-bold shadow-brutal-sm cursor-pointer transition-colors">
                        <span id="app-confirm-ok-text">Xác nhận</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Client-side Scripts -->
    <script>
        // Drawer toggle & accessibility focus management
        const dashboardSidebar = document.getElementById('dashboard-sidebar');
        const dashboardOverlay = document.getElementById('dashboard-overlay');
        const sidebarOpenButton = document.querySelector('[data-sidebar-open]');
        const sidebarCloseButton = document.querySelector('[data-sidebar-close]');

        const setSidebarOpen = (isOpen) => {
            dashboardSidebar.classList.toggle('-translate-x-full', !isOpen);
            dashboardOverlay.classList.toggle('hidden', !isOpen);
            sidebarOpenButton.setAttribute('aria-expanded', String(isOpen));
            document.body.classList.toggle('overflow-hidden', isOpen);

            if (isOpen) {
                sidebarCloseButton?.focus();
            } else {
                sidebarOpenButton?.focus();
            }
        };

        sidebarOpenButton?.addEventListener('click', () => setSidebarOpen(true));
        dashboardOverlay?.addEventListener('click', () => setSidebarOpen(false));
        sidebarCloseButton?.addEventListener('click', () => setSidebarOpen(false));

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !dashboardSidebar.classList.contains('-translate-x-full')) {
                setSidebarOpen(false);
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

        // Double submit prevention
        document.addEventListener('submit', (event) => {
            const form = event.target;
            if (!(form instanceof HTMLFormElement) || !form.checkValidity()) {
                return;
            }
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

        // --- GLOBAL FLOATING CONFIRMATION MODAL ---
        const CONFIRM_ICONS = {
            success: '<svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
            danger: '<svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>',
            warning: '<svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>',
            info: '<svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>'
        };

        const CONFIRM_THEMES = {
            success: {
                box: 'bg-lime text-white border-line-strong',
                btn: 'border border-line-strong bg-lime text-white hover:bg-ink hover:text-white',
                subtext: 'border-line bg-paper text-ink'
            },
            danger: {
                box: 'bg-rose-100 text-danger border-line-strong',
                btn: 'border border-line-strong bg-danger text-white hover:opacity-90',
                subtext: 'border-danger/30 bg-rose-50 text-danger'
            },
            warning: {
                box: 'bg-[#dce6f2] text-ink border-line-strong',
                btn: 'border border-line-strong bg-lime text-white hover:bg-ink hover:text-white',
                subtext: 'border-line bg-paper text-ink'
            },
            info: {
                box: 'bg-lime text-white border-line-strong',
                btn: 'border border-line-strong bg-lime text-white hover:bg-ink hover:text-white',
                subtext: 'border-line bg-paper text-ink'
            }
        };

        let currentConfirmCallback = null;

        function getConfirmElements() {
            return {
                modal: document.getElementById('app-confirm-modal'),
                panel: document.getElementById('app-confirm-panel'),
                iconBox: document.getElementById('app-confirm-icon-box'),
                icon: document.getElementById('app-confirm-icon'),
                title: document.getElementById('app-confirm-title'),
                message: document.getElementById('app-confirm-message'),
                subtext: document.getElementById('app-confirm-subtext'),
                cancelBtn: document.getElementById('app-confirm-cancel-btn'),
                okBtn: document.getElementById('app-confirm-ok-btn'),
                okText: document.getElementById('app-confirm-ok-text'),
            };
        }

        window.openConfirmModal = function({
            title = 'Xác nhận thao tác',
            message = 'Bạn có chắc chắn muốn thực hiện hành động này?',
            subtext = null,
            confirmText = 'Xác nhận',
            type = 'warning',
            onConfirm = null
        }) {
            const els = getConfirmElements();
            if (!els.modal) return;

            currentConfirmCallback = onConfirm;

            els.title.textContent = title;
            els.message.textContent = message;
            els.okText.textContent = confirmText;

            if (subtext) {
                els.subtext.textContent = subtext;
                els.subtext.className = 'mt-2.5 text-xs font-semibold p-2.5 border text-left line-clamp-2 ' + (CONFIRM_THEMES[type]?.subtext || CONFIRM_THEMES.warning.subtext);
                els.subtext.classList.remove('hidden');
            } else {
                els.subtext.classList.add('hidden');
            }

            els.icon.innerHTML = CONFIRM_ICONS[type] || CONFIRM_ICONS.warning;
            els.iconBox.className = 'grid size-10 shrink-0 place-items-center border-2 ' + (CONFIRM_THEMES[type]?.box || CONFIRM_THEMES.warning.box);
            els.okBtn.className = 'border px-4 py-2 text-xs font-bold shadow-brutal-sm cursor-pointer transition-colors ' + (CONFIRM_THEMES[type]?.btn || CONFIRM_THEMES.warning.btn);

            els.modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');

            requestAnimationFrame(() => {
                els.panel?.classList.remove('scale-95', 'opacity-0');
                els.panel?.classList.add('scale-100', 'opacity-100');
            });
        };

        window.closeConfirmModal = function() {
            const els = getConfirmElements();
            if (!els.modal) return;
            els.panel?.classList.remove('scale-100', 'opacity-100');
            els.panel?.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                els.modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                currentConfirmCallback = null;
            }, 150);
        };

        document.addEventListener('click', (e) => {
            const els = getConfirmElements();
            if (e.target === els.cancelBtn || e.target.closest('#app-confirm-cancel-btn')) {
                window.closeConfirmModal();
            } else if (e.target === els.okBtn || e.target.closest('#app-confirm-ok-btn')) {
                const cb = currentConfirmCallback;
                window.closeConfirmModal();
                if (typeof cb === 'function') {
                    cb();
                }
            } else if (e.target === els.modal) {
                window.closeConfirmModal();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const els = getConfirmElements();
                if (els.modal && !els.modal.classList.contains('hidden')) {
                    window.closeConfirmModal();
                }
            }
        });

        // Custom Client-Side Inline Form Validation (Prevents ugly browser HTML5 popups)
        document.addEventListener('invalid', (e) => {
            e.preventDefault();
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
                    return 'Vui lòng nhập đúng định dạng liên kết URL.';
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

        // Global capture listener for form submission
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (!(form instanceof HTMLFormElement)) return;

            // Validate all fields inline first
            form.querySelectorAll('.custom-field-error').forEach(el => el.remove());
            const fields = Array.from(form.querySelectorAll('input, select, textarea'));
            let firstInvalid = null;
            let hasError = false;

            for (const field of fields) {
                if (field.disabled || field.type === 'hidden' || field.type === 'submit' || field.type === 'button') {
                    continue;
                }
                if (!field.checkValidity()) {
                    hasError = true;
                    if (!firstInvalid) firstInvalid = field;
                    renderFieldInlineError(field);
                }
            }

            if (hasError) {
                e.preventDefault();
                e.stopImmediatePropagation();
                if (firstInvalid) {
                    firstInvalid.focus();
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return;
            }

            // [data-confirm] confirmation handler
            if (form.hasAttribute('data-confirm') && !form.dataset.confirmed) {
                e.preventDefault();
                e.stopImmediatePropagation();

                const message = form.getAttribute('data-confirm');
                const title = form.getAttribute('data-confirm-title') || 'Xác nhận thao tác';
                const type = form.getAttribute('data-confirm-type') || 'warning';
                const confirmBtn = form.getAttribute('data-confirm-btn') || 'Xác nhận';
                const subtext = form.getAttribute('data-confirm-subtext');
                const submitter = e.submitter;

                window.openConfirmModal({
                    title,
                    message,
                    subtext,
                    type,
                    confirmText: confirmBtn,
                    onConfirm: () => {
                        form.dataset.confirmed = 'true';
                        if (submitter && submitter.name && !form.querySelector(`input[name="${submitter.name}"]`)) {
                            const hidden = document.createElement('input');
                            hidden.type = 'hidden';
                            hidden.name = submitter.name;
                            hidden.value = submitter.value;
                            form.appendChild(hidden);
                        }
                        if (submitter && typeof form.requestSubmit === 'function') {
                            form.requestSubmit(submitter);
                        } else {
                            form.submit();
                        }
                    }
                });
            }
        }, true);
    </script>
</body>
</html>
