@extends('layouts.app', [
    'title' => 'Điều khoản dịch vụ độc giả - NewsHub',
    'metaDescription' => 'Điều khoản sử dụng, quy định bản quyền báo chí và tiêu chuẩn cộng đồng bình luận trên Báo điện tử NewsHub.'
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
            <li class="font-bold text-ink">Điều khoản dịch vụ</li>
        </ol>
    </nav>

    <!-- Hero Header -->
    <header class="border-2 border-line-strong bg-surface p-6 sm:p-10 shadow-brutal space-y-4">
        <div class="inline-flex items-center gap-2 border border-line-strong bg-lime px-3 py-1 text-xs font-mono font-bold uppercase tracking-wider text-ink shadow-brutal-sm">
            <span>●</span>
            <span>Thỏa Ước Người Dùng • Chuẩn Mực Báo Chí</span>
        </div>
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black uppercase tracking-tight text-ink">
            Điều Khoản Dịch Vụ Độc Giả & Tiêu Chuẩn Sử Dụng
        </h1>
        <p class="text-sm sm:text-base leading-relaxed text-ink-muted max-w-4xl">
            Thỏa ước pháp lý giữa Báo điện tử NewsHub và quý bạn đọc, quy định chi tiết về quyền sở hữu trí tuệ tác phẩm, quy tắc tham gia bình luận văn minh và cơ chế vận hành nội dung.
        </p>
        <div class="flex flex-wrap items-center gap-3 pt-3 border-t border-line text-xs font-mono text-ink-muted">
            <span>Hiệu lực: <strong class="text-ink">01/01/2026</strong></span>
            <span class="text-line-strong">|</span>
            <span>Cập nhật: <strong class="text-ink">15/09/2026</strong></span>
            <span class="text-line-strong">|</span>
            <span class="bg-paper border border-line px-2 py-0.5 text-ink font-bold">Văn bản: 02/QĐ-NEWSHUB-DK</span>
        </div>
    </header>

    <!-- Main Content Area -->
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12 items-start">
        <!-- Sidebar Navigation -->
        <aside class="space-y-6 lg:col-span-4 lg:sticky lg:top-24">
            <!-- Table of Contents -->
            <div class="border-2 border-line-strong bg-surface p-5 shadow-brutal-sm space-y-4">
                <h2 class="text-xs font-mono font-black uppercase tracking-wider text-ink flex items-center justify-between pb-3 border-b border-line">
                    <span>Mục Lục Điều Khoản</span>
                    <span class="bg-lime text-ink px-1.5 py-0.5 text-[10px] font-bold border border-ink">TOC</span>
                </h2>
                <nav class="space-y-1.5 text-xs font-medium">
                    <a href="#dieu-1" class="block py-1.5 px-2 border-l-2 border-transparent text-ink-muted hover:border-line-strong hover:bg-paper hover:text-ink transition-colors">1. Chấp thuận các điều khoản</a>
                    <a href="#dieu-2" class="block py-1.5 px-2 border-l-2 border-transparent text-ink-muted hover:border-line-strong hover:bg-paper hover:text-ink transition-colors">2. Bản quyền & Sở hữu trí tuệ</a>
                    <a href="#dieu-3" class="block py-1.5 px-2 border-l-2 border-transparent text-ink-muted hover:border-line-strong hover:bg-paper hover:text-ink transition-colors">3. Quy chuẩn bình luận cộng đồng</a>
                    <a href="#dieu-4" class="block py-1.5 px-2 border-l-2 border-transparent text-ink-muted hover:border-line-strong hover:bg-paper hover:text-ink transition-colors">4. Trách nhiệm Tác giả & CTV</a>
                    <a href="#dieu-5" class="block py-1.5 px-2 border-l-2 border-transparent text-ink-muted hover:border-line-strong hover:bg-paper hover:text-ink transition-colors">5. Tuyên bố miễn trừ trách nhiệm</a>
                    <a href="#dieu-6" class="block py-1.5 px-2 border-l-2 border-transparent text-ink-muted hover:border-line-strong hover:bg-paper hover:text-ink transition-colors">6. Luật áp dụng & Giải quyết tranh chấp</a>
                </nav>
            </div>

            <!-- Legal Documents Quick Switcher -->
            <div class="border border-line bg-surface p-5 space-y-3">
                <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-ink-muted pb-2 border-b border-line">Văn bản liên quan</h3>
                <ul class="space-y-2 text-xs">
                    <li>
                        <a href="{{ route('pages.privacy') }}" class="group flex items-center justify-between p-2 border border-transparent hover:border-line hover:bg-paper transition-colors">
                            <span class="font-bold text-ink group-hover:underline">Chính sách bảo mật thông tin</span>
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

            <!-- Editorial Notice Box -->
            <div class="border-2 border-line-strong bg-paper p-5 space-y-2 text-xs text-ink shadow-brutal-sm">
                <div class="font-mono font-bold uppercase tracking-wider flex items-center gap-1.5 text-ink">
                    <span class="bg-ink text-paper size-4 inline-flex items-center justify-center text-[10px]">!</span>
                    Lưu ý về bản quyền tác phẩm
                </div>
                <p class="leading-relaxed text-ink-muted">
                    Nghiêm cấm sao chép nguyên văn tin bài, hình ảnh của NewsHub dưới mọi hình thức thương mại khi chưa có chấp thuận chính thức từ Ban Biên tập.
                </p>
            </div>
        </aside>

        <!-- Document Body Articles -->
        <article class="space-y-8 lg:col-span-8 text-ink leading-relaxed text-sm">
            <!-- Điều 1 -->
            <section id="dieu-1" class="scroll-mt-24 border border-line bg-surface p-6 sm:p-8 space-y-4">
                <div class="flex items-center gap-3 pb-3 border-b border-line">
                    <span class="size-7 grid place-items-center bg-ink text-paper font-mono font-bold text-xs">01</span>
                    <h2 class="text-lg font-black uppercase text-ink">Điều 1: Chấp Thuận Các Điều Khoản Sử Dụng</h2>
                </div>
                <p>
                    Chào mừng quý độc giả đến với Báo điện tử <strong>NewsHub</strong> tại địa chỉ <code class="border border-line bg-paper px-1.5 py-0.5 text-ink font-mono text-xs">newshub.vn</code>. Khi bạn truy cập trang web, đọc tin tức, chia sẻ bài viết, đăng ký thành viên hoặc tương tác bình luận, bạn được xem là đã đọc, hiểu và đồng ý vô điều kiện với tất cả các điều khoản được quy định trong thỏa ước này.
                </p>
                <p class="text-ink-muted">
                    Nếu bạn không đồng ý với bất kỳ phần nào của các điều khoản, vui lòng dừng việc truy cập và sử dụng dịch vụ của cổng thông tin NewsHub.
                </p>
            </section>

            <!-- Điều 2 -->
            <section id="dieu-2" class="scroll-mt-24 border border-line bg-surface p-6 sm:p-8 space-y-4">
                <div class="flex items-center gap-3 pb-3 border-b border-line">
                    <span class="size-7 grid place-items-center bg-ink text-paper font-mono font-bold text-xs">02</span>
                    <h2 class="text-lg font-black uppercase text-ink">Điều 2: Quyền Sở Hữu Trí Tuệ & Bản Quyền Báo Chí</h2>
                </div>
                <p>
                    Toàn bộ tài nguyên trên NewsHub (bao gồm bài viết, phóng sự, tiêu đề, ảnh chụp thời sự, infographic, video clip, đồ họa và logo thương hiệu) đều thuộc quyền sở hữu trí tuệ của Tòa soạn NewsHub hoặc tác giả được cấp phép, được pháp luật Việt Nam và quốc tế bảo hộ:
                </p>
                <div class="space-y-3 pt-1 text-xs sm:text-sm">
                    <div class="border border-line bg-paper p-4 space-y-2">
                        <strong class="text-ink font-mono uppercase tracking-wider block border-b border-line pb-1">
                            Quy định trích dẫn nguồn hợp lệ:
                        </strong>
                        <p class="text-ink-muted leading-relaxed">
                            Cá nhân và các đơn vị truyền thông được phép trích dẫn thông tin từ NewsHub với dung lượng tối đa <strong>không quá 150 từ</strong>, đồng thời phải nêu rõ ràng: <em>"Theo Báo điện tử NewsHub"</em> và gắn đường dẫn siêu liên kết (hyperlink) trỏ trực tiếp đến bài viết gốc trên website NewsHub.
                        </p>
                    </div>
                    <div class="border-2 border-line-strong bg-surface p-4 space-y-2">
                        <strong class="text-danger font-mono uppercase tracking-wider block border-b border-line pb-1">
                            Các hành vi bị nghiêm cấm tuyệt đối:
                        </strong>
                        <ul class="list-disc list-inside space-y-1.5 text-ink-muted text-xs">
                            <li>Sao chép toàn bộ hoặc một phần lớn nội dung bài viết khi chưa có thỏa thuận chia sẻ bản quyền bằng văn bản;</li>
                            <li>Sử dụng các công cụ thu thập tự động (cào dữ liệu/crawlers/scrapers) gây quá tải hạ tầng máy chủ tòa soạn;</li>
                            <li>Tự ý sử dụng kho dữ liệu bài viết của NewsHub để huấn luyện mô hình ngôn ngữ lớn (AI Training) vì mục đích thương mại;</li>
                            <li>Cắt ghép, chỉnh sửa ảnh phóng sự làm sai lệch tính chất khách quan của sự kiện thời sự.</li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Điều 3 -->
            <section id="dieu-3" class="scroll-mt-24 border border-line bg-surface p-6 sm:p-8 space-y-4">
                <div class="flex items-center gap-3 pb-3 border-b border-line">
                    <span class="size-7 grid place-items-center bg-ink text-paper font-mono font-bold text-xs">03</span>
                    <h2 class="text-lg font-black uppercase text-ink">Điều 3: Quy Chuẩn Bình Luận & Chuẩn Mực Cộng Đồng</h2>
                </div>
                <p>
                    NewsHub khuyến khích độc giả bình luận, tranh luận văn minh và đa chiều. Tuy nhiên, để bảo vệ không gian mạng trong sạch, người tham gia bình luận phải tuân thủ nghiêm ngặt Điều 8 Luật An ninh mạng:
                </p>
                <div class="grid gap-3 sm:grid-cols-2 text-xs">
                    <div class="border border-line bg-paper p-3.5 space-y-1">
                        <strong class="text-ink font-mono uppercase block">Nghiêm cấm ngôn từ thù ghét:</strong>
                        <p class="text-ink-muted">Không chửi bới, lăng mạ danh dự, nhân phẩm của cá nhân, tổ chức; không phân biệt giới tính, tôn giáo hoặc kích động chia rẽ vùng miền.</p>
                    </div>
                    <div class="border border-line bg-paper p-3.5 space-y-1">
                        <strong class="text-ink font-mono uppercase block">Chống tin giả & xuyên tạc:</strong>
                        <p class="text-ink-muted">Không bịa đặt sự việc không có thật, không phát tán thuyết âm mưu chưa được kiểm chứng gây hoang mang dư luận xã hội.</p>
                    </div>
                    <div class="border border-line bg-paper p-3.5 space-y-1">
                        <strong class="text-ink font-mono uppercase block">Cấm spam & quảng cáo bẩn:</strong>
                        <p class="text-ink-muted">Nghiêm cấm chèn liên kết cờ bạc, cá độ bóng đá, mua bán tiền ảo, hàng cấm hoặc nội dung khiêu dâm vào mục phản hồi bài viết.</p>
                    </div>
                    <div class="border border-line bg-paper p-3.5 space-y-1">
                        <strong class="text-ink font-mono uppercase block">Quyền của Quản trị viên:</strong>
                        <p class="text-ink-muted">Đội ngũ kiểm duyệt viên có toàn quyền gỡ bỏ bình luận vi phạm và tạm ngừng hoặc đình chỉ vĩnh viễn quyền đăng bình luận của tài khoản vi phạm.</p>
                    </div>
                </div>
            </section>

            <!-- Điều 4 -->
            <section id="dieu-4" class="scroll-mt-24 border border-line bg-surface p-6 sm:p-8 space-y-4">
                <div class="flex items-center gap-3 pb-3 border-b border-line">
                    <span class="size-7 grid place-items-center bg-ink text-paper font-mono font-bold text-xs">04</span>
                    <h2 class="text-lg font-black uppercase text-ink">Điều 4: Quyền & Trách Nhiệm Của Tác Giả / Cộng Tác Viên</h2>
                </div>
                <p>
                    Đối với các tác giả và phóng viên/cộng tác viên gửi bài cộng tác trên cổng NewsHub:
                </p>
                <ul class="list-disc list-inside space-y-2 text-ink-muted pl-2 text-xs sm:text-sm">
                    <li><strong class="text-ink">Cam kết tính nguyên bản:</strong> Mọi bài viết gửi kiểm duyệt phải là tác phẩm nguyên gốc của tác giả, không đạo văn và tự chịu trách nhiệm về tính xác thực của thông tin.</li>
                    <li><strong class="text-ink">Quyền yêu cầu đính chính / gỡ bài:</strong> Tác giả có quyền gửi yêu cầu cập nhật, đính chính sai sót nghiệp vụ hoặc yêu cầu gỡ bài khi có căn cứ khách quan qua hệ thống quản trị NewsHub để Ban Biên tập xem xét.</li>
                    <li><strong class="text-ink">Chính sách nhuận bút:</strong> Tác phẩm sau khi được duyệt xuất bản chính thức sẽ được hưởng chế độ thù lao theo thang bảng nhuận bút hiện hành của Tòa soạn.</li>
                </ul>
            </section>

            <!-- Điều 5 -->
            <section id="dieu-5" class="scroll-mt-24 border border-line bg-surface p-6 sm:p-8 space-y-4">
                <div class="flex items-center gap-3 pb-3 border-b border-line">
                    <span class="size-7 grid place-items-center bg-ink text-paper font-mono font-bold text-xs">05</span>
                    <h2 class="text-lg font-black uppercase text-ink">Điều 5: Tuyên Bố Miễn Trừ Trách Nhiệm (Disclaimer)</h2>
                </div>
                <p>
                    Tòa soạn NewsHub nỗ lực tối đa để thẩm định và đăng tải các luồng tin chính xác, khách quan và đa chiều. Tuy nhiên:
                </p>
                <div class="space-y-2 text-xs sm:text-sm text-ink-muted">
                    <p>
                        &bull; <strong class="text-ink">Ý kiến chuyên gia & bài phân tích:</strong> Các bài nhận định thị trường tài chính, chứng khoán, đầu tư bất động sản hay kiến thức y học gia đình chỉ mang tính chất tham khảo chung. Độc giả cần tự chịu trách nhiệm về các quyết định tài chính cá nhân.
                    </p>
                    <p>
                        &bull; <strong class="text-ink">Liên kết ngoài (External links):</strong> NewsHub có thể chứa các liên kết đến website bên thứ ba nhằm cung cấp thêm tư liệu. Tòa soạn không chịu trách nhiệm về nội dung, chất lượng dịch vụ hay chính sách bảo mật của các trang bên ngoài này.
                    </p>
                    <p>
                        &bull; <strong class="text-ink">Sự cố kỹ thuật khách quan:</strong> Chúng tôi không chịu trách nhiệm trong trường hợp việc truy cập dịch vụ bị gián đoạn do sự cố cáp quang biển, thiên tai, hỏng hóc hạ tầng viễn thông ngoài tầm kiểm soát của Tòa soạn.
                    </p>
                </div>
            </section>

            <!-- Điều 6 -->
            <section id="dieu-6" class="scroll-mt-24 border border-line bg-surface p-6 sm:p-8 space-y-4">
                <div class="flex items-center gap-3 pb-3 border-b border-line">
                    <span class="size-7 grid place-items-center bg-ink text-paper font-mono font-bold text-xs">06</span>
                    <h2 class="text-lg font-black uppercase text-ink">Điều 6: Luật Áp Dụng & Thẩm Quyền Giải Quyết Tranh Chấp</h2>
                </div>
                <p>
                    Các điều khoản này được điều chỉnh và giải thích hoàn toàn theo quy định pháp luật nước Cộng hòa Xã hội Chủ nghĩa Việt Nam.
                </p>
                <p class="text-ink-muted">
                    Bất kỳ tranh chấp nào phát sinh từ hoặc liên quan đến việc sử dụng dịch vụ trên Báo điện tử NewsHub trước hết sẽ được hai bên giải quyết thông qua thương lượng, hòa giải trên tinh thần tôn trọng quyền lợi của nhau. Trường hợp hòa giải không thành công, tranh chấp sẽ được chuyển đến Tòa án nhân dân có thẩm quyền tại Thành phố Hà Nội để phân xử theo pháp luật.
                </p>
            </section>
        </article>
    </div>
</div>
@endsection
