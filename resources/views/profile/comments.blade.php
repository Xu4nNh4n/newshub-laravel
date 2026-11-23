@extends('layouts.dashboard', ['title' => 'Bình luận của tôi'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b border-[#E2E8F0] pb-5">
        <div>
            <h1 class="text-xl font-black uppercase tracking-tight text-ink sm:text-2xl">Bình luận của tôi</h1>
            <p class="text-xs font-mono text-ink-muted">Theo dõi toàn bộ lịch sử bình luận bao gồm cả bình luận đang hiển thị, đã ẩn hoặc đã xóa.</p>
        </div>
        <a href="{{ route('profile.edit') }}" class="inline-flex min-h-9 items-center gap-1.5 rounded-[8px] border border-[#CBD5E1] bg-surface px-3.5 py-1.5 text-xs font-mono font-bold uppercase tracking-wider text-ink shadow-none hover:bg-paper transition-colors">
            &larr; Quay lại hồ sơ
        </a>
    </div>

    <div class="space-y-4">
        @forelse ($comments as $comment)
            <article class="rounded-[12px] border border-[#E2E8F0] bg-surface p-5 shadow-[0_1px_3px_rgb(15_23_42_/_0.04)] space-y-3">
                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between text-xs font-mono text-ink-muted pb-2 border-b border-[#E2E8F0]">
                    <div>
                        @if ($comment->post
                            && $comment->post->status === \App\Enums\PostStatus::Published
                            && $comment->post->published_at?->isPast()
                            && $comment->post->category?->status === \App\Enums\CategoryStatus::Active
                            && ! $comment->post->trashed())
                            <a href="{{ route('news.show', $comment->post->slug) }}" class="font-bold text-ink hover:underline">
                                {{ $comment->post->title }}
                            </a>
                        @else
                            <span class="font-medium text-ink-muted">{{ $comment->post?->title ?? 'Bài viết không còn tồn tại' }}</span>
                        @endif
                    </div>
                    <span class="text-xs font-mono text-ink-muted">{{ $comment->created_at->format('d/m/Y H:i') }}</span>
                </div>

                <p class="whitespace-pre-line text-xs leading-relaxed text-ink rounded-[12px] border border-[#E2E8F0] bg-paper p-3 font-mono">
                    {{ $comment->trashed() ? 'Bình luận đã được xóa.' : $comment->content }}
                </p>

                <div class="flex items-center justify-between text-xs font-mono">
                    <span class="rounded-[6px] border border-[#E2E8F0] px-2 py-0.5 font-bold uppercase tracking-wider bg-paper text-ink">
                        {{ $comment->trashed() ? 'Đã xóa' : $comment->status->label() }}
                    </span>
                </div>
            </article>
        @empty
            <div class="rounded-[12px] border border-dashed border-[#E2E8F0] bg-surface p-12 text-center text-xs font-mono text-ink-muted">
                Bạn chưa có bình luận nào.
            </div>
        @endforelse
    </div>

    <div>
        {{ $comments->links() }}
    </div>
</div>
@endsection
