@extends('layouts.dashboard', ['title' => 'Đơn ứng tuyển tác giả', 'breadcrumbs' => $breadcrumbs ?? null])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b border-[#E2E8F0] pb-5">
        <div>
            <span class="font-mono text-xs font-bold uppercase tracking-wider text-ink-muted block mb-1">Đội ngũ tòa soạn</span>
            <h1 class="text-2xl font-black uppercase tracking-tight text-ink sm:text-3xl font-heading">Đơn ứng tuyển Tác giả</h1>
            <p class="text-xs text-ink-muted mt-1">Xét duyệt hồ sơ của độc giả đăng ký gia nhập đội ngũ Tác giả / Phóng viên BaoUT.</p>
        </div>

        <!-- Filter by Status -->
        <form method="GET" class="shrink-0">
            <label for="status" class="sr-only">Lọc theo trạng thái</label>
            <select id="status"
                    name="status"
                    onchange="this.form.submit()"
                    class="rounded-[8px] border border-control bg-paper px-3.5 py-2 font-mono text-xs font-bold uppercase text-ink outline-none focus:border-[#1D4ED8] focus:shadow-[0_0_0_2px_rgb(29_78_216_/_0.25)]">
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
            <article class="rounded-[12px] border border-[#E2E8F0] bg-surface p-6 shadow-[0_1px_3px_rgb(15_23_42_/_0.04)] transition-all">
                <!-- Top Row: Candidate info & Status Badge -->
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="font-heading text-lg font-black uppercase text-ink">{{ $application->user->name }}</h2>
                            <span class="rounded-[6px] border border-[#E2E8F0] bg-paper px-2 py-0.5 font-mono text-xs text-ink-muted">
                                {{ $application->user->email }}
                            </span>
                            <span class="rounded-[6px] border border-[#E2E8F0] bg-lime px-2 py-0.5 font-mono text-[11px] font-bold uppercase tracking-wider text-ink">
                                Vai trò: {{ $application->user->role->label() }}
                            </span>
                        </div>
                        <p class="mt-1 font-mono text-xs text-ink-muted">
                            Chuyên mục đăng ký: <strong class="text-ink font-bold">{{ $application->category->name }}</strong> · Nộp lúc: {{ $application->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>

                    <!-- Status Pill -->
                    <span @class([
                        'inline-flex shrink-0 items-center gap-1 rounded-[6px] border border-[#E2E8F0] px-3 py-1 font-mono text-xs font-bold uppercase',
                        'bg-paper text-ink shadow-none' => $application->status === \App\Enums\AuthorApplicationStatus::Pending,
                        'bg-lime text-white shadow-none' => $application->status === \App\Enums\AuthorApplicationStatus::Approved,
                        'bg-danger text-paper shadow-none' => $application->status === \App\Enums\AuthorApplicationStatus::Rejected,
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
                <div class="mt-4 rounded-[12px] border border-[#E2E8F0] bg-paper p-4">
                    <h3 class="font-mono text-xs font-bold uppercase tracking-wider text-ink">Giới thiệu & Kinh nghiệm viết lách:</h3>
                    <p class="mt-1.5 whitespace-pre-line text-xs leading-relaxed text-ink">
                        {{ $application->bio }}
                    </p>
                </div>

                <div class="mt-3 rounded-[12px] border border-[#E2E8F0] bg-paper p-4">
                    <h3 class="font-mono text-xs font-bold uppercase tracking-wider text-ink">CV ứng viên:</h3>
                    @if ($application->cv_path)
                        <a href="{{ route('admin.author-applications.cv', $application) }}" class="mt-2 inline-flex min-h-10 items-center rounded-[8px] border border-[#CBD5E1] bg-surface px-3 text-xs font-bold text-ink hover:bg-lime">Tải CV xuống</a>
                    @else
                        <p class="mt-1 text-xs text-ink-muted">Đơn cũ chưa có CV đính kèm.</p>
                    @endif
                </div>

                <!-- Reviewer Notes if Approved or Rejected -->
                @if ($application->status === \App\Enums\AuthorApplicationStatus::Approved)
                    <div class="mt-4 flex items-center gap-2 font-mono text-xs text-ink bg-paper rounded-[12px] border border-[#E2E8F0] p-3">
                        <span class="font-bold text-ink">✓ Đã phê duyệt:</span>
                        <span>
                            Bởi <strong>{{ $application->reviewer?->name ?? 'Quản trị viên' }}</strong> vào ngày {{ $application->reviewed_at?->format('d/m/Y H:i') }}.
                        </span>
                    </div>
                @elseif ($application->status === \App\Enums\AuthorApplicationStatus::Rejected)
                    <div class="mt-4 rounded-[12px] border border-[#E2E8F0] bg-paper p-4 text-xs text-ink">
                        <p class="font-mono font-bold text-danger uppercase tracking-wider">Lý do từ chối (bởi {{ $application->reviewer?->name ?? 'Quản trị viên' }} · {{ $application->reviewed_at?->format('d/m/Y H:i') }}):</p>
                        <p class="mt-1 text-ink">{{ $application->rejection_reason }}</p>
                    </div>
                @endif

                <!-- Action Forms for Pending Applications -->
                @if ($application->status === \App\Enums\AuthorApplicationStatus::Pending)
                    <div class="mt-5 flex flex-col gap-3 border-t border-[#E2E8F0] pt-5 sm:flex-row sm:items-center sm:justify-between">
                        <!-- Form Approve -->
                        <form method="POST" action="{{ route('admin.author-applications.approve', $application) }}"
                              data-confirm="Xác nhận phê duyệt đơn ứng tuyển và nâng cấp tài khoản này thành Tác giả (Author)?"
                              data-confirm-title="Phê duyệt đơn ứng tuyển"
                              data-confirm-subtext="{{ $application->user->name }} ({{ $application->user->email }})"
                              data-confirm-type="success"
                              data-confirm-btn="Phê duyệt ngay">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="inline-flex min-h-9 cursor-pointer items-center justify-center gap-1.5 rounded-[8px] border border-[#E2E8F0] bg-lime px-5 py-1.5 font-mono text-xs font-bold uppercase text-ink shadow-none hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
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
                                       class="w-full rounded-[8px] border border-control bg-paper px-3 py-1.5 text-xs text-ink placeholder-ink-muted outline-none focus:border-danger">
                            </div>
                            <button type="submit" class="inline-flex min-h-9 shrink-0 cursor-pointer items-center justify-center rounded-[8px] border border-[#E2E8F0] bg-danger px-4 py-1.5 font-mono text-xs font-bold uppercase text-paper shadow-none hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
                                Từ chối
                            </button>
                        </form>
                    </div>
                @endif
            </article>
        @empty
            <div class="rounded-[12px] border border-[#E2E8F0] bg-surface p-12 text-center font-mono text-xs text-ink-muted shadow-[0_1px_3px_rgb(15_23_42_/_0.04)]">
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
