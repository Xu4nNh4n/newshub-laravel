@extends('layouts.dashboard', ['title' => 'Bài viết của tôi'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b-2 border-line-strong pb-5">
        <div>
            <h1 class="text-xl font-black uppercase tracking-tight text-ink sm:text-2xl">Bài viết của tôi</h1>
            <p class="text-xs font-mono text-ink-muted">Tạo bản nháp, gửi Admin duyệt và theo dõi trạng thái xuất bản.</p>
        </div>
        <a href="{{ route('author.posts.create') }}" class="inline-flex min-h-10 items-center gap-1.5 border-2 border-line-strong bg-lime px-4 py-2 text-xs font-mono font-bold uppercase tracking-wider text-ink shadow-brutal hover:bg-lime-hover hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-brutal-sm transition-all">
            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="size-3.5"><path d="M12 5v14M5 12h14"/></svg>
            <span>Viết bài mới</span>
        </a>
    </div>

    <!-- Filter Bar -->
    <form method="GET" class="flex flex-col gap-3 border-2 border-line-strong bg-surface p-4 sm:flex-row sm:items-center shadow-brutal-sm">
        <div class="w-full sm:w-64">
            <label for="status" class="sr-only">Trạng thái bài viết</label>
            <select id="status" name="status" class="w-full border border-line bg-paper px-3 py-2 text-xs font-mono text-ink focus:border-line-strong focus:outline-hidden focus:bg-surface focus:shadow-brutal-sm">
                <option value="">Mọi trạng thái</option>
                @foreach($statuses as $status)
                    <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>{{ $status->value }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="inline-flex min-h-9 cursor-pointer items-center justify-center gap-1.5 border-2 border-line-strong bg-lime px-4 py-1.5 text-xs font-mono font-bold uppercase tracking-wider text-ink shadow-brutal-sm hover:bg-lime-hover active:translate-x-0.5 active:translate-y-0.5 transition-all">
            <span>Lọc</span>
        </button>
    </form>

    <!-- Table -->
    <div class="overflow-x-auto border-2 border-line-strong bg-surface shadow-brutal">
        <table class="w-full text-left text-xs">
            <thead class="border-b-2 border-line-strong bg-paper text-ink font-mono font-bold uppercase tracking-wider">
                <tr>
                    <th scope="col" class="px-5 py-3.5">Bài viết</th>
                    <th scope="col" class="px-5 py-3.5">Trạng thái</th>
                    <th scope="col" class="px-5 py-3.5 text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y border-line">
                @forelse($posts as $post)
                    @php
                        $pendingRequest = $post->requests->firstWhere('status', \App\Enums\PostRequestStatus::Pending);
                        $latestRejectedRequest = $post->requests->where('status', \App\Enums\PostRequestStatus::Rejected)->sortByDesc('id')->first();
                    @endphp
                    <tr class="transition-colors hover:bg-paper">
                        <td class="px-5 py-4">
                            <p class="font-bold text-ink sm:text-sm">{{ $post->title }}</p>
                            <p class="mt-0.5 text-xs font-mono text-ink-muted">{{ $post->category->name }}</p>

                            @if($post->rejection_reason)
                                <div class="mt-2 border-2 border-line-strong bg-paper p-3 text-xs font-mono text-danger">
                                    <strong class="uppercase font-bold">Lý do từ chối duyệt:</strong> {{ $post->rejection_reason }}
                                </div>
                            @endif

                            @if($pendingRequest)
                                <div class="mt-2 flex items-start justify-between gap-3 border-2 border-line-strong bg-lime p-3 text-xs font-mono text-ink shadow-brutal-sm">
                                    <div class="flex items-start gap-2 min-w-0 flex-1">
                                        <span class="mt-0.5 font-bold">!</span>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-wrap items-center gap-1.5 font-bold uppercase tracking-wider">
                                                <span>Đang yêu cầu: {{ $pendingRequest->type === \App\Enums\PostRequestType::Removal ? 'Gỡ bài viết' : 'Đính chính / Sửa lỗi' }}</span>
                                                <span class="border border-line-strong bg-surface px-1.5 py-0.2 text-[10px]">
                                                    {{ $pendingRequest->priority === \App\Enums\PostRequestPriority::Urgent ? 'Khẩn cấp' : 'Bình thường' }}
                                                </span>
                                                <span class="text-ink-muted font-normal">({{ $pendingRequest->created_at->format('d/m/Y H:i') }})</span>
                                            </div>
                                            <p class="mt-1 text-xs truncate">
                                                Lý do: {{ $pendingRequest->reason }}
                                            </p>
                                        </div>
                                    </div>
                                    <form method="POST" action="{{ route('author.posts.requests.destroy', [$post, $pendingRequest]) }}"
                                          data-confirm="Bạn có chắc chắn muốn hủy yêu cầu {{ $pendingRequest->type === \App\Enums\PostRequestType::Removal ? 'gỡ bài' : 'đính chính' }} này? Bài viết sẽ trở lại trạng thái bình thường."
                                          data-confirm-title="Hủy yêu cầu bài viết"
                                          data-confirm-subtext="{{ $post->title }}"
                                          data-confirm-type="danger"
                                          data-confirm-btn="Xác nhận hủy yêu cầu"
                                          class="shrink-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex cursor-pointer items-center gap-1 border border-line-strong bg-surface px-2.5 py-1 text-xs font-mono font-bold text-danger hover:bg-paper transition-all" title="Hủy bỏ yêu cầu này">
                                            <span>Hủy yêu cầu</span>
                                        </button>
                                    </form>
                                </div>
                            @elseif($latestRejectedRequest)
                                <div class="mt-2 border-2 border-line-strong bg-paper p-3 text-xs font-mono text-ink">
                                    <div class="flex items-center gap-1 font-bold text-danger uppercase">
                                        <span>Yêu cầu {{ $latestRejectedRequest->type === \App\Enums\PostRequestType::Removal ? 'gỡ bài' : 'đính chính' }} bị từ chối:</span>
                                    </div>
                                    <p class="mt-1 text-xs text-ink-muted">{{ $latestRejectedRequest->admin_notes }}</p>
                                </div>
                            @endif
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            @php
                                $statusConfig = match($post->status) {
                                    \App\Enums\PostStatus::Draft => ['label' => 'Bản nháp', 'class' => 'bg-paper text-ink border-line'],
                                    \App\Enums\PostStatus::PendingReview => ['label' => 'Chờ duyệt', 'class' => 'bg-lime text-ink border-line-strong font-bold'],
                                    \App\Enums\PostStatus::Published => ['label' => 'Đã xuất bản', 'class' => 'bg-surface text-ink border-line-strong font-bold'],
                                    \App\Enums\PostStatus::Rejected => ['label' => 'Bị từ chối', 'class' => 'bg-paper text-danger border-line-strong font-bold'],
                                    \App\Enums\PostStatus::Hidden => ['label' => 'Đã tạm ẩn', 'class' => 'bg-paper text-ink-muted border-line'],
                                    \App\Enums\PostStatus::Archived => ['label' => 'Đã lưu trữ', 'class' => 'bg-paper text-ink-muted border-line'],
                                    default => ['label' => $post->status->value, 'class' => 'bg-paper text-ink border-line']
                                };
                            @endphp
                            <div class="flex flex-col gap-1 items-start font-mono text-xs">
                                <span class="border px-2.5 py-0.5 uppercase tracking-wider {{ $statusConfig['class'] }}">
                                    {{ $statusConfig['label'] }}
                                </span>
                                @if($pendingRequest)
                                    <span class="border border-line-strong bg-lime px-2 py-0.5 text-[10px] font-bold text-ink uppercase">
                                        Có yêu cầu chờ
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-right font-mono">
                            <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                <a href="{{ route('posts.preview', $post) }}" class="inline-flex min-h-7 cursor-pointer items-center gap-1 border border-line-strong bg-surface px-2.5 py-1 text-xs font-bold uppercase text-ink hover:bg-paper">
                                    Xem trước
                                </a>
                                @can('update', $post)
                                    <a href="{{ route('author.posts.edit', $post) }}" class="inline-flex min-h-7 cursor-pointer items-center gap-1 border border-line-strong bg-surface px-2.5 py-1 text-xs font-bold uppercase text-ink hover:bg-paper">
                                        Sửa
                                    </a>
                                @endcan
                                @can('publish', $post)
                                    <form method="POST" action="{{ route('author.posts.publish', $post) }}" class="inline"
                                          data-confirm="Xác nhận xuất bản trực tiếp bài viết này lên hệ thống tin tức?"
                                          data-confirm-title="Xuất bản bài viết"
                                          data-confirm-subtext="{{ $post->title }}"
                                          data-confirm-type="success"
                                          data-confirm-btn="Xuất bản ngay">
                                        @csrf
                                        <button type="submit" class="inline-flex min-h-7 cursor-pointer items-center gap-1 border-2 border-line-strong bg-lime px-2.5 py-1 text-xs font-bold uppercase text-ink hover:bg-lime-hover shadow-brutal-sm">
                                            Xuất bản
                                        </button>
                                    </form>
                                @endcan
                                @can('submit', $post)
                                    <form method="POST" action="{{ route('author.posts.submit', $post) }}" class="inline"
                                          data-confirm="Xác nhận gửi bài viết này tới Ban biên tập để kiểm duyệt?"
                                          data-confirm-title="Gửi duyệt bài viết"
                                          data-confirm-subtext="{{ $post->title }}"
                                          data-confirm-type="success"
                                          data-confirm-btn="Gửi duyệt ngay">
                                        @csrf
                                        <button type="submit" class="inline-flex min-h-7 cursor-pointer items-center gap-1 border-2 border-line-strong bg-lime px-2.5 py-1 text-xs font-bold uppercase text-ink hover:bg-lime-hover shadow-brutal-sm">
                                            Gửi duyệt
                                        </button>
                                    </form>
                                @endcan

                                @if ($post->status === \App\Enums\PostStatus::PendingReview)
                                    @if (\Illuminate\Support\Facades\Route::has('author.posts.withdraw'))
                                        <form method="POST" action="{{ route('author.posts.withdraw', $post) }}" class="inline"
                                              data-confirm="Bạn có muốn rút bài viết này về bản nháp để chỉnh sửa lại? Bài viết sẽ tạm thời không còn hiển thị chờ duyệt."
                                              data-confirm-title="Rút bài về bản nháp"
                                              data-confirm-subtext="{{ $post->title }}"
                                              data-confirm-type="warning"
                                              data-confirm-btn="Rút về nháp">
                                            @csrf
                                            <button type="submit" class="inline-flex min-h-7 cursor-pointer items-center gap-1 border border-line-strong bg-paper px-2.5 py-1 text-xs font-bold uppercase text-ink hover:bg-surface">
                                                Rút về nháp
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" onclick="alert('Tính năng rút bài về nháp đang được chuẩn bị bởi Ban biên tập. Nếu cần chỉnh sửa gấp, vui lòng liên hệ Admin.')" class="inline-flex min-h-7 cursor-pointer items-center gap-1 border border-line-strong bg-paper px-2.5 py-1 text-xs font-bold uppercase text-ink hover:bg-surface" title="Rút bài về bản nháp để sửa">
                                            Rút về nháp
                                        </button>
                                    @endif
                                @endif

                                @if ($post->status === \App\Enums\PostStatus::Published)
                                    @if ($pendingRequest)
                                        <button type="button"
                                                data-btn-open-request-detail
                                                data-post-title="{{ $post->title }}"
                                                data-request-type="{{ $pendingRequest->type === \App\Enums\PostRequestType::Removal ? 'Gỡ bài viết (Take-down)' : 'Đính chính / Sửa lỗi (Correction)' }}"
                                                data-request-priority="{{ $pendingRequest->priority === \App\Enums\PostRequestPriority::Urgent ? 'Khẩn cấp' : 'Bình thường' }}"
                                                data-request-is-urgent="{{ $pendingRequest->priority === \App\Enums\PostRequestPriority::Urgent ? '1' : '0' }}"
                                                data-request-reason="{{ $pendingRequest->reason }}"
                                                data-request-notes="{{ $pendingRequest->notes }}"
                                                data-request-time="{{ $pendingRequest->created_at->format('d/m/Y H:i') }}"
                                                data-cancel-action="{{ route('author.posts.requests.destroy', [$post, $pendingRequest]) }}"
                                                class="inline-flex min-h-7 cursor-pointer items-center gap-1 border border-line-strong bg-lime px-2.5 py-1 text-xs font-bold uppercase text-ink hover:bg-lime-hover shadow-brutal-sm"
                                                title="Bấm để xem chi tiết yêu cầu hoặc hủy yêu cầu">
                                            <span>Đã gửi yêu cầu</span>
                                        </button>
                                    @else
                                        <button type="button"
                                                data-btn-open-request
                                                data-post-id="{{ $post->id }}"
                                                data-post-title="{{ $post->title }}"
                                                data-request-action="{{ route('author.posts.requests.store', $post) }}"
                                                class="inline-flex min-h-7 cursor-pointer items-center gap-1 border border-line-strong bg-paper px-2.5 py-1 text-xs font-bold uppercase text-ink hover:bg-surface">
                                            <span>Yêu cầu gỡ / sửa</span>
                                        </button>
                                    @endif
                                @endif

                                @can('delete', $post)
                                    <button type="button"
                                            data-btn-open-delete
                                            data-post-id="{{ $post->id }}"
                                            data-post-title="{{ $post->title }}"
                                            data-delete-action="{{ route('author.posts.destroy', $post) }}"
                                            class="inline-flex min-h-7 cursor-pointer items-center gap-1 border border-line-strong bg-paper px-2.5 py-1 text-xs font-bold uppercase text-danger hover:bg-surface">
                                        <span>Xóa</span>
                                    </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="p-8 text-center font-mono text-xs text-ink-muted">Chưa có bài viết.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $posts->links() }}
    </div>
</div>

<!-- Modal xác nhận xóa bài viết (Chỉ áp dụng cho Draft / Rejected) -->
<div id="delete-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-ink/60 p-4 sm:p-6" aria-modal="true" role="dialog">
    <div class="flex min-h-full items-center justify-center">
        <div class="relative w-full max-w-md border-2 border-line-strong bg-surface p-6 shadow-brutal space-y-4">
            <div class="size-12 grid place-items-center border-2 border-line-strong bg-danger text-paper font-mono font-bold text-xl mx-auto shadow-brutal-sm">
                !
            </div>

            <div class="text-center space-y-2">
                <h3 class="text-base font-black uppercase text-ink">Xác nhận xóa bài viết</h3>
                <p class="text-xs text-ink-muted">
                    Bạn có chắc chắn muốn xóa bài viết này không?
                </p>
                <p id="delete-modal-post-title" class="text-xs font-bold font-mono text-danger line-clamp-2 border border-line bg-paper p-3 text-left">
                    --
                </p>
                <p class="text-[11px] font-mono text-ink-muted">
                    Hành động này sẽ xóa dữ liệu và ảnh bài viết khỏi danh sách bản nháp của bạn.
                </p>
            </div>

            <div class="pt-3 border-t border-line flex items-center justify-end gap-2.5 font-mono">
                <button type="button" id="btn-cancel-delete" class="border border-line-strong bg-paper px-4 py-2 text-xs font-bold uppercase text-ink hover:bg-surface cursor-pointer">
                    Hủy bỏ
                </button>

                <form id="delete-modal-form" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="border-2 border-line-strong bg-danger px-4 py-2 text-xs font-bold uppercase text-paper shadow-brutal-sm hover:opacity-90 cursor-pointer">
                        Xác nhận xóa
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form Yêu cầu gỡ bài / Đính chính thông tin (Áp dụng cho Published) -->
<div id="request-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-ink/60 p-4 sm:p-6" aria-modal="true" role="dialog">
    <div class="flex min-h-full items-center justify-center">
        <div class="relative w-full max-w-lg border-2 border-line-strong bg-surface shadow-brutal overflow-hidden">
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b-2 border-line-strong bg-paper px-5 py-3.5">
                <div>
                    <h3 class="text-sm font-black uppercase text-ink">Yêu cầu Gỡ bài / Đính chính</h3>
                    <p class="text-[11px] font-mono text-ink-muted">Gửi trực tiếp đến Ban biên tập để xử lý bài viết đã xuất bản.</p>
                </div>
                <button type="button" id="btn-close-request" class="size-8 grid place-items-center border border-line-strong bg-surface text-ink font-mono font-bold text-sm hover:bg-paper cursor-pointer" title="Đóng (ESC)">
                    ✕
                </button>
            </div>

            <!-- Modal Form Body -->
            <form id="request-modal-form" method="POST" action="" class="p-6 space-y-4">
                @csrf
                <!-- Post info preview -->
                <div class="border border-line bg-paper p-3 font-mono">
                    <span class="text-[10px] uppercase font-bold text-ink-muted tracking-wider">Bài viết liên quan</span>
                    <p id="request-modal-post-title" class="mt-0.5 text-xs font-bold text-ink line-clamp-1">--</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1.5">
                        <label for="request_type" class="block text-xs font-mono font-bold uppercase tracking-wider text-ink">Loại yêu cầu <span class="text-danger">*</span></label>
                        <select id="request_type" name="type" required class="w-full border border-line bg-paper px-3 py-2 text-xs font-mono text-ink focus:border-line-strong focus:outline-hidden focus:bg-surface focus:shadow-brutal-sm">
                            <option value="removal">Yêu cầu Gỡ bài viết (Take-down)</option>
                            <option value="correction">Yêu cầu Đính chính / Sửa lỗi</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label for="request_priority" class="block text-xs font-mono font-bold uppercase tracking-wider text-ink">Mức độ ưu tiên</label>
                        <select id="request_priority" name="priority" class="w-full border border-line bg-paper px-3 py-2 text-xs font-mono text-ink focus:border-line-strong focus:outline-hidden focus:bg-surface focus:shadow-brutal-sm">
                            <option value="normal">Bình thường</option>
                            <option value="urgent">Khẩn cấp (Sai số liệu/Bản quyền)</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label for="request_reason" class="block text-xs font-mono font-bold uppercase tracking-wider text-ink">Lý do chính <span class="text-danger">*</span></label>
                    <input id="request_reason" name="reason" required maxlength="255" placeholder="Ví dụ: Cần cập nhật số liệu mới, bài viết có nội dung chưa chính xác..." class="w-full border border-line bg-paper px-3 py-2 text-xs text-ink placeholder:text-ink-light focus:border-line-strong focus:outline-hidden focus:bg-surface focus:shadow-brutal-sm">
                </div>

                <div class="space-y-1.5">
                    <label for="request_notes" class="block text-xs font-mono font-bold uppercase tracking-wider text-ink">Chi tiết đề xuất / Đoạn văn cần sửa đổi</label>
                    <textarea id="request_notes" name="notes" rows="4" maxlength="2000" placeholder="Mô tả cụ thể vị trí sai sót, lý do cần gỡ bài hoặc nội dung đề xuất thay thế..." class="w-full border border-line bg-paper px-3 py-2 text-xs text-ink placeholder:text-ink-light focus:border-line-strong focus:outline-hidden focus:bg-surface focus:shadow-brutal-sm leading-relaxed"></textarea>
                </div>

                <!-- Footer buttons -->
                <div class="pt-3 border-t border-line flex items-center justify-end gap-2.5 font-mono">
                    <button type="button" id="btn-cancel-request" class="border border-line-strong bg-paper px-4 py-2 text-xs font-bold uppercase text-ink hover:bg-surface cursor-pointer">
                        Đóng
                    </button>
                    <button type="submit" class="border-2 border-line-strong bg-lime px-4 py-2 text-xs font-bold uppercase text-ink shadow-brutal-sm hover:bg-lime-hover cursor-pointer">
                        Gửi tới Ban biên tập
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Chi tiết Yêu cầu Đang chờ xử lý -->
<div id="request-details-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-ink/60 p-4 sm:p-6" aria-modal="true" role="dialog">
    <div class="flex min-h-full items-center justify-center">
        <div class="relative w-full max-w-lg border-2 border-line-strong bg-surface shadow-brutal overflow-hidden">
            <div class="flex items-center justify-between border-b-2 border-line-strong bg-paper px-5 py-3.5">
                <div>
                    <h3 class="text-sm font-black uppercase text-ink">Chi tiết yêu cầu đang chờ duyệt</h3>
                    <p class="text-[11px] font-mono text-ink-muted">Yêu cầu đang được Ban biên tập xem xét xử lý.</p>
                </div>
                <button type="button" id="btn-close-request-details" class="size-8 grid place-items-center border border-line-strong bg-surface text-ink font-mono font-bold text-sm hover:bg-paper cursor-pointer" title="Đóng (ESC)">
                    ✕
                </button>
            </div>

            <div class="p-6 space-y-4 text-xs font-mono">
                <div class="border border-line bg-paper p-3">
                    <span class="text-[10px] uppercase font-bold text-ink-muted tracking-wider">Bài viết liên quan</span>
                    <p id="rd-post-title" class="mt-0.5 text-xs font-bold text-ink"></p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="border border-line bg-paper p-2.5">
                        <span class="text-[10px] uppercase text-ink-muted">Loại yêu cầu:</span>
                        <p id="rd-type" class="mt-0.5 font-bold text-ink"></p>
                    </div>
                    <div class="border border-line bg-paper p-2.5">
                        <span class="text-[10px] uppercase text-ink-muted">Mức độ ưu tiên:</span>
                        <p id="rd-priority" class="mt-0.5 font-bold text-ink"></p>
                    </div>
                </div>

                <div class="border border-line bg-paper p-3">
                    <span class="text-[10px] uppercase text-ink-muted">Lý do gửi:</span>
                    <p id="rd-reason" class="mt-1 text-ink"></p>
                </div>

                <div id="rd-notes-wrap" class="border border-line bg-paper p-3">
                    <span class="text-[10px] uppercase text-ink-muted">Ghi chú đề xuất:</span>
                    <p id="rd-notes" class="mt-1 whitespace-pre-line text-ink"></p>
                </div>

                <div class="text-[11px] text-ink-muted" id="rd-time"></div>
            </div>

            <div class="flex items-center justify-between border-t-2 border-line-strong bg-paper px-5 py-3.5 font-mono">
                <form id="rd-cancel-form" method="POST" action=""
                      data-confirm="Bạn có chắc chắn muốn hủy yêu cầu này? Bài viết sẽ trở lại trạng thái bình thường."
                      data-confirm-title="Hủy yêu cầu bài viết"
                      data-confirm-type="danger"
                      data-confirm-btn="Xác nhận hủy yêu cầu">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="border border-line-strong bg-surface px-3.5 py-1.5 text-xs font-bold uppercase text-danger hover:bg-paper cursor-pointer">
                        Hủy yêu cầu này
                    </button>
                </form>

                <button type="button" id="btn-close-request-details-footer" class="border border-line-strong bg-surface px-4 py-1.5 text-xs font-bold uppercase text-ink hover:bg-paper cursor-pointer">
                    Đóng
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- DELETE MODAL HANDLER ---
        const deleteModal = document.getElementById('delete-modal');
        const deleteForm = document.getElementById('delete-modal-form');
        const deleteTitle = document.getElementById('delete-modal-post-title');
        const btnCancelDelete = document.getElementById('btn-cancel-delete');

        const openDeleteModal = (title, actionUrl) => {
            if (!deleteModal) return;
            deleteTitle.textContent = title;
            deleteForm.action = actionUrl;
            deleteModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        };

        const closeDeleteModal = () => {
            if (!deleteModal) return;
            deleteModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        };

        document.querySelectorAll('[data-btn-open-delete]').forEach(btn => {
            btn.addEventListener('click', () => {
                const title = btn.getAttribute('data-post-title') || 'Bài viết';
                const action = btn.getAttribute('data-delete-action');
                if (action) openDeleteModal(title, action);
            });
        });

        btnCancelDelete?.addEventListener('click', closeDeleteModal);
        deleteModal?.addEventListener('click', (e) => {
            if (e.target === deleteModal) closeDeleteModal();
        });

        // --- REQUEST CREATE MODAL HANDLER ---
        const requestModal = document.getElementById('request-modal');
        const requestForm = document.getElementById('request-modal-form');
        const requestTitle = document.getElementById('request-modal-post-title');
        const btnCloseRequest = document.getElementById('btn-close-request');
        const btnCancelRequest = document.getElementById('btn-cancel-request');

        const openRequestModal = (title, actionUrl) => {
            if (!requestModal) return;
            requestTitle.textContent = title;
            requestForm.action = actionUrl || '#';
            requestModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        };

        const closeRequestModal = () => {
            if (!requestModal) return;
            requestModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        };

        document.querySelectorAll('[data-btn-open-request]').forEach(btn => {
            btn.addEventListener('click', () => {
                const title = btn.getAttribute('data-post-title') || 'Bài viết';
                const action = btn.getAttribute('data-request-action');
                openRequestModal(title, action);
            });
        });

        btnCloseRequest?.addEventListener('click', closeRequestModal);
        btnCancelRequest?.addEventListener('click', closeRequestModal);
        requestModal?.addEventListener('click', (e) => {
            if (e.target === requestModal) closeRequestModal();
        });

        // --- REQUEST DETAILS MODAL HANDLER ---
        const requestDetailsModal = document.getElementById('request-details-modal');
        const rdTitle = document.getElementById('rd-post-title');
        const rdType = document.getElementById('rd-type');
        const rdPriority = document.getElementById('rd-priority');
        const rdReason = document.getElementById('rd-reason');
        const rdNotesWrap = document.getElementById('rd-notes-wrap');
        const rdNotes = document.getElementById('rd-notes');
        const rdTime = document.getElementById('rd-time');
        const rdCancelForm = document.getElementById('rd-cancel-form');
        const btnCloseRd = document.getElementById('btn-close-request-details');
        const btnCloseRdFooter = document.getElementById('btn-close-request-details-footer');

        const openRequestDetailsModal = (data) => {
            if (!requestDetailsModal) return;
            rdTitle.textContent = data.title;
            rdType.textContent = data.type;
            rdPriority.textContent = data.priority;
            rdPriority.className = 'mt-0.5 font-bold ' + (data.isUrgent ? 'text-danger' : 'text-ink');
            rdReason.textContent = data.reason;
            if (data.notes && data.notes.trim()) {
                rdNotes.textContent = data.notes;
                rdNotesWrap.classList.remove('hidden');
            } else {
                rdNotesWrap.classList.add('hidden');
            }
            rdTime.textContent = 'Thời gian gửi: ' + data.time;
            rdCancelForm.action = data.cancelAction;
            rdCancelForm.setAttribute('data-confirm-subtext', data.title);

            requestDetailsModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        };

        const closeRequestDetailsModal = () => {
            if (!requestDetailsModal) return;
            requestDetailsModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        };

        document.querySelectorAll('[data-btn-open-request-detail]').forEach(btn => {
            btn.addEventListener('click', () => {
                openRequestDetailsModal({
                    title: btn.getAttribute('data-post-title') || '',
                    type: btn.getAttribute('data-request-type') || '',
                    priority: btn.getAttribute('data-request-priority') || '',
                    isUrgent: btn.getAttribute('data-request-is-urgent') === '1',
                    reason: btn.getAttribute('data-request-reason') || '',
                    notes: btn.getAttribute('data-request-notes') || '',
                    time: btn.getAttribute('data-request-time') || '',
                    cancelAction: btn.getAttribute('data-cancel-action') || '#'
                });
            });
        });

        btnCloseRd?.addEventListener('click', closeRequestDetailsModal);
        btnCloseRdFooter?.addEventListener('click', closeRequestDetailsModal);
        requestDetailsModal?.addEventListener('click', (e) => {
            if (e.target === requestDetailsModal) closeRequestDetailsModal();
        });

        // ESC Key to close any modal
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                if (deleteModal && !deleteModal.classList.contains('hidden')) closeDeleteModal();
                if (requestModal && !requestModal.classList.contains('hidden')) closeRequestModal();
                if (requestDetailsModal && !requestDetailsModal.classList.contains('hidden')) closeRequestDetailsModal();
            }
        });
    });
</script>
@endsection
