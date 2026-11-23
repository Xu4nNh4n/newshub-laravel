@extends('layouts.app', ['title' => 'Xem trước: '.$post->title.' - BaoUT'])

@section('content')
<div class="mx-auto max-w-4xl space-y-8 bg-surface border-x border-[#E2E8F0] p-4 sm:p-8 shadow-none font-sans">
    <!-- Preview Mode Alert Banner -->
    <div class="rounded-[12px] border border-[#E2E8F0] bg-[#dce6f2] p-4 text-xs text-ink flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-[0_1px_3px_rgb(15_23_42_/_0.04)] font-mono">
        <div class="flex items-center gap-2.5">
            <svg class="size-5 shrink-0 text-ink" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
            </svg>
            <div>
                <p class="font-bold text-ink uppercase tracking-wider">Chế độ xem trước bài viết</p>
                <p class="mt-0.5 text-ink-muted">Trạng thái hiện tại: <strong class="uppercase text-ink">{{ $post->status->label() }}</strong> (Bài viết này chưa công khai trên trang tin).</p>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('author.posts.index') }}" class="inline-flex min-h-8 items-center justify-center rounded-[8px] border border-[#CBD5E1] bg-surface px-3 py-1.5 text-xs font-bold text-ink hover:bg-paper transition-colors">
                &larr; Quản lý bài viết
            </a>
            @can('update', $post)
                <a href="{{ route('author.posts.edit', $post) }}" class="inline-flex min-h-8 items-center justify-center rounded-[8px] border border-[#E2E8F0] bg-lime px-3.5 py-1.5 text-xs font-bold text-white shadow-none hover:bg-ink hover:text-white transition-colors">
                    Sửa bài viết
                </a>
            @endcan
        </div>
    </div>

    <!-- Article Preview Content -->
    <article class="space-y-6">
        <div class="space-y-3">
            <span class="inline-flex items-center rounded-[6px] border border-[#E2E8F0] bg-paper px-3 py-1 text-xs font-bold font-mono uppercase tracking-wider text-ink">
                {{ $post->category->name }}
            </span>

            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight text-ink leading-tight">
                {{ $post->title }}
            </h1>
        </div>

        <div class="flex items-center gap-3 border-y border-[#E2E8F0] py-3 text-xs font-mono text-ink-muted">
            <span class="font-bold text-ink">{{ $post->author->name }}</span>
            <span>&middot;</span>
            <span>{{ $post->published_at?->format('d/m/Y H:i') ?? 'Chưa đặt lịch xuất bản' }}</span>
        </div>

        @if ($post->summary)
            <div class="border-l-4 border-line-strong bg-paper p-5 text-base sm:text-lg font-bold leading-relaxed text-ink font-sans">
                {{ $post->summary }}
            </div>
        @endif

        @if ($thumbnailUrl)
            <figure class="overflow-hidden rounded-[12px] border border-[#E2E8F0] bg-paper">
                <img src="{{ $thumbnailUrl }}"
                     alt="{{ $post->title }}"
                     class="aspect-video w-full object-cover">
            </figure>
        @endif

        <div @class(['article-content text-base sm:text-lg text-ink leading-relaxed sm:leading-8 font-normal space-y-5 pt-2', 'whitespace-pre-line' => ! str_contains($safeContent, '<p')])>{!! $safeContent !!}</div>

    </article>
</div>
@endsection
