@extends('layouts.dashboard', ['title' => 'Nhật ký hoạt động'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b-2 border-line-strong pb-5">
        <div>
            <span class="font-mono text-xs font-bold uppercase tracking-wider text-ink-muted block mb-1">Kiểm toán hệ thống</span>
            <h1 class="text-2xl font-black uppercase tracking-tight text-ink sm:text-3xl font-heading">Nhật ký hoạt động</h1>
            <p class="text-xs text-ink-muted mt-1">Theo dõi và truy vết các thay đổi quan trọng trong khu vực quản trị NewsHub.</p>
        </div>
        <a href="{{ route('admin.activity-logs.export') }}" class="inline-flex min-h-10 items-center gap-2 border-2 border-line-strong bg-surface px-4 py-2 font-mono text-xs font-bold uppercase text-ink shadow-brutal-sm hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
            <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4.5 2A1.5 1.5 0 003 3.5v13A1.5 1.5 0 004.5 18h11a1.5 1.5 0 001.5-1.5V7.621a1.5 1.5 0 00-.44-1.06l-4.12-4.122A1.5 1.5 0 0010.378 2H4.5zm4.75 6.75a.75.75 0 011.5 0v3.69l1.22-1.22a.75.75 0 111.06 1.06l-2.5 2.5a.75.75 0 01-1.06 0l-2.5-2.5a.75.75 0 111.06-1.06l1.22 1.22V8.75z" clip-rule="evenodd" />
            </svg>
            <span>Xuất CSV</span>
        </a>
    </div>

    <!-- Filter Form -->
    <form method="GET" class="grid gap-3 border-2 border-line-strong bg-surface p-4 sm:grid-cols-1 md:grid-cols-[1fr_1fr_auto] shadow-brutal-sm">
        <div>
            <label for="action" class="sr-only">Hành động</label>
            <select id="action" name="action" class="w-full border-2 border-line bg-paper px-3 py-2 text-xs font-mono text-ink outline-none focus:border-ink">
                <option value="">Mọi hành động</option>
                @foreach($actions as $action)
                    <option value="{{ $action }}" @selected(($filters['action'] ?? '') === $action)>{{ $action }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="user_id" class="sr-only">Người thực hiện</label>
            <select id="user_id" name="user_id" class="w-full border-2 border-line bg-paper px-3 py-2 text-xs font-mono text-ink outline-none focus:border-ink">
                <option value="">Mọi người thực hiện</option>
                @foreach($actors as $actor)
                    <option value="{{ $actor->id }}" @selected((string) ($filters['user_id'] ?? '') === (string) $actor->id)>{{ $actor->name }} ({{ $actor->email }})</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="inline-flex min-h-9 cursor-pointer items-center justify-center gap-1.5 border-2 border-line-strong bg-paper px-5 py-2 font-mono text-xs font-bold uppercase text-ink shadow-brutal-sm hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
            Lọc
        </button>
    </form>

    <!-- Table -->
    <div class="overflow-x-auto border-2 border-line-strong bg-surface shadow-brutal">
        <table class="w-full min-w-3xl text-left text-xs">
            <thead class="border-b-2 border-line-strong bg-paper font-mono uppercase text-ink">
                <tr>
                    <th scope="col" class="px-5 py-3 font-bold">Thời gian</th>
                    <th scope="col" class="px-5 py-3 font-bold">Người thực hiện</th>
                    <th scope="col" class="px-5 py-3 font-bold">Hành động</th>
                    <th scope="col" class="px-5 py-3 font-bold">Chi tiết</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @forelse($logs as $log)
                    <tr class="transition-colors hover:bg-paper/50">
                        <td class="whitespace-nowrap px-5 py-4 text-ink-muted font-mono">
                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="px-5 py-4 font-bold text-ink">
                            {{ $log->user?->name ?? 'Hệ thống / tài khoản đã xóa' }}
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="border border-line-strong bg-paper px-2 py-0.5 font-mono text-[11px] font-bold uppercase text-ink">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-ink leading-relaxed">{{ $log->description ?? '—' }}</p>
                            @if($log->subject_type)
                                <p class="mt-1 font-mono text-[10px] text-ink-muted">
                                    {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                                </p>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-12 text-center font-mono text-xs text-ink-muted">Chưa có hoạt động phù hợp.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $logs->links() }}
    </div>
</div>
@endsection
