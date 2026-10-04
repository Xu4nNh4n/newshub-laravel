@extends('layouts.dashboard', ['title' => 'Quản lý bình luận'])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b-2 border-line-strong pb-5">
        <div>
            <span class="font-mono text-xs font-bold uppercase tracking-wider text-ink-muted block mb-1">Tương tác độc giả</span>
            <h1 class="text-2xl font-black uppercase tracking-tight text-ink sm:text-3xl font-heading">Quản lý bình luận</h1>
            <p class="text-xs text-ink-muted mt-1">Ẩn, khôi phục hoặc xóa mềm bình luận vi phạm quy chuẩn cộng đồng.</p>
        </div>
        <a href="{{ route('admin.comments.export') }}" class="inline-flex min-h-10 items-center gap-2 border-2 border-line-strong bg-surface px-4 py-2 font-mono text-xs font-bold uppercase text-ink shadow-brutal-sm hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
            <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4.5 2A1.5 1.5 0 003 3.5v13A1.5 1.5 0 004.5 18h11a1.5 1.5 0 001.5-1.5V7.621a1.5 1.5 0 00-.44-1.06l-4.12-4.122A1.5 1.5 0 0010.378 2H4.5zm4.75 6.75a.75.75 0 011.5 0v3.69l1.22-1.22a.75.75 0 111.06 1.06l-2.5 2.5a.75.75 0 01-1.06 0l-2.5-2.5a.75.75 0 111.06-1.06l1.22 1.22V8.75z" clip-rule="evenodd" />
            </svg>
            <span>Xuất CSV</span>
        </a>
    </div>

    <!-- Filter Bar -->
    <form method="GET" class="flex flex-col gap-3 border-2 border-line-strong bg-surface p-4 sm:flex-row sm:items-center shadow-brutal-sm">
        <div class="min-w-0 flex-1">
            <label for="q" class="sr-only">Tìm kiếm</label>
            <input id="q" name="q" value="{{ $filters['q'] ?? '' }}" maxlength="100" placeholder="Tìm trong nội dung bình luận..." class="w-full border-2 border-line bg-paper px-3.5 py-2 text-xs text-ink placeholder-ink-muted outline-none focus:border-ink">
        </div>
        <div class="w-full sm:w-48">
            <label for="state" class="sr-only">Trạng thái</label>
            <select id="state" name="state" class="w-full border-2 border-line bg-paper px-3 py-2 text-xs font-mono text-ink outline-none focus:border-ink">
                <option value="">Mọi trạng thái</option>
                @foreach($states as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['state'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="inline-flex min-h-9 cursor-pointer shrink-0 items-center justify-center gap-1.5 border-2 border-line-strong bg-paper px-5 py-2 font-mono text-xs font-bold uppercase text-ink shadow-brutal-sm hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
            <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M2.628 1.601C5.028 1.206 7.49 1 10 1s4.973.206 7.372.601a.75.75 0 01.628.74v2.288a2.25 2.25 0 01-.659 1.59l-4.682 4.683a2.25 2.25 0 00-.659 1.59v3.037c0 .684-.31 1.33-.844 1.757l-1.937 1.55A.75.75 0 018 18.25v-5.757a2.25 2.25 0 00-.659-1.591L2.659 6.22A2.25 2.25 0 012 4.629V2.34a.75.75 0 01.628-.74z" clip-rule="evenodd" />
            </svg>
            <span>Lọc</span>
        </button>
    </form>

    <!-- Comments List -->
    <div class="space-y-4">
        @forelse($comments as $comment)
            <article class="border-2 border-line-strong bg-surface p-5 shadow-brutal transition-all">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="font-bold text-ink text-sm">
                            {{ $comment->user->name }}
                            <span class="font-mono text-xs font-normal text-ink-muted">({{ $comment->user->email }})</span>
                        </p>
                        <a href="{{ route('news.show', $comment->post->slug) }}" class="mt-1 inline-block font-mono text-xs font-bold text-ink underline hover:text-danger">
                            Bài viết: {{ $comment->post->title }}
                        </a>
                    </div>
                    <span class="shrink-0 border border-line-strong bg-paper px-2 py-0.5 font-mono text-[11px] font-bold uppercase text-ink">
                        {{ $comment->trashed() ? 'deleted' : $comment->status->value }} · {{ $comment->created_at->format('d/m/Y H:i') }}
                    </span>
                </div>

                <p class="mt-3 whitespace-pre-line text-xs leading-relaxed text-ink bg-paper p-3 border border-line">
                    {{ $comment->content }}
                </p>

                @unless($comment->trashed())
                    <div class="mt-4 flex flex-wrap items-center gap-2 pt-3 border-t border-line">
                        @if($comment->status === \App\Enums\CommentStatus::Visible)
                            <form method="POST" action="{{ route('admin.comments.update', $comment) }}">
                                @csrf @method('PATCH')
                                <button name="action" value="hide" class="inline-flex min-h-7 cursor-pointer items-center border border-line-strong bg-paper px-3 py-1 font-mono text-[11px] font-bold uppercase text-ink shadow-brutal-sm hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
                                    Ẩn bình luận
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.comments.update', $comment) }}">
                                @csrf @method('PATCH')
                                <button name="action" value="restore" class="inline-flex min-h-7 cursor-pointer items-center border border-line-strong bg-lime px-3 py-1 font-mono text-[11px] font-bold uppercase text-ink shadow-brutal-sm transition-all">
                                    Hiện lại
                                </button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('admin.comments.update', $comment) }}"
                              data-confirm="Bạn có chắc chắn muốn xóa mềm bình luận này khỏi hệ thống?"
                              data-confirm-title="Xóa bình luận"
                              data-confirm-type="danger"
                              data-confirm-btn="Xác nhận xóa">
                            @csrf @method('PATCH')
                            <button name="action" value="delete" class="inline-flex min-h-7 cursor-pointer items-center border border-line-strong bg-paper px-3 py-1 font-mono text-[11px] font-bold uppercase text-danger shadow-brutal-sm hover:bg-danger hover:text-paper transition-all">
                                Xóa
                            </button>
                        </form>
                    </div>
                @endunless
            </article>
        @empty
            <div class="border-2 border-line-strong bg-surface p-12 text-center font-mono text-xs text-ink-muted shadow-brutal">
                Không có bình luận phù hợp.
            </div>
        @endforelse
    </div>

    <div>
        {{ $comments->links() }}
    </div>
</div>
@endsection
