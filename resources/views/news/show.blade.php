@extends('layouts.app', ['title' => $post->title, 'metaDescription' => $metaDescription, 'ogImage' => $openGraphImageUrl])

@push('head')
<!-- JSON-LD NewsArticle Structured Data for SEO -->
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'NewsArticle',
    'mainEntityOfPage' => [
        '@type' => 'WebPage',
        '@id' => url()->current(),
    ],
    'headline' => $post->title,
    'description' => $metaDescription ?? $post->summary ?? '',
    'image' => [
        $openGraphImageUrl ?: asset('images/default.jpg'),
    ],
    'datePublished' => $post->published_at ? $post->published_at->toIso8601String() : $post->created_at->toIso8601String(),
    'dateModified' => $post->updated_at->toIso8601String(),
    'author' => [
        '@type' => 'Person',
        'name' => $post->author->name,
        'url' => route('authors.show', $post->author),
    ],
    'publisher' => [
        '@type' => 'Organization',
        'name' => 'BaoUT',
        'logo' => [
            '@type' => 'ImageObject',
            'url' => asset('images/logo.png'),
        ],
    ],
], JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Trang chủ',
            'item' => route('home'),
        ],
        [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => $post->category->name,
            'item' => route('news.index', ['category' => $post->category->slug]),
        ],
        [
            '@type' => 'ListItem',
            'position' => 3,
            'name' => $post->title,
        ],
    ],
], JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>

<!-- Clean Print Stylesheet -->
<style>
@media print {
    body {
        background: #ffffff !important;
        color: #000000 !important;
    }
    header, nav[aria-label="Breadcrumb"], .border-b, #reader-utilities-bar, #meta-action-buttons, #author-box, #comments-section, section[aria-labelledby="related-heading"], footer, [data-confirm-modal] {
        display: none !important;
    }
    .print-only-header {
        display: block !important;
        margin-bottom: 24px;
        padding-bottom: 12px;
        border-bottom: 2px solid #000;
    }
    #article-content-body {
        color: #000000 !important;
        font-size: 13pt !important;
        line-height: 1.6 !important;
    }
    #article-content-body * {
        color: #000000 !important;
    }
    figure img {
        max-width: 100% !important;
        page-break-inside: avoid;
    }
    .print-only-footer {
        display: block !important;
        margin-top: 30px;
        padding-top: 10px;
        border-top: 1px solid #ccc;
        font-size: 10pt;
        color: #666;
    }
}
.print-only-header, .print-only-footer {
    display: none;
}
</style>
@endpush

@php
    $wordCount = str_word_count(strip_tags($post->content));
    $readingTimeMinutes = max(1, (int) ceil($wordCount / 200));
@endphp

@section('content')
<div class="mx-auto max-w-4xl space-y-8 bg-surface rounded-[12px] border border-[#E2E8F0] p-4 sm:p-8 shadow-[0_8px_24px_rgb(15_23_42_/_0.10)]">
    <!-- Print Only Header -->
    <div class="print-only-header font-mono">
        <h2 style="font-size: 22pt; font-weight: bold; margin-bottom: 6px;">BaoUT - Báo điện tử đa phương tiện</h2>
        <p style="font-size: 10pt; color: #444;">Chuyên mục: {{ $post->category->name }} | Tác giả: {{ $post->author->name }} | Xuất bản: {{ $post->published_at?->format('d/m/Y H:i') }}</p>
        <p style="font-size: 9pt; color: #666;">Đường dẫn bài viết: {{ request()->fullUrl() }}</p>
    </div>

    <!-- Breadcrumb -->
    <nav aria-label="Breadcrumb" class="text-xs font-mono text-ink-muted">
        <ol class="flex items-center gap-1.5 flex-wrap">
            <li><a href="{{ route('home') }}" class="inline-block py-1 hover:text-ink hover:underline transition-colors">Trang chủ</a></li>
            <li class="text-line">/</li>
            <li>
                <a href="{{ route('news.index', ['category' => $post->category->slug]) }}" class="inline-block py-1 hover:text-ink hover:underline transition-colors uppercase font-bold">
                    {{ $post->category->name }}
                </a>
            </li>
            <li class="text-line">/</li>
            <li class="font-bold text-ink truncate max-w-xs sm:max-w-md">
                {{ $post->title }}
            </li>
        </ol>
    </nav>

    <!-- Main Article Body -->
    <article class="space-y-6">
        <!-- Category & Title -->
        <div class="space-y-3">
            <a href="{{ route('news.index', ['category' => $post->category->slug]) }}"
               class="inline-flex items-center rounded-[6px] border border-[#CBD5E1] bg-paper px-3 py-1 text-xs font-bold font-mono uppercase tracking-wider text-ink hover:bg-lime hover:text-white transition-colors">
                {{ $post->category->name }}
            </a>

            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight text-ink leading-tight">
                {{ $post->title }}
            </h1>
        </div>

        <!-- Meta Bar: Author, Date, View Count & Reading Time -->
        <div class="flex flex-col gap-4 border-y border-[#E2E8F0] py-3.5 sm:flex-row sm:items-center sm:justify-between text-xs font-mono text-ink-muted">
            <!-- Author & Time -->
            <div class="flex items-center gap-3">
                <a href="{{ route('authors.show', $post->author) }}" class="group flex items-center gap-2.5">
                    <span class="grid size-8 place-items-center bg-[#1D4ED8] text-xs font-bold font-mono text-white rounded-full select-none" aria-hidden="true">
                        {{ mb_strtoupper(mb_substr($post->author->name, 0, 1)) }}
                    </span>
                    <div>
                        <p class="font-bold text-ink group-hover:underline transition-colors">{{ $post->author->name }}</p>
                        <p class="text-[11px] text-ink-muted font-sans">Tác giả bài viết</p>
                    </div>
                </a>

                <span class="text-line">|</span>

                <div class="text-[11px] space-y-0.5">
                    <p class="text-ink font-bold">{{ $post->published_at->format('d/m/Y H:i') }}</p>
                    <p class="text-ink-muted tabular-nums flex items-center gap-1.5">
                        <span>{{ number_format($post->view_count) }} lượt xem</span>
                        <span>&bull;</span>
                        <span class="inline-flex items-center gap-1 font-bold text-ink">
                            <svg class="size-3 text-ink" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                            {{ $readingTimeMinutes }} phút đọc
                        </span>
                    </p>
                </div>
            </div>

        </div>

        <!-- Reader Utilities Bar (Print, Social Share) -->
        <div id="reader-utilities-bar" class="flex flex-wrap items-center justify-between gap-3 rounded-[12px] border border-[#E2E8F0] bg-paper p-3 shadow-[0_1px_3px_rgb(15_23_42_/_0.04)]">
            <!-- Left: Print -->
            <div class="flex flex-wrap items-center gap-2.5">
                <!-- Print Button -->
                <button type="button"
                        onclick="window.print()"
                        class="inline-flex min-h-8 cursor-pointer items-center gap-1.5 rounded-[8px] border border-[#CBD5E1] bg-surface px-3 py-1.5 text-xs font-bold font-mono text-ink shadow-2xs transition-all hover:bg-paper active:scale-[0.98]"
                        title="In bài viết hoặc lưu PDF sạch">
                    <svg class="size-3.5 text-ink" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" />
                    </svg>
                    <span>In bài</span>
                </button>
            </div>

            <!-- Right: Social Share Buttons -->
            <div class="flex items-center gap-1.5 font-mono">
                <span class="text-[11px] text-ink-muted font-bold mr-1 hidden md:inline">CHIA SẺ:</span>

                <!-- Facebook -->
                <button type="button"
                        onclick="window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(window.location.href), 'facebook-share', 'width=580,height=420'); return false;"
                        class="inline-flex min-h-8 cursor-pointer items-center gap-1 rounded-[8px] border border-[#CBD5E1] bg-surface px-2.5 py-1.5 text-xs font-bold text-ink shadow-2xs transition-all hover:bg-paper active:scale-[0.98]"
                        title="Chia sẻ lên Facebook">
                    <svg class="size-3.5 fill-current" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    <span class="hidden sm:inline">FB</span>
                </button>

                <!-- X (Twitter) -->
                <button type="button"
                        onclick="window.open('https://twitter.com/intent/tweet?url=' + encodeURIComponent(window.location.href) + '&text=' + encodeURIComponent('{{ addslashes($post->title) }}'), 'twitter-share', 'width=580,height=420'); return false;"
                        class="inline-flex min-h-8 cursor-pointer items-center gap-1 rounded-[8px] border border-[#CBD5E1] bg-surface px-2.5 py-1.5 text-xs font-bold text-ink shadow-2xs transition-all hover:bg-paper active:scale-[0.98]"
                        title="Chia sẻ lên X (Twitter)">
                    <svg class="size-3.5 fill-current" viewBox="0 0 24 24">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                    <span class="hidden sm:inline">X</span>
                </button>

                <!-- Zalo -->
                <button type="button"
                        onclick="window.open('https://sp.zalo.me/share_inline?link=' + encodeURIComponent(window.location.href), 'zalo-share', 'width=580,height=420'); return false;"
                        class="inline-flex min-h-8 cursor-pointer items-center gap-1 rounded-[8px] border border-[#CBD5E1] bg-surface px-2.5 py-1.5 text-xs font-bold text-ink shadow-2xs transition-all hover:bg-paper active:scale-[0.98]"
                        title="Chia sẻ qua Zalo">
                    <span class="font-bold text-[11px] tracking-tight">Zalo</span>
                </button>

                <!-- Copy Link Button with Toast Feedback -->
                <button type="button"
                        id="btn-copy-article-link"
                        class="inline-flex min-h-8 cursor-pointer items-center gap-1.5 rounded-[8px] border border-[#E2E8F0] bg-lime px-3 py-1.5 text-xs font-bold text-white shadow-none transition-all hover:bg-ink hover:text-white active:scale-[0.98]"
                        title="Sao chép liên kết bài viết">
                    <svg class="size-3.5 text-white" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M12.232 4.232a2.5 2.5 0 013.536 3.536l-1.225 1.224a.75.75 0 001.061 1.06l1.224-1.224a4 4 0 00-5.656-5.656l-3 3a4 4 0 00.225 5.865.75.75 0 00.977-1.138 2.5 2.5 0 01-.142-3.667l3-3z" />
                        <path d="M11.603 7.963a.75.75 0 00-.977 1.138 2.5 2.5 0 01.142 3.667l-3 3a2.5 2.5 0 01-3.536-3.536l1.225-1.224a.75.75 0 00-1.061-1.06l-1.224 1.224a4 4 0 105.656 5.656l3-3a4 4 0 00-.225-5.865z" />
                    </svg>
                    <span id="copy-btn-text">Sao chép</span>
                </button>
            </div>
        </div>

        <!-- Sapo / Executive Summary -->
        @if ($post->summary)
            <div class="border-l-4 border-line-strong bg-paper p-5 text-base sm:text-lg font-bold leading-relaxed text-ink font-sans">
                {{ $post->summary }}
            </div>
        @endif

        <!-- Featured Image with Caption -->
        @if ($thumbnailUrl)
            <figure class="overflow-hidden rounded-[12px] border border-[#E2E8F0] bg-paper">
                <img src="{{ $thumbnailUrl }}"
                     alt="{{ $post->title }}"
                     class="aspect-video w-full object-cover"
                     loading="eager">
                <figcaption class="px-4 py-2 text-center text-xs font-mono text-ink-muted italic bg-paper-light border-t border-[#E2E8F0]">
                    Hình ảnh minh họa cho bài viết · Nguồn: BaoUT
                </figcaption>
            </figure>
        @endif

        <!-- Body Content -->
        <div id="article-content-body" @class(['article-content text-base sm:text-lg text-ink leading-relaxed sm:leading-8 font-normal space-y-5 pt-2', 'whitespace-pre-line' => ! str_contains($safeContent, '<p')])>{!! $safeContent !!}</div>

        <!-- Author Profile Box Card -->
        <div id="author-box" class="rounded-[12px] border border-[#E2E8F0] bg-paper p-5 shadow-[0_1px_3px_rgb(15_23_42_/_0.04)]">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <span class="grid size-12 place-items-center bg-[#1D4ED8] text-base font-bold font-mono text-white rounded-full select-none" aria-hidden="true">
                        {{ mb_strtoupper(mb_substr($post->author->name, 0, 1)) }}
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-black text-ink">{{ $post->author->name }}</h3>
                            <span class="rounded-[6px] border border-[#E2E8F0] bg-lime px-2 py-0.5 text-[11px] font-bold font-mono uppercase tracking-wider text-white shadow-2xs">
                                {{ $post->author->role->label() }}
                            </span>
                        </div>
                        <p class="text-xs text-ink-muted mt-1">Cây bút chuyên môn tại tòa soạn BaoUT.</p>
                    </div>
                </div>

                <a href="{{ route('authors.show', $post->author) }}"
                   class="inline-flex items-center justify-center gap-1.5 rounded-[8px] border border-[#CBD5E1] bg-surface px-4 py-2 text-xs font-bold font-mono text-ink shadow-2xs transition-colors hover:bg-paper hover:shadow-[0_4px_12px_rgb(15_23_42_/_0.08)]">
                    <span>Xem bài viết của tác giả</span>
                    <span aria-hidden="true">&rarr;</span>
                </a>
            </div>
        </div>
    </article>

    <!-- Comments Section -->
    <section id="comments-section" class="border-t border-[#E2E8F0] pt-8 space-y-6">
        <div class="flex items-center justify-between border-b border-[#E2E8F0] pb-3">
            <div class="flex items-center gap-2">
                <span class="size-2.5 bg-lime border border-ink"></span>
                <h2 class="text-xl font-black tracking-tight text-ink font-mono uppercase">
                    Bình luận & Ý kiến độc giả
                </h2>
                <span class="rounded-[6px] border border-[#E2E8F0] bg-paper px-2.5 py-0.5 text-xs font-bold font-mono tabular-nums text-ink">
                    {{ $comments->total() }}
                </span>
            </div>
        </div>

        <!-- Add Comment Form -->
        @auth
            @if (auth()->user()->hasVerifiedEmail())
                <form action="{{ route('comments.store', $post) }}" method="POST" class="rounded-[12px] border border-[#E2E8F0] bg-surface p-4 shadow-[0_1px_3px_rgb(15_23_42_/_0.04)] font-sans">
                    @csrf
                    <div class="flex items-start gap-3">
                        <span class="grid size-8 shrink-0 place-items-center bg-[#1D4ED8] text-xs font-bold font-mono text-white rounded-full select-none" aria-hidden="true">
                            {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                        </span>

                        <div class="flex-1 space-y-2.5">
                            <textarea name="content"
                                      aria-label="Nội dung bình luận"
                                      rows="3"
                                      required
                                      maxlength="1000"
                                      placeholder="Chia sẻ quan điểm của bạn về bài viết này..."
                                      class="w-full rounded-[8px] border border-control bg-paper p-3 text-xs sm:text-sm text-ink placeholder:text-ink-muted outline-none transition-all focus:border-[#1D4ED8] focus:shadow-[0_0_0_2px_rgb(29_78_216_/_0.25)]"></textarea>

                            <div class="flex items-center justify-between text-xs text-ink-muted font-mono">
                                <span class="text-[11px]">Tối đa 1000 ký tự. Vui lòng tuân thủ quy chế kiểm duyệt.</span>
                                <button type="submit"
                                        class="inline-flex cursor-pointer items-center gap-1.5 rounded-[8px] border border-[#E2E8F0] bg-lime px-4 py-2 text-xs font-bold text-white shadow-none transition-colors hover:bg-ink hover:text-white active:scale-[0.98]">
                                    <span>Gửi bình luận</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            @else
                <div class="rounded-[12px] border border-[#E2E8F0] bg-[#dce6f2] px-4 py-3 text-xs text-ink flex items-center justify-between gap-3 shadow-2xs">
                    <p>Bạn cần hoàn tất xác minh email tài khoản để tham gia gửi bình luận.</p>
                    <a href="{{ route('verification.notice') }}" class="shrink-0 font-bold underline hover:text-[#587dae]">Xác minh ngay</a>
                </div>
            @endif
        @else
            <div class="rounded-[12px] border border-[#E2E8F0] bg-paper px-4 py-3 text-center text-xs text-ink-muted">
                <p>Vui lòng <a href="{{ route('login') }}" class="font-bold text-ink underline">đăng nhập</a> hoặc <a href="{{ route('register') }}" class="font-bold text-ink underline">đăng ký</a> để tham gia gửi bình luận cùng độc giả khác.</p>
            </div>
        @endauth

        <!-- Comments List -->
        <div class="space-y-3">
            @forelse ($comments as $comment)
                <div class="space-y-2">
                    <x-comment-item :comment="$comment" :post="$post" />
                    @if ($comment->replies->isNotEmpty())
                        <div class="ml-3 sm:ml-6 space-y-2 border-l border-[#E2E8F0] pl-2.5 sm:pl-3.5">
                            @foreach ($comment->replies as $reply)
                                <x-comment-item :comment="$reply" :post="$post" :is-reply="true" />
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <div class="rounded-[12px] border border-[#E2E8F0] bg-paper p-6 text-center text-xs font-mono text-ink-muted">
                    Chưa có bình luận nào cho bài viết này. Hãy là người đầu tiên chia sẻ cảm nghĩ của bạn!
                </div>
            @endforelse
        </div>

        @if ($comments->hasPages())
            <div class="pt-2 flex justify-center">
                {{ $comments->links() }}
            </div>
        @endif
    </section>

    <!-- Related Articles -->
    @if ($relatedPosts->isNotEmpty())
        <section aria-labelledby="related-heading" class="border-t border-[#E2E8F0] pt-8 space-y-5">
            <div class="flex items-center justify-between border-b border-[#E2E8F0] pb-3">
                <div class="flex items-center gap-2">
                    <span class="size-2.5 bg-lime border border-ink"></span>
                    <h2 id="related-heading" class="text-xl font-black tracking-tight text-ink font-mono uppercase">
                        Bài viết cùng chuyên mục
                    </h2>
                </div>
                <a href="{{ route('news.index', ['category' => $post->category->slug]) }}" class="text-xs font-bold font-mono text-ink hover:underline transition-colors">
                    Xem thêm &rarr;
                </a>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                @foreach ($relatedPosts as $relatedPost)
                    <x-post-card :post="$relatedPost" />
                @endforeach
            </div>
        </section>
    @endif

    <!-- Print Only Footer -->
    <div class="print-only-footer font-mono">
        <p>© {{ date('Y') }} BaoUT. Mọi quyền được bảo lưu. Bài viết được trích xuất từ phiên bản điện tử chính thức của BaoUT.</p>
    </div>
</div>

<!-- Reader Utilities Scripts -->
<script>
    // --- COPY LINK FEEDBACK ---
    document.getElementById('btn-copy-article-link')?.addEventListener('click', function () {
        navigator.clipboard.writeText(window.location.href).then(() => {
            const btnText = document.getElementById('copy-btn-text');
            if (btnText) {
                const originalText = btnText.textContent;
                btnText.textContent = 'Đã chép!';
                btnText.classList.add('underline');
                setTimeout(() => {
                    btnText.textContent = originalText;
                    btnText.classList.remove('underline');
                }, 2500);
            }
        });
    });
</script>
@endsection
