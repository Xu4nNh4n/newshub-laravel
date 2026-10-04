@extends('layouts.app', ['title' => 'Không có quyền truy cập (403) - NewsHub'])

@section('content')
<div class="mx-auto max-w-lg py-16 sm:py-24 text-center">
    <div class="border-2 border-line-strong bg-surface p-8 sm:p-12 shadow-brutal">
        <span class="inline-block font-mono text-6xl sm:text-7xl font-black text-danger tracking-tight">403</span>
        <div class="my-4 h-1 w-12 bg-danger mx-auto"></div>
        <h1 class="font-heading text-xl sm:text-2xl font-black uppercase tracking-tight text-ink">Bạn không có quyền truy cập</h1>
        <p class="mt-3 text-xs sm:text-sm text-ink-muted leading-relaxed max-w-sm mx-auto">
            Tài khoản hiện tại không được phép truy cập khu vực này. Nếu bạn muốn viết bài, hãy nộp đơn ứng tuyển làm Tác giả tại bảng điều khiển.
        </p>

        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('home') }}" class="inline-flex min-h-10 items-center justify-center border-2 border-line-strong bg-lime px-6 py-2 font-mono text-xs font-bold uppercase text-ink shadow-brutal hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
                &larr; Về trang chủ NewsHub
            </a>
            @auth
                <a href="{{ route('dashboard') }}" class="inline-flex min-h-10 items-center justify-center border-2 border-line-strong bg-paper px-5 py-2 font-mono text-xs font-bold uppercase text-ink shadow-brutal-sm hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
                    Vào Bảng điều khiển
                </a>
            @endauth
        </div>
    </div>
</div>
@endsection
