@extends('layouts.app', [
    'title' => 'Chính sách bảo mật thông tin - NewsHub',
    'metaDescription' => 'Chính sách bảo vệ thông tin cá nhân và dữ liệu độc giả trên Báo điện tử NewsHub, tuân thủ Nghị định 13/2023/NĐ-CP và Luật Báo chí Việt Nam.'
])

@section('content')
<div class="space-y-8 py-2">
    <!-- Breadcrumb -->
    <nav aria-label="Breadcrumb" class="text-xs font-mono text-ink-muted">
        <ol class="flex items-center gap-2 flex-wrap">
            <li><a href="{{ route('home') }}" class="hover:text-ink hover:underline">Trang chủ</a></li>
            <li class="text-line-strong">/</li>
            <li><span>Tiêu chuẩn & Pháp lý</span></li>
            <li class="text-line-strong">/</li>
            <li class="font-bold text-ink">Chính sách bảo mật</li>
        </ol>
    </nav>

    <!-- Hero Header -->
    <header class="border-2 border-line-strong bg-surface p-6 sm:p-10 shadow-brutal space-y-4">
        <div class="inline-flex items-center gap-2 border border-line-strong bg-lime px-3 py-1 text-xs font-mono font-bold uppercase tracking-wider text-ink shadow-brutal-sm">
            <span>●</span>
            <span>Nghị định 13/2023/NĐ-CP & Luật Báo chí</span>
        </div>
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black uppercase tracking-tight text-ink">
            Chính Sách Bảo Mật Thông Tin & Dữ Liệu Độc Giả
        </h1>
        <p class="text-sm sm:text-base leading-relaxed text-ink-muted max-w-4xl">
            Tòa soạn Báo điện tử NewsHub cam kết bảo vệ toàn vẹn dữ liệu cá nhân, tôn trọng quyền riêng tư của quý độc giả, tác giả và đối tác theo chuẩn mực an toàn cao nhất của pháp luật Việt Nam.
        </p>
        <div class="flex flex-wrap items-center gap-3 pt-3 border-t border-line text-xs font-mono text-ink-muted">
            <span>Hiệu lực: <strong class="text-ink">01/01/2026</strong></span>
            <span class="text-line-strong">|</span>
            <span>Cập nhật: <strong class="text-ink">15/09/2026</strong></span>
            <span class="text-line-strong">|</span>
            <span class="bg-paper border border-line px-2 py-0.5 text-ink font-bold">Văn bản: 04/QĐ-NEWSHUB-BM</span>
        </div>
    </header>

    <!-- Main Grid: Sidebar Navigator + Article Content -->
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12 items-start">
        <!-- Sticky Sidebar Navigation -->
        <aside class="space-y-6 lg:col-span-4 lg:sticky lg:top-24">
            <!-- Table of contents card -->
            <div class="border-2 border-line-strong bg-surface p-5 shadow-brutal-sm space-y-4">
                <h2 class="text-xs font-mono font-black uppercase tracking-wider text-ink flex items-center justify-between pb-3 border-b border-line">
                    <span>Mục Lục Văn Bản</span>
                    <span class="bg-lime text-ink px-1.5 py-0.5 text-[10px] font-bold border border-ink">TOC</span>
                </h2>
                <nav class="space-y-1.5 text-xs font-medium">
                    <a href="#dieu-1" class="block py-1.5 px-2 border-l-2 border-transparent text-ink-muted hover:border-line-strong hover:bg-paper hover:text-ink transition-colors">1. Căn cứ pháp lý & phạm vi áp dụng</a>
                    <a href="#dieu-2" class="block py-1.5 px-2 border-l-2 border-transparent text-ink-muted hover:border-line-strong hover:bg-paper hover:text-ink transition-colors">2. Dữ liệu cá nhân thu thập</a>
                    <a href="#dieu-3" class="block py-1.5 px-2 border-l-2 border-transparent text-ink-muted hover:border-line-strong hover:bg-paper hover:text-ink transition-colors">3. Mục đích sử dụng thông tin</a>
                    <a href="#dieu-4" class="block py-1.5 px-2 border-l-2 border-transparent text-ink-muted hover:border-line-strong hover:bg-paper hover:text-ink transition-colors">4. Cơ chế lưu trữ & bảo mật</a>
                    <a href="#dieu-5" class="block py-1.5 px-2 border-l-2 border-transparent text-ink-muted hover:border-line-strong hover:bg-paper hover:text-ink transition-colors">5. Chia sẻ thông tin bên thứ ba</a>
                    <a href="#dieu-6" class="block py-1.5 px-2 border-l-2 border-transparent text-ink-muted hover:border-line-strong hover:bg-paper hover:text-ink transition-colors">6. Quyền & nghĩa vụ của bạn đọc</a>
                    <a href="#dieu-7" class="block py-1.5 px-2 border-l-2 border-transparent text-ink-muted hover:border-line-strong hover:bg-paper hover:text-ink transition-colors">7. Chính sách Cookies</a>
                    <a href="#dieu-8" class="block py-1.5 px-2 border-l-2 border-transparent text-ink-muted hover:border-line-strong hover:bg-paper hover:text-ink transition-colors">8. Đầu mối tiếp nhận khiếu nại</a>
                </nav>
            </div>

            <!-- Legal Documents Quick Switcher -->
            <div class="border border-line bg-surface p-5 space-y-3">
                <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-ink-muted pb-2 border-b border-line">Văn bản liên quan</h3>
                <ul class="space-y-2 text-xs">
                    <li>
                        <a href="{{ route('pages.terms') }}" class="group flex items-center justify-between p-2 border border-transparent hover:border-line hover:bg-paper transition-colors">
                            <span class="font-bold text-ink group-hover:underline">Điều khoản dịch vụ độc giả</span>
                            <span class="font-mono text-ink-muted group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pages.moderation-policy') }}" class="group flex items-center justify-between p-2 border border-transparent hover:border-line hover:bg-paper transition-colors">
                            <span class="font-bold text-ink group-hover:underline">Quy chế kiểm duyệt nội dung</span>
                            <span class="font-mono text-ink-muted group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pages.contact') }}" class="group flex items-center justify-between p-2 border border-transparent hover:border-line hover:bg-paper transition-colors">
                            <span class="font-bold text-ink group-hover:underline">Liên hệ quảng cáo & Tòa soạn</span>
                            <span class="font-mono text-ink-muted group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Direct Contact Card -->
            <div class="border-2 border-line-strong bg-paper p-5 space-y-3 text-xs shadow-brutal-sm">
                <div class="flex items-center gap-2 text-ink font-bold uppercase font-mono tracking-wider">
                    <span class="bg-ink text-paper size-4 inline-flex items-center justify-center text-[10px]">i</span>
                    Bộ phận Bảo vệ Dữ liệu (DPO)
                </div>
                <p class="text-ink-muted leading-relaxed">
                    Quý độc giả có bất kỳ câu hỏi, yêu cầu tra cứu hoặc đề nghị xóa dữ liệu cá nhân, xin gửi về:
                </p>
                <div class="space-y-1.5 font-mono pt-2 border-t border-line text-xs">
                    <p class="text-ink-muted">Email: <a href="mailto:privacy@newshub.vn" class="text-ink font-bold underline">privacy@newshub.vn</a></p>
                    <p class="text-ink-muted">Hotline: <strong class="text-ink font-bold">1900 8888</strong> (Nhánh 3)</p>
                </div>
            </div>
        </aside>

        <!-- Document Body Content -->
        <article class="space-y-8 lg:col-span-8 text-ink leading-relaxed text-sm">
            <!-- Điều 1 -->
            <section id="dieu-1" class="scroll-mt-24 border border-line bg-surface p-6 sm:p-8 space-y-4">
                <div class="flex items-center gap-3 pb-3 border-b border-line">
                    <span class="size-7 grid place-items-center bg-ink text-paper font-mono font-bold text-xs">01</span>
                    <h2 class="text-lg font-black uppercase text-ink">Điều 1: Căn Cứ Pháp Lý & Phạm Vi Áp Dụng</h2>
                </div>
                <p>
                    Chính sách bảo mật này thiết lập quy chuẩn và phương thức Tòa soạn Báo điện tử <strong>NewsHub</strong> thu thập, xử lý, lưu trữ, sử dụng và bảo vệ thông tin nhận dạng cá nhân khi người dùng truy cập địa chỉ <code class="border border-line bg-paper px-1.5 py-0.5 text-ink font-mono text-xs">https://newshub.vn</code> hoặc sử dụng bất kỳ ứng dụng, dịch vụ trực tuyến liên quan.
                </p>
                <p>Chính sách này được xây dựng phù hợp và tuân thủ các quy định hiện hành:</p>
                <ul class="list-disc list-inside space-y-1.5 text-ink-muted pl-2 text-xs sm:text-sm">
                    <li><strong class="text-ink">Nghị định số 13/2023/NĐ-CP</strong> ngày 17/04/2023 của Chính phủ về Bảo vệ dữ liệu cá nhân;</li>
                    <li><strong class="text-ink">Luật An ninh mạng số 24/2018/QH14</strong> của Quốc hội nước CHXHCN Việt Nam;</li>
                    <li><strong class="text-ink">Luật An toàn thông tin mạng số 86/2015/QH13</strong>;</li>
                    <li><strong class="text-ink">Luật Báo chí số 103/2016/QH13</strong> và các văn bản chỉ đạo của Bộ Thông tin & Truyền thông.</li>
                </ul>
            </section>

            <!-- Điều 2 -->
            <section id="dieu-2" class="scroll-mt-24 border border-line bg-surface p-6 sm:p-8 space-y-4">
                <div class="flex items-center gap-3 pb-3 border-b border-line">
                    <span class="size-7 grid place-items-center bg-ink text-paper font-mono font-bold text-xs">02</span>
                    <h2 class="text-lg font-black uppercase text-ink">Điều 2: Các Loại Dữ Liệu Cá Nhân Thu Thập</h2>
                </div>
                <p>Để phục vụ độc giả tốt nhất và đáp ứng quy định pháp lý về quản lý thông tin mạng, hệ thống thu thập các nhóm dữ liệu:</p>
                <div class="grid gap-4 sm:grid-cols-2 pt-2">
                    <div class="border border-line bg-paper p-4 space-y-2">
                        <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-ink border-b border-line pb-1">Dữ liệu độc giả chủ động cung cấp</h3>
                        <p class="text-xs text-ink-muted leading-relaxed">
                            Họ và tên, địa chỉ email, số điện thoại, mật khẩu tài khoản đã mã hóa một chiều, ảnh đại diện tải lên và tiểu sử giới thiệu.
                        </p>
                    </div>
                    <div class="border border-line bg-paper p-4 space-y-2">
                        <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-ink border-b border-line pb-1">Dữ liệu Tác giả & CTV</h3>
                        <p class="text-xs text-ink-muted leading-relaxed">
                            Thông tin hồ sơ năng lực báo chí, số Căn cước công dân và Mã số thuế cá nhân phục vụ kê khai chi trả nhuận bút theo quy định của Tổng cục Thuế.
                        </p>
                    </div>
                    <div class="border border-line bg-paper p-4 space-y-2">
                        <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-ink border-b border-line pb-1">Dữ liệu kỹ thuật tự động</h3>
                        <p class="text-xs text-ink-muted leading-relaxed">
                            Địa chỉ giao thức Internet (IP), loại trình duyệt, hệ điều hành, thời gian truy cập, chỉ số tải trang nhằm chống tấn công DDoS và tối ưu hiệu năng.
                        </p>
                    </div>
                    <div class="border border-line bg-paper p-4 space-y-2">
                        <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-ink border-b border-line pb-1">Dữ liệu tương tác cộng đồng</h3>
                        <p class="text-xs text-ink-muted leading-relaxed">
                            Danh sách bài viết đã lưu yêu thích, bình luận độc giả gửi công khai, lượt báo cáo sai phạm nội dung và lịch sử tìm kiếm từ khóa.
                        </p>
                    </div>
                </div>
            </section>

            <!-- Điều 3 -->
            <section id="dieu-3" class="scroll-mt-24 border border-line bg-surface p-6 sm:p-8 space-y-4">
                <div class="flex items-center gap-3 pb-3 border-b border-line">
                    <span class="size-7 grid place-items-center bg-ink text-paper font-mono font-bold text-xs">03</span>
                    <h2 class="text-lg font-black uppercase text-ink">Điều 3: Mục Đích Xử Lý & Sử Dụng Thông Tin</h2>
                </div>
                <p>Toàn bộ dữ liệu thu thập chỉ được phục vụ cho các mục đích chính đáng sau:</p>
                <ul class="list-disc list-inside space-y-2 text-ink-muted pl-2 text-xs sm:text-sm">
                    <li><strong class="text-ink">Vận hành cổng thông tin:</strong> Đảm bảo hệ thống phát hành tin bài tức thời, duy trì phiên đăng nhập và các tính năng tương tác.</li>
                    <li><strong class="text-ink">Cá nhân hóa nội dung:</strong> Đề xuất các luồng tin thời sự, công nghệ, kinh tế phù hợp với thói quen đọc của từng cá nhân.</li>
                    <li><strong class="text-ink">Xác thực tài khoản & Khôi phục mật khẩu:</strong> Gửi mã xác minh email, thông báo bảo mật và liên kết cấp lại mật khẩu an toàn.</li>
                    <li><strong class="text-ink">Quản lý nhuận bút & Bản quyền:</strong> Kiểm duyệt quy trình xuất bản của Tác giả, xác minh tính nguyên gốc của tác phẩm báo chí.</li>
                    <li><strong class="text-ink">An ninh thông tin & Ngăn chặn tội phạm:</strong> Phát hiện kịp thời các hành vi gian lận, phát tán mã độc, spam quảng cáo bẩn hoặc tấn công mạng.</li>
                </ul>
            </section>

            <!-- Điều 4 -->
            <section id="dieu-4" class="scroll-mt-24 border border-line bg-surface p-6 sm:p-8 space-y-4">
                <div class="flex items-center gap-3 pb-3 border-b border-line">
                    <span class="size-7 grid place-items-center bg-ink text-paper font-mono font-bold text-xs">04</span>
                    <h2 class="text-lg font-black uppercase text-ink">Điều 4: Cơ Chế Lưu Trữ & Bảo Mật Dữ Liệu</h2>
                </div>
                <p>
                    Tòa soạn áp dụng các biện pháp an ninh mạng đa tầng để bảo vệ dữ liệu khỏi nguy cơ mất mát, rò rỉ, truy cập trái phép hoặc can thiệp phá hoại:
                </p>
                <div class="space-y-3 text-xs sm:text-sm">
                    <div class="flex items-start gap-3 border border-line bg-paper p-3.5">
                        <span class="font-mono font-bold text-ink">01/</span>
                        <div>
                            <strong class="text-ink">Mã hóa chuẩn cao:</strong>
                            Mọi luồng dữ liệu truyền tải giữa thiết bị độc giả và máy chủ NewsHub đều được mã hóa bằng chứng chỉ SSL/TLS 256-bit. Mật khẩu người dùng được băm một chiều bằng thuật toán Argon2/Bcrypt không thể dịch ngược.
                        </div>
                    </div>
                    <div class="flex items-start gap-3 border border-line bg-paper p-3.5">
                        <span class="font-mono font-bold text-ink">02/</span>
                        <div>
                            <strong class="text-ink">Phân quyền nghiêm ngặt (RBAC):</strong>
                            Chỉ nhân sự kỹ thuật và quản trị viên được giao trách nhiệm cụ thể mới có quyền truy cập cơ sở dữ liệu. Mọi thao tác truy vấn đều được ghi nhận vào hệ thống nhật ký hoạt động (Audit Logs) không thể xóa sửa.
                        </div>
                    </div>
                    <div class="flex items-start gap-3 border border-line bg-paper p-3.5">
                        <span class="font-mono font-bold text-ink">03/</span>
                        <div>
                            <strong class="text-ink">Sao lưu dự phòng (Backups):</strong>
                            Dữ liệu được sao lưu định kỳ đa điểm theo mô hình cô lập, sẵn sàng phục hồi khi có sự cố thiên tai hoặc lỗi phần cứng ngoài ý muốn.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Điều 5 -->
            <section id="dieu-5" class="scroll-mt-24 border border-line bg-surface p-6 sm:p-8 space-y-4">
                <div class="flex items-center gap-3 pb-3 border-b border-line">
                    <span class="size-7 grid place-items-center bg-ink text-paper font-mono font-bold text-xs">05</span>
                    <h2 class="text-lg font-black uppercase text-ink">Điều 5: Chia Sẻ Thông Tin Với Bên Thứ Ba</h2>
                </div>
                <div class="border-2 border-line-strong bg-lime p-4 text-xs font-medium text-ink shadow-brutal-sm">
                    <strong class="uppercase font-mono tracking-wider">Cam kết danh dự:</strong> NewsHub tuyệt đối không bán, cho thuê, trao đổi thông tin dữ liệu của bạn đọc cho bất kỳ bên thứ ba nào vì mục đích quảng cáo rác hay thương mại trái phép.
                </div>
                <p>Thông tin chỉ được cung cấp trong các trường hợp giới hạn sau:</p>
                <ul class="list-disc list-inside space-y-1.5 text-ink-muted pl-2 text-xs sm:text-sm">
                    <li>Khi có văn bản yêu cầu chính thức từ Cơ quan Công an, Tòa án hoặc Viện kiểm sát nhân dân theo đúng quy định của Bộ luật Tố tụng Hình sự;</li>
                    <li>Các nhà cung cấp hạ tầng máy chủ đám mây, mạng phân phối nội dung (CDN) đã ký kết thỏa thuận bảo mật dữ liệu nghiêm ngặt (NDA);</li>
                    <li>Trường hợp khẩn cấp nhằm bảo vệ an toàn tính mạng, tài sản của độc giả hoặc ngăn ngừa hành vi khủng bố, phá hoại an ninh quốc gia.</li>
                </ul>
            </section>

            <!-- Điều 6 -->
            <section id="dieu-6" class="scroll-mt-24 border border-line bg-surface p-6 sm:p-8 space-y-4">
                <div class="flex items-center gap-3 pb-3 border-b border-line">
                    <span class="size-7 grid place-items-center bg-ink text-paper font-mono font-bold text-xs">06</span>
                    <h2 class="text-lg font-black uppercase text-ink">Điều 6: Quyền Của Bạn Đọc Đối Với Dữ Liệu Cá Nhân</h2>
                </div>
                <p>Theo Nghị định 13/2023/NĐ-CP, quý độc giả có đầy đủ các quyền sau:</p>
                <div class="grid gap-3 sm:grid-cols-2 text-xs">
                    <div class="border border-line bg-paper p-3.5 space-y-1">
                        <strong class="text-ink font-mono uppercase">Quyền tiếp cận & chỉnh sửa:</strong>
                        <p class="text-ink-muted">Độc giả có quyền truy cập trang <a href="{{ route('profile.edit') }}" class="text-ink font-bold underline">Hồ sơ cá nhân</a> để chỉnh sửa tên, ảnh đại diện hoặc thay đổi mật khẩu bất kỳ lúc nào.</p>
                    </div>
                    <div class="border border-line bg-paper p-3.5 space-y-1">
                        <strong class="text-ink font-mono uppercase">Quyền yêu cầu xóa dữ liệu:</strong>
                        <p class="text-ink-muted">Bạn đọc có quyền yêu cầu tòa soạn xóa toàn bộ thông tin cá nhân và tài khoản trên hệ thống NewsHub.</p>
                    </div>
                    <div class="border border-line bg-paper p-3.5 space-y-1">
                        <strong class="text-ink font-mono uppercase">Quyền phản đối xử lý:</strong>
                        <p class="text-ink-muted">Từ chối nhận các email thông báo tin mới, bản tin tổng hợp tuần (Newsletter) bằng cách hủy đăng ký trực tiếp ở chân email.</p>
                    </div>
                    <div class="border border-line bg-paper p-3.5 space-y-1">
                        <strong class="text-ink font-mono uppercase">Quyền khiếu nại:</strong>
                        <p class="text-ink-muted">Gửi văn bản khiếu nại tới Tòa soạn hoặc Cơ quan chức năng nếu phát hiện dữ liệu của mình bị xâm phạm.</p>
                    </div>
                </div>
            </section>

            <!-- Điều 7 -->
            <section id="dieu-7" class="scroll-mt-24 border border-line bg-surface p-6 sm:p-8 space-y-4">
                <div class="flex items-center gap-3 pb-3 border-b border-line">
                    <span class="size-7 grid place-items-center bg-ink text-paper font-mono font-bold text-xs">07</span>
                    <h2 class="text-lg font-black uppercase text-ink">Điều 7: Chính Sách Cookie & Công Nghệ Đo Lường</h2>
                </div>
                <p>
                    Website NewsHub sử dụng <strong>Cookie</strong> (tệp văn bản nhỏ được lưu trên thiết bị của bạn) nhằm ghi nhớ phiên đăng nhập an toàn, bảo vệ chống giả mạo liên kết (CSRF) và đo lường lưu lượng xem tin để cải thiện nội dung.
                </p>
                <p class="text-ink-muted">
                    Bạn có thể tùy chọn tắt cookie trong phần Cài đặt trình duyệt (Chrome, Safari, Edge, Firefox). Tuy nhiên, việc tắt hoàn toàn cookie có thể khiến một số tính năng như ghi nhớ tài khoản hoặc lưu bài viết yêu thích hoạt động không chính xác.
                </p>
            </section>

            <!-- Điều 8 -->
            <section id="dieu-8" class="scroll-mt-24 border border-line bg-surface p-6 sm:p-8 space-y-4">
                <div class="flex items-center gap-3 pb-3 border-b border-line">
                    <span class="size-7 grid place-items-center bg-ink text-paper font-mono font-bold text-xs">08</span>
                    <h2 class="text-lg font-black uppercase text-ink">Điều 8: Đầu Mối Tiếp Nhận & Giải Quyết Khiếu Nại</h2>
                </div>
                <p>
                    Mọi ý kiến đóng góp, khiếu nại hoặc yêu cầu thực thi quyền chủ thể dữ liệu xin vui lòng gửi về Ban Bảo vệ Dữ liệu Báo điện tử NewsHub:
                </p>
                <div class="border border-line bg-paper p-4 space-y-2 text-xs font-mono">
                    <p><strong class="text-ink uppercase">Cơ quan:</strong> Tòa soạn Báo điện tử NewsHub - Ban Bảo vệ Dữ liệu Số</p>
                    <p><strong class="text-ink uppercase">Địa chỉ:</strong> Tòa nhà Báo chí Tri Thức, Số 68 Phố Báo Chí, Quận Cầu Giấy, TP. Hà Nội</p>
                    <p><strong class="text-ink uppercase">Email DPO:</strong> <a href="mailto:privacy@newshub.vn" class="text-ink font-bold underline">privacy@newshub.vn</a></p>
                    <p><strong class="text-ink uppercase">Đường dây nóng:</strong> 1900 8888 (Hoạt động 08h00 - 18h00 từ Thứ 2 đến Thứ 6)</p>
                    <p class="text-ink-muted text-[11px] pt-1 border-t border-line">Thời gian giải quyết khiếu nại: Trong vòng 48 giờ làm việc kể từ thời điểm tiếp nhận.</p>
                </div>
            </section>
        </article>
    </div>
</div>
@endsection
