@extends('layouts.dashboard', ['title' => 'Hồ sơ cá nhân'])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b-2 border-line-strong pb-5">
        <h1 class="text-xl font-black uppercase tracking-tight text-ink sm:text-2xl">Hồ sơ & Tài khoản</h1>
        <p class="text-xs font-mono text-ink-muted">Quản lý thông tin định danh, ảnh đại diện và bảo mật tài khoản.</p>
    </div>

    <!-- Overview Identity Card -->
    <section class="border-2 border-line-strong bg-surface p-6 shadow-brutal">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-5">
                <!-- Avatar Circular Frame with crisp ink border -->
                <div class="relative group size-20 sm:size-24 shrink-0 rounded-full border-2 border-line-strong bg-paper shadow-brutal-sm overflow-hidden">
                    <img id="header-avatar-img"
                         src="{{ $avatarUrl ?? '' }}"
                         alt="Ảnh đại diện của {{ auth()->user()->name }}"
                         class="size-full object-cover {{ $avatarUrl ? '' : 'hidden' }}">
                    <div id="header-avatar-fallback"
                         class="size-full grid place-items-center bg-paper text-2xl font-black font-mono text-ink {{ $avatarUrl ? 'hidden' : '' }}"
                         aria-hidden="true">
                        {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                    </div>
                </div>

                <!-- User Details -->
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-lg font-black uppercase text-ink sm:text-xl truncate">{{ auth()->user()->name }}</h2>
                        <!-- Role Badge -->
                        @php
                            $role = auth()->user()->role;
                        @endphp
                        <span @class([
                            'border border-line-strong px-2 py-0.5 text-xs font-mono font-bold uppercase tracking-wider',
                            'bg-lime text-ink' => $role === \App\Enums\UserRole::Admin,
                            'bg-pink text-ink' => $role === \App\Enums\UserRole::Author,
                            'bg-paper text-ink-muted' => $role === \App\Enums\UserRole::User,
                        ])>
                            {{ $role->value }}
                        </span>
                        <!-- Status Badge -->
                        <span class="inline-flex items-center gap-1.5 border border-line-strong bg-paper px-2 py-0.5 text-xs font-mono font-bold uppercase text-ink">
                            <span class="size-1.5 bg-success inline-block"></span>
                            {{ auth()->user()->status->value }}
                        </span>
                    </div>

                    <p class="mt-1 text-xs font-mono text-ink-muted">{{ auth()->user()->email }}</p>

                    <!-- Verification status pill -->
                    <div class="mt-2 flex items-center gap-2 text-xs font-mono">
                        @if (auth()->user()->hasVerifiedEmail())
                            <span class="inline-flex items-center gap-1 text-success font-bold">
                                <span>✓</span> Email đã xác thực
                            </span>
                        @else
                            <a href="{{ route('verification.notice') }}" class="inline-flex items-center gap-1 text-danger font-bold hover:underline">
                                <span>!</span> Chưa xác thực email (Xác minh ngay)
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Metrics -->
            <div class="flex flex-wrap items-center gap-2 border-t border-line pt-4 sm:border-0 sm:pt-0 font-mono text-xs">
                <span class="inline-flex items-center gap-1.5 border border-line bg-paper px-3 py-1.5 text-ink">
                    Tham gia: <strong>{{ auth()->user()->created_at?->format('d/m/Y') ?? '—' }}</strong>
                </span>
                @if (isset($stats['posts_count']) && (auth()->user()->role === \App\Enums\UserRole::Admin || auth()->user()->role === \App\Enums\UserRole::Author))
                    <a href="{{ route('author.posts.index') }}" class="inline-flex items-center gap-1.5 border border-line bg-paper px-3 py-1.5 text-ink hover:border-line-strong hover:bg-surface transition-colors font-bold">
                        <span>{{ $stats['posts_count'] }} bài viết</span>
                    </a>
                @endif
                <a href="{{ route('profile.comments') }}" class="inline-flex items-center gap-1.5 border border-line bg-paper px-3 py-1.5 text-ink hover:border-line-strong hover:bg-surface transition-colors font-bold">
                    @if (isset($stats['comments_count']))
                        {{ $stats['comments_count'] }} bình luận
                    @else
                        Lịch sử bình luận
                    @endif
                </a>
                <a href="{{ route('favorites.index') }}" class="inline-flex items-center gap-1.5 border border-line bg-paper px-3 py-1.5 text-ink hover:border-line-strong hover:bg-surface transition-colors font-bold">
                    @if (isset($stats['favorites_count']))
                        {{ $stats['favorites_count'] }} bài lưu
                    @else
                        Bài viết đã lưu
                    @endif
                </a>
                <a href="{{ route('reading-history.index') }}" class="inline-flex items-center gap-1.5 border border-line bg-paper px-3 py-1.5 text-ink hover:border-line-strong hover:bg-surface transition-colors font-bold">
                    @if (isset($postViewsCount))
                        {{ $postViewsCount }} đã đọc
                    @else
                        Bài viết đã đọc
                    @endif
                </a>
                <a href="{{ route('profile.activity') }}" class="inline-flex items-center gap-1.5 border border-line bg-paper px-3 py-1.5 text-ink hover:border-line-strong hover:bg-surface transition-colors font-bold">
                    <span>Lịch sử hoạt động</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Main Content Form Grid -->
    <div class="grid gap-6 lg:grid-cols-12">
        <!-- Left: Personal Info & Avatar -->
        <div class="space-y-6 lg:col-span-7">
            <!-- Form Update Profile -->
            <section class="border-2 border-line-strong bg-surface p-6 shadow-brutal space-y-5">
                <div class="border-b border-line pb-3">
                    <h3 class="text-sm font-black uppercase text-ink">Thông tin cá nhân & Ảnh đại diện</h3>
                    <p class="text-xs font-mono text-ink-muted">Thay đổi họ tên hiển thị và cập nhật ảnh đại diện chuẩn khung tròn.</p>
                </div>

                <form id="profile-update-form" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    @method('PATCH')

                    <!-- Avatar Section with Interactive Crop & Circular Preview -->
                    <div class="space-y-2">
                        <span class="block text-xs font-mono font-bold uppercase tracking-wider text-ink">Ảnh đại diện</span>
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                            <!-- Circular Preview Target -->
                            <div class="relative size-20 shrink-0 rounded-full border-2 border-line-strong bg-paper shadow-brutal-sm overflow-hidden">
                                <img id="form-avatar-preview"
                                     src="{{ $avatarUrl ?? '' }}"
                                     alt="Xem trước avatar"
                                     class="size-full object-cover {{ $avatarUrl ? '' : 'hidden' }}">
                                <div id="form-avatar-fallback"
                                     class="size-full grid place-items-center bg-paper text-xl font-black font-mono text-ink {{ $avatarUrl ? 'hidden' : '' }}"
                                     aria-hidden="true">
                                    {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            </div>

                            <div class="flex-1 space-y-2">
                                <div class="flex flex-wrap items-center gap-2">
                                    <!-- Hidden real file input -->
                                    <input id="avatar"
                                           type="file"
                                           name="avatar"
                                           accept=".jpg,.jpeg,.png,.webp"
                                           class="sr-only">

                                    <button type="button"
                                            id="btn-choose-avatar"
                                            class="inline-flex min-h-9 items-center gap-1.5 border border-line-strong bg-paper px-3.5 py-1.5 text-xs font-mono font-bold uppercase tracking-wider text-ink shadow-brutal-sm hover:bg-surface cursor-pointer">
                                        <svg class="size-3.5 text-ink" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <span>Chọn ảnh & Cắt khung tròn</span>
                                    </button>

                                    <span id="avatar-filename-status" class="text-xs font-mono text-ink-muted">Chưa chọn ảnh mới</span>
                                </div>

                                @if ($avatarUrl || auth()->user()->avatar)
                                    <div class="pt-1">
                                        <label class="inline-flex items-center gap-2 text-xs font-mono text-danger cursor-pointer select-none">
                                            <input type="checkbox"
                                                   id="remove_avatar"
                                                   name="remove_avatar"
                                                   value="1"
                                                   class="border-line bg-paper text-danger focus:ring-0">
                                            <span>Gỡ bỏ ảnh đại diện hiện tại</span>
                                        </label>
                                    </div>
                                @endif

                                @error('avatar')
                                    <p class="text-xs text-danger font-mono">{{ $message }}</p>
                                @enderror
                                @error('remove_avatar')
                                    <p class="text-xs text-danger font-mono">{{ $message }}</p>
                                @enderror

                                <p class="text-[11px] font-mono text-ink-muted">
                                    Định dạng JPG, PNG hoặc WebP; dung lượng tối đa 2 MB. Có thể kéo và phóng to thu nhỏ ảnh để căn chỉnh khung tròn.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Display Name Field -->
                    <div class="space-y-1.5">
                        <label for="name" class="block text-xs font-mono font-bold uppercase tracking-wider text-ink">
                            Họ và tên <span class="text-danger">*</span>
                        </label>
                        <input id="name"
                               type="text"
                               name="name"
                               value="{{ old('name', auth()->user()->name) }}"
                               required
                               maxlength="255"
                               class="w-full border border-line bg-paper px-3.5 py-2.5 text-xs text-ink focus:border-line-strong focus:outline-hidden focus:bg-surface focus:shadow-brutal-sm">
                        @error('name')
                            <p class="text-xs text-danger font-mono">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div class="space-y-1.5">
                        <label for="email" class="block text-xs font-mono font-bold uppercase tracking-wider text-ink">
                            Địa chỉ Email <span class="text-danger">*</span>
                        </label>
                        <input id="email"
                               type="email"
                               name="email"
                               value="{{ old('email', auth()->user()->email) }}"
                               required
                               maxlength="255"
                               class="w-full border border-line bg-paper px-3.5 py-2.5 text-xs text-ink focus:border-line-strong focus:outline-hidden focus:bg-surface focus:shadow-brutal-sm">
                        @error('email')
                            <p class="text-xs text-danger font-mono">{{ $message }}</p>
                        @enderror
                        <p class="text-[11px] font-mono text-ink-muted">
                            Nếu thay đổi địa chỉ email, hệ thống sẽ gửi liên kết xác thực mới và bạn cần xác minh lại email.
                        </p>
                    </div>

                    <div class="pt-3 border-t border-line">
                        <button type="submit" class="inline-flex min-h-11 cursor-pointer items-center justify-center border-2 border-line-strong bg-lime px-6 py-2.5 text-xs font-mono font-bold uppercase tracking-wider text-ink shadow-brutal hover:bg-lime-hover hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-brutal-sm transition-all active:translate-x-1 active:translate-y-1">
                            Lưu thông tin hồ sơ
                        </button>
                    </div>
                </form>
            </section>

            <!-- Form Update Password -->
            <section class="border-2 border-line-strong bg-surface p-6 shadow-brutal space-y-4">
                <div class="border-b border-line pb-3">
                    <h3 class="text-sm font-black uppercase text-ink">Đổi mật khẩu</h3>
                    <p class="text-xs font-mono text-ink-muted">Đảm bảo tài khoản sử dụng mật khẩu mạnh để tăng tính an toàn.</p>
                </div>

                <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="space-y-1.5">
                        <label for="current_password" class="block text-xs font-mono font-bold uppercase tracking-wider text-ink">
                            Mật khẩu hiện tại <span class="text-danger">*</span>
                        </label>
                        <input id="current_password"
                               type="password"
                               name="current_password"
                               required
                               autocomplete="current-password"
                               class="w-full border border-line bg-paper px-3.5 py-2.5 text-xs text-ink focus:border-line-strong focus:outline-hidden focus:bg-surface focus:shadow-brutal-sm">
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-1.5">
                            <label for="password" class="block text-xs font-mono font-bold uppercase tracking-wider text-ink">
                                Mật khẩu mới <span class="text-danger">*</span>
                            </label>
                            <input id="password"
                                   type="password"
                                   name="password"
                                   required
                                   autocomplete="new-password"
                                   class="w-full border border-line bg-paper px-3.5 py-2.5 text-xs text-ink focus:border-line-strong focus:outline-hidden focus:bg-surface focus:shadow-brutal-sm">
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
                                   class="w-full border border-line bg-paper px-3.5 py-2.5 text-xs text-ink focus:border-line-strong focus:outline-hidden focus:bg-surface focus:shadow-brutal-sm">
                        </div>
                    </div>

                    <div class="pt-3 border-t border-line">
                        <button type="submit" class="inline-flex min-h-11 cursor-pointer items-center justify-center border-2 border-line-strong bg-paper px-6 py-2.5 text-xs font-mono font-bold uppercase tracking-wider text-ink shadow-brutal hover:bg-surface hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-brutal-sm transition-all active:translate-x-1 active:translate-y-1">
                            Cập nhật mật khẩu
                        </button>
                    </div>
                </form>
            </section>
        </div>

        <!-- Right: Role Capabilities & Activity Shortcuts -->
        <div class="space-y-6 lg:col-span-5">
            <!-- Role Privileges Card -->
            <section class="border-2 border-line-strong bg-surface p-6 shadow-brutal space-y-4">
                <div class="border-b border-line pb-3">
                    <h3 class="text-sm font-black uppercase text-ink">Quyền hạn & Phạm vi tài khoản</h3>
                    <p class="text-xs font-mono text-ink-muted">Chi tiết về vai trò hiện tại của bạn trong hệ thống NewsHub.</p>
                </div>

                <div class="space-y-3">
                    <div class="border border-line bg-paper p-4 space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="size-6 grid place-items-center border border-line-strong bg-lime text-ink font-mono font-bold text-xs">
                                ★
                            </span>
                            <span class="text-xs font-mono font-bold text-ink">
                                Vai trò: <strong class="uppercase underline">{{ auth()->user()->role->value }}</strong>
                            </span>
                        </div>
                        <p class="text-xs leading-relaxed text-ink-muted">
                            @if (auth()->user()->role === \App\Enums\UserRole::Admin)
                                Toàn quyền quản trị hệ thống: duyệt bài viết, phân quyền thành viên, quản lý danh mục, thẻ, kiểm duyệt bình luận và giám sát nhật ký.
                            @elseif (auth()->user()->role === \App\Enums\UserRole::Author)
                                Quyền tác giả: tạo và chỉnh sửa bài viết nháp, theo dõi thống kê lượt xem và gửi bài cho ban biên tập kiểm duyệt.
                            @else
                                Quyền thành viên đọc tin: đọc toàn bộ bài viết, lưu bài viết yêu thích và gửi bình luận tương tác.
                            @endif
                        </p>
                    </div>

                    <!-- Shortcut Links -->
                    <div class="space-y-2 pt-2">
                        <p class="text-[11px] font-mono font-bold uppercase tracking-wider text-ink-muted">Lối tắt nhanh</p>
                        <a href="{{ route('profile.comments') }}" class="flex items-center justify-between border border-line bg-paper p-3 text-xs font-mono font-bold text-ink hover:border-line-strong hover:bg-surface transition-colors">
                            <span>Lịch sử bình luận của tôi</span>
                            <span>&rarr;</span>
                        </a>

                        <a href="{{ route('favorites.index') }}" class="flex items-center justify-between border border-line bg-paper p-3 text-xs font-mono font-bold text-ink hover:border-line-strong hover:bg-surface transition-colors">
                            <span>Danh sách bài viết đã lưu</span>
                            <span>&rarr;</span>
                        </a>

                        @if (auth()->user()->role === \App\Enums\UserRole::Admin || auth()->user()->role === \App\Enums\UserRole::Author)
                            <a href="{{ route('author.posts.index') }}" class="flex items-center justify-between border border-line bg-paper p-3 text-xs font-mono font-bold text-ink hover:border-line-strong hover:bg-surface transition-colors">
                                <span>Quản lý bài viết của tôi</span>
                                <span>&rarr;</span>
                            </a>
                        @endif
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- INTERACTIVE CIRCULAR AVATAR CROPPER MODAL (Pure Vanilla JS & Canvas)     -->
<!-- ========================================================================= -->
<div id="crop-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-ink/60 p-4"
     role="dialog"
     aria-modal="true"
     aria-labelledby="crop-modal-title">
    <div class="relative w-full max-w-md border-2 border-line-strong bg-surface p-6 shadow-brutal space-y-4">
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b-2 border-line-strong pb-3">
            <h3 id="crop-modal-title" class="text-sm font-black uppercase text-ink">Căn chỉnh ảnh đại diện</h3>
            <button type="button" id="btn-cancel-crop-x" class="size-8 grid place-items-center border border-line-strong bg-paper text-ink font-mono font-bold text-sm hover:bg-surface" aria-label="Đóng cửa sổ">
                ✕
            </button>
        </div>

        <!-- Crop Viewport -->
        <div class="flex flex-col items-center space-y-3">
            <!-- Viewport container: 260px x 260px -->
            <div id="crop-canvas-wrapper" class="relative size-64 select-none overflow-hidden border-2 border-line-strong bg-paper cursor-grab active:cursor-grabbing shadow-brutal-sm">
                <canvas id="crop-canvas" width="256" height="256" class="size-full"></canvas>

                <!-- Circular Cutout Overlay: pointer-events-none -->
                <div class="pointer-events-none absolute inset-0 flex items-center justify-center">
                    <div class="size-48 rounded-full border-2 border-lime shadow-[0_0_0_9999px_rgba(23,23,21,0.7)]"></div>
                </div>
            </div>

            <p class="text-center text-[11px] font-mono text-ink-muted">
                Nhấp giữ và kéo để di chuyển ảnh vào tâm vòng tròn
            </p>

            <!-- Zoom Control Slider -->
            <div class="flex w-full items-center gap-3 px-2 font-mono text-xs text-ink-muted">
                <span>Thu nhỏ</span>
                <input id="crop-zoom"
                       type="range"
                       min="1"
                       max="3"
                       step="0.02"
                       value="1"
                       class="h-2 flex-1 cursor-pointer appearance-none bg-paper border border-line-strong accent-ink">
                <span>Phóng to</span>
            </div>

            <!-- Mini Live Circular Preview -->
            <div class="flex items-center gap-3 border border-line bg-paper px-3 py-2">
                <div class="size-10 overflow-hidden rounded-full border-2 border-line-strong">
                    <canvas id="crop-preview-mini" width="40" height="40" class="size-full"></canvas>
                </div>
                <span class="text-xs font-mono font-bold text-ink">Xem trước hiển thị thực tế</span>
            </div>
        </div>

        <!-- Modal Footer Actions -->
        <div class="flex items-center justify-end gap-2 border-t border-line pt-3">
            <button type="button"
                    id="btn-cancel-crop"
                    class="border border-line-strong bg-paper px-4 py-2 text-xs font-mono font-bold uppercase tracking-wider text-ink hover:bg-surface cursor-pointer">
                Hủy
            </button>
            <button type="button"
                    id="btn-apply-crop"
                    class="inline-flex items-center gap-1.5 border-2 border-line-strong bg-lime px-4 py-2 text-xs font-mono font-bold uppercase tracking-wider text-ink shadow-brutal-sm hover:bg-lime-hover cursor-pointer">
                <span>Áp dụng ảnh này</span>
            </button>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- JAVASCRIPT: Cropper Engine & Focus Management                             -->
<!-- ========================================================================= -->
<script>
    (function () {
        const avatarInput = document.getElementById('avatar');
        const btnChooseAvatar = document.getElementById('btn-choose-avatar');
        const avatarStatusText = document.getElementById('avatar-filename-status');
        const formAvatarPreview = document.getElementById('form-avatar-preview');
        const formAvatarFallback = document.getElementById('form-avatar-fallback');
        const headerAvatarImg = document.getElementById('header-avatar-img');
        const headerAvatarFallback = document.getElementById('header-avatar-fallback');

        // Modal elements
        const cropModal = document.getElementById('crop-modal');
        const btnCancelCrop = document.getElementById('btn-cancel-crop');
        const btnCancelCropX = document.getElementById('btn-cancel-crop-x');
        const btnApplyCrop = document.getElementById('btn-apply-crop');
        const cropZoom = document.getElementById('crop-zoom');
        const cropCanvas = document.getElementById('crop-canvas');
        const cropCanvasWrapper = document.getElementById('crop-canvas-wrapper');
        const cropPreviewMini = document.getElementById('crop-preview-mini');

        const ctx = cropCanvas.getContext('2d');
        const miniCtx = cropPreviewMini.getContext('2d');

        let rawImage = new Image();
        let scale = 1;
        let baseScale = 1;
        let imgX = 0;
        let imgY = 0;
        let isDragging = false;
        let startX = 0;
        let startY = 0;

        // Open native file dialog
        btnChooseAvatar.addEventListener('click', () => avatarInput.click());

        // When a file is chosen
        avatarInput.addEventListener('change', (e) => {
            const file = e.target.files?.[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (event) => {
                rawImage = new Image();
                rawImage.onload = () => {
                    initCropModal();
                };
                rawImage.src = event.target.result;
            };
            reader.readAsDataURL(file);
        });

        function initCropModal() {
            // Calculate base scale so image fills the 192px circular area (diameter = 192, canvas = 256)
            const targetSize = 192;
            const minDim = Math.min(rawImage.width, rawImage.height);
            baseScale = targetSize / minDim;
            scale = baseScale;

            cropZoom.value = 1;

            // Center image on canvas (256x256)
            imgX = (256 - rawImage.width * scale) / 2;
            imgY = (256 - rawImage.height * scale) / 2;

            drawCrop();
            cropModal.classList.remove('hidden');
            cropModal.classList.add('flex');
            btnApplyCrop.focus();
        }

        function closeCropModal() {
            cropModal.classList.add('hidden');
            cropModal.classList.remove('flex');
            btnChooseAvatar.focus();
        }

        btnCancelCrop.addEventListener('click', closeCropModal);
        btnCancelCropX.addEventListener('click', closeCropModal);

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !cropModal.classList.contains('hidden')) {
                closeCropModal();
            }
        });

        // Zoom control
        cropZoom.addEventListener('input', (e) => {
            const zoomFactor = parseFloat(e.target.value);
            const prevScale = scale;
            scale = baseScale * zoomFactor;

            // Zoom towards center of canvas (128, 128)
            const centerX = 128;
            const centerY = 128;
            imgX = centerX - (centerX - imgX) * (scale / prevScale);
            imgY = centerY - (centerY - imgY) * (scale / prevScale);

            drawCrop();
        });

        // Dragging / Pan support (Mouse & Touch)
        cropCanvasWrapper.addEventListener('mousedown', (e) => {
            isDragging = true;
            startX = e.clientX - imgX;
            startY = e.clientY - imgY;
        });

        window.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            imgX = e.clientX - startX;
            imgY = e.clientY - startY;
            drawCrop();
        });

        window.addEventListener('mouseup', () => {
            isDragging = false;
        });

        // Touch drag
        cropCanvasWrapper.addEventListener('touchstart', (e) => {
            if (e.touches.length === 1) {
                isDragging = true;
                startX = e.touches[0].clientX - imgX;
                startY = e.touches[0].clientY - imgY;
            }
        }, { passive: true });

        window.addEventListener('touchmove', (e) => {
            if (!isDragging || e.touches.length !== 1) return;
            imgX = e.touches[0].clientX - startX;
            imgY = e.touches[0].clientY - startY;
            drawCrop();
        }, { passive: true });

        window.addEventListener('touchend', () => {
            isDragging = false;
        });

        function drawCrop() {
            ctx.clearRect(0, 0, 256, 256);
            ctx.drawImage(rawImage, imgX, imgY, rawImage.width * scale, rawImage.height * scale);

            // Draw mini circular preview
            miniCtx.clearRect(0, 0, 40, 40);
            miniCtx.save();
            miniCtx.beginPath();
            miniCtx.arc(20, 20, 20, 0, Math.PI * 2);
            miniCtx.clip();

            // The cutout circle in main canvas is centered at (128, 128) with radius 96 (size 192)
            const sx = 128 - 96;
            const sy = 128 - 96;
            const sSize = 192;
            miniCtx.drawImage(cropCanvas, sx, sy, sSize, sSize, 0, 0, 40, 40);
            miniCtx.restore();
        }

        // Apply crop button
        btnApplyCrop.addEventListener('click', () => {
            // Render 512x512 high-res cropped image
            const finalCanvas = document.createElement('canvas');
            finalCanvas.width = 512;
            finalCanvas.height = 512;
            const finalCtx = finalCanvas.getContext('2d');

            // Source rectangle from raw image:
            const sCircleSize = 192 / scale;
            const sCircleX = (128 - 96 - imgX) / scale;
            const sCircleY = (128 - 96 - imgY) / scale;

            finalCtx.drawImage(
                rawImage,
                sCircleX, sCircleY, sCircleSize, sCircleSize,
                0, 0, 512, 512
            );

            // Export as WebP / JPEG Blob and inject into file input
            finalCanvas.toBlob((blob) => {
                if (!blob) return;

                const croppedFile = new File([blob], 'avatar.webp', { type: 'image/webp' });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(croppedFile);
                avatarInput.files = dataTransfer.files;

                // Update live page previews
                const dataUrl = finalCanvas.toDataURL('image/webp');
                formAvatarPreview.src = dataUrl;
                formAvatarPreview.classList.remove('hidden');
                formAvatarFallback.classList.add('hidden');

                if (headerAvatarImg) {
                    headerAvatarImg.src = dataUrl;
                    headerAvatarImg.classList.remove('hidden');
                }
                if (headerAvatarFallback) {
                    headerAvatarFallback.classList.add('hidden');
                }

                const removeAvatarCheckbox = document.getElementById('remove_avatar');
                if (removeAvatarCheckbox) {
                    removeAvatarCheckbox.checked = false;
                }
                formAvatarPreview.classList.remove('opacity-40');

                avatarStatusText.textContent = 'Đã căn chỉnh ảnh (Sẵn sàng lưu)';
                avatarStatusText.classList.remove('text-ink-muted');
                avatarStatusText.classList.add('text-ink', 'font-bold');

                closeCropModal();
            }, 'image/webp', 0.92);
        });

        // Remove avatar checkbox interaction
        const removeAvatarCheckbox = document.getElementById('remove_avatar');
        if (removeAvatarCheckbox) {
            removeAvatarCheckbox.addEventListener('change', () => {
                if (removeAvatarCheckbox.checked) {
                    avatarInput.value = '';
                    avatarStatusText.textContent = 'Sẽ gỡ bỏ ảnh khi lưu';
                    avatarStatusText.classList.remove('text-ink', 'font-bold');
                    avatarStatusText.classList.add('text-danger');
                    formAvatarPreview.classList.add('opacity-40');
                } else {
                    avatarStatusText.textContent = 'Chưa chọn ảnh mới';
                    avatarStatusText.classList.remove('text-danger');
                    avatarStatusText.classList.add('text-ink-muted');
                    formAvatarPreview.classList.remove('opacity-40');
                }
            });
        }
    })();
</script>
@endsection
