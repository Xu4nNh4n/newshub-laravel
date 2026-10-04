@extends('layouts.dashboard', ['title' => 'Quản lý bài viết'])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b-2 border-line-strong pb-5">
        <div>
            <span class="font-mono text-xs font-bold uppercase tracking-wider text-ink-muted block mb-1">Hệ thống bài viết</span>
            <h1 class="text-2xl font-black uppercase tracking-tight text-ink sm:text-3xl font-heading">Quản lý bài viết</h1>
            <p class="text-xs text-ink-muted mt-1">Ẩn, lưu trữ, khôi phục hoặc chọn bài nổi bật trên trang chủ.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.posts.export') }}" class="inline-flex min-h-10 items-center gap-2 border-2 border-line-strong bg-surface px-4 py-2 font-mono text-xs font-bold uppercase text-ink shadow-brutal-sm hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
                <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.5 2A1.5 1.5 0 003 3.5v13A1.5 1.5 0 004.5 18h11a1.5 1.5 0 001.5-1.5V7.621a1.5 1.5 0 00-.44-1.06l-4.12-4.122A1.5 1.5 0 0010.378 2H4.5zm4.75 6.75a.75.75 0 011.5 0v3.69l1.22-1.22a.75.75 0 111.06 1.06l-2.5 2.5a.75.75 0 01-1.06 0l-2.5-2.5a.75.75 0 111.06-1.06l1.22 1.22V8.75z" clip-rule="evenodd" />
                </svg>
                <span>Xuất CSV</span>
            </a>
            <a href="{{ route('author.posts.create') }}" class="inline-flex min-h-10 items-center gap-2 border-2 border-line-strong bg-lime px-4 py-2 font-mono text-xs font-bold uppercase text-ink shadow-brutal hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="size-3.5"><path d="M12 5v14M5 12h14"/></svg>
                <span>Tạo bài</span>
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <form method="GET" class="flex flex-col gap-3 border-2 border-line-strong bg-surface p-4 sm:flex-row sm:items-center shadow-brutal-sm">
        <div class="min-w-0 flex-1">
            <label for="q" class="sr-only">Tìm kiếm</label>
            <input id="q" name="q" value="{{ $filters['q'] ?? '' }}" maxlength="100" placeholder="Tiêu đề hoặc slug bài viết..." class="w-full border-2 border-line bg-paper px-3.5 py-2 text-xs text-ink placeholder-ink-muted outline-none focus:border-ink">
        </div>
        <div class="w-full sm:w-48">
            <label for="status" class="sr-only">Trạng thái</label>
            <select id="status" name="status" class="w-full border-2 border-line bg-paper px-3 py-2 text-xs font-mono text-ink outline-none focus:border-ink">
                <option value="">Mọi trạng thái</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>{{ $status->value }}</option>
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

    <!-- Table -->
    <div class="overflow-x-auto border-2 border-line-strong bg-surface shadow-brutal">
        <table class="w-full min-w-3xl text-left text-xs">
            <thead class="border-b-2 border-line-strong bg-paper font-mono uppercase text-ink">
                <tr>
                    <th scope="col" class="px-5 py-3 font-bold">Bài viết</th>
                    <th scope="col" class="px-5 py-3 font-bold">Trạng thái</th>
                    <th scope="col" class="px-5 py-3 font-bold">Xuất bản</th>
                    <th scope="col" class="px-5 py-3 font-bold text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @forelse ($posts as $post)
                    <tr class="transition-colors hover:bg-paper/60">
                        <td class="px-5 py-4 min-w-64">
                            <p class="font-bold text-ink text-sm">{{ $post->title }}</p>
                            <p class="mt-1 font-mono text-[11px] text-ink-muted">{{ $post->author->name }} · {{ $post->category->name }}</p>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1 border border-line-strong bg-paper px-2 py-0.5 font-mono text-[11px] font-bold text-ink uppercase">
                                {{ $post->status->value }}
                            </span>
                            @if($post->is_featured)
                                <span class="ml-1.5 border border-line-strong bg-lime px-1.5 py-0.5 font-mono text-[10px] font-bold uppercase text-ink">
                                    Nổi bật
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap font-mono text-xs text-ink-muted">
                            {{ $post->published_at?->format('d/m/Y H:i') ?? '—' }}
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('posts.preview', $post) }}" class="inline-flex min-h-7 cursor-pointer items-center border border-line-strong bg-paper px-2.5 py-1 font-mono text-[11px] font-bold text-ink shadow-brutal-sm hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
                                    Xem trước
                                </a>
                                <a href="{{ route('author.posts.edit', $post) }}" class="inline-flex min-h-7 cursor-pointer items-center border border-line-strong bg-surface px-2.5 py-1 font-mono text-[11px] font-bold text-ink shadow-brutal-sm hover:bg-lime transition-all">
                                    Sửa
                                </a>
                                @if ($post->status === \App\Enums\PostStatus::Published)
                                    <form method="POST" action="{{ route('admin.posts.update', $post) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="action" value="{{ $post->is_featured ? 'unfeature' : 'feature' }}">
                                        <button class="inline-flex min-h-7 cursor-pointer items-center border border-line-strong bg-paper px-2.5 py-1 font-mono text-[11px] font-bold text-ink shadow-brutal-sm hover:bg-lime transition-all">
                                            {{ $post->is_featured ? 'Bỏ nổi bật' : 'Nổi bật' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.posts.update', $post) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="action" value="hide">
                                        <button class="inline-flex min-h-7 cursor-pointer items-center border border-line-strong bg-paper px-2.5 py-1 font-mono text-[11px] font-bold text-ink shadow-brutal-sm hover:border-danger hover:text-danger transition-all">
                                            Ẩn
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.posts.update', $post) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="action" value="archive">
                                        <button class="inline-flex min-h-7 cursor-pointer items-center border border-line bg-surface px-2.5 py-1 font-mono text-[11px] text-ink-muted hover:text-ink hover:border-line-strong transition-all">
                                            Lưu trữ
                                        </button>
                                    </form>
                                @elseif ($post->status === \App\Enums\PostStatus::Hidden)
                                    <form method="POST" action="{{ route('admin.posts.update', $post) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="action" value="restore">
                                        <button class="inline-flex min-h-7 cursor-pointer items-center border border-line-strong bg-lime px-2.5 py-1 font-mono text-[11px] font-bold text-ink shadow-brutal-sm transition-all">
                                            Khôi phục
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.posts.update', $post) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="action" value="archive">
                                        <button class="inline-flex min-h-7 cursor-pointer items-center border border-line bg-surface px-2.5 py-1 font-mono text-[11px] text-ink-muted hover:text-ink hover:border-line-strong transition-all">
                                            Lưu trữ
                                        </button>
                                    </form>
                                @elseif ($post->status === \App\Enums\PostStatus::Archived)
                                    <form method="POST" action="{{ route('admin.posts.update', $post) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="action" value="restore">
                                        <button class="inline-flex min-h-7 cursor-pointer items-center border border-line-strong bg-lime px-2.5 py-1 font-mono text-[11px] font-bold text-ink shadow-brutal-sm transition-all">
                                            Khôi phục
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-12 text-center font-mono text-xs text-ink-muted">
                            Không tìm thấy bài viết phù hợp.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $posts->links() }}
    </div>
</div>
@endsection
