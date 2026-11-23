@extends('layouts.app', ['title' => 'Lỗi hệ thống (500) - BaoUT'])

@section('content')
<div class="mx-auto max-w-lg py-16 sm:py-24 text-center">
    <div class="rounded-[12px] border border-[#E2E8F0] bg-surface p-8 sm:p-12 shadow-[0_1px_3px_rgb(15_23_42_/_0.04)]">
        <span class="inline-block font-mono text-6xl sm:text-7xl font-black text-danger tracking-tight">500</span>
        <div class="my-4 h-1 w-12 bg-danger mx-auto"></div>
        <h1 class="font-heading text-xl sm:text-2xl font-black uppercase tracking-tight text-ink">Hệ thống đang gặp sự cố</h1>
        <p class="mt-3 text-xs sm:text-sm text-ink-muted leading-relaxed max-w-sm mx-auto">
            Đã xảy ra lỗi trong quá trình xử lý yêu cầu. Sự cố đã được ghi nhận vào nhật ký hệ thống để đội ngũ kỹ thuật khắc phục.
        </p>

        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('home') }}" class="inline-flex min-h-10 items-center justify-center rounded-[8px] border border-[#E2E8F0] bg-lime px-6 py-2 font-mono text-xs font-bold uppercase text-ink shadow-none hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
                &larr; Về trang chủ BaoUT
            </a>
            <button type="button" onclick="window.location.reload()" class="inline-flex min-h-10 items-center justify-center rounded-[8px] border border-[#CBD5E1] bg-paper px-5 py-2 font-mono text-xs font-bold uppercase text-ink shadow-none hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all cursor-pointer">
                Tải lại trang
            </button>
        </div>
    </div>
</div>
@endsection
