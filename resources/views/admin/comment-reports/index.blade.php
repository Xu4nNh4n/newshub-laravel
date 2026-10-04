@extends('layouts.dashboard', ['title' => 'Báo cáo bình luận'])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b-2 border-line-strong pb-5">
        <div>
            <span class="font-mono text-xs font-bold uppercase tracking-wider text-ink-muted block mb-1">Kiểm duyệt tương tác</span>
            <h1 class="text-2xl font-black uppercase tracking-tight text-ink sm:text-3xl font-heading">Báo cáo bình luận</h1>
            <p class="text-xs text-ink-muted mt-1">Kiểm tra nội dung bị độc giả báo cáo và ghi nhận quyết định xử lý.</p>
        </div>
        <form method="GET" class="shrink-0">
            <label for="status" class="sr-only">Lọc theo trạng thái báo cáo</label>
            <select id="status" name="status" onchange="this.form.submit()" class="border-2 border-line-strong bg-paper px-3.5 py-2 font-mono text-xs font-bold uppercase text-ink outline-none focus:border-ink">
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}" @selected($selectedStatus === $status)>{{ $status->value }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Reports list -->
    <div class="space-y-4">
        @forelse ($reports as $report)
            <article class="border-2 border-line-strong bg-surface p-5 shadow-brutal transition-all">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="border border-line-strong bg-paper px-2 py-0.5 font-mono text-[11px] font-bold uppercase text-danger">
                                {{ $report->reason->label() }}
                            </span>
                            <span class="font-mono text-xs text-ink-muted">
                                Người báo cáo: <strong class="text-ink font-bold">{{ $report->reporter->name }}</strong> ({{ $report->reporter->email }})
                            </span>
                        </div>
                        <p class="mt-1 font-mono text-[11px] text-ink-muted">
                            Thời gian: {{ $report->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    <span class="border border-line-strong bg-paper px-2.5 py-0.5 font-mono text-[11px] font-bold uppercase text-ink">
                        {{ $report->status->value }}
                    </span>
                </div>

                @if ($report->description)
                    <div class="mt-3 bg-paper border border-line p-3 text-xs text-ink">
                        <strong class="font-mono text-[11px] font-bold text-ink uppercase block mb-1">Ghi chú từ người báo cáo:</strong>
                        {{ $report->description }}
                    </div>
                @endif

                <div class="mt-3 border-l-3 border-ink bg-paper p-3">
                    @if ($report->comment)
                        <p class="font-mono text-[11px] font-bold text-ink-muted uppercase">Tác giả bình luận: {{ $report->comment->user->name }}</p>
                        <p class="mt-1 text-xs text-ink whitespace-pre-line">{{ $report->comment->trashed() ? 'Bình luận đã bị xóa.' : $report->comment->content }}</p>
                    @else
                        <p class="italic font-mono text-xs text-ink-muted">Bình luận không còn tồn tại.</p>
                    @endif
                </div>

                @if ($report->status === \App\Enums\CommentReportStatus::Pending)
                    <form method="POST" action="{{ route('admin.comment-reports.update', $report) }}" class="mt-4 flex flex-wrap items-center gap-2 pt-3 border-t border-line">
                        @csrf
                        @method('PUT')
                        <button type="submit" name="action" value="{{ \App\Enums\CommentReportAction::Dismiss->value }}" class="inline-flex min-h-8 cursor-pointer items-center justify-center border-2 border-line bg-surface px-4 py-1.5 font-mono text-xs font-bold uppercase text-ink hover:border-line-strong transition-all">
                            Bỏ qua
                        </button>
                        <button type="submit" name="action" value="{{ \App\Enums\CommentReportAction::Hide->value }}" class="inline-flex min-h-8 cursor-pointer items-center justify-center border-2 border-line-strong bg-paper px-4 py-1.5 font-mono text-xs font-bold uppercase text-ink shadow-brutal-sm hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
                            Ẩn bình luận
                        </button>
                        <button type="submit" name="action" value="{{ \App\Enums\CommentReportAction::Delete->value }}" class="inline-flex min-h-8 cursor-pointer items-center justify-center border-2 border-line-strong bg-danger px-4 py-1.5 font-mono text-xs font-bold uppercase text-paper shadow-brutal-sm hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
                            Xóa bình luận
                        </button>
                    </form>
                @endif
            </article>
        @empty
            <div class="border-2 border-line-strong bg-surface p-12 text-center font-mono text-xs text-ink-muted shadow-brutal">
                Không có báo cáo ở trạng thái này.
            </div>
        @endforelse
    </div>

    <div>
        {{ $reports->links() }}
    </div>
</div>
@endsection
