@extends('layouts.dashboard', ['title' => 'Quản lý tài khoản'])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b-2 border-line-strong pb-5">
        <span class="font-mono text-xs font-bold uppercase tracking-wider text-ink-muted block mb-1">Quản trị người dùng</span>
        <h1 class="text-2xl font-black uppercase tracking-tight text-ink sm:text-3xl font-heading">Quản lý tài khoản</h1>
        <p class="text-xs text-ink-muted mt-1">Khóa tài khoản hoặc cấp và thu hồi quyền tác giả trong hệ thống.</p>
    </div>

    <!-- Filter Form -->
    <form method="GET" class="grid gap-3 border-2 border-line-strong bg-surface p-4 sm:grid-cols-1 md:grid-cols-[1fr_auto_auto_auto] shadow-brutal-sm">
        <div>
            <label for="q" class="sr-only">Tìm kiếm</label>
            <input id="q" name="q" value="{{ $filters['q'] ?? '' }}" maxlength="100" placeholder="Tên hoặc email..." class="w-full border-2 border-line bg-paper px-3.5 py-2 text-xs text-ink placeholder-ink-muted outline-none focus:border-ink">
        </div>
        <div>
            <label for="role" class="sr-only">Vai trò</label>
            <select id="role" name="role" class="w-full border-2 border-line bg-paper px-3 py-2 text-xs font-mono text-ink outline-none focus:border-ink">
                <option value="">Mọi vai trò</option>
                <option value="user" @selected(($filters['role'] ?? '') === 'user')>User</option>
                <option value="author" @selected(($filters['role'] ?? '') === 'author')>Author</option>
            </select>
        </div>
        <div>
            <label for="status" class="sr-only">Trạng thái</label>
            <select id="status" name="status" class="w-full border-2 border-line bg-paper px-3 py-2 text-xs font-mono text-ink outline-none focus:border-ink">
                <option value="">Mọi trạng thái</option>
                <option value="active" @selected(($filters['status'] ?? '') === 'active')>Active</option>
                <option value="blocked" @selected(($filters['status'] ?? '') === 'blocked')>Blocked</option>
            </select>
        </div>
        <button type="submit" class="inline-flex min-h-9 cursor-pointer items-center justify-center gap-1.5 border-2 border-line-strong bg-paper px-5 py-2 font-mono text-xs font-bold uppercase text-ink shadow-brutal-sm hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
            <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M2.628 1.601C5.028 1.206 7.49 1 10 1s4.973.206 7.372.601a.75.75 0 01.628.74v2.288a2.25 2.25 0 01-.659 1.59l-4.682 4.683a2.25 2.25 0 00-.659 1.59v3.037c0 .684-.31 1.33-.844 1.757l-1.937 1.55A.75.75 0 018 18.25v-5.757a2.25 2.25 0 00-.659-1.591L2.659 6.22A2.25 2.25 0 012 4.629V2.34a.75.75 0 01.628-.74z" clip-rule="evenodd" />
            </svg>
            <span>Lọc</span>
        </button>
    </form>

    <!-- Users Table -->
    <div class="overflow-x-auto border-2 border-line-strong bg-surface shadow-brutal">
        <table class="w-full min-w-3xl text-left text-xs">
            <thead class="border-b-2 border-line-strong bg-paper font-mono uppercase text-ink">
                <tr>
                    <th scope="col" class="px-5 py-3 font-bold">Tài khoản</th>
                    <th scope="col" class="px-5 py-3 font-bold">Hoạt động</th>
                    <th scope="col" class="px-5 py-3 font-bold">Quyền và trạng thái</th>
                    <th scope="col" class="px-5 py-3 font-bold text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @forelse ($users as $user)
                    <tr class="transition-colors hover:bg-paper/50">
                        <td class="px-5 py-4">
                            <p class="font-bold text-ink text-sm">{{ $user->name }}</p>
                            <p class="font-mono text-[11px] text-ink-muted mt-0.5">{{ $user->email }}</p>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap font-mono text-xs text-ink-muted">
                            {{ $user->posts_count }} bài viết · {{ $user->comments_count }} bình luận
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <form id="user-access-{{ $user->id }}" method="POST" action="{{ route('admin.users.update', $user) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="role" aria-label="Vai trò của {{ $user->name }}" class="border-2 border-line bg-paper px-2.5 py-1 font-mono text-xs text-ink outline-none focus:border-ink">
                                    <option value="user" @selected($user->role === \App\Enums\UserRole::User)>User</option>
                                    <option value="author" @selected($user->role === \App\Enums\UserRole::Author)>Author</option>
                                </select>
                                <select name="status" aria-label="Trạng thái của {{ $user->name }}" class="border-2 border-line bg-paper px-2.5 py-1 font-mono text-xs text-ink outline-none focus:border-ink">
                                    <option value="active" @selected($user->status === \App\Enums\UserStatus::Active)>Active</option>
                                    <option value="blocked" @selected($user->status === \App\Enums\UserStatus::Blocked)>Blocked</option>
                                </select>
                            </form>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button form="user-access-{{ $user->id }}" type="submit" class="inline-flex min-h-7 cursor-pointer items-center justify-center border-2 border-line-strong bg-lime px-3.5 py-1 font-mono text-[11px] font-bold uppercase text-ink shadow-brutal-sm hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
                                    Lưu
                                </button>
                                <form method="POST" action="{{ route('admin.users.send-reset-link', $user) }}"
                                      data-confirm="Xác nhận tạo token bảo mật và gửi email liên kết đặt lại mật khẩu đến hòm thư của tài khoản này?"
                                      data-confirm-title="Gửi link đặt lại mật khẩu"
                                      data-confirm-subtext="{{ $user->name }} ({{ $user->email }})"
                                      data-confirm-type="info"
                                      data-confirm-btn="Gửi email"
                                      class="inline">
                                    @csrf
                                    <button type="submit"
                                            title="Gửi email đặt lại mật khẩu cho {{ $user->name }}"
                                            aria-label="Gửi email đặt lại mật khẩu cho {{ $user->name }}"
                                            class="inline-flex min-h-7 cursor-pointer items-center justify-center gap-1.5 border-2 border-line-strong bg-paper px-2.5 py-1 font-mono text-[11px] font-bold text-ink shadow-brutal-sm hover:bg-lime hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
                                        <svg class="size-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="11" width="18" height="11" rx="0" ry="0"></rect>
                                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                        </svg>
                                        <span>Gửi link reset</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-12 text-center font-mono text-xs text-ink-muted">
                            Không tìm thấy tài khoản phù hợp.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $users->links() }}
    </div>
</div>
@endsection
