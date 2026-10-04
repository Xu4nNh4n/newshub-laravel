@extends('layouts.dashboard', ['title' => 'Yêu cầu bài viết', 'breadcrumbs' => $breadcrumbs ?? null])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b-2 border-line-strong pb-5">
        <div>
            <span class="font-mono text-xs font-bold uppercase tracking-wider text-ink-muted block mb-1">Quy trình điều chỉnh xuất bản</span>
            <h1 class="text-2xl font-black uppercase tracking-tight text-ink sm:text-3xl font-heading">Yêu cầu Gỡ bài & Đính chính</h1>
            <p class="text-xs text-ink-muted mt-1">Xem xét và xử lý các yêu cầu thu hồi, tạm ẩn hoặc sửa đổi bài viết đã xuất bản từ Tác giả.</p>
        </div>

        <!-- Filter by Status -->
        <form method="GET" class="shrink-0">
            <label for="status" class="sr-only">Lọc theo trạng thái</label>
            <select id="status"
                    name="status"
                    onchange="this.form.submit()"
                    class="border-2 border-line-strong bg-paper px-3.5 py-2 font-mono text-xs font-bold uppercase text-ink outline-none focus:border-ink">
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}" @selected($selectedStatus === $status)>
                        {{ match ($status->value) {
                            'pending' => 'Chờ xử lý',
                            'approved' => 'Đã chấp thuận',
                            'rejected' => 'Đã từ chối',
                            'cancelled' => 'Đã hủy',
                            default => $status->value,
                        } }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Requests List -->
    <div class="space-y-5">
        @forelse ($postRequests as $requestItem)
            <article class="border-2 border-line-strong bg-surface p-6 shadow-brutal transition-all">
                <!-- Top Row: Request Type, Priority & Status -->
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <!-- Type Badge -->
                            @if ($requestItem->type === \App\Enums\PostRequestType::Removal)
                                <span class="inline-flex items-center gap-1 border border-line-strong bg-paper px-2.5 py-0.5 font-mono text-xs font-bold uppercase text-danger shadow-brutal-sm">
                                    <svg class="size-3" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Yêu cầu gỡ bài (Take-down)</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 border border-line-strong bg-paper px-2.5 py-0.5 font-mono text-xs font-bold uppercase text-ink shadow-brutal-sm">
                                    <svg class="size-3" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                    <span>Yêu cầu đính chính / Sửa lỗi</span>
                                </span>
                            @endif

                            <!-- Priority Badge -->
                            @if ($requestItem->priority === \App\Enums\PostRequestPriority::Urgent)
                                <span class="inline-flex items-center gap-1 border border-line-strong bg-danger px-2 py-0.5 font-mono text-[10px] font-bold uppercase tracking-wider text-paper">
                                    <span class="size-1.5 bg-paper"></span>
                                    <span>Khẩn cấp</span>
                                </span>
                            @else
                                <span class="border border-line bg-paper px-2 py-0.5 font-mono text-[10px] text-ink-muted uppercase">
                                    Bình thường
                                </span>
                            @endif

                            <span class="font-mono text-xs text-ink-muted">· {{ $requestItem->created_at->format('d/m/Y H:i') }}</span>
                        </div>

                        <!-- Target Post Link & Author -->
                        <div class="mt-3 flex flex-wrap items-baseline gap-2">
                            <span class="font-mono text-xs text-ink-muted uppercase">Bài viết:</span>
                            @if ($requestItem->post)
                                <a href="{{ route('posts.preview', $requestItem->post) }}" class="font-heading text-base font-black uppercase text-ink hover:text-danger transition-colors underline">
                                    {{ $requestItem->post->title }}
                                </a>
                                <span class="border border-line bg-paper px-2 py-0.5 font-mono text-[10px] uppercase text-ink-muted">
                                    Trạng thái: {{ $requestItem->post->status->value }}
                                </span>
                            @else
                                <span class="font-mono text-xs font-bold text-ink-muted italic">[Bài viết đã bị xóa khỏi hệ thống]</span>
                            @endif
                        </div>

                        <p class="mt-1 font-mono text-xs text-ink-muted">
                            Người gửi: <strong class="text-ink font-bold">{{ $requestItem->author?->name ?? 'Tác giả' }}</strong>
                            @if ($requestItem->author?->email)
                                <span>({{ $requestItem->author->email }})</span>
                            @endif
                        </p>
                    </div>

                    <!-- Status Badge -->
                    <span @class([
                        'inline-flex shrink-0 items-center gap-1 border-2 border-line-strong px-3 py-1 font-mono text-xs font-bold uppercase',
                        'bg-paper text-ink shadow-brutal-sm' => $requestItem->status === \App\Enums\PostRequestStatus::Pending,
                        'bg-lime text-ink shadow-brutal-sm' => $requestItem->status === \App\Enums\PostRequestStatus::Approved,
                        'bg-danger text-paper shadow-brutal-sm' => $requestItem->status === \App\Enums\PostRequestStatus::Rejected,
                        'bg-surface text-ink-muted shadow-brutal-sm' => $requestItem->status === \App\Enums\PostRequestStatus::Cancelled,
                    ])>
                        {{ match ($requestItem->status->value) {
                            'pending' => 'Chờ xử lý',
                            'approved' => 'Đã chấp thuận',
                            'rejected' => 'Đã từ chối',
                            'cancelled' => 'Tác giả đã hủy',
                            default => $requestItem->status->value,
                        } }}
                    </span>
                </div>

                <!-- Reason Section -->
                <div class="mt-4 border border-line bg-paper p-4">
                    <h3 class="font-mono text-xs font-bold uppercase tracking-wider text-ink">Lý do chính của tác giả:</h3>
                    <p class="mt-1 text-xs font-bold text-ink">
                        {{ $requestItem->reason }}
                    </p>

                    @if ($requestItem->notes)
                        <div class="mt-3 border-t border-line pt-3">
                            <span class="font-mono text-[11px] font-bold uppercase tracking-wider text-ink-muted">Chi tiết đề xuất / Đoạn văn cần sửa đổi:</span>
                            <p class="mt-1 whitespace-pre-line text-xs leading-relaxed text-ink">
                                {{ $requestItem->notes }}
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Resolution Information if Approved, Rejected, or Cancelled -->
                @if ($requestItem->status === \App\Enums\PostRequestStatus::Approved)
                    <div class="mt-4 flex items-center gap-2 font-mono text-xs text-ink bg-paper border border-line-strong p-3">
                        <span class="font-bold text-ink">✓ Đã chấp thuận:</span>
                        <span>
                            Bởi <strong>{{ $requestItem->handler?->name ?? 'Quản trị viên' }}</strong> vào lúc {{ $requestItem->handled_at?->format('d/m/Y H:i') }}.
                            @if ($requestItem->admin_notes)
                                <span class="block mt-0.5 text-ink-muted">Ghi chú: {{ $requestItem->admin_notes }}</span>
                            @endif
                        </span>
                    </div>
                @elseif ($requestItem->status === \App\Enums\PostRequestStatus::Rejected)
                    <div class="mt-4 border-2 border-line-strong bg-paper p-4 text-xs text-ink">
                        <p class="font-mono font-bold text-danger uppercase tracking-wider">Lý do từ chối (bởi {{ $requestItem->handler?->name ?? 'Quản trị viên' }} · {{ $requestItem->handled_at?->format('d/m/Y H:i') }}):</p>
                        <p class="mt-1 text-ink">{{ $requestItem->admin_notes }}</p>
                    </div>
                @elseif ($requestItem->status === \App\Enums\PostRequestStatus::Cancelled)
                    <div class="mt-4 border border-line bg-paper p-3 font-mono text-xs text-ink-muted">
                        <p class="font-bold text-ink">Yêu cầu này đã được tác giả chủ động hủy bỏ vào lúc {{ $requestItem->handled_at?->format('d/m/Y H:i') ?? $requestItem->updated_at->format('d/m/Y H:i') }}.</p>
                    </div>
                @endif

                <!-- Actions for Pending Requests -->
                @if ($requestItem->status === \App\Enums\PostRequestStatus::Pending)
                    <div class="mt-5 flex flex-col gap-3 border-t-2 border-line-strong pt-5 sm:flex-row sm:items-center sm:justify-between">
                        <!-- Form Approve -->
                        <form method="POST" action="{{ route('admin.post-requests.update', $requestItem) }}"
                              data-confirm="{{ $requestItem->type === \App\Enums\PostRequestType::Removal ? 'Xác nhận chấp thuận gỡ bài? Bài viết sẽ được chuyển sang trạng thái Tạm ẩn (Hidden) và không hiển thị công khai.' : 'Xác nhận đồng ý cho sửa bài? Bài viết sẽ được chuyển về Bản nháp (Draft) để tác giả cập nhật nội dung.' }}"
                              data-confirm-title="{{ $requestItem->type === \App\Enums\PostRequestType::Removal ? 'Chấp thuận Gỡ bài' : 'Đồng ý cho sửa bài' }}"
                              data-confirm-subtext="{{ $requestItem->post?->title ?? 'Bài viết' }}"
                              data-confirm-type="success"
                              data-confirm-btn="{{ $requestItem->type === \App\Enums\PostRequestType::Removal ? 'Chấp thuận Gỡ bài' : 'Đồng ý cho sửa bài' }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" class="inline-flex min-h-9 cursor-pointer items-center justify-center gap-1.5 border-2 border-line-strong bg-lime px-5 py-1.5 font-mono text-xs font-bold uppercase text-ink shadow-brutal-sm hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
                                <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                </svg>
                                <span>
                                    {{ $requestItem->type === \App\Enums\PostRequestType::Removal ? 'Chấp thuận Gỡ bài' : 'Đồng ý cho sửa bài' }}
                                </span>
                            </button>
                        </form>

                        <!-- Form Reject -->
                        <form method="POST" action="{{ route('admin.post-requests.update', $requestItem) }}" class="flex flex-1 flex-col gap-2 sm:max-w-md sm:flex-row sm:items-center"
                              data-confirm="Xác nhận từ chối yêu cầu này?"
                              data-confirm-title="Từ chối yêu cầu bài viết"
                              data-confirm-subtext="{{ $requestItem->post?->title ?? 'Bài viết' }}"
                              data-confirm-type="danger"
                              data-confirm-btn="Từ chối yêu cầu">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="rejected">
                            <div class="min-w-0 flex-1">
                                <label for="admin-notes-{{ $requestItem->id }}" class="sr-only">Lý do từ chối</label>
                                <input id="admin-notes-{{ $requestItem->id }}"
                                       name="admin_notes"
                                       required
                                       placeholder="Lý do từ chối gửi tác giả (bắt buộc)..."
                                       class="w-full border-2 border-line bg-paper px-3 py-1.5 text-xs text-ink placeholder-ink-muted outline-none focus:border-danger">
                            </div>
                            <button type="submit" class="inline-flex min-h-9 shrink-0 cursor-pointer items-center justify-center border-2 border-line-strong bg-danger px-4 py-1.5 font-mono text-xs font-bold uppercase text-paper shadow-brutal-sm hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
                                Từ chối
                            </button>
                        </form>
                    </div>
                @endif
            </article>
        @empty
            <div class="border-2 border-line-strong bg-surface p-12 text-center font-mono text-xs text-ink-muted shadow-brutal">
                Không có yêu cầu bài viết nào ở trạng thái này.
            </div>
        @endforelse
    </div>

    @if ($postRequests->hasPages())
        <div class="pt-2">
            {{ $postRequests->links() }}
        </div>
    @endif
</div>
@endsection
