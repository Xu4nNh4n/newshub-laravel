@extends('layouts.dashboard', ['title' => 'Bài viết đã lưu'])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b-2 border-line-strong pb-5">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-black uppercase tracking-tight text-ink sm:text-2xl">Bài viết đã lưu</h1>
                @if ($favorites->total() > 0)
                    <span class="border border-line-strong bg-lime px-2.5 py-0.5 text-xs font-mono font-bold text-ink">
                        {{ $favorites->total() }} bài
                    </span>
                @endif
            </div>
            <p class="mt-1 text-xs font-mono text-ink-muted">Danh sách tin tức và bài viết bạn đã đánh dấu quan tâm (chỉ hiển thị các bài viết đang công khai).</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('news.index') }}" class="inline-flex min-h-9 items-center gap-1.5 border border-line-strong bg-surface px-3.5 py-1.5 text-xs font-mono font-bold uppercase tracking-wider text-ink shadow-brutal-sm hover:bg-paper transition-colors">
                <svg aria-hidden="true" class="size-3.5 text-ink" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/>
                </svg>
                <span>Khám phá tin tức</span>
            </a>
        </div>
    </div>

    <!-- Grid List -->
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($favorites as $favorite)
            @php
                $post = $favorite->post;
            @endphp
            <article class="group flex flex-col justify-between border-2 border-line-strong bg-surface shadow-brutal transition-all duration-200 hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-brutal-sm">
                <div>
                    <!-- Thumbnail -->
                    <a href="{{ route('news.show', $post->slug) }}" class="relative block aspect-video w-full overflow-hidden border-b-2 border-line-strong bg-paper">
                        @if ($post->thumbnail)
                            <img src="{{ asset('storage/' . $post->thumbnail) }}"
                                 alt="{{ $post->title }}"
                                 class="size-full object-cover grayscale-25 group-hover:grayscale-0 transition-all duration-300"
                                 loading="lazy">
                        @else
                            <div class="flex size-full items-center justify-center bg-paper text-ink-light">
                                <svg class="size-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                            </div>
                        @endif
                        <span class="absolute left-2 top-2 border border-line-strong bg-surface px-2 py-0.5 text-[10px] font-mono font-bold uppercase tracking-wider text-ink shadow-brutal-sm">
                            {{ $post->category?->name ?? 'Tin tức' }}
                        </span>
                    </a>

                    <!-- Content Details -->
                    <div class="space-y-2 p-5">
                        <h2 class="text-sm font-black uppercase leading-snug text-ink transition-colors group-hover:underline line-clamp-2">
                            <a href="{{ route('news.show', $post->slug) }}">
                                {{ $post->title }}
                            </a>
                        </h2>

                        @if ($post->summary)
                            <p class="text-xs text-ink-muted line-clamp-2 leading-relaxed">
                                {{ $post->summary }}
                            </p>
                        @endif

                        <div class="flex items-center gap-1.5 text-xs font-mono text-ink-muted pt-1">
                            <span class="truncate font-bold text-ink">{{ $post->author?->name ?? 'Tác giả' }}</span>
                            <span>&middot;</span>
                            <span class="shrink-0">{{ $post->published_at?->format('d/m/Y') ?? '—' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Footer Action Bar -->
                <div class="flex items-center justify-between border-t border-line bg-paper px-5 py-3">
                    <a href="{{ route('news.show', $post->slug) }}" class="inline-flex items-center gap-1 text-xs font-mono font-bold uppercase text-ink hover:underline">
                        <span>Đọc tiếp</span>
                        <span>&rarr;</span>
                    </a>

                    <form method="POST" action="{{ route('favorites.destroy', $post) }}"
                          data-confirm="Bạn có chắc chắn muốn bỏ lưu bài viết này khỏi danh sách quan tâm?"
                          data-confirm-title="Bỏ lưu bài viết"
                          data-confirm-subtext="{{ $post->title }}"
                          data-confirm-type="warning"
                          data-confirm-btn="Bỏ lưu">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex min-h-7 cursor-pointer items-center gap-1 border border-line-strong bg-surface px-2.5 py-1 text-xs font-mono font-bold text-danger hover:bg-paper transition-all">
                            <span>Bỏ lưu</span>
                        </button>
                    </form>
                </div>
            </article>
        @empty
            <div class="col-span-full flex flex-col items-center justify-center border-2 border-dashed border-line-strong bg-surface p-12 text-center shadow-brutal space-y-3">
                <span class="size-12 grid place-items-center border-2 border-line-strong bg-paper text-ink font-mono font-bold text-lg shadow-brutal-sm">
                    ★
                </span>
                <h3 class="text-base font-black uppercase text-ink">Bạn chưa lưu bài viết nào</h3>
                <p class="max-w-sm text-xs font-mono text-ink-muted leading-relaxed">
                    Khi đọc tin tức, bạn có thể bấm nút "Lưu bài viết" để xem lại các tin tức quan tâm ngay tại đây.
                </p>
                <a href="{{ route('news.index') }}" class="mt-2 inline-flex min-h-10 items-center gap-2 border-2 border-line-strong bg-lime px-5 py-2 text-xs font-mono font-bold uppercase tracking-wider text-ink shadow-brutal hover:bg-lime-hover hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-brutal-sm transition-all">
                    <span>Khám phá tin tức &rarr;</span>
                </a>
            </div>
        @endforelse
    </div>

    @if ($favorites->hasPages())
        <div class="pt-4">
            {{ $favorites->links() }}
        </div>
    @endif
</div>
@endsection
