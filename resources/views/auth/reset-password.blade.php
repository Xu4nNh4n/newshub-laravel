@extends('layouts.app', ['title' => 'Đặt lại mật khẩu - NewsHub'])

@section('content')
<div class="mx-auto max-w-md py-6 sm:py-12">
    <section class="border-2 border-line-strong bg-surface p-6 sm:p-8 shadow-brutal space-y-6">
        <div class="text-center space-y-2 border-b border-line pb-5">
            <span class="mx-auto grid size-12 place-items-center border-2 border-line-strong bg-lime text-ink font-mono font-black text-xl shadow-brutal-sm">
                N
            </span>
            <h1 class="text-xl sm:text-2xl font-black uppercase tracking-tight text-ink">Đặt lại mật khẩu</h1>
            <p class="text-xs font-mono text-ink-muted">Thiết lập mật khẩu mới an toàn cho tài khoản của bạn.</p>
        </div>

        <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-mono font-bold uppercase tracking-wider text-ink">
                    Địa chỉ Email <span class="text-danger">*</span>
                </label>
                <input id="email"
                       type="email"
                       name="email"
                       value="{{ old('email', $email) }}"
                       required
                       readonly
                       class="w-full border border-line bg-paper/60 px-3.5 py-2.5 text-xs font-mono text-ink-muted outline-none cursor-not-allowed">
                @error('email')
                    <p class="text-xs text-danger font-mono">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-1.5">
                <label for="password" class="block text-xs font-mono font-bold uppercase tracking-wider text-ink">
                    Mật khẩu mới <span class="text-danger">*</span>
                </label>
                <input id="password"
                       type="password"
                       name="password"
                       required
                       autocomplete="new-password"
                       placeholder="Tối thiểu 8 ký tự"
                       class="w-full border border-line bg-paper px-3.5 py-2.5 text-xs text-ink placeholder:text-ink-light focus:border-line-strong focus:outline-hidden focus:bg-surface focus:shadow-brutal-sm">
                @error('password')
                    <p class="text-xs text-danger font-mono">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-1.5">
                <label for="password_confirmation" class="block text-xs font-mono font-bold uppercase tracking-wider text-ink">
                    Xác nhận mật khẩu mới <span class="text-danger">*</span>
                </label>
                <input id="password_confirmation"
                       type="password"
                       name="password_confirmation"
                       required
                       autocomplete="new-password"
                       placeholder="Nhập lại mật khẩu mới"
                       class="w-full border border-line bg-paper px-3.5 py-2.5 text-xs text-ink placeholder:text-ink-light focus:border-line-strong focus:outline-hidden focus:bg-surface focus:shadow-brutal-sm">
                @error('password_confirmation')
                    <p class="text-xs text-danger font-mono">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full min-h-11 cursor-pointer border-2 border-line-strong bg-lime py-2.5 text-xs font-mono font-bold uppercase tracking-wider text-ink shadow-brutal hover:bg-lime-hover hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-brutal-sm transition-all active:translate-x-1 active:translate-y-1">
                Cập nhật mật khẩu mới
            </button>
        </form>
    </section>
</div>
@endsection
