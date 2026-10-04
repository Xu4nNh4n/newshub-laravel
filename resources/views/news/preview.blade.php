@extends('layouts.app', ['title' => 'Xem trước: '.$post->title.' - NewsHub'])

@section('content')
<div class="mx-auto max-w-4xl space-y-8 bg-surface border-x-2 border-line-strong p-4 sm:p-8 shadow-brutal font-sans">
    <!-- Preview Mode Alert Banner -->
    <div class="border-2 border-line-strong bg-amber-100 p-4 text-xs text-ink flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-brutal-sm font-mono">
        <div class="flex items-center gap-2.5">
            <svg class="size-5 shrink-0 text-ink" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
            </svg>
            <div>
                <p class="font-bold text-ink uppercase tracking-wider">Chế độ xem trước bài viết</p>
                <p class="mt-0.5 text-ink-muted">Trạng thái hiện tại: <strong class="uppercase text-ink">{{ $post->status->value }}</strong> (Bài viết này chưa công khai trên trang tin).</p>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('author.posts.index') }}" class="inline-flex min-h-8 items-center justify-center border border-line-strong bg-surface px-3 py-1.5 text-xs font-bold text-ink hover:bg-paper transition-colors">
                &larr; Quản lý bài viết
            </a>
            @can('update', $post)
                <a href="{{ route('author.posts.edit', $post) }}" class="inline-flex min-h-8 items-center justify-center border border-line-strong bg-lime px-3.5 py-1.5 text-xs font-bold text-ink shadow-brutal-sm hover:bg-lime-hover transition-colors">
                    Sửa bài viết
                </a>
            @endcan
        </div>
    </div>

    <!-- Article Preview Content -->
    <article class="space-y-6">
        <div class="space-y-3">
            <span class="inline-flex items-center border border-line-strong bg-paper px-3 py-1 text-xs font-bold font-mono uppercase tracking-wider text-ink shadow-2xs">
                {{ $post->category->name }}
            </span>

            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight text-ink leading-tight">
                {{ $post->title }}
            </h1>
        </div>

        <div class="flex items-center gap-3 border-y-2 border-line-strong py-3 text-xs font-mono text-ink-muted">
            <span class="font-bold text-ink">{{ $post->author->name }}</span>
            <span>&middot;</span>
            <span>{{ $post->published_at?->format('d/m/Y H:i') ?? 'Chưa đặt lịch xuất bản' }}</span>
        </div>

        @if ($post->summary)
            <div class="border-l-4 border-line-strong bg-paper p-5 text-base sm:text-lg font-bold leading-relaxed text-ink font-sans">
                {{ $post->summary }}
            </div>
        @endif

        @if ($thumbnailUrl && $post->show_thumbnail_in_post)
            <figure class="overflow-hidden border-2 border-line-strong bg-paper">
                <img src="{{ $thumbnailUrl }}"
                     alt="{{ $post->title }}"
                     class="aspect-video w-full object-cover">
            </figure>
        @endif

        <div class="text-base sm:text-lg text-ink leading-relaxed sm:leading-8 font-normal space-y-5 whitespace-pre-line pt-2">
            {!! $safeContent !!}
        </div>

        @if ($post->tags->isNotEmpty())
            <div class="border-t-2 border-line-strong pt-5 space-y-2 font-mono">
                <p class="text-xs font-bold uppercase tracking-wider text-ink">Thẻ bài viết:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach ($post->tags as $tag)
                        <span class="inline-flex items-center gap-1 border border-line bg-paper px-3 py-1 text-xs font-bold text-ink">
                            #{{ $tag->name }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif
    </article>
</div>
@endsection
