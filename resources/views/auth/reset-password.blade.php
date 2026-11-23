@extends('layouts.app', ['title' => 'Đặt lại mật khẩu - BaoUT'])

@section('content')
<div class="mx-auto max-w-md py-6 sm:py-12">
    <section class="rounded-[12px] border border-[#E2E8F0] bg-surface p-6 sm:p-8 shadow-[0_1px_3px_rgb(15_23_42_/_0.04)] space-y-6">
        <div class="text-center space-y-2 border-b border-[#E2E8F0] pb-5">
            <x-brand-logo class="mx-auto h-16" />
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
                       class="w-full rounded-[8px] border border-control bg-paper/60 px-3.5 py-2.5 text-xs font-mono text-ink-muted outline-none cursor-not-allowed">
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
                       class="w-full rounded-[8px] border border-control bg-paper px-3.5 py-2.5 text-xs text-ink placeholder:text-ink-light focus:border-[#1D4ED8] focus:outline-hidden focus:bg-surface focus:shadow-[0_0_0_2px_rgb(29_78_216_/_0.25)]">
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
                       class="w-full rounded-[8px] border border-control bg-paper px-3.5 py-2.5 text-xs text-ink placeholder:text-ink-light focus:border-[#1D4ED8] focus:outline-hidden focus:bg-surface focus:shadow-[0_0_0_2px_rgb(29_78_216_/_0.25)]">
                @error('password_confirmation')
                    <p class="text-xs text-danger font-mono">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full min-h-11 cursor-pointer rounded-[8px] border border-[#E2E8F0] bg-lime py-2.5 text-xs font-mono font-bold uppercase tracking-wider text-white shadow-none hover:bg-ink hover:text-white hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-[0_4px_12px_rgb(15_23_42_/_0.08)] transition-all active:translate-x-1 active:translate-y-1">
                Cập nhật mật khẩu mới
            </button>
        </form>
    </section>
</div>
@endsection
