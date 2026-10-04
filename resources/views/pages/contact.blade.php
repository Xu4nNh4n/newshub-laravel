@extends('layouts.app', [
    'title' => 'Liên hệ Tòa soạn & Hợp tác Quảng cáo - NewsHub',
    'metaDescription' => 'Thông tin liên hệ Ban Biên tập Tòa soạn Báo điện tử NewsHub, đường dây nóng báo tin 24/7 và thông tin hợp tác quảng cáo truyền thông doanh nghiệp.'
])

@section('content')
<div class="space-y-8 py-2">
    <!-- Breadcrumb -->
    <nav aria-label="Breadcrumb" class="text-xs font-mono text-ink-muted">
        <ol class="flex items-center gap-2 flex-wrap">
            <li><a href="{{ route('home') }}" class="hover:text-ink hover:underline">Trang chủ</a></li>
            <li class="text-line-strong">/</li>
            <li><span>Tòa soạn & Bạn đọc</span></li>
            <li class="text-line-strong">/</li>
            <li class="font-bold text-ink">Liên hệ & Quảng cáo</li>
        </ol>
    </nav>

    <!-- Hero Header -->
    <header class="border-2 border-line-strong bg-surface p-6 sm:p-10 shadow-brutal space-y-4">
        <div class="inline-flex items-center gap-2 border border-line-strong bg-lime px-3 py-1 text-xs font-mono font-bold uppercase tracking-wider text-ink shadow-brutal-sm">
            <span>●</span>
            <span>Đường Dây Nóng 24/7 • Hợp Tác Truyền Thông</span>
        </div>
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black uppercase tracking-tight text-ink">
            Liên Hệ Ban Biên Tập & Hợp Tác Quảng Cáo
        </h1>
        <p class="text-sm sm:text-base leading-relaxed text-ink-muted max-w-4xl">
            Kênh tiếp nhận nguồn tin báo chí thời sự, góp ý nội dung của độc giả và cầu nối truyền thông, booking quảng cáo cho các doanh nghiệp, thương hiệu uy tín.
        </p>
        <div class="flex flex-wrap items-center gap-3 pt-3 border-t border-line text-xs font-mono text-ink-muted">
            <span class="inline-flex items-center gap-1.5">
                <span class="size-2 bg-success inline-block"></span>
                <span>Hotline trực ban:</span>
                <strong class="text-ink font-bold">1900 8888</strong>
            </span>
            <span class="text-line-strong">|</span>
            <span>Giấy phép: <strong class="text-ink font-bold">128/GP-BTTTT</strong></span>
            <span class="text-line-strong">|</span>
            <span>Email: <a href="mailto:toasoan@newshub.vn" class="text-ink underline font-bold">toasoan@newshub.vn</a></span>
        </div>
    </header>

    <!-- Key Statistics & Trust Pillars -->
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="border border-line bg-surface p-4 text-center shadow-brutal-sm">
            <p class="text-2xl sm:text-3xl font-mono font-black text-ink tabular-nums">2.5M+</p>
            <p class="text-xs font-mono text-ink-muted uppercase tracking-wider mt-1">Lượt đọc / tháng</p>
        </div>
        <div class="border border-line bg-surface p-4 text-center shadow-brutal-sm">
            <p class="text-2xl sm:text-3xl font-mono font-black text-ink tabular-nums">85%</p>
            <p class="text-xs font-mono text-ink-muted uppercase tracking-wider mt-1">Độc giả 22-45 tuổi</p>
        </div>
        <div class="border border-line bg-surface p-4 text-center shadow-brutal-sm">
            <p class="text-2xl sm:text-3xl font-mono font-black text-ink tabular-nums">24/7</p>
            <p class="text-xs font-mono text-ink-muted uppercase tracking-wider mt-1">Đường dây nóng</p>
        </div>
        <div class="border border-line bg-surface p-4 text-center shadow-brutal-sm">
            <p class="text-2xl sm:text-3xl font-mono font-black text-ink tabular-nums">&lt; 2h</p>
            <p class="text-xs font-mono text-ink-muted uppercase tracking-wider mt-1">Thẩm định tin nóng</p>
        </div>
    </div>

    <!-- Main Grid: Office Information + Contact Form -->
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12 items-start">
        <!-- Office & Advertising Details (Col 5) -->
        <div class="space-y-6 lg:col-span-5">
            <!-- Tòa soạn chính -->
            <div class="border border-line bg-surface p-5 space-y-4">
                <div class="flex items-center gap-2 text-ink font-mono font-bold text-xs uppercase tracking-wider pb-3 border-b border-line">
                    <span class="size-2 bg-ink inline-block"></span>
                    Hệ Thống Trụ Sở & Văn Phòng
                </div>

                <div class="space-y-4 text-xs">
                    <!-- Trụ sở Hà Nội -->
                    <div class="border-l-2 border-line-strong pl-3 space-y-1">
                        <h3 class="font-bold uppercase tracking-wider text-ink font-mono">Trụ sở chính (Hà Nội):</h3>
                        <p class="text-ink-muted">Tòa nhà Báo chí Tri Thức, Số 68 Phố Báo Chí, Phường Dịch Vọng Hậu, Quận Cầu Giấy, TP. Hà Nội</p>
                        <p class="text-ink font-mono">Điện thoại: (024) 3888 8888</p>
                    </div>

                    <!-- Văn phòng TP.HCM -->
                    <div class="border-l-2 border-line pl-3 space-y-1">
                        <h3 class="font-bold uppercase tracking-wider text-ink font-mono">Văn phòng đại diện TP. Hồ Chí Minh:</h3>
                        <p class="text-ink-muted">Tầng 12, Tòa nhà Báo chí Phương Nam, 123 Phố Nguyễn Huệ, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh</p>
                        <p class="text-ink font-mono">Điện thoại: (028) 3999 9999</p>
                    </div>

                    <!-- Văn phòng Miền Trung -->
                    <div class="border-l-2 border-line pl-3 space-y-1">
                        <h3 class="font-bold uppercase tracking-wider text-ink font-mono">Văn phòng đại diện Miền Trung & Tây Nguyên:</h3>
                        <p class="text-ink-muted">Số 45 Đường Bạch Đằng, Phường Hải Châu 1, Quận Hải Châu, TP. Đà Nẵng</p>
                        <p class="text-ink font-mono">Điện thoại: (0236) 3777 777</p>
                    </div>
                </div>
            </div>

            <!-- Dịch vụ Quảng cáo & Booking -->
            <div class="border-2 border-line-strong bg-paper p-5 space-y-4 shadow-brutal-sm">
                <div class="flex items-center gap-2 text-ink font-mono font-bold text-xs uppercase tracking-wider pb-3 border-b border-line">
                    <span class="bg-lime text-ink px-1.5 py-0.5 text-[10px] font-bold border border-ink">ADS</span>
                    Phòng Quảng Cáo & Truyền Thông
                </div>
                <p class="text-xs text-ink-muted leading-relaxed">
                    Cung cấp các giải pháp truyền thông thương hiệu toàn diện trên hệ sinh thái Báo điện tử NewsHub:
                </p>
                <div class="grid grid-cols-2 gap-2 text-xs font-mono">
                    <div class="border border-line bg-surface p-2 text-ink">
                        • <strong>Bài PR chuyên sâu</strong>
                    </div>
                    <div class="border border-line bg-surface p-2 text-ink">
                        • <strong>E-Magazine</strong>
                    </div>
                    <div class="border border-line bg-surface p-2 text-ink">
                        • <strong>Banner Display IAB</strong>
                    </div>
                    <div class="border border-line bg-surface p-2 text-ink">
                        • <strong>Tài trợ chuyên mục</strong>
                    </div>
                </div>
                <div class="border border-line bg-surface p-3 space-y-1.5 text-xs font-mono">
                    <p class="text-ink">Hotline Booking: <strong class="text-ink font-bold">0988 888 888</strong> (Mr. Hoàng Tuấn)</p>
                    <p class="text-ink">Media Kit & Báo giá: <a href="mailto:quangcao@newshub.vn" class="text-ink underline font-bold">quangcao@newshub.vn</a></p>
                    <p class="text-ink-muted text-[11px] pt-1 border-t border-line">Giờ làm việc: 08h00 - 18h00 (Thứ 2 đến Thứ 6)</p>
                </div>
            </div>

            <!-- Tiếp bạn đọc & Khiếu nại -->
            <div class="border border-line bg-surface p-5 space-y-3 text-xs">
                <h3 class="font-bold uppercase tracking-wider text-ink font-mono pb-2 border-b border-line">Lịch Tiếp Bạn Đọc & Đơn Thư:</h3>
                <p class="text-ink-muted leading-relaxed">
                    Tòa soạn tiếp công dân và bạn đọc trực tiếp tại Phòng Bạn đọc (Tầng 1 Trụ sở chính Hà Nội):
                </p>
                <ul class="list-disc list-inside space-y-1 text-ink pl-1">
                    <li>Buổi sáng: <strong class="font-mono">08h30 - 11h30</strong></li>
                    <li>Buổi chiều: <strong class="font-mono">14h00 - 16h30</strong></li>
                    <li class="text-ink-muted">Áp dụng từ Thứ Hai đến Thứ Sáu (trừ ngày Lễ, Tết).</li>
                </ul>
            </div>
        </div>

        <!-- Interactive Contact Form (Col 7) -->
        <div class="border-2 border-line-strong bg-surface p-6 sm:p-8 lg:col-span-7 space-y-6 shadow-brutal">
            <div class="space-y-2 border-b border-line pb-5">
                <div class="inline-block bg-ink text-paper font-mono text-[10px] uppercase font-bold px-2 py-0.5">Contact Desk</div>
                <h2 class="text-lg sm:text-xl font-black uppercase text-ink">Gửi Thư Trực Tuyến Tới Tòa Soạn</h2>
                <p class="text-xs text-ink-muted leading-relaxed">
                    Vui lòng điền thông tin chi tiết vào biểu mẫu bên dưới. Ban Biên tập hoặc Phòng Truyền thông NewsHub sẽ tiếp nhận và phản hồi nhanh chóng qua email hoặc số điện thoại của bạn.
                </p>
            </div>

            <form action="{{ route('pages.contact.submit') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Name & Email -->
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1.5">
                        <label for="name" class="block text-xs font-mono font-bold uppercase tracking-wider text-ink">
                            Họ và tên <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               id="name"
                               name="name"
                               value="{{ old('name', auth()->user()?->name) }}"
                               required
                               placeholder="Nguyễn Văn A"
                               class="w-full border border-line bg-paper px-3.5 py-2.5 text-xs text-ink placeholder:text-ink-light focus:border-line-strong focus:outline-hidden focus:bg-surface focus:shadow-brutal-sm">
                        @error('name')
                            <p class="text-xs text-danger font-mono">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="email" class="block text-xs font-mono font-bold uppercase tracking-wider text-ink">
                            Địa chỉ email <span class="text-danger">*</span>
                        </label>
                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email', auth()->user()?->email) }}"
                               required
                               placeholder="email@example.com"
                               class="w-full border border-line bg-paper px-3.5 py-2.5 text-xs text-ink placeholder:text-ink-light focus:border-line-strong focus:outline-hidden focus:bg-surface focus:shadow-brutal-sm">
                        @error('email')
                            <p class="text-xs text-danger font-mono">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Phone & Topic -->
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1.5">
                        <label for="phone" class="block text-xs font-mono font-bold uppercase tracking-wider text-ink">
                            Số điện thoại (tùy chọn)
                        </label>
                        <input type="tel"
                               id="phone"
                               name="phone"
                               value="{{ old('phone') }}"
                               placeholder="0912 345 678"
                               class="w-full border border-line bg-paper px-3.5 py-2.5 text-xs text-ink placeholder:text-ink-light focus:border-line-strong focus:outline-hidden focus:bg-surface focus:shadow-brutal-sm">
                        @error('phone')
                            <p class="text-xs text-danger font-mono">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="topic" class="block text-xs font-mono font-bold uppercase tracking-wider text-ink">
                            Chủ đề liên hệ <span class="text-danger">*</span>
                        </label>
                        <select id="topic"
                                name="topic"
                                required
                                class="w-full border border-line bg-paper px-3.5 py-2.5 text-xs text-ink focus:border-line-strong focus:outline-hidden focus:bg-surface focus:shadow-brutal-sm">
                            <option value="">-- Chọn chủ đề cần liên hệ --</option>
                            <option value="hotline" @selected(old('topic') === 'hotline')>Báo tin nóng / Cung cấp nguồn tin báo chí</option>
                            <option value="advertising" @selected(old('topic') === 'advertising')>Hợp tác quảng cáo / Booking bài PR</option>
                            <option value="correction" @selected(old('topic') === 'correction')>Góp ý nội dung / Đề nghị đính chính</option>
                            <option value="copyright" @selected(old('topic') === 'copyright')>Bản quyền tác phẩm / Khiếu nại pháp lý</option>
                            <option value="other" @selected(old('topic') === 'other')>Ý kiến đóng góp & Nội dung khác</option>
                        </select>
                        @error('topic')
                            <p class="text-xs text-danger font-mono">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Subject -->
                <div class="space-y-1.5">
                    <label for="subject" class="block text-xs font-mono font-bold uppercase tracking-wider text-ink">
                        Tiêu đề liên hệ <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           id="subject"
                           name="subject"
                           value="{{ old('subject') }}"
                           required
                           placeholder="Tóm tắt ngắn gọn nội dung liên hệ..."
                           class="w-full border border-line bg-paper px-3.5 py-2.5 text-xs text-ink placeholder:text-ink-light focus:border-line-strong focus:outline-hidden focus:bg-surface focus:shadow-brutal-sm">
                    @error('subject')
                        <p class="text-xs text-danger font-mono">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Message -->
                <div class="space-y-1.5">
                    <label for="message" class="block text-xs font-mono font-bold uppercase tracking-wider text-ink">
                        Nội dung chi tiết <span class="text-danger">*</span>
                    </label>
                    <textarea id="message"
                              name="message"
                              rows="5"
                              required
                              placeholder="Trình bày chi tiết thông tin sự việc, tài liệu đính kèm (link đám mây), yêu cầu hợp tác hoặc phản hồi của bạn..."
                              class="w-full border border-line bg-paper px-3.5 py-2.5 text-xs text-ink placeholder:text-ink-light focus:border-line-strong focus:outline-hidden focus:bg-surface focus:shadow-brutal-sm leading-relaxed">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="text-xs text-danger font-mono">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="inline-flex w-full sm:w-auto items-center justify-center gap-2 border-2 border-line-strong bg-lime px-6 py-3 text-xs font-mono font-bold uppercase tracking-wider text-ink hover:bg-lime-hover shadow-brutal hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-brutal-sm transition-all cursor-pointer">
                        <svg class="size-4" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M3.105 2.289a.75.75 0 00-.826.95l1.414 4.925A1.5 1.5 0 004.835 9.25h4.415a.75.75 0 010 1.5H4.835a1.5 1.5 0 00-1.142 1.086l-1.414 4.926a.75.75 0 00.826.95 28.896 28.896 0 0015.293-7.154.75.75 0 000-1.114A28.897 28.897 0 003.105 2.289z" />
                        </svg>
                        Gửi Thông Tin Tới Tòa Soạn
                    </button>
                </div>
            </form>

            <div class="border border-line bg-paper p-3.5 text-xs font-mono text-ink-muted leading-relaxed">
                <strong class="text-ink uppercase">Cam kết bảo mật:</strong> Mọi thông tin danh tính người cung cấp nguồn tin hoặc hồ sơ doanh nghiệp booking quảng cáo đều được Tòa soạn NewsHub bảo mật tuyệt đối theo Điều 25 Luật Báo chí và Quy chế bảo vệ dữ liệu cá nhân.
            </div>
        </div>
    </div>
</div>
@endsection
