@extends('layouts.app', ['title' => 'Quên mật khẩu - NewsHub'])

@section('content')
<div class="mx-auto max-w-md py-6 sm:py-12">
    <section class="border-2 border-line-strong bg-surface p-6 sm:p-8 shadow-brutal space-y-6">
        <!-- Logo & Header -->
        <div class="text-center space-y-2 border-b border-line pb-5">
            <span class="mx-auto grid size-12 place-items-center border-2 border-line-strong bg-lime text-ink font-mono font-black text-xl shadow-brutal-sm">
                N
            </span>
            <h1 class="text-xl sm:text-2xl font-black uppercase tracking-tight text-ink">Quên mật khẩu</h1>
            <p class="text-xs font-mono text-ink-muted">Nhập địa chỉ email đăng ký để nhận liên kết khôi phục mật khẩu.</p>
        </div>

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-mono font-bold uppercase tracking-wider text-ink">
                    Địa chỉ Email <span class="text-danger">*</span>
                </label>
                <div class="relative">
                    <input id="email"
                           type="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           placeholder="name@example.com"
                           class="w-full border border-line bg-paper py-2.5 pl-10 pr-3 text-xs text-ink placeholder:text-ink-light focus:border-line-strong focus:outline-hidden focus:bg-surface focus:shadow-brutal-sm">
                    <svg class="pointer-events-none absolute left-3 top-3 size-4 text-ink-muted" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M3 4a2 2 0 00-2 2v1.161l8.441 4.221a1.25 1.25 0 001.118 0L19 7.162V6a2 2 0 00-2-2H3z" />
                        <path d="M19 8.839l-7.77 3.885a2.75 2.75 0 01-2.46 0L1 8.839V14a2 2 0 002 2h14a2 2 0 002-2V8.839z" />
                    </svg>
                </div>
                @error('email')
                    <p class="text-xs text-danger font-mono">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full min-h-11 cursor-pointer border-2 border-line-strong bg-lime py-2.5 text-xs font-mono font-bold uppercase tracking-wider text-ink shadow-brutal hover:bg-lime-hover hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-brutal-sm transition-all active:translate-x-1 active:translate-y-1">
                Gửi liên kết đặt lại mật khẩu
            </button>
        </form>

        <div class="border-t border-line pt-4 text-center text-xs font-mono text-ink-muted">
            <a href="{{ route('login') }}" class="font-bold text-ink hover:underline">
                &larr; Quay lại đăng nhập
            </a>
        </div>
    </section>
</div>
@endsection
