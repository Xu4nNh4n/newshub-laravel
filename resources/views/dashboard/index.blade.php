@extends('layouts.dashboard', ['title' => 'Dashboard'])

@section('content')
<div class="space-y-8">
    <!-- Greeting & Status Header -->
    <section class="flex flex-col gap-4 border-b-2 border-line-strong pb-6 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-mono font-bold uppercase tracking-wider text-ink-muted">
                <span class="inline-block size-2 bg-success" aria-hidden="true"></span>
                <span>Hệ thống trực tuyến</span>
            </div>
            <h1 class="mt-2 text-2xl font-black uppercase tracking-tight text-ink sm:text-3xl">Xin chào, {{ auth()->user()->name }}</h1>
            <p class="mt-1 max-w-2xl text-xs sm:text-sm text-ink-muted leading-relaxed">
                {{ ($adminStats ?? null) ? 'Theo dõi nội dung, độc giả và các việc cần xử lý trong một màn hình.' : 'Quản lý bài viết và theo dõi tiến trình xuất bản của bạn.' }}
            </p>
        </div>
        <div class="flex shrink-0 items-center gap-2">
            <span class="inline-flex items-center gap-2 border border-line-strong bg-surface px-3 py-1.5 text-xs font-mono font-bold text-ink shadow-brutal-sm">
                <svg class="size-3.5 text-ink-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ now()->translatedFormat('l, d/m/Y') }}
            </span>
        </div>
    </section>

    <!-- Verification Notice (if unverified) -->
    @unless (auth()->user()->hasVerifiedEmail())
        <section class="flex flex-col gap-4 border-2 border-line-strong bg-lime p-5 sm:flex-row sm:items-center sm:justify-between shadow-brutal" role="region" aria-label="Xác minh tài khoản">
            <div class="flex items-start gap-3">
                <span class="size-6 grid place-items-center bg-ink text-paper font-mono font-bold text-xs shrink-0 mt-0.5">!</span>
                <div>
                    <h2 class="text-xs sm:text-sm font-black uppercase tracking-tight text-ink">Xác minh email để bắt đầu đăng bài</h2>
                    <p class="mt-0.5 text-xs text-ink/80 font-medium">Sau khi xác minh, bạn có thể tạo bản nháp và gửi bài cho quản trị viên duyệt.</p>
                </div>
            </div>
            <a href="{{ route('verification.notice') }}" class="inline-flex min-h-10 sm:min-h-9 shrink-0 items-center justify-center border-2 border-line-strong bg-surface px-4 py-1.5 text-xs font-mono font-bold uppercase tracking-wider text-ink shadow-brutal-sm hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
                Xác minh ngay
            </a>
        </section>
    @endunless

    <!-- ADMIN STATISTICS & WORKSPACE -->
    @if ($adminStats ?? null)
        @php
            $maximumMonthlyPosts = max(1, collect($adminStats['posts_by_month'])->max('count'));
            $maximumDailyViews = max(1, collect($adminStats['views_by_day'])->max('count'));
        @endphp

        <!-- System Overview Section -->
        <section aria-labelledby="admin-overview-title" class="space-y-4">
            <div class="flex items-center justify-between gap-4 border-b border-line pb-3">
                <div>
                    <h2 id="admin-overview-title" class="text-sm sm:text-base font-black uppercase tracking-tight text-ink">Tổng quan quản trị</h2>
                    <p class="text-xs font-mono text-ink-muted">Những chỉ số quan trọng của hệ thống.</p>
                </div>
                <a href="{{ route('admin.posts.index') }}" class="text-xs font-mono font-bold uppercase text-ink hover:underline">
                    Xem bài viết &rarr;
                </a>
            </div>

            <!-- Stats Grid -->
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Tổng bài viết -->
                <article class="flex flex-col justify-between border-2 border-line-strong bg-surface p-5 shadow-brutal-sm">
                    <div>
                        <div class="flex items-center justify-between gap-2 border-b border-line pb-2">
                            <span class="text-xs font-mono font-bold uppercase tracking-wider text-ink-muted">Tổng bài viết</span>
                            <span class="size-7 grid place-items-center border border-line-strong bg-paper text-ink font-mono text-xs">
                                #
                            </span>
                        </div>
                        <p class="mt-3 text-3xl sm:text-4xl font-mono font-black tabular-nums text-ink">{{ number_format($adminStats['posts_total']) }}</p>
                    </div>
                    <p class="mt-3 text-xs font-mono text-ink-muted border-t border-line pt-2">{{ $adminStats['published_total'] }} bài đã được duyệt</p>
                </article>

                <!-- Người dùng -->
                <article class="flex flex-col justify-between border-2 border-line-strong bg-surface p-5 shadow-brutal-sm">
                    <div>
                        <div class="flex items-center justify-between gap-2 border-b border-line pb-2">
                            <span class="text-xs font-mono font-bold uppercase tracking-wider text-ink-muted">Người dùng</span>
                            <span class="size-7 grid place-items-center border border-line-strong bg-paper text-ink font-mono text-xs">
                                U
                            </span>
                        </div>
                        <p class="mt-3 text-3xl sm:text-4xl font-mono font-black tabular-nums text-ink">{{ number_format($adminStats['users_total'] + $adminStats['authors_total']) }}</p>
                    </div>
                    <p class="mt-3 text-xs font-mono text-ink-muted border-t border-line pt-2">{{ $adminStats['authors_total'] }} tài khoản author</p>
                </article>

                <!-- Bài chờ duyệt -->
                <article class="flex flex-col justify-between border-2 border-line-strong bg-lime p-5 shadow-brutal">
                    <div>
                        <div class="flex items-center justify-between gap-2 border-b border-line-strong pb-2">
                            <span class="text-xs font-mono font-bold uppercase tracking-wider text-ink">Bài chờ duyệt</span>
                            <span class="size-7 grid place-items-center border border-line-strong bg-surface text-ink font-mono font-bold text-xs">
                                !
                            </span>
                        </div>
                        <p class="mt-3 text-3xl sm:text-4xl font-mono font-black tabular-nums text-ink">{{ number_format($adminStats['pending_posts']) }}</p>
                    </div>
                    <a href="{{ route('admin.post-reviews.index') }}" class="mt-3 inline-flex items-center gap-1 text-xs font-mono font-black uppercase text-ink hover:underline border-t border-line-strong pt-2">
                        <span>Mở hàng đợi duyệt &rarr;</span>
                    </a>
                </article>

                <!-- Đang hiển thị -->
                <article class="flex flex-col justify-between border-2 border-line-strong bg-surface p-5 shadow-brutal-sm">
                    <div>
                        <div class="flex items-center justify-between gap-2 border-b border-line pb-2">
                            <span class="text-xs font-mono font-bold uppercase tracking-wider text-ink-muted">Đang hiển thị</span>
                            <span class="size-7 grid place-items-center border border-line-strong bg-paper text-ink font-mono text-xs">
                                OK
                            </span>
                        </div>
                        <p class="mt-3 text-3xl sm:text-4xl font-mono font-black tabular-nums text-ink">{{ number_format($adminStats['published_visible']) }}</p>
                    </div>
                    <p class="mt-3 text-xs font-mono text-ink-muted border-t border-line pt-2">Có thể đọc trên trang tin</p>
                </article>

                <!-- Báo cáo chờ xử lý -->
                <article class="flex flex-col justify-between border-2 border-line-strong bg-surface p-5 shadow-brutal-sm">
                    <div>
                        <div class="flex items-center justify-between gap-2 border-b border-line pb-2">
                            <span class="text-xs font-mono font-bold uppercase tracking-wider text-ink-muted">Báo cáo chờ xử lý</span>
                            <span class="size-7 grid place-items-center border border-line-strong bg-paper text-danger font-mono font-bold text-xs">
                                ⚠
                            </span>
                        </div>
                        <p class="mt-3 text-3xl sm:text-4xl font-mono font-black tabular-nums text-ink">{{ number_format($adminStats['pending_reports']) }}</p>
                    </div>
                    <a href="{{ route('admin.comment-reports.index') }}" class="mt-3 inline-flex items-center gap-1 text-xs font-mono font-bold uppercase text-ink hover:underline border-t border-line pt-2">
                        <span>Kiểm tra báo cáo &rarr;</span>
                    </a>
                </article>

                <!-- Tài khoản bị khóa -->
                <article class="flex flex-col justify-between border-2 border-line-strong bg-surface p-5 shadow-brutal-sm">
                    <div>
                        <div class="flex items-center justify-between gap-2 border-b border-line pb-2">
                            <span class="text-xs font-mono font-bold uppercase tracking-wider text-ink-muted">Tài khoản bị khóa</span>
                            <span class="size-7 grid place-items-center border border-line-strong bg-paper text-ink font-mono text-xs">
                                X
                            </span>
                        </div>
                        <p class="mt-3 text-3xl sm:text-4xl font-mono font-black tabular-nums text-ink">{{ number_format($adminStats['blocked_total']) }}</p>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="mt-3 inline-flex items-center gap-1 text-xs font-mono font-bold uppercase text-ink hover:underline border-t border-line pt-2">
                        <span>Quản lý tài khoản &rarr;</span>
                    </a>
                </article>
            </div>
        </section>

        <!-- Charts & Activity Trends -->
        <section class="grid gap-6 xl:grid-cols-12" aria-label="Xu hướng nội dung">
            <!-- Bài tạo theo tháng (7 columns) -->
            <article class="xl:col-span-7 flex flex-col justify-between border-2 border-line-strong bg-surface p-5 shadow-brutal-sm">
                <div class="flex items-start justify-between gap-4 border-b border-line pb-3">
                    <div>
                        <h3 class="text-sm font-black uppercase text-ink">Bài tạo theo tháng</h3>
                        <p class="text-xs font-mono text-ink-muted">Số lượng nội dung mới trong 6 tháng gần nhất.</p>
                    </div>
                    <span class="border border-line bg-paper px-2 py-0.5 text-xs font-mono font-bold text-ink">6 tháng</span>
                </div>

                <div class="mt-6 flex h-48 items-end gap-2 sm:gap-4 border-b-2 border-line-strong pb-2" role="img" aria-label="Biểu đồ số bài viết được tạo trong 6 tháng gần nhất">
                    @foreach ($adminStats['posts_by_month'] as $item)
                        @php
                            $percentage = $item['count'] > 0 ? max(10, (int) round(($item['count'] / $maximumMonthlyPosts) * 100)) : 4;
                        @endphp
                        <div class="group flex h-full flex-1 flex-col items-center justify-end gap-1.5 text-center">
                            <span class="text-xs font-mono font-bold tabular-nums text-ink-muted group-hover:text-ink">{{ $item['count'] }}</span>
                            <div class="flex h-36 w-full max-w-10 items-end border border-line-strong bg-paper">
                                <span class="block w-full bg-lime border-t border-line-strong transition-colors group-hover:bg-lime-hover" style="height: {{ $percentage }}%"></span>
                            </div>
                            <span class="mt-1 block truncate text-xs font-mono font-medium text-ink-muted group-hover:text-ink">{{ $item['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </article>

            <!-- Lượt xem 7 ngày (5 columns) -->
            <article class="xl:col-span-5 flex flex-col justify-between border-2 border-line-strong bg-surface p-5 shadow-brutal-sm">
                <div class="border-b border-line pb-3">
                    <h3 class="text-sm font-black uppercase text-ink">Lượt xem 7 ngày</h3>
                    <p class="text-xs font-mono text-ink-muted">Mức quan tâm theo ngày.</p>
                </div>

                <div class="mt-6 space-y-3" aria-label="Lượt xem từng ngày trong 7 ngày gần nhất">
                    @foreach ($adminStats['views_by_day'] as $item)
                        @php
                            $percentage = $item['count'] > 0 ? max(6, (int) round(($item['count'] / $maximumDailyViews) * 100)) : 1;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-xs font-mono">
                                <span class="font-medium text-ink-muted">{{ $item['label'] }}</span>
                                <span class="font-bold tabular-nums text-ink">{{ number_format($item['count']) }}</span>
                            </div>
                            <div class="mt-1.5 h-2.5 w-full border border-line-strong bg-paper overflow-hidden">
                                <div class="h-full bg-ink" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </article>
        </section>

        <!-- Top Content & Categories Grid -->
        <section class="grid gap-6 xl:grid-cols-12">
            <!-- Bài đang được xem nhiều (7 columns) -->
            <article class="xl:col-span-7 flex flex-col border-2 border-line-strong bg-surface shadow-brutal-sm">
                <div class="flex items-center justify-between border-b-2 border-line-strong px-5 py-3.5 bg-paper">
                    <div>
                        <h3 class="text-sm font-black uppercase text-ink">Bài đang được xem nhiều</h3>
                        <p class="text-xs font-mono text-ink-muted">Nội dung công khai nổi bật theo lượt xem.</p>
                    </div>
                    <a href="{{ route('admin.posts.index') }}" class="text-xs font-mono font-bold uppercase text-ink hover:underline">
                        Tất cả &rarr;
                    </a>
                </div>

                <div class="divide-y border-line">
                    @forelse ($adminStats['top_posts'] as $index => $topPost)
                        <a href="{{ route('news.show', $topPost->slug) }}" class="group flex items-center justify-between gap-3 px-5 py-3.5 transition-colors hover:bg-paper">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="size-6 grid place-items-center border border-line-strong bg-paper text-xs font-mono font-bold text-ink shrink-0">
                                    {{ sprintf('%02d', $index + 1) }}
                                </span>
                                <div class="min-w-0">
                                    <p class="truncate text-xs sm:text-sm font-bold text-ink group-hover:underline">{{ $topPost->title }}</p>
                                    <p class="mt-0.5 truncate text-xs font-mono text-ink-muted">{{ $topPost->category->name }} · {{ $topPost->author->name }}</p>
                                </div>
                            </div>
                            <span class="shrink-0 border border-line bg-paper px-2.5 py-1 text-xs font-mono font-bold text-ink">
                                {{ number_format($topPost->view_count) }} lượt xem
                            </span>
                        </a>
                    @empty
                        <div class="p-8 text-center text-xs font-mono text-ink-muted">
                            Chưa có bài công khai.
                        </div>
                    @endforelse
                </div>
            </article>

            <!-- Chuyên mục nổi bật (5 columns) -->
            <article class="xl:col-span-5 flex flex-col border-2 border-line-strong bg-surface shadow-brutal-sm">
                <div class="border-b-2 border-line-strong px-5 py-3.5 bg-paper">
                    <h3 class="text-sm font-black uppercase text-ink">Chuyên mục nổi bật</h3>
                    <p class="text-xs font-mono text-ink-muted">Xếp theo tổng lượt xem.</p>
                </div>

                <div class="divide-y border-line">
                    @forelse ($adminStats['top_categories'] as $category)
                        <div class="flex items-center justify-between gap-3 px-5 py-3.5">
                            <div class="min-w-0">
                                <p class="truncate text-xs sm:text-sm font-bold text-ink">{{ $category->name }}</p>
                                <p class="mt-0.5 text-xs font-mono text-ink-muted">{{ $category->published_posts_count }} bài</p>
                            </div>
                            <span class="shrink-0 text-xs font-mono font-bold tabular-nums text-ink">
                                {{ number_format($category->published_views_sum ?? 0) }} xem
                            </span>
                        </div>
                    @empty
                        <div class="p-8 text-center text-xs font-mono text-ink-muted">
                            Chưa có dữ liệu.
                        </div>
                    @endforelse
                </div>
            </article>
        </section>
    @endif

    <!-- AUTHOR STATISTICS (If user has verified email) -->
    @if ($authorStats ?? null)
        <section aria-labelledby="author-overview-title" class="space-y-4 pt-2">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b border-line pb-3">
                <div>
                    <h2 id="author-overview-title" class="text-sm sm:text-base font-black uppercase tracking-tight text-ink">Thống kê bài viết của bạn</h2>
                    <p class="text-xs font-mono text-ink-muted">Theo dõi toàn bộ quy trình từ bản nháp đến xuất bản.</p>
                </div>
                <a href="{{ route('author.posts.create') }}" class="inline-flex min-h-9 items-center justify-center gap-1.5 border-2 border-line-strong bg-lime px-4 py-1.5 text-xs font-mono font-bold uppercase tracking-wider text-ink shadow-brutal-sm hover:bg-lime-hover hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
                    <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="size-3.5">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                    <span>Viết bài mới</span>
                </a>
            </div>

            <!-- Author Stats Cards (6 metrics) -->
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ([
                    ['label' => 'Tổng bài', 'value' => $authorStats['posts_total'], 'hint' => 'Tất cả nội dung của bạn'],
                    ['label' => 'Bản nháp', 'value' => $authorStats['drafts'], 'hint' => 'Có thể tiếp tục chỉnh sửa'],
                    ['label' => 'Chờ duyệt', 'value' => $authorStats['pending_posts'], 'hint' => 'Đang chờ quản trị viên'],
                    ['label' => 'Bị từ chối', 'value' => $authorStats['rejected_posts'], 'hint' => 'Cần xem lại phản hồi'],
                    ['label' => 'Đã xuất bản', 'value' => $authorStats['published_posts'], 'hint' => 'Đã xuất hiện trên trang tin'],
                    ['label' => 'Tổng lượt xem', 'value' => $authorStats['total_views'], 'hint' => 'Cộng dồn trên các bài viết'],
                ] as $stat)
                    <article class="flex flex-col justify-between border-2 border-line-strong bg-surface p-5 shadow-brutal-sm">
                        <div>
                            <span class="text-xs font-mono font-bold uppercase tracking-wider text-ink-muted">{{ $stat['label'] }}</span>
                            <p class="mt-2 text-3xl font-mono font-black tabular-nums text-ink">{{ number_format($stat['value']) }}</p>
                        </div>
                        <p class="mt-3 text-xs font-mono text-ink-muted border-t border-line pt-2">{{ $stat['hint'] }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <!-- Author Top Posts & Workflow Details -->
        <section class="grid gap-6 lg:grid-cols-12">
            <!-- Bài viết có lượt xem cao nhất (7 columns) -->
            <article class="flex flex-col border-2 border-line-strong bg-surface lg:col-span-7 shadow-brutal-sm">
                <div class="flex items-center justify-between border-b-2 border-line-strong px-5 py-3.5 bg-paper">
                    <div>
                        <h3 class="text-sm font-black uppercase text-ink">Bài viết có lượt xem cao nhất</h3>
                        <p class="text-xs font-mono text-ink-muted">Hiệu quả nội dung của riêng bạn.</p>
                    </div>
                    <a href="{{ route('author.posts.index') }}" class="text-xs font-mono font-bold uppercase text-ink hover:underline">
                        Quản lý &rarr;
                    </a>
                </div>

                <div class="divide-y border-line">
                    @forelse ($authorStats['top_posts'] as $topPost)
                        <a href="{{ route('posts.preview', $topPost) }}" class="group flex items-center justify-between gap-3 px-5 py-3.5 transition-colors hover:bg-paper">
                            <div class="min-w-0">
                                <p class="truncate text-xs sm:text-sm font-bold text-ink group-hover:underline">{{ $topPost->title }}</p>
                                <p class="mt-0.5 text-xs font-mono text-ink-muted">{{ $topPost->category->name }} · {{ str_replace('_', ' ', $topPost->status->value) }}</p>
                            </div>
                            <span class="shrink-0 border border-line bg-paper px-2.5 py-1 text-xs font-mono font-bold text-ink">
                                {{ number_format($topPost->view_count) }} lượt xem
                            </span>
                        </a>
                    @empty
                        <div class="p-8 text-center font-mono">
                            <p class="text-xs font-bold uppercase text-ink">Bạn chưa có bài viết</p>
                            <p class="mt-1 text-xs text-ink-muted">Tạo bản nháp đầu tiên để bắt đầu.</p>
                        </div>
                    @endforelse
                </div>
            </article>

            <!-- Quy trình xuất bản (5 columns) -->
            <article class="flex flex-col border-2 border-line-strong bg-surface lg:col-span-5 shadow-brutal-sm">
                <div class="flex items-center justify-between border-b-2 border-line-strong px-5 py-3.5 bg-paper">
                    <div>
                        <h3 class="text-sm font-black uppercase text-ink">Quy trình xuất bản</h3>
                        <p class="text-xs font-mono text-ink-muted">Kiểm duyệt trước khi xuất hiện công khai.</p>
                    </div>
                    <span class="border border-line-strong bg-lime px-2 py-0.5 text-xs font-mono font-bold text-ink">
                        3 bước
                    </span>
                </div>

                <div class="flex flex-1 flex-col justify-between gap-4 p-5">
                    <ol class="space-y-3.5">
                        @foreach ([
                            ['01', 'Tạo bản nháp', 'Viết và chỉnh sửa nội dung hoàn chỉnh.'],
                            ['02', 'Gửi duyệt', 'Khóa chỉnh sửa trong lúc chờ ban biên tập xét duyệt.'],
                            ['03', 'Xuất bản', 'Quản trị viên duyệt và bài viết chính thức lên trang tin.']
                        ] as $step)
                            <li class="flex items-start gap-3 border border-line bg-paper p-2.5">
                                <span class="size-6 grid place-items-center border border-line-strong bg-lime text-xs font-mono font-bold text-ink shrink-0">
                                    {{ $step[0] }}
                                </span>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold uppercase tracking-wider text-ink font-mono">{{ $step[1] }}</p>
                                    <p class="text-xs text-ink-muted leading-relaxed mt-0.5">{{ $step[2] }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ol>

                    <div class="mt-auto flex items-center justify-between gap-2.5 border border-line bg-paper p-3 text-xs font-mono">
                        <div class="flex min-w-0 items-center gap-2">
                            <span class="text-ink font-bold">i</span>
                            <p class="truncate text-ink-muted">Có thể lưu nháp nhiều lần trước khi gửi.</p>
                        </div>
                        <a href="{{ route('author.posts.create') }}" class="shrink-0 font-bold text-ink hover:underline">Viết bài &rarr;</a>
                    </div>
                </div>
            </article>
        </section>
    @endif

    <!-- USER ROLE: READER HUB & AUTHOR APPLICATION PROGRAM -->
    @if (auth()->user()->role === \App\Enums\UserRole::User)
        <!-- Reader Quick Activity Section -->
        <section aria-labelledby="reader-overview-title" class="space-y-4">
            <div class="flex items-center justify-between gap-4 border-b border-line pb-3">
                <div>
                    <h2 id="reader-overview-title" class="text-sm sm:text-base font-black uppercase tracking-tight text-ink">Không gian Độc giả</h2>
                    <p class="text-xs font-mono text-ink-muted">Các hoạt động đọc tin, lưu trữ và tương tác của bạn.</p>
                </div>
                <a href="{{ route('home') }}" class="text-xs font-mono font-bold uppercase text-ink hover:underline">
                    Khám phá trang tin &rarr;
                </a>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Bài viết đã lưu -->
                <a href="{{ route('favorites.index') }}" class="group flex flex-col justify-between border-2 border-line-strong bg-surface p-5 shadow-brutal-sm hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
                    <div>
                        <div class="flex items-center justify-between gap-2 border-b border-line pb-2">
                            <span class="text-xs font-mono font-bold uppercase tracking-wider text-ink-muted">Bài viết đã lưu</span>
                            <span class="size-7 grid place-items-center border border-line-strong bg-paper text-ink font-mono text-xs">
                                ★
                            </span>
                        </div>
                        <p class="mt-3 text-sm font-bold uppercase text-ink group-hover:underline">Bộ sưu tập bài viết</p>
                    </div>
                    <p class="mt-3 text-xs font-mono text-ink-muted border-t border-line pt-2">Đọc lại các bài viết bạn quan tâm &rarr;</p>
                </a>

                <!-- Lịch sử bình luận -->
                <a href="{{ route('profile.comments') }}" class="group flex flex-col justify-between border-2 border-line-strong bg-surface p-5 shadow-brutal-sm hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
                    <div>
                        <div class="flex items-center justify-between gap-2 border-b border-line pb-2">
                            <span class="text-xs font-mono font-bold uppercase tracking-wider text-ink-muted">Bình luận của tôi</span>
                            <span class="size-7 grid place-items-center border border-line-strong bg-paper text-ink font-mono text-xs">
                                “
                            </span>
                        </div>
                        <p class="mt-3 text-sm font-bold uppercase text-ink group-hover:underline">Ý kiến đóng góp</p>
                    </div>
                    <p class="mt-3 text-xs font-mono text-ink-muted border-t border-line pt-2">Xem và quản lý các bình luận &rarr;</p>
                </a>

                <!-- Hồ sơ & Thiết lập -->
                <a href="{{ route('profile.edit') }}" class="group flex flex-col justify-between border-2 border-line-strong bg-surface p-5 shadow-brutal-sm hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
                    <div>
                        <div class="flex items-center justify-between gap-2 border-b border-line pb-2">
                            <span class="text-xs font-mono font-bold uppercase tracking-wider text-ink-muted">Tài khoản</span>
                            <span class="size-7 grid place-items-center border border-line-strong bg-paper text-ink font-mono text-xs">
                                ID
                            </span>
                        </div>
                        <p class="mt-3 text-sm font-bold uppercase text-ink group-hover:underline">Hồ sơ cá nhân</p>
                    </div>
                    <p class="mt-3 text-xs font-mono text-ink-muted border-t border-line pt-2">Cài đặt mật khẩu, avatar tròn &rarr;</p>
                </a>
            </div>
        </section>

        <!-- Author Application Program Section -->
        <section aria-labelledby="author-program-title" class="space-y-6 pt-2">
            <!-- Program Header Banner -->
            <div class="border-2 border-line-strong bg-surface p-6 sm:p-7 shadow-brutal space-y-4">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                    <div class="space-y-2 max-w-2xl">
                        <div class="inline-flex items-center gap-2 border border-line-strong bg-lime px-3 py-1 text-xs font-mono font-bold uppercase tracking-wider text-ink shadow-brutal-sm">
                            <span>●</span>
                            <span>Chương trình Cộng tác viên & Tác giả NewsHub</span>
                        </div>
                        <h2 id="author-program-title" class="text-xl font-black uppercase tracking-tight text-ink sm:text-2xl">
                            Trở thành Tác giả bài viết trên NewsHub
                        </h2>
                        <p class="text-xs sm:text-sm text-ink-muted leading-relaxed">
                            Bạn đam mê chia sẻ chuyên môn, phân tích góc nhìn và viết bài? Hãy gia nhập đội ngũ tác giả để xuất bản nội dung chất lượng tới hàng nghìn độc giả trên toàn hệ thống.
                        </p>
                    </div>

                    <!-- 3 Steps Mini Timeline -->
                    <div class="grid grid-cols-3 gap-2 sm:gap-3 border border-line bg-paper p-3 sm:p-4 shrink-0">
                        <div class="text-center font-mono">
                            <span class="mx-auto grid size-6 place-items-center border border-line-strong bg-lime text-xs font-bold text-ink">1</span>
                            <p class="mt-1 text-xs font-bold uppercase text-ink">Gửi bài mẫu</p>
                            <p class="text-[10px] text-ink-muted">Chọn chuyên mục</p>
                        </div>
                        <div class="text-center font-mono">
                            <span class="mx-auto grid size-6 place-items-center border border-line-strong bg-surface text-xs font-bold text-ink">2</span>
                            <p class="mt-1 text-xs font-bold uppercase text-ink">Xét duyệt</p>
                            <p class="text-[10px] text-ink-muted">Ban biên tập</p>
                        </div>
                        <div class="text-center font-mono">
                            <span class="mx-auto grid size-6 place-items-center border border-line-strong bg-surface text-xs font-bold text-ink">3</span>
                            <p class="mt-1 text-xs font-bold uppercase text-ink">Đăng bài</p>
                            <p class="text-[10px] text-ink-muted">Nâng cấp Author</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STATE 1: PENDING APPLICATION -->
            @if ($authorApplication && $authorApplication->status === \App\Enums\AuthorApplicationStatus::Pending)
                <article class="border-2 border-line-strong bg-surface p-5 sm:p-6 shadow-brutal space-y-4">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div class="flex items-start gap-3.5">
                            <span class="grid size-10 shrink-0 place-items-center border-2 border-line-strong bg-lime text-ink font-mono font-bold text-base shadow-brutal-sm">
                                ⏳
                            </span>
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-base font-black uppercase text-ink">Đơn ứng tuyển của bạn đang được xét duyệt</h3>
                                    <span class="border border-line-strong bg-lime px-2.5 py-0.5 text-xs font-mono font-bold uppercase text-ink">
                                        Chờ duyệt
                                    </span>
                                </div>
                                <p class="mt-1 text-xs text-ink-muted leading-relaxed">
                                    Hồ sơ và bài viết mẫu của bạn đã được chuyển đến Ban biên tập NewsHub. Chúng tôi sẽ thẩm định tiêu chuẩn chất lượng và phản hồi trong thời gian sớm nhất.
                                </p>
                            </div>
                        </div>

                        <span class="shrink-0 text-xs font-mono text-ink-muted">
                            Nộp ngày: <strong class="text-ink">{{ $authorApplication->created_at->format('d/m/Y H:i') }}</strong>
                        </span>
                    </div>

                    <!-- Submitted Details Preview -->
                    <div class="mt-5 space-y-3 border border-line bg-paper p-4 text-xs font-mono">
                        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-line pb-2.5">
                            <span class="text-ink-muted uppercase">Chuyên mục đăng ký:</span>
                            <span class="font-bold text-ink">{{ $authorApplication->category->name }}</span>
                        </div>
                        <div>
                            <span class="block text-ink-muted uppercase font-bold">Giới thiệu bản thân & kinh nghiệm:</span>
                            <p class="mt-1 text-ink whitespace-pre-line leading-relaxed font-sans">{{ $authorApplication->bio }}</p>
                        </div>
                        <div class="border-t border-line pt-2.5">
                            <span class="block text-ink-muted uppercase font-bold">Tiêu đề bài viết mẫu:</span>
                            <p class="mt-0.5 font-black uppercase text-ink font-sans text-sm">{{ $authorApplication->sample_title }}</p>
                            <details class="group mt-2">
                                <summary class="cursor-pointer text-xs font-bold uppercase text-ink hover:underline">
                                    Xem nội dung bài mẫu đã nộp &darr;
                                </summary>
                                <div class="mt-2.5 whitespace-pre-line text-xs leading-relaxed text-ink border-t border-line pt-2 font-sans">
                                    {{ $authorApplication->sample_content }}
                                </div>
                            </details>
                        </div>
                    </div>

                    <p class="mt-4 text-xs font-mono text-ink-muted flex items-center gap-1.5">
                        <span class="font-bold text-ink">i</span>
                        <span>Mỗi độc giả chỉ duy trì 1 đơn xét duyệt trong một thời điểm. Bạn sẽ nhận được thông báo tại trang này khi đơn được xử lý.</span>
                    </p>
                </article>
            @endif

            <!-- STATE 2: REJECTED APPLICATION (Show Reason + Allow Re-applying) -->
            @if ($authorApplication && $authorApplication->status === \App\Enums\AuthorApplicationStatus::Rejected)
                <div class="border-2 border-line-strong bg-surface p-5 shadow-brutal">
                    <div class="flex items-start gap-3">
                        <span class="size-6 grid place-items-center bg-danger text-paper font-mono font-bold text-xs shrink-0 mt-0.5">X</span>
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-line pb-2">
                                <h3 class="text-sm font-black uppercase text-danger">Đơn ứng tuyển trước đó chưa được chấp thuận</h3>
                                <span class="text-xs font-mono text-ink-muted">
                                    Xét duyệt ngày: {{ $authorApplication->reviewed_at?->format('d/m/Y H:i') }}
                                </span>
                            </div>
                            <div class="mt-3 border border-line bg-paper p-3 text-xs font-mono text-ink">
                                <p class="font-bold uppercase text-danger">Phản hồi từ Ban biên tập:</p>
                                <p class="mt-1 leading-relaxed whitespace-pre-line font-sans">{{ $authorApplication->rejection_reason }}</p>
                            </div>
                            <p class="mt-3 text-xs text-ink-muted">
                                Bạn đừng nản lòng! Bạn hoàn toàn có thể hoàn thiện lại bài viết mẫu, bổ sung thêm thông tin giới thiệu và gửi lại đơn ứng tuyển mới bên dưới.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- APPLICATION FORM (Rendered when no active pending application) -->
            @if (! $authorApplication || $authorApplication->status === \App\Enums\AuthorApplicationStatus::Rejected)
                <div class="border-2 border-line-strong bg-surface p-5 sm:p-6 shadow-brutal space-y-5">
                    <div class="border-b border-line pb-4">
                        <h3 class="text-base font-black uppercase text-ink">Nộp hồ sơ ứng tuyển Tác giả</h3>
                        <p class="text-xs font-mono text-ink-muted">Vui lòng điền đầy đủ các thông tin và bài viết mẫu để Ban biên tập đánh giá.</p>
                    </div>

                    @unless (auth()->user()->hasVerifiedEmail())
                        <div class="border-2 border-line-strong bg-lime p-4 text-xs font-mono text-ink space-y-2 shadow-brutal-sm">
                            <p class="font-bold uppercase flex items-center gap-2">
                                <span>!</span>
                                Yêu cầu xác thực tài khoản
                            </p>
                            <p>
                                Bạn cần hoàn tất xác minh địa chỉ email trước khi có thể nộp đơn ứng tuyển làm Tác giả.
                            </p>
                            <div class="pt-2">
                                <a href="{{ route('verification.notice') }}" class="inline-flex min-h-8 items-center justify-center border border-line-strong bg-surface px-3 py-1 text-xs font-bold uppercase text-ink hover:bg-paper">
                                    Xác minh email ngay &rarr;
                                </a>
                            </div>
                        </div>
                    @else
                        <form method="POST" action="{{ route('author-applications.store') }}" class="space-y-4">
                            @csrf

                            <!-- Chuyên mục sở trường -->
                            <div class="space-y-1.5">
                                <label for="category_id" class="block text-xs font-mono font-bold uppercase tracking-wider text-ink">
                                    Chuyên mục ứng tuyển sở trường <span class="text-danger">*</span>
                                </label>
                                <select id="category_id"
                                        name="category_id"
                                        required
                                        class="w-full border border-line bg-paper px-3 py-2.5 text-xs text-ink focus:border-line-strong focus:outline-hidden focus:bg-surface focus:shadow-brutal-sm">
                                    <option value="">-- Chọn chuyên mục bạn muốn viết bài --</option>
                                    @foreach ($authorApplicationCategories as $category)
                                        <option value="{{ $category->id }}" @selected((int) old('category_id') === $category->id)>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="text-xs text-danger font-mono">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Giới thiệu bản thân & kinh nghiệm -->
                            <div class="space-y-1.5">
                                <label for="bio" class="block text-xs font-mono font-bold uppercase tracking-wider text-ink">
                                    Giới thiệu bản thân & kinh nghiệm viết lách <span class="text-danger">*</span>
                                </label>
                                <textarea id="bio"
                                          name="bio"
                                          rows="3"
                                          required
                                          maxlength="5000"
                                          placeholder="Chia sẻ ngắn về bạn, lĩnh vực am hiểu, kinh nghiệm viết bài hoặc link bài viết/blog bạn từng thực hiện..."
                                          class="w-full border border-line bg-paper px-3 py-2.5 text-xs text-ink placeholder:text-ink-light focus:border-line-strong focus:outline-hidden focus:bg-surface focus:shadow-brutal-sm leading-relaxed">{{ old('bio') }}</textarea>
                                @error('bio')
                                    <p class="text-xs text-danger font-mono">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tiêu đề bài viết mẫu -->
                            <div class="space-y-1.5">
                                <label for="sample_title" class="block text-xs font-mono font-bold uppercase tracking-wider text-ink">
                                    Tiêu đề bài viết mẫu đề xuất <span class="text-danger">*</span>
                                </label>
                                <input id="sample_title"
                                       type="text"
                                       name="sample_title"
                                       required
                                       maxlength="255"
                                       value="{{ old('sample_title') }}"
                                       placeholder="Ví dụ: Phân tích xu hướng trí tuệ nhân tạo và tác động thực tế năm 2026..."
                                       class="w-full border border-line bg-paper px-3 py-2.5 text-xs text-ink placeholder:text-ink-light focus:border-line-strong focus:outline-hidden focus:bg-surface focus:shadow-brutal-sm">
                                @error('sample_title')
                                    <p class="text-xs text-danger font-mono">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nội dung bài viết mẫu -->
                            <div class="space-y-1.5">
                                <label for="sample_content" class="block text-xs font-mono font-bold uppercase tracking-wider text-ink">
                                    Nội dung bài viết mẫu <span class="text-danger">*</span>
                                </label>
                                <textarea id="sample_content"
                                          name="sample_content"
                                          rows="9"
                                          required
                                          maxlength="20000"
                                          placeholder="Soạn thảo bài viết mẫu hoàn chỉnh (hoặc một bài phân tích tiêu biểu) để ban biên tập thẩm định văn phong, tư duy lập luận và độ sâu thông tin..."
                                          class="w-full border border-line bg-paper p-3 text-xs leading-relaxed text-ink font-mono placeholder:text-ink-light focus:border-line-strong focus:outline-hidden focus:bg-surface focus:shadow-brutal-sm">{{ old('sample_content') }}</textarea>
                                @error('sample_content')
                                    <p class="text-xs text-danger font-mono">{{ $message }}</p>
                                @enderror
                                <p class="text-[11px] font-mono text-ink-muted">
                                    Hỗ trợ tối đa 20.000 ký tự. Bài viết mẫu càng chi tiết và mạch lạc thì hồ sơ xét duyệt càng nhanh chóng.
                                </p>
                            </div>

                            <!-- Submit Action -->
                            <div class="pt-3 border-t border-line">
                                <button type="submit"
                                        class="inline-flex min-h-11 cursor-pointer items-center justify-center gap-2 border-2 border-line-strong bg-lime px-6 py-2.5 text-xs font-mono font-bold uppercase tracking-wider text-ink shadow-brutal hover:bg-lime-hover hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-brutal-sm transition-all active:translate-x-1 active:translate-y-1">
                                    <svg class="size-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M3.105 2.289a.75.75 0 00-.826.95l1.414 4.925A1.5 1.5 0 005.135 9.25h6.115a.75.75 0 010 1.5H5.135a1.5 1.5 0 00-1.442 1.086l-1.414 4.926a.75.75 0 00.826.95 28.896 28.896 0 0015.293-7.154.75.75 0 000-1.115A28.897 28.897 0 003.105 2.289z" />
                                    </svg>
                                    <span>Gửi đơn ứng tuyển Tác giả</span>
                                </button>
                            </div>
                        </form>
                    @endunless
                </div>
            @endif
        </section>
    @endif
</div>
@endsection
