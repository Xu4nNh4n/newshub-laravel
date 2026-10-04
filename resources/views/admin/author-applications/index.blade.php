@extends('layouts.dashboard', ['title' => 'Đơn ứng tuyển tác giả', 'breadcrumbs' => $breadcrumbs ?? null])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b-2 border-line-strong pb-5">
        <div>
            <span class="font-mono text-xs font-bold uppercase tracking-wider text-ink-muted block mb-1">Đội ngũ tòa soạn</span>
            <h1 class="text-2xl font-black uppercase tracking-tight text-ink sm:text-3xl font-heading">Đơn ứng tuyển Tác giả</h1>
            <p class="text-xs text-ink-muted mt-1">Xét duyệt hồ sơ của độc giả đăng ký gia nhập đội ngũ Tác giả / Phóng viên NewsHub.</p>
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
                            'pending' => 'Chờ duyệt',
                            'approved' => 'Đã phê duyệt',
                            'rejected' => 'Đã từ chối',
                            default => $status->value,
                        } }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Applications List -->
    <div class="space-y-5">
        @forelse ($applications as $application)
            <article class="border-2 border-line-strong bg-surface p-6 shadow-brutal transition-all">
                <!-- Top Row: Candidate info & Status Badge -->
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="font-heading text-lg font-black uppercase text-ink">{{ $application->user->name }}</h2>
                            <span class="border border-line-strong bg-paper px-2 py-0.5 font-mono text-xs text-ink-muted">
                                {{ $application->user->email }}
                            </span>
                            <span class="border border-line-strong bg-lime px-2 py-0.5 font-mono text-[10px] font-bold uppercase tracking-wider text-ink">
                                Vai trò: {{ $application->user->role->value }}
                            </span>
                        </div>
                        <p class="mt-1 font-mono text-xs text-ink-muted">
                            Chuyên mục đăng ký: <strong class="text-ink font-bold">{{ $application->category->name }}</strong> · Nộp lúc: {{ $application->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>

                    <!-- Status Pill -->
                    <span @class([
                        'inline-flex shrink-0 items-center gap-1 border-2 border-line-strong px-3 py-1 font-mono text-xs font-bold uppercase',
                        'bg-paper text-ink shadow-brutal-sm' => $application->status === \App\Enums\AuthorApplicationStatus::Pending,
                        'bg-lime text-ink shadow-brutal-sm' => $application->status === \App\Enums\AuthorApplicationStatus::Approved,
                        'bg-danger text-paper shadow-brutal-sm' => $application->status === \App\Enums\AuthorApplicationStatus::Rejected,
                    ])>
                        {{ match ($application->status->value) {
                            'pending' => 'Chờ duyệt',
                            'approved' => 'Đã phê duyệt',
                            'rejected' => 'Đã từ chối',
                            default => $application->status->value,
                        } }}
                    </span>
                </div>

                <!-- Bio & Experience -->
                <div class="mt-4 border border-line bg-paper p-4">
                    <h3 class="font-mono text-xs font-bold uppercase tracking-wider text-ink">Giới thiệu & Kinh nghiệm viết lách:</h3>
                    <p class="mt-1.5 whitespace-pre-line text-xs leading-relaxed text-ink">
                        {{ $application->bio }}
                    </p>
                </div>

                <!-- Sample Article -->
                <div class="mt-3 border border-line bg-paper p-4">
                    <h3 class="font-mono text-xs font-bold uppercase tracking-wider text-ink">Bài viết mẫu đề xuất:</h3>
                    <p class="mt-1 font-heading text-sm font-black uppercase text-ink">
                        {{ $application->sample_title }}
                    </p>
                    <details class="group mt-2">
                        <summary class="cursor-pointer font-mono text-xs font-bold uppercase tracking-wider text-ink-muted hover:text-ink select-none">
                            Xem nội dung chi tiết bài mẫu &darr;
                        </summary>
                        <div class="mt-2.5 whitespace-pre-line text-xs leading-relaxed text-ink border-t border-line pt-2.5">
                            {{ $application->sample_content }}
                        </div>
                    </details>
                </div>

                <!-- Reviewer Notes if Approved or Rejected -->
                @if ($application->status === \App\Enums\AuthorApplicationStatus::Approved)
                    <div class="mt-4 flex items-center gap-2 font-mono text-xs text-ink bg-paper border border-line-strong p-3">
                        <span class="font-bold text-ink">✓ Đã phê duyệt:</span>
                        <span>
                            Bởi <strong>{{ $application->reviewer?->name ?? 'Quản trị viên' }}</strong> vào ngày {{ $application->reviewed_at?->format('d/m/Y H:i') }}.
                        </span>
                    </div>
                @elseif ($application->status === \App\Enums\AuthorApplicationStatus::Rejected)
                    <div class="mt-4 border-2 border-line-strong bg-paper p-4 text-xs text-ink">
                        <p class="font-mono font-bold text-danger uppercase tracking-wider">Lý do từ chối (bởi {{ $application->reviewer?->name ?? 'Quản trị viên' }} · {{ $application->reviewed_at?->format('d/m/Y H:i') }}):</p>
                        <p class="mt-1 text-ink">{{ $application->rejection_reason }}</p>
                    </div>
                @endif

                <!-- Action Forms for Pending Applications -->
                @if ($application->status === \App\Enums\AuthorApplicationStatus::Pending)
                    <div class="mt-5 flex flex-col gap-3 border-t-2 border-line-strong pt-5 sm:flex-row sm:items-center sm:justify-between">
                        <!-- Form Approve -->
                        <form method="POST" action="{{ route('admin.author-applications.approve', $application) }}"
                              data-confirm="Xác nhận phê duyệt đơn ứng tuyển và nâng cấp tài khoản này thành Tác giả (Author)?"
                              data-confirm-title="Phê duyệt đơn ứng tuyển"
                              data-confirm-subtext="{{ $application->user->name }} ({{ $application->user->email }})"
                              data-confirm-type="success"
                              data-confirm-btn="Phê duyệt ngay">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="inline-flex min-h-9 cursor-pointer items-center justify-center gap-1.5 border-2 border-line-strong bg-lime px-5 py-1.5 font-mono text-xs font-bold uppercase text-ink shadow-brutal-sm hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
                                <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                </svg>
                                <span>Phê duyệt làm Tác giả</span>
                            </button>
                        </form>

                        <!-- Form Reject -->
                        <form method="POST" action="{{ route('admin.author-applications.reject', $application) }}" class="flex flex-1 flex-col gap-2 sm:max-w-md sm:flex-row sm:items-center"
                              data-confirm="Xác nhận từ chối đơn ứng tuyển này?"
                              data-confirm-title="Từ chối đơn ứng tuyển"
                              data-confirm-subtext="{{ $application->user->name }}"
                              data-confirm-type="danger"
                              data-confirm-btn="Từ chối đơn">
                            @csrf
                            @method('PATCH')
                            <div class="min-w-0 flex-1">
                                <label for="reason-{{ $application->id }}" class="sr-only">Lý do từ chối</label>
                                <input id="reason-{{ $application->id }}"
                                       name="reason"
                                       required
                                       placeholder="Lý do từ chối (bắt buộc)..."
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
                Không có đơn ứng tuyển nào ở trạng thái này.
            </div>
        @endforelse
    </div>

    @if ($applications->hasPages())
        <div class="pt-2">
            {{ $applications->links() }}
        </div>
    @endif
</div>
@endsection
