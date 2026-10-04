@extends('layouts.dashboard', ['title' => 'Thông báo hệ thống'])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b-2 border-line-strong pb-5">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-xl font-black uppercase tracking-tight text-ink sm:text-2xl">Thông báo của bạn</h1>
                @if ($unreadCount > 0)
                    <span class="border border-line-strong bg-lime px-2.5 py-0.5 text-xs font-mono font-bold text-ink">
                        {{ $unreadCount }} chưa đọc
                    </span>
                @endif
            </div>
            <p class="mt-1 text-xs font-mono text-ink-muted">Cập nhật theo thời gian thực về bài viết, phản hồi bình luận và các hoạt động trong tòa soạn.</p>
        </div>

        @if ($unreadCount > 0)
            <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                @csrf
                <button type="submit" class="inline-flex min-h-9 items-center gap-1.5 border-2 border-line-strong bg-lime px-4 py-1.5 text-xs font-mono font-bold uppercase tracking-wider text-ink shadow-brutal-sm hover:bg-lime-hover cursor-pointer transition-all">
                    <span>Đánh dấu tất cả đã đọc</span>
                </button>
            </form>
        @endif
    </div>

    <!-- Notification List -->
    @if ($notifications->isNotEmpty())
        <div class="divide-y-2 divide-line-strong border-2 border-line-strong bg-surface shadow-brutal overflow-hidden">
            @foreach ($notifications as $notification)
                @php
                    $data = $notification->data;
                    $isUnread = is_null($notification->read_at);
                    $url = $data['url'] ?? null;
                @endphp
                <div class="group flex items-start justify-between gap-4 p-5 transition-colors {{ $isUnread ? 'bg-lime/20' : 'hover:bg-paper' }}">
                    <div class="flex items-start gap-4 min-w-0 flex-1">
                        <!-- Icon Badge -->
                        <div class="mt-0.5 size-9 shrink-0 grid place-items-center border border-line-strong {{ $isUnread ? 'bg-lime text-ink' : 'bg-paper text-ink-muted' }} font-mono font-bold text-xs shadow-brutal-sm">
                            @if (($data['type'] ?? '') === 'post_status')
                                @if (($data['action'] ?? '') === 'approved')
                                    ✓
                                @else
                                    ✕
                                @endif
                            @elseif (($data['type'] ?? '') === 'comment_reply')
                                “
                            @else
                                !
                            @endif
                        </div>

                        <!-- Notification Content -->
                        <div class="space-y-1 min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-black uppercase text-ink">
                                    {{ $data['title'] ?? 'Thông báo hệ thống' }}
                                </h3>
                                @if ($isUnread)
                                    <span class="size-2 bg-ink inline-block" title="Chưa đọc"></span>
                                @endif
                            </div>
                            <p class="text-xs text-ink-muted leading-relaxed">
                                {{ $data['message'] ?? '' }}
                            </p>
                            <span class="inline-block text-xs text-ink-muted font-mono pt-1">
                                {{ $notification->created_at->diffForHumans() }} ({{ $notification->created_at->format('d/m/Y H:i') }})
                            </span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2 shrink-0 pt-1 font-mono">
                        @if ($url)
                            <a href="{{ route('notifications.read', $notification->id) }}"
                               class="inline-flex min-h-8 items-center gap-1 border border-line-strong bg-surface px-3 py-1.5 text-xs font-bold uppercase text-ink hover:bg-paper shadow-brutal-sm">
                                <span>Xem</span>
                                <span>&rarr;</span>
                            </a>
                        @endif

                        @if ($isUnread)
                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="inline-flex min-h-8 items-center gap-1 border border-line bg-paper px-2.5 py-1 text-xs text-ink-muted hover:border-line-strong hover:text-ink cursor-pointer"
                                        title="Đánh dấu đã đọc">
                                    <span>Đã đọc</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pt-4">
            {{ $notifications->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="flex flex-col items-center justify-center border-2 border-dashed border-line-strong bg-surface py-16 text-center space-y-3 shadow-brutal">
            <div class="size-12 grid place-items-center border-2 border-line-strong bg-paper text-ink font-mono font-bold text-lg shadow-brutal-sm">
                !
            </div>
            <h3 class="text-base font-black uppercase text-ink">Chưa có thông báo nào</h3>
            <p class="max-w-sm text-xs font-mono text-ink-muted">
                Khi có phản hồi về bài viết, tương tác bình luận hoặc thông báo từ tòa soạn, các cập nhật sẽ hiển thị tại đây.
            </p>
        </div>
    @endif
</div>
@endsection
