@extends('layouts.dashboard', ['title' => 'Hồ sơ cá nhân'])

@section('content')
<div class="space-y-6">
    <header class="border-b border-[#E2E8F0] pb-5">
        <h1 class="text-xl font-black uppercase tracking-tight text-ink sm:text-2xl">Hồ sơ cá nhân</h1>
        <p class="text-xs font-mono text-ink-muted">Quản lý thông tin tài khoản và bảo mật.</p>
    </header>
    <section class="rounded-[12px] border border-[#E2E8F0] bg-surface p-6 shadow-[0_1px_3px_rgb(15_23_42_/_0.04)]">
        <div class="flex items-center gap-4">
            <div class="grid size-16 shrink-0 place-items-center rounded-[12px] border border-[#E2E8F0] bg-paper text-2xl font-black font-mono text-ink" aria-hidden="true">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</div>
            <div class="min-w-0">
                <h2 class="truncate text-lg font-black uppercase text-ink">{{ auth()->user()->name }}</h2>
                <p class="truncate text-xs font-mono text-ink-muted">{{ auth()->user()->email }}</p>
                <p class="mt-1 text-xs font-mono text-ink-muted">Vai trò: {{ auth()->user()->role->label() }} · Tham gia: {{ auth()->user()->created_at?->format('d/m/Y') ?? '—' }}</p>
            </div>
        </div>
        <div class="mt-4 flex flex-wrap gap-2 text-xs font-mono">
            @if (isset($stats['posts_count']) && in_array(auth()->user()->role, [\App\Enums\UserRole::Admin, \App\Enums\UserRole::Author], true))
                <a href="{{ route('author.posts.index') }}" class="rounded-[8px] border border-[#E2E8F0] bg-paper px-3 py-1.5 font-bold text-ink hover:border-[#CBD5E1]">{{ $stats['posts_count'] }} bài viết</a>
            @endif
            @if (isset($stats['comments_count']))
                <a href="{{ route('profile.comments') }}" class="rounded-[8px] border border-[#E2E8F0] bg-paper px-3 py-1.5 font-bold text-ink hover:border-[#CBD5E1]">{{ $stats['comments_count'] }} bình luận</a>
            @endif
        </div>
    </section>
    <div class="grid gap-6 lg:grid-cols-2">
        <section class="rounded-[12px] border border-[#E2E8F0] bg-surface p-6 shadow-[0_1px_3px_rgb(15_23_42_/_0.04)]">
            <h2 class="text-sm font-black uppercase text-ink">Thông tin cá nhân</h2>
            <form method="POST" action="{{ route('profile.update') }}" class="mt-5 space-y-4">
                @csrf
                @method('PATCH')
                <div class="space-y-1.5">
                    <label for="name" class="block text-xs font-mono font-bold uppercase text-ink">Họ và tên</label>
                    <input id="name" name="name" type="text" value="{{ old('name', auth()->user()->name) }}" required maxlength="255" autocomplete="name" class="w-full rounded-[8px] border border-control bg-paper px-3.5 py-2.5 text-xs text-ink focus:border-[#1D4ED8] focus:shadow-[0_0_0_2px_rgb(29_78_216_/_0.25)] focus:outline-hidden">
                    @error('name') <p class="text-xs text-danger font-mono">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label for="email" class="block text-xs font-mono font-bold uppercase text-ink">Địa chỉ email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', auth()->user()->email) }}" required maxlength="255" autocomplete="email" class="w-full rounded-[8px] border border-control bg-paper px-3.5 py-2.5 text-xs text-ink focus:border-[#1D4ED8] focus:shadow-[0_0_0_2px_rgb(29_78_216_/_0.25)] focus:outline-hidden">
                    @error('email') <p class="text-xs text-danger font-mono">{{ $message }}</p> @enderror
                    <p class="text-[11px] font-mono text-ink-muted">Nếu đổi email, bạn cần xác minh lại bằng liên kết gửi đến email mới.</p>
                </div>
                <button type="submit" class="rounded-[8px] border border-[#E2E8F0] bg-lime px-5 py-2.5 text-xs font-mono font-bold uppercase text-white shadow-none hover:bg-ink hover:text-white cursor-pointer transition-colors">Lưu thông tin</button>
            </form>
        </section>
        <section class="rounded-[12px] border border-[#E2E8F0] bg-surface p-6 shadow-[0_1px_3px_rgb(15_23_42_/_0.04)]">
            <h2 class="text-sm font-black uppercase text-ink">Đổi mật khẩu</h2>
            <form method="POST" action="{{ route('profile.password.update') }}" class="mt-5 space-y-4">
                @csrf
                @method('PUT')
                <div class="space-y-1.5">
                    <label for="current_password" class="block text-xs font-mono font-bold uppercase text-ink">Mật khẩu hiện tại</label>
                    <input id="current_password" name="current_password" type="password" required autocomplete="current-password" class="w-full rounded-[8px] border border-control bg-paper px-3.5 py-2.5 text-xs text-ink focus:border-[#1D4ED8] focus:shadow-[0_0_0_2px_rgb(29_78_216_/_0.25)] focus:outline-hidden">
                    @error('current_password') <p class="text-xs text-danger font-mono">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label for="password" class="block text-xs font-mono font-bold uppercase text-ink">Mật khẩu mới</label>
                    <input id="password" name="password" type="password" required autocomplete="new-password" class="w-full rounded-[8px] border border-control bg-paper px-3.5 py-2.5 text-xs text-ink focus:border-[#1D4ED8] focus:shadow-[0_0_0_2px_rgb(29_78_216_/_0.25)] focus:outline-hidden">
                    @error('password') <p class="text-xs text-danger font-mono">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label for="password_confirmation" class="block text-xs font-mono font-bold uppercase text-ink">Xác nhận mật khẩu mới</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="w-full rounded-[8px] border border-control bg-paper px-3.5 py-2.5 text-xs text-ink focus:border-[#1D4ED8] focus:shadow-[0_0_0_2px_rgb(29_78_216_/_0.25)] focus:outline-hidden">
                </div>
                <button type="submit" class="rounded-[8px] border border-[#E2E8F0] bg-lime px-5 py-2.5 text-xs font-mono font-bold uppercase text-white shadow-none hover:bg-ink hover:text-white cursor-pointer transition-colors">Cập nhật mật khẩu</button>
            </form>
        </section>
    </div>
</div>
@endsection
