@extends('layouts.app', ['title' => 'Đăng nhập - NewsHub'])

@section('content')
<div class="mx-auto max-w-md py-6 sm:py-12">
    <section class="border-2 border-line-strong bg-surface p-6 sm:p-8 shadow-brutal space-y-6">
        <!-- Logo & Header -->
        <div class="text-center space-y-2 border-b border-line pb-5">
            <span class="mx-auto grid size-12 place-items-center border-2 border-line-strong bg-lime text-ink font-mono font-black text-xl shadow-brutal-sm">
                N
            </span>
            <h1 class="text-xl sm:text-2xl font-black uppercase tracking-tight text-ink">Đăng nhập tài khoản</h1>
            <p class="text-xs font-mono text-ink-muted">Chào mừng bạn quay lại hệ thống tin tức NewsHub.</p>
        </div>

        <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
            @csrf

            <!-- Email -->
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
                           autocomplete="email"
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

            <!-- Password -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-xs font-mono font-bold uppercase tracking-wider text-ink">
                        Mật khẩu <span class="text-danger">*</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-xs font-mono text-ink-muted hover:text-ink hover:underline">
                        Quên mật khẩu?
                    </a>
                </div>
                <div class="relative">
                    <input id="password"
                           type="password"
                           name="password"
                           required
                           autocomplete="current-password"
                           placeholder="••••••••"
                           class="w-full border border-line bg-paper py-2.5 pl-10 pr-3 text-xs text-ink placeholder:text-ink-light focus:border-line-strong focus:outline-hidden focus:bg-surface focus:shadow-brutal-sm">
                    <svg class="pointer-events-none absolute left-3 top-3 size-4 text-ink-muted" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" />
                    </svg>
                </div>
                @error('password')
                    <p class="text-xs text-danger font-mono">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember me -->
            <div class="flex items-center justify-between pt-1">
                <label class="flex cursor-pointer items-center gap-2 text-xs font-mono text-ink select-none">
                    <input type="checkbox"
                           name="remember"
                           value="1"
                           class="border-line bg-paper text-ink focus:ring-0 cursor-pointer">
                    <span>Ghi nhớ đăng nhập</span>
                </label>
            </div>

            <!-- Submit -->
            <button type="submit"
                    class="w-full min-h-11 cursor-pointer border-2 border-line-strong bg-lime py-2.5 text-xs font-mono font-bold uppercase tracking-wider text-ink shadow-brutal hover:bg-lime-hover hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-brutal-sm transition-all active:translate-x-1 active:translate-y-1">
                Đăng nhập
            </button>
        </form>

        <div class="border-t border-line pt-4 text-center text-xs font-mono text-ink-muted">
            Chưa có tài khoản độc giả?
            <a href="{{ route('register') }}" class="font-bold text-ink hover:underline ml-1">
                Đăng ký ngay &rarr;
            </a>
        </div>
    </section>
</div>
@endsection
