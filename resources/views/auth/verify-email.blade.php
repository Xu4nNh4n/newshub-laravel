@extends('layouts.app', ['title' => 'Xác minh tài khoản - NewsHub'])

@section('content')
<div class="mx-auto max-w-lg py-8 sm:py-16">
    <section class="border-2 border-line-strong bg-surface p-6 sm:p-10 shadow-brutal text-center space-y-6">
        <div class="mx-auto grid size-14 place-items-center border-2 border-line-strong bg-lime text-ink font-mono font-black shadow-brutal-sm">
            <svg class="size-7" viewBox="0 0 20 20" fill="currentColor">
                <path d="M3 4a2 2 0 00-2 2v1.161l8.441 4.221a1.25 1.25 0 001.118 0L19 7.162V6a2 2 0 00-2-2H3z" />
                <path d="M19 8.839l-7.77 3.885a2.75 2.75 0 01-2.46 0L1 8.839V14a2 2 0 002 2h14a2 2 0 002-2V8.839z" />
            </svg>
        </div>

        <div class="space-y-2">
            <h1 class="text-xl sm:text-2xl font-black uppercase tracking-tight text-ink">Xác minh địa chỉ Email</h1>
            <p class="text-xs sm:text-sm text-ink-muted leading-relaxed max-w-md mx-auto">
                Hệ thống đã gửi liên kết xác nhận đến hộp thư của bạn. Vui lòng kiểm tra email (bao gồm cả thư mục Spam) và nhấp vào liên kết để kích hoạt đầy đủ quyền bình luận, lưu bài và ứng tuyển làm Tác giả.
            </p>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="border-2 border-line-strong bg-lime p-3 text-xs font-mono font-bold text-ink shadow-brutal-sm">
                Một liên kết xác minh mới đã được gửi tới địa chỉ email của bạn!
            </div>
        @endif

        <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit"
                        class="inline-flex min-h-11 cursor-pointer items-center justify-center border-2 border-line-strong bg-lime px-6 py-2.5 text-xs font-mono font-bold uppercase tracking-wider text-ink shadow-brutal hover:bg-lime-hover hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-brutal-sm transition-all active:translate-x-1 active:translate-y-1">
                    Gửi lại email xác minh
                </button>
            </form>

            <a href="{{ route('home') }}" class="inline-flex min-h-11 items-center justify-center border border-line bg-paper px-5 py-2.5 text-xs font-mono font-bold uppercase tracking-wider text-ink hover:border-line-strong hover:bg-surface transition-colors">
                Về trang chủ
            </a>
        </div>
    </section>
</div>
@endsection
