@extends('layouts.dashboard', ['title' => 'Duyệt bài'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-2 border-b border-[#E2E8F0] pb-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <span class="font-mono text-xs font-bold uppercase tracking-wider text-ink-muted block mb-1">Kiểm duyệt nội dung</span>
            <h1 class="text-2xl font-black uppercase tracking-tight text-ink sm:text-3xl font-heading">Hàng đợi duyệt bài</h1>
            <p class="text-xs text-ink-muted mt-1">Duyệt bài xuất bản ngay, hẹn giờ đăng hoặc trả bài kèm lý do phản hồi chi tiết.</p>
        </div>
        <span class="inline-flex items-center gap-2 self-start rounded-[6px] border border-[#E2E8F0] bg-paper px-3 py-1 font-mono text-xs font-bold uppercase text-ink">
            <span class="size-2 bg-lime"></span>
            Hàng đợi kiểm duyệt
        </span>
    </div>

    <div class="space-y-5">
        @forelse($posts as $post)
            <article class="rounded-[12px] border border-[#E2E8F0] bg-surface p-6 shadow-[0_1px_3px_rgb(15_23_42_/_0.04)] transition-all">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-[6px] border border-[#E2E8F0] bg-lime px-2 py-0.5 font-mono text-[11px] font-bold uppercase text-ink">
                                {{ $post->status->label() }}
                            </span>
                            <span class="font-mono text-xs text-ink-muted">
                                {{ $post->category->name }} · Tác giả: <strong class="text-ink font-bold">{{ $post->author->name }}</strong>
                            </span>
                        </div>
                        <h2 class="mt-2.5 font-heading text-lg sm:text-xl font-black uppercase tracking-tight text-ink">
                            {{ $post->title }}
                        </h2>

                    </div>
                    <a href="{{ route('posts.preview', $post) }}" class="inline-flex shrink-0 items-center gap-1.5 rounded-[8px] border border-[#CBD5E1] bg-paper px-3.5 py-1.5 font-mono text-xs font-bold uppercase text-ink shadow-none hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <span>Xem trước</span>
                    </a>
                </div>

                @if($post->summary)
                    <p class="mt-4 border-l-3 border-ink bg-paper p-3 text-xs leading-relaxed text-ink font-serif italic">
                        {{ $post->summary }}
                    </p>
                @endif

                <details class="group mt-4 rounded-[12px] border border-[#E2E8F0] bg-paper p-3">
                    <summary class="cursor-pointer font-mono text-xs font-bold uppercase tracking-wider text-ink hover:text-danger select-none">
                        Xem nội dung chi tiết &darr;
                    </summary>
                    <div @class(['article-content mt-3 text-xs leading-relaxed text-ink border-t border-[#E2E8F0] pt-3', 'whitespace-pre-line' => ! str_contains($sanitizedContents[$post->id], '<p')])>{!! $sanitizedContents[$post->id] !!}</div>
                </details>

                <div class="mt-6 grid gap-4 border-t border-[#E2E8F0] pt-5 md:grid-cols-2">
                    <!-- Form Duyệt / Hẹn giờ -->
                    <form method="POST" action="{{ route('admin.post-reviews.approve', $post) }}" class="flex flex-col gap-2 sm:flex-row sm:items-center"
                          data-confirm="Xác nhận phê duyệt và xuất bản bài viết này?"
                          data-confirm-title="Phê duyệt bài viết"
                          data-confirm-subtext="{{ $post->title }}"
                          data-confirm-type="success"
                          data-confirm-btn="Phê duyệt ngay">
                        @csrf
                        <div class="min-w-0 grow">
                            <label class="sr-only" for="published_at_{{ $post->id }}">Thời gian xuất bản</label>
                            <input id="published_at_{{ $post->id }}" type="datetime-local" name="published_at" class="w-full rounded-[8px] border border-control bg-paper px-3 py-1.5 font-mono text-xs text-ink outline-none focus:border-[#1D4ED8] focus:shadow-[0_0_0_2px_rgb(29_78_216_/_0.25)]">
                        </div>
                        <button type="submit" class="inline-flex min-h-9 shrink-0 items-center justify-center rounded-[8px] border border-[#E2E8F0] bg-lime px-5 py-1.5 font-mono text-xs font-bold uppercase text-ink shadow-none hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all cursor-pointer">
                            Duyệt bài
                        </button>
                    </form>

                    <!-- Form Từ chối -->
                    <form method="POST" action="{{ route('admin.post-reviews.reject', $post) }}" class="flex flex-col gap-2 sm:flex-row sm:items-center"
                          data-confirm="Xác nhận từ chối duyệt bài viết này và gửi lý do phản hồi cho tác giả?"
                          data-confirm-title="Từ chối duyệt bài viết"
                          data-confirm-subtext="{{ $post->title }}"
                          data-confirm-type="danger"
                          data-confirm-btn="Từ chối duyệt">
                        @csrf
                        <div class="min-w-0 grow">
                            <label class="sr-only" for="reason_{{ $post->id }}">Lý do từ chối</label>
                            <input id="reason_{{ $post->id }}" name="reason" required placeholder="Lý do từ chối (bắt buộc)..." class="w-full rounded-[8px] border border-control bg-paper px-3 py-1.5 text-xs text-ink placeholder-ink-muted outline-none focus:border-danger">
                        </div>
                        <button type="submit" class="inline-flex min-h-9 shrink-0 items-center justify-center rounded-[8px] border border-[#E2E8F0] bg-danger px-4 py-1.5 font-mono text-xs font-bold uppercase text-paper shadow-none hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all cursor-pointer">
                            Từ chối
                        </button>
                    </form>
                </div>
            </article>
        @empty
            <div class="rounded-[12px] border border-[#E2E8F0] bg-surface p-12 text-center font-mono text-xs text-ink-muted shadow-[0_1px_3px_rgb(15_23_42_/_0.04)]">
                Không có bài chờ duyệt trong hệ thống.
            </div>
        @endforelse
    </div>

    <div>
        {{ $posts->links() }}
    </div>
</div>
@endsection
