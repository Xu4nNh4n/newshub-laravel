@extends('layouts.dashboard', ['title' => 'Bài viết đã đọc'])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b-2 border-line-strong pb-5">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-xl font-black uppercase tracking-tight text-ink sm:text-2xl">Bài viết đã đọc</h1>
                @if ($totalCount > 0)
                    <span class="border border-line-strong bg-lime px-2.5 py-0.5 text-xs font-mono font-bold text-ink">
                        {{ $totalCount }} bài
                    </span>
                @endif
            </div>
            <p class="mt-1 text-xs font-mono text-ink-muted">Xem lại các tin tức và bài báo bạn đã từng theo dõi trên NewsHub theo danh sách thời gian.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if ($totalCount > 0)
                <form method="POST" action="{{ route('reading-history.clear') }}" data-confirm="Bạn có chắc chắn muốn xóa toàn bộ lịch sử đọc bài viết không? Thao tác này không thể hoàn tác.">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex min-h-9 items-center gap-1.5 border border-line-strong bg-surface px-3.5 py-1.5 text-xs font-mono font-bold uppercase tracking-wider text-danger shadow-brutal-sm hover:bg-paper cursor-pointer transition-colors">
                        <span>Xóa tất cả lịch sử</span>
                    </button>
                </form>
            @endif

            <a href="{{ route('news.index') }}" class="inline-flex min-h-9 items-center gap-1.5 border border-line-strong bg-surface px-3.5 py-1.5 text-xs font-mono font-bold uppercase tracking-wider text-ink shadow-brutal-sm hover:bg-paper transition-colors">
                <svg aria-hidden="true" class="size-3.5 text-ink" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/>
                </svg>
                <span>Khám phá tin tức</span>
            </a>
        </div>
    </div>

    <!-- Timeframe Filter Tabs -->
    <div class="flex items-center gap-2 border-b border-line pb-3 text-xs font-mono overflow-x-auto">
        <span class="text-ink-muted uppercase font-bold tracking-wider mr-1 shrink-0">Lọc thời gian:</span>
        <a href="{{ route('reading-history.index', ['timeframe' => 'all']) }}"
           @class([
               'px-3 py-1 border font-bold uppercase shrink-0 transition-colors',
               'border-line-strong bg-lime text-ink shadow-brutal-sm' => $timeframe === 'all',
               'border-line bg-surface text-ink-muted hover:border-line-strong hover:text-ink' => $timeframe !== 'all',
           ])>
            Tất cả
        </a>
        <a href="{{ route('reading-history.index', ['timeframe' => 'today']) }}"
           @class([
               'px-3 py-1 border font-bold uppercase shrink-0 transition-colors',
               'border-line-strong bg-lime text-ink shadow-brutal-sm' => $timeframe === 'today',
               'border-line bg-surface text-ink-muted hover:border-line-strong hover:text-ink' => $timeframe !== 'today',
           ])>
            Hôm nay
        </a>
        <a href="{{ route('reading-history.index', ['timeframe' => 'week']) }}"
           @class([
               'px-3 py-1 border font-bold uppercase shrink-0 transition-colors',
               'border-line-strong bg-lime text-ink shadow-brutal-sm' => $timeframe === 'week',
               'border-line bg-surface text-ink-muted hover:border-line-strong hover:text-ink' => $timeframe !== 'week',
           ])>
            7 ngày qua
        </a>
        <a href="{{ route('reading-history.index', ['timeframe' => 'month']) }}"
           @class([
               'px-3 py-1 border font-bold uppercase shrink-0 transition-colors',
               'border-line-strong bg-lime text-ink shadow-brutal-sm' => $timeframe === 'month',
               'border-line bg-surface text-ink-muted hover:border-line-strong hover:text-ink' => $timeframe !== 'month',
           ])>
            30 ngày qua
        </a>
    </div>

    <!-- Reading History List View -->
    @if ($readingHistory->isNotEmpty())
        <div class="space-y-4">
            @foreach ($readingHistory as $viewItem)
                @php
                    $post = $viewItem->post;
                @endphp
                <article class="group flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-2 border-line-strong bg-surface p-4 transition-all duration-200 hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-brutal-sm shadow-brutal">
                    <!-- Left: Thumbnail & Main Content -->
                    <div class="flex flex-1 items-start gap-4 min-w-0 w-full sm:w-auto">
                        <!-- Thumbnail -->
                        <a href="{{ route('news.show', $post->slug) }}" class="relative aspect-video w-32 sm:w-44 shrink-0 overflow-hidden border border-line-strong bg-paper block">
                            @if ($post->thumbnail)
                                <img src="{{ asset('storage/' . $post->thumbnail) }}"
                                     alt="{{ $post->title }}"
                                     class="size-full object-cover grayscale-25 group-hover:grayscale-0 transition-all duration-300"
                                     loading="lazy">
                            @else
                                <div class="flex size-full items-center justify-center bg-paper text-ink-light">
                                    <svg class="size-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                    </svg>
                                </div>
                            @endif
                        </a>

                        <!-- Text Details -->
                        <div class="space-y-1.5 min-w-0 flex-1">
                            <!-- Category & Read Time -->
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="border border-line-strong bg-paper px-2 py-0.5 text-[10px] font-mono font-bold uppercase tracking-wider text-ink">
                                    {{ $post->category?->name ?? 'Tin tức' }}
                                </span>
                                <span class="inline-flex items-center gap-1 text-xs font-mono text-ink-muted">
                                    Đã đọc {{ $viewItem->viewed_at->diffForHumans() }}
                                </span>
                            </div>

                            <!-- Post Title -->
                            <h2 class="text-sm sm:text-base font-black uppercase text-ink transition-colors hover:underline line-clamp-2">
                                <a href="{{ route('news.show', $post->slug) }}">
                                    {{ $post->title }}
                                </a>
                            </h2>

                            <!-- Post Summary -->
                            @if ($post->summary)
                                <p class="text-xs text-ink-muted line-clamp-2 hidden sm:block leading-relaxed">
                                    {{ $post->summary }}
                                </p>
                            @endif

                            <!-- Meta line: Author & Date -->
                            <div class="flex flex-wrap items-center gap-3 text-xs font-mono text-ink-muted pt-0.5">
                                <span>Tác giả: <strong class="text-ink font-bold">{{ $post->author?->name ?? 'Tòa soạn' }}</strong></span>
                                <span>&bull;</span>
                                <span class="tabular-nums">{{ number_format($post->view_count) }} lượt xem</span>
                                <span>&bull;</span>
                                <span>{{ $viewItem->viewed_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right / Mobile Actions -->
                    <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-center gap-2 shrink-0 border-t border-line pt-3 sm:border-0 sm:pt-0 w-full sm:w-auto">
                        <a href="{{ route('news.show', $post->slug) }}"
                           class="inline-flex min-h-9 items-center gap-1.5 border-2 border-line-strong bg-lime px-4 py-1.5 text-xs font-mono font-bold uppercase tracking-wider text-ink shadow-brutal-sm hover:bg-lime-hover">
                            <span>Đọc lại</span>
                            <span>&rarr;</span>
                        </a>

                        <form method="POST" action="{{ route('reading-history.destroy', $post) }}" data-confirm="Xóa bài viết này khỏi lịch sử đọc của bạn?">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex min-h-8 items-center gap-1 px-2.5 py-1 text-xs font-mono text-danger hover:underline cursor-pointer"
                                    title="Xóa bài viết khỏi lịch sử đọc">
                                <span>Xóa khỏi lịch sử</span>
                            </button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="pt-4">
            {{ $readingHistory->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="flex flex-col items-center justify-center border-2 border-dashed border-line-strong bg-surface py-16 text-center space-y-3 shadow-brutal">
            <div class="size-12 grid place-items-center border-2 border-line-strong bg-paper text-ink font-mono font-bold text-lg shadow-brutal-sm">
                !
            </div>
            <h3 class="text-base font-black uppercase text-ink">Chưa có bài viết nào trong lịch sử</h3>
            <p class="max-w-sm text-xs font-mono text-ink-muted">
                Khi bạn đọc các bài báo trên NewsHub, hệ thống sẽ tự động lưu lại tại đây theo danh sách để bạn có thể xem lại dễ dàng bất kỳ lúc nào.
            </p>
            <div class="pt-2">
                <a href="{{ route('news.index') }}" class="inline-flex min-h-10 items-center gap-2 border-2 border-line-strong bg-lime px-5 py-2 text-xs font-mono font-bold uppercase tracking-wider text-ink shadow-brutal hover:bg-lime-hover hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-brutal-sm transition-all">
                    <span>Đọc tin tức ngay &rarr;</span>
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
