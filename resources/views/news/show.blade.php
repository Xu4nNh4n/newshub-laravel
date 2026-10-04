@extends('layouts.app', ['title' => $post->meta_title ?: $post->title, 'metaDescription' => $metaDescription, 'ogImage' => $openGraphImageUrl])

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
        'name' => 'NewsHub',
        'logo' => [
            '@type' => 'ImageObject',
            'url' => asset('images/logo.png'),
        ],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
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
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
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
<div class="mx-auto max-w-4xl space-y-8 bg-surface border-x-2 border-line-strong p-4 sm:p-8 shadow-brutal">
    <!-- Print Only Header -->
    <div class="print-only-header font-mono">
        <h2 style="font-size: 22pt; font-weight: bold; margin-bottom: 6px;">NewsHub - Báo điện tử đa phương tiện</h2>
        <p style="font-size: 10pt; color: #444;">Chuyên mục: {{ $post->category->name }} | Tác giả: {{ $post->author->name }} | Xuất bản: {{ $post->published_at?->format('d/m/Y H:i') }}</p>
        <p style="font-size: 9pt; color: #666;">Đường dẫn bài viết: {{ request()->fullUrl() }}</p>
    </div>

    <!-- Breadcrumb -->
    <nav aria-label="Breadcrumb" class="text-xs font-mono text-ink-muted">
        <ol class="flex items-center gap-1.5 flex-wrap">
            <li><a href="{{ route('home') }}" class="hover:text-ink hover:underline transition-colors">Trang chủ</a></li>
            <li class="text-line">/</li>
            <li>
                <a href="{{ route('news.index', ['category' => $post->category->slug]) }}" class="hover:text-ink hover:underline transition-colors uppercase font-bold">
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
               class="inline-flex items-center border border-line-strong bg-paper px-3 py-1 text-xs font-bold font-mono uppercase tracking-wider text-ink shadow-2xs hover:bg-lime transition-colors">
                {{ $post->category->name }}
            </a>

            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight text-ink leading-tight">
                {{ $post->title }}
            </h1>
        </div>

        <!-- Meta Bar: Author, Date, View Count & Reading Time -->
        <div class="flex flex-col gap-4 border-y-2 border-line-strong py-3.5 sm:flex-row sm:items-center sm:justify-between text-xs font-mono text-ink-muted">
            <!-- Author & Time -->
            <div class="flex items-center gap-3">
                <a href="{{ route('authors.show', $post->author) }}" class="group flex items-center gap-2.5">
                    @if ($post->author->avatar)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($post->author->avatar) }}" alt="Avatar" class="size-8 rounded-none object-cover border border-line-strong">
                    @else
                        <span class="grid size-8 place-items-center bg-ink text-xs font-bold font-mono text-lime border border-ink">
                            {{ mb_strtoupper(mb_substr($post->author->name, 0, 1)) }}
                        </span>
                    @endif
                    <div>
                        <p class="font-bold text-ink group-hover:underline transition-colors">{{ $post->author->name }}</p>
                        <p class="text-[10px] text-ink-muted font-sans">Tác giả bài viết</p>
                    </div>
                </a>

                <span class="text-line">|</span>

                <div class="text-[11px] space-y-0.5">
                    <p class="text-ink font-bold">{{ $post->published_at->format('d/m/Y H:i') }}</p>
                    <p class="text-ink-muted tabular-nums flex items-center gap-1.5">
                        <span>{{ number_format($post->view_count) }} xem</span>
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

            <!-- Action Buttons: Bookmark -->
            <div id="meta-action-buttons" class="flex items-center gap-2">
                @auth
                    @if (auth()->user()->hasVerifiedEmail())
                        <form method="POST" action="{{ $isFavorited ? route('favorites.destroy', $post) : route('favorites.store', $post) }}" class="inline">
                            @csrf
                            @if ($isFavorited) @method('DELETE') @endif
                            <button type="submit"
                                    class="inline-flex min-h-8 cursor-pointer items-center gap-1.5 border border-line-strong px-3 py-1.5 text-xs font-bold shadow-2xs transition-all active:scale-[0.98] {{ $isFavorited ? 'bg-lime text-ink shadow-brutal-sm' : 'bg-surface text-ink hover:bg-paper' }}">
                                <svg class="size-3.5 {{ $isFavorited ? 'text-ink fill-current' : 'text-ink-muted' }}" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd" />
                                </svg>
                                <span>{{ $isFavorited ? 'Bỏ lưu bài viết' : 'Lưu bài viết' }}</span>
                            </button>
                        </form>
                    @endif
                @endauth
            </div>
        </div>

        <!-- Reader Utilities Bar (TTS, Font Resizer, Print, Social Share) -->
        <div id="reader-utilities-bar" class="flex flex-wrap items-center justify-between gap-3 border-2 border-line-strong bg-paper p-3 shadow-brutal-sm">
            <!-- Left: TTS & Font Resizer -->
            <div class="flex flex-wrap items-center gap-2.5">
                <!-- Text-to-Speech (Demo) -->
                <button type="button"
                        id="btn-tts"
                        class="inline-flex min-h-8 cursor-pointer items-center gap-1.5 border border-line-strong bg-surface px-3 py-1.5 text-xs font-bold font-mono text-ink shadow-2xs transition-all hover:bg-lime active:scale-[0.98]">
                    <svg id="tts-icon-play" class="size-3.5 text-ink" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                    </svg>
                    <svg id="tts-icon-stop" class="size-3.5 text-danger hidden animate-pulse" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 00-1 1v4a1 1 0 001 1h4a1 1 0 001-1V8a1 1 0 00-1-1H8z" clip-rule="evenodd" />
                    </svg>
                    <span id="tts-text">Nghe đọc bài (Demo)</span>
                </button>

                <!-- Font Resizer -->
                <div class="inline-flex items-center border border-line-strong bg-surface p-0.5 text-xs font-mono font-bold text-ink">
                    <button type="button" id="btn-font-decrease" class="px-2 py-1 hover:bg-paper transition-colors cursor-pointer" title="Thu nhỏ cỡ chữ">A-</button>
                    <span class="px-0.5 text-line">|</span>
                    <button type="button" id="btn-font-reset" class="px-1.5 py-1 text-[11px] hover:bg-paper transition-colors cursor-pointer" title="Cỡ chữ mặc định">A</button>
                    <span class="px-0.5 text-line">|</span>
                    <button type="button" id="btn-font-increase" class="px-2 py-1 hover:bg-paper transition-colors cursor-pointer font-black" title="Phóng to cỡ chữ">A+</button>
                </div>

                <!-- Print Button -->
                <button type="button"
                        onclick="window.print()"
                        class="inline-flex min-h-8 cursor-pointer items-center gap-1.5 border border-line-strong bg-surface px-3 py-1.5 text-xs font-bold font-mono text-ink shadow-2xs transition-all hover:bg-paper active:scale-[0.98]"
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
                        class="inline-flex min-h-8 cursor-pointer items-center gap-1 border border-line-strong bg-surface px-2.5 py-1.5 text-xs font-bold text-ink shadow-2xs transition-all hover:bg-paper active:scale-[0.98]"
                        title="Chia sẻ lên Facebook">
                    <svg class="size-3.5 fill-current" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    <span class="hidden sm:inline">FB</span>
                </button>

                <!-- X (Twitter) -->
                <button type="button"
                        onclick="window.open('https://twitter.com/intent/tweet?url=' + encodeURIComponent(window.location.href) + '&text=' + encodeURIComponent('{{ addslashes($post->title) }}'), 'twitter-share', 'width=580,height=420'); return false;"
                        class="inline-flex min-h-8 cursor-pointer items-center gap-1 border border-line-strong bg-surface px-2.5 py-1.5 text-xs font-bold text-ink shadow-2xs transition-all hover:bg-paper active:scale-[0.98]"
                        title="Chia sẻ lên X (Twitter)">
                    <svg class="size-3.5 fill-current" viewBox="0 0 24 24">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                    <span class="hidden sm:inline">X</span>
                </button>

                <!-- Zalo -->
                <button type="button"
                        onclick="window.open('https://sp.zalo.me/share_inline?link=' + encodeURIComponent(window.location.href), 'zalo-share', 'width=580,height=420'); return false;"
                        class="inline-flex min-h-8 cursor-pointer items-center gap-1 border border-line-strong bg-surface px-2.5 py-1.5 text-xs font-bold text-ink shadow-2xs transition-all hover:bg-paper active:scale-[0.98]"
                        title="Chia sẻ qua Zalo">
                    <span class="font-bold text-[10px] tracking-tight">Zalo</span>
                </button>

                <!-- Copy Link Button with Toast Feedback -->
                <button type="button"
                        id="btn-copy-article-link"
                        class="inline-flex min-h-8 cursor-pointer items-center gap-1.5 border border-line-strong bg-lime px-3 py-1.5 text-xs font-bold text-ink shadow-brutal-sm transition-all hover:bg-lime-hover active:scale-[0.98]"
                        title="Sao chép liên kết bài viết">
                    <svg class="size-3.5 text-ink" viewBox="0 0 20 20" fill="currentColor">
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
        @if ($thumbnailUrl && ($post->show_thumbnail_in_post ?? true))
            <figure class="overflow-hidden border-2 border-line-strong bg-paper">
                <img src="{{ $thumbnailUrl }}"
                     alt="{{ $post->title }}"
                     class="aspect-video w-full object-cover"
                     loading="eager">
                <figcaption class="px-4 py-2 text-center text-xs font-mono text-ink-muted italic bg-paper-light border-t border-line">
                    Hình ảnh minh họa cho bài viết · Nguồn: NewsHub
                </figcaption>
            </figure>
        @endif

        <!-- Body Content with Dynamic Font Resizer ID -->
        <div id="article-content-body" class="text-base sm:text-lg text-ink leading-relaxed sm:leading-8 font-normal space-y-5 whitespace-pre-line pt-2 transition-all duration-150">
            {!! $safeContent !!}
        </div>

        <!-- Tags List -->
        @if ($post->tags->isNotEmpty())
            <div class="border-t-2 border-line-strong pt-5 space-y-2 font-mono">
                <p class="text-xs font-bold uppercase tracking-wider text-ink">Từ khóa liên quan:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach ($post->tags as $tag)
                        <a href="{{ route('news.index', ['tag' => $tag->slug]) }}"
                           class="inline-flex items-center gap-1 border border-line bg-paper px-3 py-1 text-xs text-ink transition-colors hover:border-line-strong hover:bg-lime font-bold">
                            <span>#{{ $tag->name }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Author Profile Box Card -->
        <div id="author-box" class="border-2 border-line-strong bg-paper p-5 shadow-brutal-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    @if ($post->author->avatar)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($post->author->avatar) }}" alt="{{ $post->author->name }}" class="size-12 rounded-none object-cover border-2 border-line-strong">
                    @else
                        <span class="grid size-12 place-items-center bg-ink text-base font-bold font-mono text-lime border-2 border-ink">
                            {{ mb_strtoupper(mb_substr($post->author->name, 0, 1)) }}
                        </span>
                    @endif
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-black text-ink">{{ $post->author->name }}</h3>
                            <span class="border border-line-strong bg-lime px-2 py-0.5 text-[10px] font-bold font-mono uppercase tracking-wider text-ink shadow-2xs">
                                {{ $post->author->role?->value ?? 'Tác giả' }}
                            </span>
                        </div>
                        <p class="text-xs text-ink-muted mt-1">Cây bút chuyên môn tại tòa soạn NewsHub.</p>
                    </div>
                </div>

                <a href="{{ route('authors.show', $post->author) }}"
                   class="inline-flex items-center justify-center gap-1.5 border border-line-strong bg-surface px-4 py-2 text-xs font-bold font-mono text-ink shadow-2xs transition-colors hover:bg-paper hover:shadow-brutal-sm">
                    <span>Xem bài viết của tác giả</span>
                    <span aria-hidden="true">&rarr;</span>
                </a>
            </div>
        </div>
    </article>

    <!-- Comments Section -->
    <section id="comments-section" class="border-t-2 border-line-strong pt-8 space-y-6">
        <div class="flex items-center justify-between border-b border-line pb-3">
            <div class="flex items-center gap-2">
                <span class="size-2.5 bg-lime border border-ink"></span>
                <h2 class="text-xl font-black tracking-tight text-ink font-mono uppercase">
                    Bình luận & Ý kiến độc giả
                </h2>
                <span class="border border-line-strong bg-paper px-2.5 py-0.5 text-xs font-bold font-mono tabular-nums text-ink">
                    {{ $comments->total() }}
                </span>
            </div>
        </div>

        <!-- Add Comment Form -->
        @auth
            @if (auth()->user()->hasVerifiedEmail())
                <form action="{{ route('comments.store', $post) }}" method="POST" class="border-2 border-line-strong bg-surface p-4 shadow-brutal-sm font-sans">
                    @csrf
                    <div class="flex items-start gap-3">
                        @if (auth()->user()->avatar)
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url(auth()->user()->avatar) }}" alt="Avatar" class="size-8 rounded-none object-cover border border-line-strong shrink-0">
                        @else
                            <span class="grid size-8 shrink-0 place-items-center bg-ink text-xs font-bold font-mono text-lime border border-ink">
                                {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                            </span>
                        @endif

                        <div class="flex-1 space-y-2.5">
                            <textarea name="content"
                                      rows="3"
                                      required
                                      maxlength="1000"
                                      placeholder="Chia sẻ quan điểm của bạn về bài viết này..."
                                      class="w-full border border-line bg-paper p-3 text-xs sm:text-sm text-ink placeholder:text-ink-muted outline-none transition-all focus:border-line-strong focus:shadow-brutal-sm"></textarea>

                            <div class="flex items-center justify-between text-xs text-ink-muted font-mono">
                                <span class="text-[11px]">Tối đa 1000 ký tự. Vui lòng tuân thủ quy chế kiểm duyệt.</span>
                                <button type="submit"
                                        class="inline-flex cursor-pointer items-center gap-1.5 border border-line-strong bg-lime px-4 py-2 text-xs font-bold text-ink shadow-brutal-sm transition-colors hover:bg-lime-hover active:scale-[0.98]">
                                    <span>Gửi bình luận</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            @else
                <div class="border-2 border-line-strong bg-amber-50 px-4 py-3 text-xs text-amber-900 flex items-center justify-between gap-3 shadow-2xs">
                    <p>Bạn cần hoàn tất xác minh email tài khoản để tham gia gửi bình luận.</p>
                    <a href="{{ route('verification.notice') }}" class="shrink-0 font-bold underline hover:text-ink">Xác minh ngay</a>
                </div>
            @endif
        @else
            <div class="border border-line bg-paper px-4 py-3 text-center text-xs text-ink-muted">
                <p>Vui lòng <a href="{{ route('login') }}" class="font-bold text-ink underline">đăng nhập</a> hoặc <a href="{{ route('register') }}" class="font-bold text-ink underline">đăng ký</a> để tham gia gửi bình luận cùng độc giả khác.</p>
            </div>
        @endauth

        <!-- Comments List -->
        <div class="space-y-3">
            @forelse ($comments as $comment)
                <div class="space-y-2">
                    <x-comment-item :comment="$comment" :post="$post" :report-reasons="$reportReasons" />
                    @if ($comment->replies->isNotEmpty())
                        <div class="ml-3 sm:ml-6 space-y-2 border-l-2 border-line pl-2.5 sm:pl-3.5">
                            @foreach ($comment->replies as $reply)
                                <x-comment-item :comment="$reply" :post="$post" :report-reasons="$reportReasons" :is-reply="true" />
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <div class="border border-line bg-paper p-6 text-center text-xs font-mono text-ink-muted">
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
        <section aria-labelledby="related-heading" class="border-t-2 border-line-strong pt-8 space-y-5">
            <div class="flex items-center justify-between border-b border-line pb-3">
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

            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($relatedPosts as $relatedPost)
                    <x-post-card :post="$relatedPost" />
                @endforeach
            </div>
        </section>
    @endif

    <!-- Print Only Footer -->
    <div class="print-only-footer font-mono">
        <p>© {{ date('Y') }} NewsHub. Mọi quyền được bảo lưu. Bài viết được trích xuất từ phiên bản điện tử chính thức của NewsHub.</p>
    </div>
</div>

<!-- Reader Utilities Scripts -->
<script>
    // --- 1. COPY LINK FEEDBACK ---
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

    // --- 2. FONT RESIZER ---
    const articleBody = document.getElementById('article-content-body');
    const fontSizes = ['text-sm sm:text-base', 'text-base sm:text-lg', 'text-lg sm:text-xl', 'text-xl sm:text-2xl'];
    let currentFontIndex = parseInt(localStorage.getItem('newshub_font_size_idx') || '1', 10);

    function applyFontSize(index) {
        if (!articleBody) return;
        fontSizes.forEach(cls => articleBody.classList.remove(...cls.split(' ')));
        articleBody.classList.add(...fontSizes[index].split(' '));
        localStorage.setItem('newshub_font_size_idx', index);
    }

    if (currentFontIndex !== 1 && currentFontIndex >= 0 && currentFontIndex < fontSizes.length) {
        applyFontSize(currentFontIndex);
    }

    document.getElementById('btn-font-increase')?.addEventListener('click', () => {
        if (currentFontIndex < fontSizes.length - 1) {
            currentFontIndex++;
            applyFontSize(currentFontIndex);
        }
    });

    document.getElementById('btn-font-decrease')?.addEventListener('click', () => {
        if (currentFontIndex > 0) {
            currentFontIndex--;
            applyFontSize(currentFontIndex);
        }
    });

    document.getElementById('btn-font-reset')?.addEventListener('click', () => {
        currentFontIndex = 1;
        applyFontSize(currentFontIndex);
    });

    // --- 3. TEXT-TO-SPEECH (DEMO) ---
    const ttsBtn = document.getElementById('btn-tts');
    const ttsText = document.getElementById('tts-text');
    const ttsIconPlay = document.getElementById('tts-icon-play');
    const ttsIconStop = document.getElementById('tts-icon-stop');
    let isSpeaking = false;

    if ('speechSynthesis' in window && ttsBtn) {
        function stopTTS() {
            isSpeaking = false;
            if (!ttsBtn) return;
            ttsText.textContent = 'Nghe đọc bài (Demo)';
            ttsBtn.classList.remove('bg-rose-100', 'text-danger');
            ttsBtn.classList.add('bg-surface', 'text-ink');
            ttsIconPlay?.classList.remove('hidden');
            ttsIconStop?.classList.add('hidden');
        }

        ttsBtn.addEventListener('click', () => {
            if (isSpeaking) {
                window.speechSynthesis.cancel();
                stopTTS();
                return;
            }

            const title = @json($post->title);
            const summary = @json($post->summary ?? '');
            const bodyText = articleBody ? articleBody.innerText : '';
            const fullSpeechText = title + '. ' + (summary ? summary + '. ' : '') + bodyText;

            const utterance = new SpeechSynthesisUtterance(fullSpeechText);
            utterance.rate = 0.95;
            utterance.lang = 'vi-VN';

            const voices = window.speechSynthesis.getVoices();
            const viVoice = voices.find(v => v.lang === 'vi-VN' || v.lang.startsWith('vi'));
            if (viVoice) {
                utterance.voice = viVoice;
            }

            utterance.onstart = () => {
                isSpeaking = true;
                ttsText.textContent = 'Đang đọc... (Bấm dừng)';
                ttsBtn.classList.remove('bg-surface', 'text-ink');
                ttsBtn.classList.add('bg-rose-100', 'text-danger');
                ttsIconPlay?.classList.add('hidden');
                ttsIconStop?.classList.remove('hidden');
            };

            utterance.onend = stopTTS;
            utterance.onerror = stopTTS;

            window.speechSynthesis.speak(utterance);
        });

        window.addEventListener('beforeunload', () => {
            if (window.speechSynthesis.speaking) {
                window.speechSynthesis.cancel();
            }
        });
    } else if (ttsBtn) {
        ttsBtn.title = 'Trình duyệt không hỗ trợ Web Speech API';
        ttsBtn.classList.add('opacity-50', 'cursor-not-allowed');
    }
</script>
@endsection
