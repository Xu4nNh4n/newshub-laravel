@extends('layouts.app', ['title' => 'Tác giả '.$author->name.' - NewsHub'])

@section('content')
<div class="space-y-8 font-sans">
    <!-- Breadcrumb -->
    <nav aria-label="Breadcrumb" class="text-xs font-mono text-ink-muted">
        <ol class="flex items-center gap-1.5">
            <li><a href="{{ route('home') }}" class="hover:text-ink hover:underline transition-colors">Trang chủ</a></li>
            <li class="text-line">/</li>
            <li class="text-ink-muted">Tác giả</li>
            <li class="text-line">/</li>
            <li class="font-bold text-ink">{{ $author->name }}</li>
        </ol>
    </nav>

    <!-- Author Profile Hero Card -->
    <section class="border-2 border-line-strong bg-surface p-6 sm:p-8 shadow-brutal rounded-none">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="size-20 sm:size-24 shrink-0 overflow-hidden border-2 border-line-strong bg-paper">
                    @if ($avatarUrl)
                        <img src="{{ $avatarUrl }}" alt="{{ $author->name }}" class="size-full object-cover">
                    @else
                        <div class="size-full grid place-items-center bg-ink text-2xl font-black font-mono text-lime">
                            {{ mb_strtoupper(mb_substr($author->name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                <div class="space-y-1.5">
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-ink">{{ $author->name }}</h1>
                        <span class="border border-line-strong bg-lime px-2.5 py-0.5 text-xs font-bold font-mono uppercase tracking-wider text-ink shadow-2xs">
                            {{ $author->role->value }}
                        </span>
                    </div>
                    <p class="text-xs text-ink-muted">Tác giả / Thành viên ban biên tập NewsHub</p>
                    <p class="text-xs font-mono text-ink-muted">
                        Tham gia từ: <strong class="text-ink">{{ $author->created_at?->format('d/m/Y') ?? '—' }}</strong>
                    </p>
                </div>
            </div>

            <!-- Stats Box -->
            <div class="flex items-center gap-4 border-t border-line pt-4 sm:border-t-0 sm:pt-0">
                <div class="border-2 border-line-strong bg-paper px-5 py-3 text-center min-w-32 shadow-2xs font-mono">
                    <span class="text-xs text-ink-muted font-bold uppercase tracking-wider">Tổng bài viết</span>
                    <p class="mt-1 text-2xl font-black text-ink tabular-nums">
                        {{ number_format($posts->total()) }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Published Articles -->
    <section class="space-y-5">
        <div class="flex items-center justify-between border-b-2 border-line-strong pb-3">
            <div class="flex items-center gap-2.5">
                <span class="size-2.5 bg-lime border border-ink"></span>
                <h2 class="text-lg sm:text-xl font-black tracking-tight text-ink font-mono uppercase">
                    Bài viết đã xuất bản bởi {{ $author->name }}
                </h2>
            </div>
            <span class="text-xs font-mono text-ink-muted tabular-nums">
                Trang {{ $posts->currentPage() }} / {{ $posts->lastPage() }}
            </span>
        </div>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($posts as $post)
                <x-post-card :post="$post" />
            @empty
                <div class="col-span-full border-2 border-line bg-paper p-10 text-center text-xs font-mono text-ink-muted">
                    Tác giả chưa có bài viết công khai nào.
                </div>
            @endforelse
        </div>

        @if ($posts->hasPages())
            <div class="pt-4 flex justify-center">
                {{ $posts->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
