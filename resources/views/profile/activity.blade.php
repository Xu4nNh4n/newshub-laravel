@extends('layouts.dashboard', ['title' => 'Lịch sử hoạt động & thay đổi'])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b-2 border-line-strong pb-5">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-xl font-black uppercase tracking-tight text-ink sm:text-2xl">Lịch sử hoạt động</h1>
                <span class="border border-line-strong bg-lime px-2.5 py-0.5 text-xs font-mono font-bold text-ink">
                    {{ $stats['total'] }} hoạt động
                </span>
            </div>
            <p class="mt-1 text-xs font-mono text-ink-muted">Theo dõi nhật ký các thay đổi tài khoản, tương tác bình luận và vòng đời bài viết của bạn.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('profile.edit') }}" class="inline-flex min-h-9 items-center gap-1.5 border border-line-strong bg-surface px-3.5 py-1.5 text-xs font-mono font-bold uppercase tracking-wider text-ink shadow-brutal-sm hover:bg-paper transition-colors">
                <svg aria-hidden="true" class="size-3.5 text-ink" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>
                </svg>
                <span>Hồ sơ cá nhân</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 font-mono">
        <div class="border-2 border-line-strong bg-surface p-4 shadow-brutal-sm">
            <p class="text-xs uppercase text-ink-muted font-bold">Tổng ghi nhận</p>
            <p class="mt-2 text-2xl sm:text-3xl font-black text-ink tabular-nums">{{ number_format($stats['total']) }}</p>
        </div>
        @if ($isAuthor)
            <div class="border-2 border-line-strong bg-surface p-4 shadow-brutal-sm">
                <p class="text-xs uppercase text-ink font-bold">Bài viết & Biên tập</p>
                <p class="mt-2 text-2xl sm:text-3xl font-black text-ink tabular-nums">{{ number_format($stats['posts_count']) }}</p>
            </div>
        @endif
        <div class="border-2 border-line-strong bg-surface p-4 shadow-brutal-sm">
            <p class="text-xs uppercase text-ink-muted font-bold">Bảo mật & Tài khoản</p>
            <p class="mt-2 text-2xl sm:text-3xl font-black text-ink tabular-nums">{{ number_format($stats['account_count']) }}</p>
        </div>
        <div class="border-2 border-line-strong bg-surface p-4 shadow-brutal-sm">
            <p class="text-xs uppercase text-ink-muted font-bold">Tương tác & Đơn từ</p>
            <p class="mt-2 text-2xl sm:text-3xl font-black text-ink tabular-nums">{{ number_format($stats['interactions_count']) }}</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="flex flex-col gap-3 border-2 border-line-strong bg-surface p-4 sm:flex-row sm:items-center sm:justify-between text-xs font-mono shadow-brutal-sm">
        <!-- Type Filter -->
        <div class="flex flex-wrap items-center gap-1.5">
            <span class="text-ink-muted font-bold uppercase tracking-wider mr-1">Chủ đề:</span>
            <a href="{{ route('profile.activity', ['type' => 'all', 'timeframe' => $timeframe]) }}"
               @class([
                   'px-2.5 py-1 border transition-colors',
                   'border-line-strong bg-lime text-ink font-bold shadow-brutal-sm' => $type === 'all',
                   'border-line bg-paper text-ink-muted hover:border-line-strong hover:text-ink' => $type !== 'all',
               ])>
                Tất cả
            </a>
            @if ($isAuthor)
                <a href="{{ route('profile.activity', ['type' => 'posts', 'timeframe' => $timeframe]) }}"
                   @class([
                       'px-2.5 py-1 border transition-colors',
                       'border-line-strong bg-lime text-ink font-bold shadow-brutal-sm' => $type === 'posts',
                       'border-line bg-paper text-ink-muted hover:border-line-strong hover:text-ink' => $type !== 'posts',
                   ])>
                    Bài viết
                </a>
            @endif
            <a href="{{ route('profile.activity', ['type' => 'account', 'timeframe' => $timeframe]) }}"
               @class([
                   'px-2.5 py-1 border transition-colors',
                   'border-line-strong bg-lime text-ink font-bold shadow-brutal-sm' => $type === 'account',
                   'border-line bg-paper text-ink-muted hover:border-line-strong hover:text-ink' => $type !== 'account',
               ])>
                Tài khoản & Bảo mật
            </a>
            <a href="{{ route('profile.activity', ['type' => 'interactions', 'timeframe' => $timeframe]) }}"
               @class([
                   'px-2.5 py-1 border transition-colors',
                   'border-line-strong bg-lime text-ink font-bold shadow-brutal-sm' => $type === 'interactions',
                   'border-line bg-paper text-ink-muted hover:border-line-strong hover:text-ink' => $type !== 'interactions',
               ])>
                Tương tác cộng đồng
            </a>
        </div>

        <!-- Timeframe Filter -->
        <div class="flex items-center gap-1.5 shrink-0 border-t border-line pt-2 sm:border-0 sm:pt-0">
            <span class="text-ink-muted font-bold uppercase tracking-wider mr-1">Thời gian:</span>
            <a href="{{ route('profile.activity', ['type' => $type, 'timeframe' => 'all']) }}"
               @class([
                   'px-2 py-0.5 border text-xs',
                   'border-line-strong bg-ink text-paper font-bold' => $timeframe === 'all',
                   'border-line bg-paper text-ink-muted hover:border-line-strong hover:text-ink' => $timeframe !== 'all',
               ])>
                Toàn bộ
            </a>
            <a href="{{ route('profile.activity', ['type' => $type, 'timeframe' => 'today']) }}"
               @class([
                   'px-2 py-0.5 border text-xs',
                   'border-line-strong bg-ink text-paper font-bold' => $timeframe === 'today',
                   'border-line bg-paper text-ink-muted hover:border-line-strong hover:text-ink' => $timeframe !== 'today',
               ])>
                Hôm nay
            </a>
            <a href="{{ route('profile.activity', ['type' => $type, 'timeframe' => 'week']) }}"
               @class([
                   'px-2 py-0.5 border text-xs',
                   'border-line-strong bg-ink text-paper font-bold' => $timeframe === 'week',
                   'border-line bg-paper text-ink-muted hover:border-line-strong hover:text-ink' => $timeframe !== 'week',
               ])>
                7 ngày
            </a>
            <a href="{{ route('profile.activity', ['type' => $type, 'timeframe' => 'month']) }}"
               @class([
                   'px-2 py-0.5 border text-xs',
                   'border-line-strong bg-ink text-paper font-bold' => $timeframe === 'month',
                   'border-line bg-paper text-ink-muted hover:border-line-strong hover:text-ink' => $timeframe !== 'month',
               ])>
                30 ngày
            </a>
        </div>
    </div>

    <!-- Timeline List -->
    @if ($logs->isNotEmpty())
        <div class="relative pl-6 space-y-6 before:absolute before:bottom-0 before:left-2.5 before:top-2 before:w-0.5 before:bg-line-strong">
            @foreach ($logs as $log)
                @php
                    $action = $log->action;
                    $badgeClass = match (true) {
                        str_starts_with($action, 'post.approved'), str_starts_with($action, 'post.published') => 'bg-lime text-ink border-line-strong',
                        str_starts_with($action, 'post.rejected') => 'bg-danger text-paper border-line-strong',
                        str_starts_with($action, 'post.submitted'), str_starts_with($action, 'post.withdrawn') => 'bg-paper text-ink border-line-strong',
                        str_starts_with($action, 'post_request.') => 'bg-pink text-ink border-line-strong',
                        str_starts_with($action, 'comment.') => 'bg-paper text-ink border-line-strong',
                        default => 'bg-paper text-ink border-line',
                    };

                    $actionLabel = match ($action) {
                        'post.created' => 'Tạo bài viết mới',
                        'post.updated' => 'Sửa nội dung bài viết',
                        'post.submitted' => 'Gửi duyệt bài viết',
                        'post.withdrawn' => 'Rút bài về bản nháp',
                        'post.approved' => 'Bài viết được phê duyệt',
                        'post.published' => 'Xuất bản bài viết',
                        'post.rejected' => 'Bài viết bị từ chối',
                        'post_request.submitted' => 'Gửi yêu cầu bài viết',
                        'post_request.approved' => 'Yêu cầu được chấp thuận',
                        'post_request.rejected' => 'Yêu cầu bị từ chối',
                        'post_request.cancelled' => 'Hủy yêu cầu bài viết',
                        'profile.updated' => 'Cập nhật hồ sơ',
                        'password.updated' => 'Đổi mật khẩu tài khoản',
                        'comment.created' => 'Đăng bình luận',
                        'comment.approved' => 'Bình luận được duyệt',
                        'comment.hidden' => 'Bình luận bị ẩn',
                        'comment.deleted' => 'Bình luận bị xóa',
                        'application.submitted' => 'Nộp đơn ứng tuyển tác giả',
                        'application.approved' => 'Đơn ứng tuyển được duyệt',
                        'application.rejected' => 'Đơn ứng tuyển bị từ chối',
                        default => $action,
                    };
                @endphp

                <div class="relative group">
                    <!-- Timeline Node Marker (Square Brutalist Marker) -->
                    <span class="absolute -left-[29px] top-2 size-3 border border-line-strong bg-lime"></span>

                    <!-- Event Card -->
                    <div class="border-2 border-line-strong bg-surface p-4 shadow-brutal-sm space-y-2">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between text-xs font-mono">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="border px-2.5 py-0.5 text-xs font-bold uppercase tracking-wider {{ $badgeClass }}">
                                    {{ $actionLabel }}
                                </span>
                                @if ($log->user_id !== auth()->id() && $log->user)
                                    <span class="text-ink-muted">
                                        thực hiện bởi <strong class="text-ink">{{ $log->user->name }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center gap-2 text-ink-muted text-xs">
                                <span>{{ $log->created_at->diffForHumans() }}</span>
                                <span>&bull;</span>
                                <time datetime="{{ $log->created_at->toIso8601String() }}" class="font-bold text-ink">
                                    {{ $log->created_at->format('d/m/Y H:i:s') }}
                                </time>
                            </div>
                        </div>

                        <!-- Description -->
                        @if ($log->description)
                            <p class="text-xs leading-relaxed text-ink border-t border-line pt-2 font-mono">
                                {{ $log->description }}
                            </p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pt-4">
            {{ $logs->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="flex flex-col items-center justify-center border-2 border-dashed border-line-strong bg-surface py-16 text-center space-y-3">
            <div class="size-12 grid place-items-center border-2 border-line-strong bg-paper text-ink font-mono font-bold text-lg shadow-brutal-sm">
                !
            </div>
            <h3 class="text-base font-black uppercase text-ink">Chưa ghi nhận hoạt động nào</h3>
            <p class="max-w-sm text-xs font-mono text-ink-muted">
                Các thao tác bảo mật tài khoản, hoạt động đăng bài và tương tác của bạn sẽ được hiển thị theo dòng thời gian tại đây.
            </p>
        </div>
    @endif
</div>
@endsection
