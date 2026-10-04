@extends('layouts.app', [
    'title' => 'Quy chế kiểm duyệt nội dung & Tiêu chuẩn xuất bản - NewsHub',
    'metaDescription' => 'Quy trình kiểm duyệt bài viết 3 cấp độ, tiêu chuẩn kiểm chứng nguồn tin báo chí, chính sách đính chính và gỡ bài trên Báo điện tử NewsHub.'
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
            <li class="font-bold text-ink">Quy chế kiểm duyệt</li>
        </ol>
    </nav>

    <!-- Hero Header -->
    <header class="border-2 border-line-strong bg-surface p-6 sm:p-10 shadow-brutal space-y-4">
        <div class="inline-flex items-center gap-2 border border-line-strong bg-lime px-3 py-1 text-xs font-mono font-bold uppercase tracking-wider text-ink shadow-brutal-sm">
            <span>●</span>
            <span>Quy Chuẩn Tòa Soạn • Kiểm Chứng Nghiêm Ngặt</span>
        </div>
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black uppercase tracking-tight text-ink">
            Quy Chế Kiểm Duyệt Nội Dung & Tiêu Chuẩn Xuất Bản
        </h1>
        <p class="text-sm sm:text-base leading-relaxed text-ink-muted max-w-4xl">
            Quy trình thẩm định bài viết 3 cấp độ, kiểm chứng nguồn tin độc lập, chính sách đính chính sai sót công khai và cơ chế giám sát tương tác bạn đọc tại Tòa soạn NewsHub.
        </p>
        <div class="flex flex-wrap items-center gap-3 pt-3 border-t border-line text-xs font-mono text-ink-muted">
            <span>Hiệu lực: <strong class="text-ink">01/01/2026</strong></span>
            <span class="text-line-strong">|</span>
            <span>Ban Biên tập phê chuẩn</span>
            <span class="text-line-strong">|</span>
            <span class="bg-paper border border-line px-2 py-0.5 text-ink font-bold">Văn bản: 01/QC-NEWSHUB-BBT</span>
        </div>
    </header>

    <!-- Main Content Area -->
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12 items-start">
        <!-- Sidebar Navigation -->
        <aside class="space-y-6 lg:col-span-4 lg:sticky lg:top-24">
            <!-- Table of Contents -->
            <div class="border-2 border-line-strong bg-surface p-5 shadow-brutal-sm space-y-4">
                <h2 class="text-xs font-mono font-black uppercase tracking-wider text-ink flex items-center justify-between pb-3 border-b border-line">
                    <span>Mục Lục Quy Chế</span>
                    <span class="bg-lime text-ink px-1.5 py-0.5 text-[10px] font-bold border border-ink">TOC</span>
                </h2>
                <nav class="space-y-1.5 text-xs font-medium">
                    <a href="#dieu-1" class="block py-1.5 px-2 border-l-2 border-transparent text-ink-muted hover:border-line-strong hover:bg-paper hover:text-ink transition-colors">1. Tôn chỉ & đạo đức báo chí</a>
                    <a href="#dieu-2" class="block py-1.5 px-2 border-l-2 border-transparent text-ink-muted hover:border-line-strong hover:bg-paper hover:text-ink transition-colors">2. Quy trình thẩm định 3 cấp độ</a>
                    <a href="#dieu-3" class="block py-1.5 px-2 border-l-2 border-transparent text-ink-muted hover:border-line-strong hover:bg-paper hover:text-ink transition-colors">3. Tiêu chuẩn kiểm chứng nguồn tin</a>
                    <a href="#dieu-4" class="block py-1.5 px-2 border-l-2 border-transparent text-ink-muted hover:border-line-strong hover:bg-paper hover:text-ink transition-colors">4. Đính chính, sửa bài & gỡ bài</a>
                    <a href="#dieu-5" class="block py-1.5 px-2 border-l-2 border-transparent text-ink-muted hover:border-line-strong hover:bg-paper hover:text-ink transition-colors">5. Kiểm duyệt bình luận bạn đọc</a>
                    <a href="#dieu-6" class="block py-1.5 px-2 border-l-2 border-transparent text-ink-muted hover:border-line-strong hover:bg-paper hover:text-ink transition-colors">6. Tiếp nhận phản ánh & xử lý khiếu nại</a>
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
                        <a href="{{ route('pages.terms') }}" class="group flex items-center justify-between p-2 border border-transparent hover:border-line hover:bg-paper transition-colors">
                            <span class="font-bold text-ink group-hover:underline">Điều khoản dịch vụ độc giả</span>
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

            <!-- Hotline Verification Box -->
            <div class="border-2 border-line-strong bg-paper p-5 space-y-3 text-xs shadow-brutal-sm">
                <div class="flex items-center gap-2 text-ink font-bold uppercase font-mono tracking-wider">
                    <span class="bg-ink text-paper size-4 inline-flex items-center justify-center text-[10px]">!</span>
                    Ban Kiểm tra & Thẩm định tin bài
                </div>
                <p class="text-ink-muted leading-relaxed">
                    Phát hiện sai sót hoặc có tài liệu chứng minh thông tin chưa chính xác? Liên hệ trực tiếp với Thư ký Tòa soạn:
                </p>
                <div class="space-y-1.5 font-mono pt-2 border-t border-line text-xs">
                    <p class="text-ink-muted">Hotline: <strong class="text-ink font-bold">1900 8888</strong> (Nhánh 1)</p>
                    <p class="text-ink-muted">Email: <a href="mailto:kiemduyet@newshub.vn" class="text-ink font-bold underline">kiemduyet@newshub.vn</a></p>
                </div>
            </div>
        </aside>

        <!-- Document Body Content -->
        <article class="space-y-8 lg:col-span-8 text-ink leading-relaxed text-sm">
            <!-- Điều 1 -->
            <section id="dieu-1" class="scroll-mt-24 border border-line bg-surface p-6 sm:p-8 space-y-4">
                <div class="flex items-center gap-3 pb-3 border-b border-line">
                    <span class="size-7 grid place-items-center bg-ink text-paper font-mono font-bold text-xs">01</span>
                    <h2 class="text-lg font-black uppercase text-ink">Điều 1: Tôn Chỉ Báo Chí & Đạo Đức Nghề Nghiệp</h2>
                </div>
                <p>
                    Báo điện tử <strong>NewsHub</strong> hoạt động theo Giấy phép xuất bản báo chí số <strong>128/GP-BTTTT</strong> do Bộ Thông tin và Truyền thông cấp. Chúng tôi kiên định với sứ mệnh mang đến dòng chảy tin tức trung thực, công tâm, kịp thời và có chiều sâu tri thức.
                </p>
                <p>Mọi phóng viên, biên tập viên và cộng tác viên của NewsHub có nghĩa vụ chấp hành nghiêm túc:</p>
                <ul class="list-disc list-inside space-y-1.5 text-ink-muted pl-2 text-xs sm:text-sm">
                    <li>10 điều Quy định đạo đức nghề nghiệp người làm báo Việt Nam;</li>
                    <li>Tôn trọng sự thật khách quan, không vì áp lực câu view hay lợi ích kinh tế mà bẻ cong ngòi bút;</li>
                    <li>Bảo vệ bí mật nguồn tin và an toàn cho nhân chứng theo đúng quy định của Luật Báo chí;</li>
                    <li>Không nhận bất kỳ lợi ích tài chính bất chính nào từ đối tượng được phản ánh trong bài viết.</li>
                </ul>
            </section>

            <!-- Điều 2 -->
            <section id="dieu-2" class="scroll-mt-24 border border-line bg-surface p-6 sm:p-8 space-y-4">
                <div class="flex items-center gap-3 pb-3 border-b border-line">
                    <span class="size-7 grid place-items-center bg-ink text-paper font-mono font-bold text-xs">02</span>
                    <h2 class="text-lg font-black uppercase text-ink">Điều 2: Quy Trình Thẩm Định 3 Cấp Độ (Editorial Pipeline)</h2>
                </div>
                <p>
                    Để đảm bảo không có sai sót trước khi đến với bạn đọc, mọi tác phẩm đều phải trải qua quy trình thẩm định 3 vòng khép kín trên hệ thống tòa soạn:
                </p>
                <div class="space-y-4 pt-2">
                    <div class="flex items-start gap-4 border border-line bg-paper p-4">
                        <div class="size-9 shrink-0 grid place-items-center border border-line-strong bg-surface text-ink font-mono font-bold text-xs shadow-brutal-sm">
                            V1
                        </div>
                        <div class="space-y-1 text-xs sm:text-sm">
                            <h3 class="font-bold uppercase tracking-wider text-ink font-mono">Vòng 1: Tác giả / Phóng viên biên soạn bản thảo (Draft)</h3>
                            <p class="text-ink-muted leading-relaxed">
                                Thu thập thông tin, chứng từ gốc, số liệu thực địa, phỏng vấn nhân chứng. Tác giả chịu trách nhiệm kiểm tra tính chính xác của bản thảo ban đầu trước khi nhấn gửi duyệt (Submit for Review).
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 border border-line bg-paper p-4">
                        <div class="size-9 shrink-0 grid place-items-center border border-line-strong bg-lime text-ink font-mono font-bold text-xs shadow-brutal-sm">
                            V2
                        </div>
                        <div class="space-y-1 text-xs sm:text-sm">
                            <h3 class="font-bold uppercase tracking-wider text-ink font-mono">Vòng 2: Biên tập viên Ban chuyên môn rà soát nghiệp vụ (In Review)</h3>
                            <p class="text-ink-muted leading-relaxed">
                                Kiểm tra cấu trúc ngữ pháp, phong cách báo chí, đối chiếu nguồn số liệu (Fact-checking), thẩm định bản quyền ảnh chụp và video đính kèm. BTV có quyền yêu cầu tác giả bổ sung chứng cứ hoặc từ chối bài không đạt chuẩn.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 border border-line bg-paper p-4">
                        <div class="size-9 shrink-0 grid place-items-center border border-line-strong bg-ink text-paper font-mono font-bold text-xs shadow-brutal-sm">
                            V3
                        </div>
                        <div class="space-y-1 text-xs sm:text-sm">
                            <h3 class="font-bold uppercase tracking-wider text-ink font-mono">Vòng 3: Ban Thư ký Tòa soạn & Tổng biên tập phê chuẩn (Publish)</h3>
                            <p class="text-ink-muted leading-relaxed">
                                Rà soát định hướng chính trị, pháp lý, tác động xã hội. Tổng biên tập hoặc Phó Tổng biên tập trực ban ký lệnh xuất bản chính thức lên mạng lưới độc giả toàn quốc.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Điều 3 -->
            <section id="dieu-3" class="scroll-mt-24 border border-line bg-surface p-6 sm:p-8 space-y-4">
                <div class="flex items-center gap-3 pb-3 border-b border-line">
                    <span class="size-7 grid place-items-center bg-ink text-paper font-mono font-bold text-xs">03</span>
                    <h2 class="text-lg font-black uppercase text-ink">Điều 3: Tiêu Chuẩn Kiểm Chứng Nguồn Tin & Bản Quyền Ảnh</h2>
                </div>
                <div class="grid gap-4 sm:grid-cols-2 text-xs">
                    <div class="border border-line bg-paper p-4 space-y-2">
                        <strong class="text-ink font-mono uppercase block border-b border-line pb-1">Nguyên tắc kiểm chứng tối thiểu 2 nguồn độc lập:</strong>
                        <p class="text-ink-muted leading-relaxed">
                            Đối với các đề tài điều tra, thông tin nhạy cảm về tai nạn, kinh tế, tố tụng hình sự, phóng viên bắt buộc phải kiểm chứng chéo tối thiểu từ 2 nguồn tin độc lập hoặc có văn bản xác nhận từ cơ quan chức năng.
                        </p>
                    </div>
                    <div class="border border-line bg-paper p-4 space-y-2">
                        <strong class="text-ink font-mono uppercase block border-b border-line pb-1">Quy chuẩn bản quyền ảnh & video:</strong>
                        <p class="text-ink-muted leading-relaxed">
                            Mọi hình ảnh đăng tải phải có chú thích rõ ràng về tác giả, địa điểm và thời gian chụp. Tuyệt đối cấm sử dụng ảnh do AI tạo dựng (Deepfake) để minh họa cho tin tức thời sự nếu không ghi rõ nhãn "Ảnh minh họa mô phỏng".
                        </p>
                    </div>
                </div>
            </section>

            <!-- Điều 4 -->
            <section id="dieu-4" class="scroll-mt-24 border border-line bg-surface p-6 sm:p-8 space-y-4">
                <div class="flex items-center gap-3 pb-3 border-b border-line">
                    <span class="size-7 grid place-items-center bg-ink text-paper font-mono font-bold text-xs">04</span>
                    <h2 class="text-lg font-black uppercase text-ink">Điều 4: Chính Sách Đính Chính, Sửa Bài & Gỡ Bài (Corrections & Takedowns)</h2>
                </div>
                <p>
                    Tòa soạn NewsHub coi trọng sự thật trên hết. Khi phát hiện thông tin chưa chuẩn xác hoặc có dữ liệu mới phát sinh, chúng tôi thực thi chính sách đính chính minh bạch theo các chuẩn mực sau:
                </p>
                <div class="space-y-3 pt-2 text-xs sm:text-sm">
                    <div class="border border-line bg-paper p-4 space-y-2">
                        <strong class="text-ink font-mono uppercase tracking-wider block border-b border-line pb-1">
                            Quy trình Đính chính & Cập nhật nội dung (Correction Request):
                        </strong>
                        <p class="text-ink-muted leading-relaxed">
                            Khi tác giả hoặc độc giả phát hiện lỗi chính tả, sai lệch số liệu hay thông tin nhân vật, Tác giả/Biên tập viên sẽ tạo yêu cầu chỉnh sửa kèm lý do chi tiết. Khi được phê duyệt, bài viết sẽ hiển thị công khai thông báo đính chính: <em>"Đính chính ngày...: [Nội dung đã sửa]"</em> để bạn đọc theo dõi.
                        </p>
                    </div>
                    <div class="border-2 border-line-strong bg-surface p-4 space-y-2">
                        <strong class="text-danger font-mono uppercase tracking-wider block border-b border-line pb-1">
                            Chính sách Gỡ bài viết (Take-down Policy):
                        </strong>
                        <p class="text-ink leading-relaxed">
                            NewsHub <strong>không tùy tiện gỡ bài đã xuất bản</strong> trừ các trường hợp đặc biệt:
                        </p>
                        <ul class="list-disc list-inside space-y-1 text-ink-muted text-xs">
                            <li>Có văn bản yêu cầu rút bài từ Cơ quan quản lý báo chí hoặc Bản án/Quyết định có hiệu lực của Tòa án;</li>
                            <li>Phát hiện vi phạm bản quyền nghiêm trọng không thể khắc phục bằng việc trích dẫn lại;</li>
                            <li>Tác giả gửi yêu cầu gỡ bài kèm lý do chính đáng và được Hội đồng Biên tập phê chuẩn bằng văn bản.</li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Điều 5 -->
            <section id="dieu-5" class="scroll-mt-24 border border-line bg-surface p-6 sm:p-8 space-y-4">
                <div class="flex items-center gap-3 pb-3 border-b border-line">
                    <span class="size-7 grid place-items-center bg-ink text-paper font-mono font-bold text-xs">05</span>
                    <h2 class="text-lg font-black uppercase text-ink">Điều 5: Kiểm Duyệt Bình Luận & Tương Tác Bạn Đọc</h2>
                </div>
                <p>
                    Nhằm gìn giữ môi trường thảo luận văn minh và thượng tôn pháp luật, hệ thống bình luận trên NewsHub được kiểm soát theo mô hình kết hợp:
                </p>
                <ul class="list-disc list-inside space-y-2 text-ink-muted pl-2 text-xs sm:text-sm">
                    <li><strong class="text-ink">Bộ lọc từ khóa tự động (Automated Filtering):</strong> Chặn ngay lập tức các bình luận chứa từ ngữ dung tục, khiêu dâm, quảng cáo số đề, cờ bạc, đường dẫn độc hại hoặc công kích cá nhân.</li>
                    <li><strong class="text-ink">Kiểm duyệt viên trực ban 24/7:</strong> Rà soát các luồng thảo luận có tính tranh cãi cao, bảo đảm tranh luận đúng trọng tâm chủ đề bài viết.</li>
                    <li><strong class="text-ink">Cơ chế "Báo cáo bình luận xấu":</strong> Độc giả có thể bấm nút Báo cáo tại mỗi bình luận. Khi một bình luận nhận nhiều phản ánh, hệ thống sẽ tự động hạ mức hiển thị và chuyển sang Ban Quản trị xác minh xử lý.</li>
                </ul>
            </section>

            <!-- Điều 6 -->
            <section id="dieu-6" class="scroll-mt-24 border border-line bg-surface p-6 sm:p-8 space-y-4">
                <div class="flex items-center gap-3 pb-3 border-b border-line">
                    <span class="size-7 grid place-items-center bg-ink text-paper font-mono font-bold text-xs">06</span>
                    <h2 class="text-lg font-black uppercase text-ink">Điều 6: Tiếp Nhận Ý Kiến Phản Ánh & Xử Lý Khiếu Nại</h2>
                </div>
                <p>
                    Mọi cá nhân, tổ chức có thông tin phản hồi, cung cấp tài liệu đính chính hoặc khiếu nại về nội dung tin bài, xin vui lòng gửi về:
                </p>
                <div class="border border-line bg-paper p-4 space-y-2 text-xs font-mono">
                    <p><strong class="text-ink uppercase">Bộ phận tiếp nhận:</strong> Ban Thư ký Biên tập & Kiểm tra Xuất bản NewsHub</p>
                    <p><strong class="text-ink uppercase">Email chuyên trách:</strong> <a href="mailto:kiemduyet@newshub.vn" class="text-ink font-bold underline">kiemduyet@newshub.vn</a> hoặc <a href="mailto:toasoan@newshub.vn" class="text-ink font-bold underline">toasoan@newshub.vn</a></p>
                    <p><strong class="text-ink uppercase">Đường dây nóng 24/7:</strong> 1900 8888 (Phím 1 gặp Trực ban biên tập)</p>
                    <p class="text-ink-muted text-[11px] pt-1 border-t border-line">
                        Cam kết thời gian xử lý: Tiếp nhận sơ bộ trong vòng 2 giờ; kiểm tra thực địa và phản hồi kết luận thẩm định trong vòng tối đa 24 giờ làm việc.
                    </p>
                </div>
            </section>
        </article>
    </div>
</div>
@endsection
