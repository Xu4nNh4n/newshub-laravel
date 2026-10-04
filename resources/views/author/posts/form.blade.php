@extends('layouts.dashboard', ['title' => isset($post) ? 'Sửa bài viết' : 'Viết bài', 'breadcrumbs' => $breadcrumbs ?? null])

@section('content')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
<style>
    /* OpenJev Editorial Theme for Quill Editor */
    .ql-toolbar.ql-snow {
        background-color: var(--color-paper) !important;
        border: 2px solid var(--color-line-strong) !important;
        border-bottom: none !important;
        border-radius: 0 !important;
        font-family: inherit;
        padding: 0.625rem !important;
    }
    .ql-container.ql-snow {
        background-color: var(--color-surface) !important;
        border: 2px solid var(--color-line-strong) !important;
        border-radius: 0 !important;
        color: var(--color-ink) !important;
        font-family: inherit;
        font-size: 0.95rem;
    }
    .ql-snow .ql-stroke {
        stroke: var(--color-ink) !important;
    }
    .ql-snow .ql-fill {
        fill: var(--color-ink) !important;
    }
    .ql-snow .ql-picker {
        color: var(--color-ink) !important;
        font-family: var(--font-mono, monospace);
        font-size: 0.75rem;
    }
    .ql-snow .ql-picker-options {
        background-color: var(--color-surface) !important;
        border: 2px solid var(--color-line-strong) !important;
        border-radius: 0 !important;
        box-shadow: 2px 2px 0px var(--color-line-strong) !important;
    }
    .ql-snow.ql-toolbar button:hover,
    .ql-snow .ql-toolbar button:hover,
    .ql-snow.ql-toolbar button.ql-active,
    .ql-snow .ql-toolbar button.ql-active {
        background-color: var(--color-lime) !important;
    }
    .ql-snow.ql-toolbar button:hover .ql-stroke,
    .ql-snow .ql-toolbar button:hover .ql-stroke,
    .ql-snow.ql-toolbar button.ql-active .ql-stroke,
    .ql-snow .ql-toolbar button.ql-active .ql-stroke {
        stroke: var(--color-ink) !important;
    }
    .ql-snow.ql-toolbar button:hover .ql-fill,
    .ql-snow .ql-toolbar button:hover .ql-fill,
    .ql-snow.ql-toolbar button.ql-active .ql-fill,
    .ql-snow .ql-toolbar button.ql-active .ql-fill {
        fill: var(--color-ink) !important;
    }
    .ql-editor {
        min-height: 340px;
        line-height: 1.8;
        padding: 1.25rem !important;
    }
    .ql-editor.ql-blank::before {
        color: var(--color-ink-muted) !important;
        font-style: italic;
    }
    .ql-editor img {
        max-width: 100%;
        height: auto;
        border: 2px solid var(--color-line-strong);
        margin: 1.5rem auto;
        display: block;
        box-shadow: 2px 2px 0px var(--color-line-strong);
    }
    .ql-editor iframe {
        width: 100%;
        aspect-ratio: 16 / 9;
        height: auto;
        border: 2px solid var(--color-line-strong);
        margin: 1.5rem 0;
    }
    .ql-editor blockquote {
        border-left: 4px solid var(--color-ink) !important;
        background-color: var(--color-paper);
        padding: 1rem 1.25rem;
        color: var(--color-ink);
        font-style: italic;
        margin: 1.5rem 0;
    }
    .ql-editor h2 {
        font-size: 1.5rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: var(--color-ink);
        margin-top: 1.75rem;
        margin-bottom: 0.75rem;
    }
    .ql-editor h3 {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--color-ink);
        margin-top: 1.25rem;
        margin-bottom: 0.5rem;
    }
</style>

<div class="mx-auto max-w-4xl space-y-6">
    <!-- Header -->
    <div class="border-b-2 border-line-strong pb-5">
        <span class="font-mono text-xs font-bold uppercase tracking-wider text-ink-muted block mb-1">Tòa soạn / Biên soạn nội dung</span>
        <h1 class="text-2xl font-black uppercase tracking-tight text-ink sm:text-3xl font-heading">
            {{ isset($post) ? 'Chỉnh sửa bài viết' : 'Viết bài mới' }}
        </h1>
        <p class="mt-1 text-xs text-ink-muted leading-relaxed">
            @if(auth()->user()->role === \App\Enums\UserRole::Admin)
                Quản trị viên có thể lưu bản nháp hoặc chọn xuất bản trực tiếp bài viết lên hệ thống tin tức.
            @else
                Bài viết được lưu dưới dạng bản nháp. Sau khi hoàn tất, bạn gửi bài để Admin kiểm duyệt trước khi xuất bản.
            @endif
        </p>
    </div>

    @isset($post)
        @php
            $pendingRequest = $post->requests->firstWhere('status', \App\Enums\PostRequestStatus::Pending);
            $latestRejectedRequest = $post->requests->where('status', \App\Enums\PostRequestStatus::Rejected)->sortByDesc('id')->first();
        @endphp

        @if($pendingRequest)
            <div class="border-2 border-line-strong bg-paper p-4 text-xs text-ink flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-brutal-sm">
                <div class="flex items-start gap-3 min-w-0 flex-1">
                    <span class="mt-0.5 grid size-5 shrink-0 place-items-center bg-lime text-ink border border-line-strong font-mono font-bold text-xs">
                        !
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <strong class="font-black text-ink uppercase tracking-tight">Bài viết có yêu cầu {{ $pendingRequest->type === \App\Enums\PostRequestType::Removal ? 'Gỡ bài' : 'Đính chính / Sửa lỗi' }} chờ Ban biên tập</strong>
                            <span class="border border-line-strong px-2 py-0.5 text-[10px] font-mono font-bold uppercase {{ $pendingRequest->priority === \App\Enums\PostRequestPriority::Urgent ? 'bg-danger text-paper' : 'bg-lime text-ink' }}">
                                {{ $pendingRequest->priority === \App\Enums\PostRequestPriority::Urgent ? 'Khẩn cấp' : 'Bình thường' }}
                            </span>
                            <span class="font-mono text-[11px] text-ink-muted">· Gửi {{ $pendingRequest->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <p class="mt-1 text-xs text-ink"><strong class="font-bold">Lý do:</strong> {{ $pendingRequest->reason }}</p>
                        @if($pendingRequest->notes)
                            <p class="mt-1 text-[11px] text-ink-muted italic">Đề xuất: {{ $pendingRequest->notes }}</p>
                        @endif
                    </div>
                </div>

                <form method="POST" action="{{ route('author.posts.requests.destroy', [$post, $pendingRequest]) }}"
                      data-confirm="Bạn có chắc chắn muốn hủy yêu cầu {{ $pendingRequest->type === \App\Enums\PostRequestType::Removal ? 'gỡ bài' : 'đính chính' }} này? Bài viết sẽ trở lại trạng thái bình thường."
                      data-confirm-title="Hủy yêu cầu bài viết"
                      data-confirm-subtext="{{ $post->title }}"
                      data-confirm-type="danger"
                      data-confirm-btn="Xác nhận hủy yêu cầu"
                      class="shrink-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex cursor-pointer items-center justify-center gap-1.5 border-2 border-line-strong bg-danger px-3 py-1.5 text-xs font-mono font-bold uppercase text-paper shadow-brutal-sm hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
                        <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                        <span>Hủy yêu cầu này</span>
                    </button>
                </form>
            </div>
        @elseif($latestRejectedRequest)
            <div class="border-2 border-line-strong bg-paper p-4 text-xs text-ink shadow-brutal-sm">
                <div class="flex items-center gap-2 font-bold text-danger uppercase tracking-tight">
                    <span class="font-mono font-black text-sm">⚠</span>
                    <span>Yêu cầu {{ $latestRejectedRequest->type === \App\Enums\PostRequestType::Removal ? 'gỡ bài' : 'đính chính' }} gần nhất đã bị từ chối</span>
                </div>
                <p class="mt-1 text-xs text-ink"><strong class="font-bold text-ink">Lý do từ Ban biên tập:</strong> {{ $latestRejectedRequest->admin_notes }}</p>
            </div>
        @endif
    @endisset

    <form method="POST" enctype="multipart/form-data" action="{{ isset($post) ? route('author.posts.update', $post) : route('author.posts.store') }}" class="space-y-6 border-2 border-line-strong bg-surface p-6 sm:p-8 shadow-brutal">
        @csrf
        @isset($post) @method('PUT') @endisset

        <div>
            <label for="title" class="block font-mono text-xs font-bold uppercase tracking-wider text-ink">
                Tiêu đề bài viết <span class="text-danger">*</span>
            </label>
            <input id="title" name="title" required value="{{ old('title', $post->title ?? '') }}" placeholder="Nhập tiêu đề ấn tượng cho bài viết..." class="mt-1.5 w-full border-2 border-line bg-paper px-3.5 py-2.5 text-sm font-medium text-ink outline-none focus:border-ink transition-colors">
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="meta_title" class="block font-mono text-xs font-bold uppercase tracking-wider text-ink">
                    SEO title (tối đa 60 ký tự)
                </label>
                <input id="meta_title" name="meta_title" maxlength="60" value="{{ old('meta_title', $post->meta_title ?? '') }}" placeholder="Tiêu đề chuẩn tìm kiếm Google..." class="mt-1.5 w-full border-2 border-line bg-paper px-3.5 py-2.5 text-xs text-ink outline-none focus:border-ink transition-colors">
            </div>

            <div>
                <label for="slug" class="block font-mono text-xs font-bold uppercase tracking-wider text-ink">
                    Slug (để trống sẽ tự sinh từ tiêu đề)
                </label>
                <input id="slug" name="slug" value="{{ old('slug', $post->slug ?? '') }}" placeholder="duong-dan-bai-viet" class="mt-1.5 w-full border-2 border-line bg-paper px-3.5 py-2.5 text-xs font-mono text-ink outline-none focus:border-ink transition-colors">
            </div>
        </div>

        <div>
            <label for="category_id" class="block font-mono text-xs font-bold uppercase tracking-wider text-ink">
                Chuyên mục <span class="text-danger">*</span>
            </label>
            <select id="category_id" name="category_id" required class="mt-1.5 w-full border-2 border-line bg-paper px-3.5 py-2.5 text-xs text-ink outline-none focus:border-ink transition-colors">
                @php
                    $pCategories = $categories->whereNull('parent_id');
                @endphp
                @foreach($pCategories as $pCat)
                    @php $cCategories = $categories->where('parent_id', $pCat->id); @endphp
                    @if($cCategories->isNotEmpty())
                        <optgroup label="{{ $pCat->name }}" class="font-bold">
                            <option value="{{ $pCat->id }}" @selected((int) old('category_id', $post->category_id ?? 0) === $pCat->id)>
                                {{ $pCat->name }} (Chuyên mục chính)
                            </option>
                            @foreach($cCategories as $cCat)
                                <option value="{{ $cCat->id }}" @selected((int) old('category_id', $post->category_id ?? 0) === $cCat->id)>
                                    &nbsp;&nbsp;— {{ $cCat->name }}
                                </option>
                            @endforeach
                        </optgroup>
                    @else
                        <option value="{{ $pCat->id }}" @selected((int) old('category_id', $post->category_id ?? 0) === $pCat->id)>
                            {{ $pCat->name }}
                        </option>
                    @endif
                @endforeach
            </select>
        </div>

        @if(auth()->user()->role === \App\Enums\UserRole::Admin)
            <div>
                <label for="published_at" class="block font-mono text-xs font-bold uppercase tracking-wider text-ink">
                    Thời gian xuất bản (Tùy chọn)
                </label>
                <input id="published_at"
                       type="datetime-local"
                       name="published_at"
                       value="{{ old('published_at', isset($post) && $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : '') }}"
                       class="mt-1.5 w-full border-2 border-line bg-paper px-3.5 py-2.5 text-xs font-mono text-ink outline-none focus:border-ink transition-colors">
                <p class="mt-1 font-mono text-[11px] text-ink-muted">
                    Để trống sẽ tự động lấy thời điểm hiện tại khi bạn chọn Xuất bản ngay.
                </p>
                @error('published_at')
                    <p class="mt-1 text-xs text-danger font-bold">{{ $message }}</p>
                @enderror
            </div>
        @endif

        <div>
            <label for="summary" class="block font-mono text-xs font-bold uppercase tracking-wider text-ink">
                Tóm tắt bài viết / Sa-pô
            </label>
            <textarea id="summary" name="summary" rows="3" class="mt-1.5 w-full border-2 border-line bg-paper px-3.5 py-2.5 text-xs text-ink outline-none focus:border-ink transition-colors" placeholder="Đoạn văn ngắn mở đầu giới thiệu nội dung chính của bài viết...">{{ old('summary', $post->summary ?? '') }}</textarea>
        </div>

        <div>
            <label for="meta_description" class="block font-mono text-xs font-bold uppercase tracking-wider text-ink">
                SEO description (tối đa 160 ký tự)
            </label>
            <textarea id="meta_description" name="meta_description" maxlength="160" rows="2" class="mt-1.5 w-full border-2 border-line bg-paper px-3.5 py-2.5 text-xs text-ink outline-none focus:border-ink transition-colors">{{ old('meta_description', $post->meta_description ?? '') }}</textarea>
        </div>

        <div>
            <label for="thumbnail" class="block font-mono text-xs font-bold uppercase tracking-wider text-ink">
                Ảnh đại diện (JPG, PNG, WEBP; tối đa 4 MB)
            </label>
            <input id="thumbnail" type="file" name="thumbnail" accept="image/jpeg,image/png,image/webp" class="mt-1.5 w-full border-2 border-line bg-paper px-3.5 py-2 text-xs text-ink file:mr-3 file:border-2 file:border-line-strong file:bg-surface file:px-3 file:py-1 file:font-mono file:text-xs file:font-bold file:uppercase file:text-ink hover:file:bg-lime">
            
            <div class="mt-2.5 flex flex-wrap items-center gap-4">
                <label class="inline-flex items-center gap-2 font-mono text-xs text-ink cursor-pointer select-none">
                    <input type="hidden" name="show_thumbnail_in_post" value="0">
                    <input type="checkbox" name="show_thumbnail_in_post" value="1" @checked(old('show_thumbnail_in_post', $post->show_thumbnail_in_post ?? true)) class="border-2 border-line text-ink focus:ring-0">
                    <span>Hiển thị ảnh đại diện ở đầu bài viết (Bỏ chọn nếu bạn muốn đặt ảnh ở vị trí khác)</span>
                </label>
                @if(isset($post) && $post->thumbnail)
                    <label class="inline-flex items-center gap-2 font-mono text-xs text-danger font-bold cursor-pointer select-none">
                        <input type="checkbox" name="remove_thumbnail" value="1" class="border-2 border-line text-danger focus:ring-0">
                        Xóa ảnh hiện tại
                    </label>
                @endif
            </div>
        </div>

        <div>
            <div class="flex flex-wrap items-center justify-between gap-1 pb-1">
                <label class="block font-mono text-xs font-bold uppercase tracking-wider text-ink">
                    Nội dung bài viết <span class="text-danger">*</span>
                </label>
                <div class="flex items-center gap-2 font-mono text-[11px] text-ink-muted">
                    <span class="inline-flex items-center gap-1 font-bold text-ink">
                        <span>● Trực quan WYSIWYG</span>
                    </span>
                    <span>· Hỗ trợ ảnh & video YouTube</span>
                </div>
            </div>

            <!-- Hidden input for standard HTTP form submit -->
            <textarea id="content" name="content" required class="sr-only">{{ old('content', $post->content ?? '') }}</textarea>

            <!-- Quill Editor Container -->
            <div class="mt-1.5">
                <div id="quill-editor">
                    {!! $editorContent !!}
                </div>
            </div>
            <div class="mt-2 flex flex-wrap items-center justify-between gap-2 font-mono text-[11px] text-ink-muted">
                <span>Mẹo: Nhấn nút <strong>Ảnh</strong> để chèn ảnh vào thân bài, hoặc nút <strong>Video</strong> để dán link YouTube.</span>
                <span>Hỗ trợ trích dẫn, tiêu đề H2/H3</span>
            </div>
        </div>

        <!-- Interactive Tag Manager Component -->
        <div class="border-t-2 border-line-strong pt-5 space-y-3" id="tag-manager"
             data-existing-tags='@json($tags->map(fn($t) => ['id' => $t->id, 'name' => $t->name, 'slug' => $t->slug]))'>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                <div>
                    <label class="block font-mono text-xs font-bold uppercase tracking-wider text-ink">
                        Thẻ bài viết (Tags)
                    </label>
                    <p class="font-mono text-[11px] text-ink-muted mt-0.5">
                        Gõ từ khóa rồi nhấn <strong>Enter</strong> hoặc dấu phẩy (<strong>,</strong>), hoặc chọn nhanh bên dưới.
                    </p>
                </div>
                <span class="font-mono text-[11px] text-ink-muted shrink-0">
                    Đã chọn <strong class="text-ink font-bold" id="selected-tags-count">0</strong> thẻ
                </span>
            </div>

            <!-- Tag Input & Selected Chips Box -->
            <div class="min-h-12 w-full border-2 border-line bg-paper p-2.5 text-xs transition-colors focus-within:border-ink">
                <div class="flex flex-wrap items-center gap-2" id="tags-chips-wrapper">
                    <!-- Selected Chips Container -->
                    <div id="selected-tags-container" class="contents"></div>

                    <!-- Text Input for new/searched tag -->
                    <div class="flex-1 min-w-[200px] flex items-center gap-2 py-0.5">
                        <input type="text"
                               id="tag-text-input"
                               placeholder="Nhập tên thẻ mới hoặc từ khóa..."
                               autocomplete="off"
                               class="w-full bg-transparent text-xs text-ink placeholder-ink-muted outline-none">
                        <button type="button"
                                id="btn-add-tag"
                                class="shrink-0 inline-flex items-center gap-1 border-2 border-line-strong bg-surface hover:bg-lime px-2.5 py-1 font-mono text-[11px] font-bold text-ink transition-colors cursor-pointer shadow-brutal-sm">
                            + Thêm
                        </button>
                    </div>
                </div>
            </div>

            <!-- Quick Suggestions Cloud -->
            <div class="space-y-1.5 pt-1">
                <div class="flex items-center justify-between font-mono text-[11px] text-ink-muted">
                    <span>Gợi ý thẻ phổ biến trong hệ thống:</span>
                    <span>Bấm để thêm hoặc gỡ</span>
                </div>
                <div class="flex flex-wrap gap-1.5" id="suggested-tags-cloud">
                    @foreach($tags as $tag)
                        @php
                            $isSelected = in_array($tag->id, old('tag_ids', isset($post) ? $post->tags->pluck('id')->all() : []));
                        @endphp
                        <button type="button"
                                data-tag-suggestion="{{ $tag->id }}"
                                data-tag-name="{{ $tag->name }}"
                                @class([
                                    'inline-flex items-center gap-1 border px-2.5 py-1 font-mono text-xs transition-all cursor-pointer',
                                    'border-line-strong bg-ink text-paper font-bold' => $isSelected,
                                    'border-line bg-surface text-ink hover:border-line-strong hover:bg-paper' => !$isSelected,
                                ])>
                            <span>#{{ $tag->name }}</span>
                            <span class="text-[10px] {{ $isSelected ? 'text-lime' : 'text-ink-muted' }}">
                                {{ $isSelected ? '✓' : '+' }}
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Hidden container for initial server-side rendering / non-JS support -->
            <div id="hidden-tags-inputs" class="hidden">
                @foreach($tags as $tag)
                    @if(in_array($tag->id, old('tag_ids', isset($post) ? $post->tags->pluck('id')->all() : [])))
                        <input type="checkbox" name="tag_ids[]" value="{{ $tag->id }}" checked data-fallback-id="{{ $tag->id }}">
                    @endif
                @endforeach
                @if(old('tag_names'))
                    @foreach(old('tag_names') as $name)
                        <input type="hidden" name="tag_names[]" value="{{ $name }}" data-fallback-name="{{ $name }}">
                    @endforeach
                @endif
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 pt-5 border-t-2 border-line-strong">
            <div class="flex flex-wrap items-center gap-2.5">
                @if(auth()->user()->role === \App\Enums\UserRole::Admin)
                    <button type="submit" name="action" value="publish" class="inline-flex min-h-10 items-center justify-center gap-1.5 border-2 border-line-strong bg-lime px-5 py-2 font-mono text-xs font-bold uppercase text-ink shadow-brutal hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all cursor-pointer">
                        <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                        </svg>
                        <span>Xuất bản ngay</span>
                    </button>
                    <button type="submit" name="action" value="draft" class="inline-flex min-h-10 items-center justify-center border-2 border-line-strong bg-surface px-5 py-2 font-mono text-xs font-bold uppercase text-ink shadow-brutal hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all cursor-pointer">
                        Lưu bản nháp
                    </button>
                @else
                    <button type="submit" name="action" value="draft" class="inline-flex min-h-10 items-center justify-center border-2 border-line-strong bg-lime px-5 py-2 font-mono text-xs font-bold uppercase text-ink shadow-brutal hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all cursor-pointer">
                        Lưu bản nháp
                    </button>
                @endif

                <!-- Live Preview Button -->
                <button type="button" id="btn-open-preview" class="inline-flex min-h-10 items-center justify-center gap-1.5 border-2 border-line-strong bg-paper px-4 py-2 font-mono text-xs font-bold uppercase text-ink shadow-brutal-sm hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all cursor-pointer">
                    <svg class="size-4" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 12.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" />
                        <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 010-1.186A10.004 10.004 0 0110 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0110 17c-4.257 0-7.893-2.66-9.336-6.41zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                    </svg>
                    <span>Xem trước</span>
                </button>

                <a href="{{ route('author.posts.index') }}" class="inline-flex min-h-10 items-center justify-center border-2 border-line bg-surface px-4 py-2 font-mono text-xs font-bold uppercase text-ink-muted hover:text-ink hover:border-line-strong transition-all">
                    Quay lại
                </a>
            </div>

            <!-- Contextual Actions on Existing Post (Delete / Request take-down) -->
            @isset($post)
                <div class="flex items-center gap-2">
                    @if ($post->status === \App\Enums\PostStatus::Published)
                        @if ($pendingRequest)
                            <div class="flex items-center gap-2">
                                <span class="inline-flex min-h-10 items-center justify-center gap-1.5 border-2 border-line-strong bg-paper px-3.5 py-2 font-mono text-xs font-bold text-ink">
                                    <span class="size-2 bg-lime"></span>
                                    <span>Đã gửi yêu cầu (Chờ duyệt)</span>
                                </span>
                                <form method="POST" action="{{ route('author.posts.requests.destroy', [$post, $pendingRequest]) }}"
                                      data-confirm="Bạn có chắc chắn muốn hủy yêu cầu {{ $pendingRequest->type === \App\Enums\PostRequestType::Removal ? 'gỡ bài' : 'đính chính' }} này? Bài viết sẽ trở lại trạng thái bình thường."
                                      data-confirm-title="Hủy yêu cầu bài viết"
                                      data-confirm-subtext="{{ $post->title }}"
                                      data-confirm-type="danger"
                                      data-confirm-btn="Xác nhận hủy yêu cầu">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex min-h-10 cursor-pointer items-center justify-center border-2 border-line-strong bg-danger px-3.5 py-2 font-mono text-xs font-bold uppercase text-paper shadow-brutal-sm hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
                                        <span>Hủy</span>
                                    </button>
                                </form>
                            </div>
                        @else
                            <button type="button" id="btn-open-request-form" class="inline-flex min-h-10 items-center justify-center gap-1.5 border-2 border-line-strong bg-paper px-3.5 py-2 font-mono text-xs font-bold uppercase text-ink shadow-brutal-sm hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all cursor-pointer">
                                <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                                </svg>
                                <span>Yêu cầu gỡ / sửa bài</span>
                            </button>
                        @endif
                    @endif

                    @can('delete', $post)
                        <button type="button" id="btn-delete-post" class="inline-flex min-h-10 items-center justify-center gap-1.5 border-2 border-line-strong bg-danger px-3.5 py-2 font-mono text-xs font-bold uppercase text-paper shadow-brutal-sm hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all cursor-pointer">
                            <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
                            </svg>
                            <span>Xóa bài</span>
                        </button>
                    @endcan
                </div>
            @endisset
        </div>
    </form>
</div>

<!-- Live Preview Modal -->
<div id="preview-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-ink/75 p-3 sm:p-6 md:p-8" aria-modal="true" role="dialog">
    <div class="mx-auto max-w-4xl border-2 border-line-strong bg-surface shadow-brutal flex flex-col max-h-[92vh]">
        <!-- Preview Header Bar -->
        <div class="flex items-center justify-between border-b-2 border-line-strong bg-paper px-6 py-3 shrink-0">
            <div class="flex items-center gap-2">
                <span class="font-mono text-xs font-bold uppercase tracking-wider text-ink">Xem trước bài viết (Live Preview)</span>
                <span class="border border-line-strong bg-lime px-2 py-0.5 font-mono text-[10px] font-bold uppercase text-ink">Bản nháp</span>
            </div>
            <button type="button" id="btn-close-preview" class="border border-line-strong bg-surface p-1.5 text-ink hover:bg-lime transition-colors cursor-pointer" title="Đóng xem trước (ESC)">
                <svg class="size-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        <!-- Preview Body Content (Scrollable) -->
        <div class="overflow-y-auto p-6 sm:p-10 space-y-6 flex-1 text-ink bg-surface">
            <!-- Category Badge & Time -->
            <div class="flex flex-wrap items-center gap-3">
                <span id="preview-category" class="border border-line-strong bg-lime px-2.5 py-1 font-mono text-xs font-bold uppercase text-ink">Chuyên mục</span>
                <span class="font-mono text-xs text-ink-muted" id="preview-date">Bản xem trước · {{ now()->format('d/m/Y H:i') }}</span>
            </div>

            <!-- Title -->
            <h1 id="preview-title" class="font-heading text-2xl sm:text-4xl font-black uppercase tracking-tight text-ink leading-tight">
                Tiêu đề bài viết
            </h1>

            <!-- Author info meta -->
            <div class="flex items-center gap-3 border-y-2 border-line-strong py-3 font-mono text-xs text-ink-muted">
                <span class="grid size-8 place-items-center border-2 border-line-strong bg-paper font-bold text-ink">
                    {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                </span>
                <div>
                    <span class="font-bold text-ink">{{ auth()->user()->name }}</span>
                    <span class="text-ink-muted block text-[11px]">Tác giả biên soạn nội dung</span>
                </div>
            </div>

            <!-- Sapo / Summary -->
            <div id="preview-summary-wrapper" class="hidden">
                <div id="preview-summary" class="border-l-4 border-ink bg-paper p-4 font-serif text-base sm:text-lg italic leading-relaxed text-ink">
                </div>
            </div>

            <!-- Featured Thumbnail -->
            <div id="preview-thumbnail-wrapper" class="hidden">
                <figure class="border-2 border-line-strong bg-paper shadow-brutal-sm">
                    <img id="preview-thumbnail-img" src="" alt="Thumbnail bài viết" class="aspect-video w-full object-cover">
                    <figcaption class="px-4 py-2 text-center font-mono text-xs text-ink-muted border-t-2 border-line-strong">
                        Hình ảnh minh họa cho bài viết · Nguồn: NewsHub
                    </figcaption>
                </figure>
            </div>
            <div id="preview-thumbnail-hidden-note" class="hidden border border-line bg-paper p-3 font-mono text-xs text-ink-muted italic text-center">
                (Ảnh đại diện đã được ẩn ở đầu bài theo tùy chọn, chỉ hiển thị ngoài danh sách bài viết)
            </div>

            <!-- Article Body Content (Rendered from Quill) -->
            <div id="preview-content" class="text-base sm:text-lg text-ink leading-relaxed sm:leading-8 space-y-4 pt-2">
            </div>

            <!-- Tags -->
            <div id="preview-tags-wrapper" class="hidden border-t-2 border-line-strong pt-5 space-y-2">
                <p class="font-mono text-xs font-bold uppercase tracking-wider text-ink-muted">Từ khóa liên quan:</p>
                <div id="preview-tags-container" class="flex flex-wrap gap-2"></div>
            </div>
        </div>

        <!-- Preview Footer -->
        <div class="flex items-center justify-between border-t-2 border-line-strong bg-paper px-6 py-3 shrink-0">
            <span class="font-mono text-xs text-ink-muted">Xem trước hoàn chỉnh bài viết.</span>
            <button type="button" id="btn-close-preview-footer" class="border-2 border-line-strong bg-surface px-4 py-1.5 font-mono text-xs font-bold uppercase text-ink hover:bg-lime transition-colors cursor-pointer shadow-brutal-sm">
                Đóng & Tiếp tục sửa
            </button>
        </div>
    </div>
</div>

@isset($post)
    @can('delete', $post)
        <!-- Modal xác nhận xóa bài viết -->
        <div id="delete-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-ink/75 p-4 sm:p-6" aria-modal="true" role="dialog">
            <div class="flex min-h-full items-center justify-center">
                <div class="relative w-full max-w-md border-2 border-line-strong bg-surface p-6 shadow-brutal transition-all">
                    <!-- Warning Icon -->
                    <div class="flex size-12 items-center justify-center border-2 border-line-strong bg-danger text-paper mx-auto font-mono font-black text-xl shadow-brutal-sm">
                        !
                    </div>

                    <!-- Content -->
                    <div class="mt-4 text-center">
                        <h3 class="font-heading text-lg font-black uppercase text-ink">Xác nhận xóa bài viết</h3>
                        <p class="mt-2 text-xs text-ink-muted">
                            Bạn có chắc chắn muốn xóa bài viết này không?
                        </p>
                        <p class="mt-2 text-xs font-bold text-danger line-clamp-2 bg-paper p-3 border border-line text-left">
                            {{ $post->title }}
                        </p>
                        <p class="mt-2 font-mono text-[11px] text-ink-muted">
                            Hành động này sẽ xóa dữ liệu bài viết khỏi hệ thống của bạn.
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="mt-6 flex items-center justify-end gap-2.5">
                        <button type="button" id="btn-cancel-delete" class="border-2 border-line bg-surface px-4 py-2 font-mono text-xs font-bold uppercase text-ink hover:border-line-strong transition-colors cursor-pointer">
                            Hủy bỏ
                        </button>

                        <form method="POST" action="{{ route('author.posts.destroy', $post) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="border-2 border-line-strong bg-danger px-4 py-2 font-mono text-xs font-bold uppercase text-paper shadow-brutal-sm hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all cursor-pointer">
                                Xác nhận xóa
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan

    @if ($post->status === \App\Enums\PostStatus::Published)
        <!-- Modal Form Yêu cầu gỡ bài / Đính chính thông tin -->
        <div id="request-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-ink/75 p-4 sm:p-6" aria-modal="true" role="dialog">
            <div class="flex min-h-full items-center justify-center">
                <div class="relative w-full max-w-lg border-2 border-line-strong bg-surface shadow-brutal transition-all overflow-hidden">
                    <div class="flex items-center justify-between border-b-2 border-line-strong bg-paper px-6 py-4">
                        <div class="flex items-center gap-2.5">
                            <span class="grid size-7 place-items-center border border-line-strong bg-lime text-ink font-mono font-bold text-xs">
                                ✉
                            </span>
                            <div>
                                <h3 class="font-heading text-sm font-black uppercase text-ink">Yêu cầu Gỡ bài / Đính chính</h3>
                                <p class="font-mono text-[11px] text-ink-muted">Gửi trực tiếp đến Ban biên tập để xử lý.</p>
                            </div>
                        </div>
                        <button type="button" id="btn-close-request" class="border border-line-strong bg-surface p-1 text-ink hover:bg-lime transition-colors cursor-pointer" title="Đóng (ESC)">
                            <svg class="size-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>

                    <form id="request-modal-form" method="POST" action="{{ \Illuminate\Support\Facades\Route::has('author.posts.requests.store') ? route('author.posts.requests.store', $post) : '#' }}" class="p-6 space-y-4">
                        @csrf
                        <div class="border border-line bg-paper p-3">
                            <span class="font-mono text-[10px] uppercase font-bold text-ink-muted tracking-wider">Bài viết liên quan</span>
                            <p class="mt-0.5 text-xs font-bold text-ink line-clamp-1">{{ $post->title }}</p>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="request_type" class="block font-mono text-xs font-bold uppercase tracking-wider text-ink">Loại yêu cầu <span class="text-danger">*</span></label>
                                <select id="request_type" name="type" required class="mt-1.5 w-full border-2 border-line bg-paper px-3 py-2 text-xs text-ink outline-none focus:border-ink">
                                    <option value="removal">Yêu cầu Gỡ bài viết (Take-down)</option>
                                    <option value="correction">Yêu cầu Đính chính / Sửa lỗi</option>
                                </select>
                            </div>

                            <div>
                                <label for="request_priority" class="block font-mono text-xs font-bold uppercase tracking-wider text-ink">Mức độ ưu tiên</label>
                                <select id="request_priority" name="priority" class="mt-1.5 w-full border-2 border-line bg-paper px-3 py-2 text-xs text-ink outline-none focus:border-ink">
                                    <option value="normal">Bình thường</option>
                                    <option value="urgent">Khẩn cấp (Sai số liệu/Bản quyền)</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="request_reason" class="block font-mono text-xs font-bold uppercase tracking-wider text-ink">Lý do chính <span class="text-danger">*</span></label>
                            <input id="request_reason" name="reason" required maxlength="255" placeholder="Ví dụ: Cần cập nhật số liệu mới, bài viết có nội dung chưa chính xác..." class="mt-1.5 w-full border-2 border-line bg-paper px-3 py-2 text-xs text-ink placeholder-ink-muted outline-none focus:border-ink">
                        </div>

                        <div>
                            <label for="request_notes" class="block font-mono text-xs font-bold uppercase tracking-wider text-ink">Chi tiết đề xuất / Đoạn văn cần sửa đổi</label>
                            <textarea id="request_notes" name="notes" rows="4" maxlength="2000" placeholder="Mô tả cụ thể vị trí sai sót, lý do cần gỡ bài hoặc nội dung đề xuất thay thế..." class="mt-1.5 w-full border-2 border-line bg-paper px-3 py-2 text-xs text-ink placeholder-ink-muted outline-none focus:border-ink"></textarea>
                        </div>

                        <div class="pt-4 border-t-2 border-line-strong flex items-center justify-end gap-2.5">
                            <button type="button" id="btn-cancel-request" class="border-2 border-line bg-surface px-4 py-2 font-mono text-xs font-bold uppercase text-ink hover:border-line-strong transition-colors cursor-pointer">
                                Đóng
                            </button>
                            <button type="submit" class="border-2 border-line-strong bg-lime px-4 py-2 font-mono text-xs font-bold uppercase text-ink shadow-brutal-sm hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all cursor-pointer">
                                <span>Gửi tới Ban biên tập</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endisset

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        /* ==========================================================================
           1. Quill WYSIWYG Editor Setup with Image & Video Handlers
           ========================================================================== */
        const editorEl = document.getElementById('quill-editor');
        const contentInput = document.getElementById('content');
        let quill = null;

        function imageHandler() {
            const input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/jpeg,image/png,image/webp,image/gif');
            input.click();

            input.onchange = async () => {
                const file = input.files[0];
                if (!file) return;

                if (file.size > 5 * 1024 * 1024) {
                    alert('Kích thước ảnh tối đa cho phép là 5 MB.');
                    return;
                }

                const range = quill.getSelection(true);
                const tempUrl = URL.createObjectURL(file);
                quill.insertEmbed(range.index, 'image', tempUrl);

                try {
                    const formData = new FormData();
                    formData.append('image', file);
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
                        || document.querySelector('input[name="_token"]')?.value;
                    if (csrfToken) {
                        formData.append('_token', csrfToken);
                    }

                    const response = await fetch('{{ url('/author/media/upload') }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken || ''
                        },
                        body: formData
                    });

                    if (response.ok) {
                        const data = await response.json();
                        if (data.url) {
                            const images = quill.root.querySelectorAll('img');
                            images.forEach(img => {
                                if (img.src === tempUrl) {
                                    img.src = data.url;
                                }
                            });
                        }
                    }
                } catch (err) {
                    console.info('Sử dụng ảnh xem trước cục bộ.');
                }
            };
        }

        function videoHandler() {
            const url = prompt('Nhập đường dẫn video YouTube (ví dụ: https://www.youtube.com/watch?v=... hoặc https://youtu.be/...):');
            if (!url) return;

            const match = url.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i);
            const videoId = match ? match[1] : null;
            const embedUrl = videoId ? `https://www.youtube.com/embed/${videoId}` : url;

            const range = quill.getSelection(true);
            quill.insertEmbed(range.index, 'video', embedUrl);
        }

        if (editorEl && typeof Quill !== 'undefined') {
            quill = new Quill(editorEl, {
                theme: 'snow',
                placeholder: 'Soạn thảo nội dung bài viết, phóng sự, phân tích hoặc tin tức tại đây...',
                modules: {
                    toolbar: {
                        container: [
                            [{ 'header': [2, 3, false] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            ['blockquote', 'code-block'],
                            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                            ['link', 'image', 'video'],
                            ['clean']
                        ],
                        handlers: {
                            image: imageHandler,
                            video: videoHandler
                        }
                    }
                }
            });

            quill.on('text-change', () => {
                contentInput.value = quill.root.innerHTML;
            });

            const postForm = document.querySelector('form');
            if (postForm) {
                postForm.addEventListener('submit', () => {
                    contentInput.value = quill.root.innerHTML;
                });
            }
        }

        /* ==========================================================================
           2. Interactive Tag Manager
           ========================================================================== */
        const manager = document.getElementById('tag-manager');
        let existingTags = [];
        if (manager) {
            try {
                existingTags = JSON.parse(manager.dataset.existingTags || '[]');
            } catch (e) {
                existingTags = [];
            }
        }

        const container = document.getElementById('selected-tags-container');
        const input = document.getElementById('tag-text-input');
        const btnAdd = document.getElementById('btn-add-tag');
        const cloud = document.getElementById('suggested-tags-cloud');
        const countEl = document.getElementById('selected-tags-count');
        const hiddenFallback = document.getElementById('hidden-tags-inputs');

        const selectedExisting = new Map();
        const selectedNew = new Set();

        if (hiddenFallback) {
            hiddenFallback.querySelectorAll('input[data-fallback-id]').forEach(cb => {
                const id = parseInt(cb.value, 10);
                const found = existingTags.find(t => t.id === id);
                if (found) {
                    selectedExisting.set(id, found.name);
                }
            });

            hiddenFallback.querySelectorAll('input[data-fallback-name]').forEach(inp => {
                const val = (inp.value || '').trim();
                if (val) {
                    selectedNew.add(val);
                }
            });

            hiddenFallback.innerHTML = '';
        }

        const updateUI = () => {
            if (!container) return;
            container.innerHTML = '';

            selectedExisting.forEach((name, id) => {
                const chip = document.createElement('span');
                chip.className = 'inline-flex items-center gap-1.5 border border-line-strong bg-surface px-2.5 py-1 font-mono text-xs font-bold text-ink shadow-brutal-sm';
                chip.innerHTML = `
                    <span>#${name}</span>
                    <button type="button" class="text-ink hover:text-danger cursor-pointer text-sm font-bold leading-none" title="Gỡ thẻ">&times;</button>
                    <input type="hidden" name="tag_ids[]" value="${id}">
                `;
                chip.querySelector('button').addEventListener('click', () => {
                    selectedExisting.delete(id);
                    updateUI();
                });
                container.appendChild(chip);
            });

            selectedNew.forEach(name => {
                const chip = document.createElement('span');
                chip.className = 'inline-flex items-center gap-1.5 border border-line-strong bg-lime px-2.5 py-1 font-mono text-xs font-bold text-ink shadow-brutal-sm';
                chip.innerHTML = `
                    <span>#${name}</span>
                    <span class="bg-ink text-paper px-1 py-0.2 text-[9px] font-bold">mới</span>
                    <button type="button" class="text-ink hover:text-danger cursor-pointer text-sm font-bold leading-none" title="Gỡ thẻ">&times;</button>
                    <input type="hidden" name="tag_names[]" value="${name}">
                `;
                chip.querySelector('button').addEventListener('click', () => {
                    selectedNew.delete(name);
                    updateUI();
                });
                container.appendChild(chip);
            });

            if (cloud) {
                cloud.querySelectorAll('[data-tag-suggestion]').forEach(btn => {
                    const id = parseInt(btn.dataset.tagSuggestion, 10);
                    const isSel = selectedExisting.has(id);
                    if (isSel) {
                        btn.className = 'inline-flex items-center gap-1 border border-line-strong bg-ink text-paper font-bold px-2.5 py-1 font-mono text-xs transition-all cursor-pointer';
                        btn.querySelector('span:last-child').textContent = '✓';
                        btn.querySelector('span:last-child').className = 'text-[10px] text-lime';
                    } else {
                        btn.className = 'inline-flex items-center gap-1 border border-line bg-surface text-ink hover:border-line-strong hover:bg-paper px-2.5 py-1 font-mono text-xs transition-all cursor-pointer';
                        btn.querySelector('span:last-child').textContent = '+';
                        btn.querySelector('span:last-child').className = 'text-[10px] text-ink-muted';
                    }
                });
            }

            if (countEl) {
                countEl.textContent = selectedExisting.size + selectedNew.size;
            }
        };

        const addTag = (raw) => {
            let val = (raw || '').trim().replace(/^#+/, '').trim();
            if (!val) return;

            const match = existingTags.find(t => t.name.toLowerCase() === val.toLowerCase() || (t.slug && t.slug.toLowerCase() === val.toLowerCase()));
            if (match) {
                selectedExisting.set(match.id, match.name);
            } else {
                let existsInNew = false;
                for (const n of selectedNew) {
                    if (n.toLowerCase() === val.toLowerCase()) {
                        existsInNew = true;
                        break;
                    }
                }
                if (!existsInNew) {
                    selectedNew.add(val);
                }
            }

            if (input) {
                input.value = '';
                input.focus();
            }
            updateUI();
        };

        input?.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();
                e.stopPropagation();
                addTag(input.value);
            } else if (e.key === 'Backspace' && input.value === '') {
                if (selectedNew.size > 0) {
                    const arr = Array.from(selectedNew);
                    selectedNew.delete(arr[arr.length - 1]);
                    updateUI();
                } else if (selectedExisting.size > 0) {
                    const keys = Array.from(selectedExisting.keys());
                    selectedExisting.delete(keys[keys.length - 1]);
                    updateUI();
                }
            }
        });

        btnAdd?.addEventListener('click', (e) => {
            e.preventDefault();
            if (input) {
                addTag(input.value);
            }
        });

        if (cloud) {
            cloud.querySelectorAll('[data-tag-suggestion]').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const id = parseInt(btn.dataset.tagSuggestion, 10);
                    const name = btn.dataset.tagName;
                    if (selectedExisting.has(id)) {
                        selectedExisting.delete(id);
                    } else {
                        selectedExisting.set(id, name);
                    }
                    updateUI();
                });
            });
        }

        updateUI();

        /* ==========================================================================
           3. Live Article Preview Modal Logic
           ========================================================================== */
        const modal = document.getElementById('preview-modal');
        const btnOpen = document.getElementById('btn-open-preview');
        const btnClose = document.getElementById('btn-close-preview');
        const btnCloseFooter = document.getElementById('btn-close-preview-footer');

        const openPreview = () => {
            if (!modal) return;

            // Title
            const titleVal = document.getElementById('title')?.value?.trim() || 'Chưa có tiêu đề bài viết';
            document.getElementById('preview-title').textContent = titleVal;

            // Category
            const categorySelect = document.getElementById('category_id');
            const categoryName = categorySelect?.selectedOptions[0]?.textContent?.trim() || 'Chuyên mục';
            document.getElementById('preview-category').textContent = categoryName;

            // Sapo / Summary
            const summaryVal = document.getElementById('summary')?.value?.trim();
            const summaryWrapper = document.getElementById('preview-summary-wrapper');
            const summaryEl = document.getElementById('preview-summary');
            if (summaryVal) {
                summaryEl.textContent = summaryVal;
                summaryWrapper.classList.remove('hidden');
            } else {
                summaryWrapper.classList.add('hidden');
            }

            // Thumbnail display
            const showThumbCheckbox = document.querySelector('input[type="checkbox"][name="show_thumbnail_in_post"]');
            const isThumbEnabled = showThumbCheckbox ? showThumbCheckbox.checked : true;
            const thumbInput = document.getElementById('thumbnail');
            const thumbWrapper = document.getElementById('preview-thumbnail-wrapper');
            const thumbImg = document.getElementById('preview-thumbnail-img');
            const thumbHiddenNote = document.getElementById('preview-thumbnail-hidden-note');

            let thumbSrc = null;
            if (thumbInput?.files && thumbInput.files[0]) {
                thumbSrc = URL.createObjectURL(thumbInput.files[0]);
            } else {
                @if(isset($post) && $post->thumbnail)
                    thumbSrc = "{{ Storage::disk('public')->url($post->thumbnail) }}";
                @endif
            }

            if (isThumbEnabled && thumbSrc) {
                thumbImg.src = thumbSrc;
                thumbWrapper.classList.remove('hidden');
                thumbHiddenNote.classList.add('hidden');
            } else if (!isThumbEnabled && thumbSrc) {
                thumbWrapper.classList.add('hidden');
                thumbHiddenNote.classList.remove('hidden');
            } else {
                thumbWrapper.classList.add('hidden');
                thumbHiddenNote.classList.add('hidden');
            }

            // Body Content
            const contentEl = document.getElementById('preview-content');
            const html = quill ? quill.root.innerHTML : document.getElementById('content')?.value || '';
            contentEl.innerHTML = html || '<p class="text-ink-muted italic">Chưa có nội dung bài viết...</p>';

            // Tags
            const tagsWrapper = document.getElementById('preview-tags-wrapper');
            const tagsContainer = document.getElementById('preview-tags-container');
            tagsContainer.innerHTML = '';

            const allTags = [];
            selectedExisting.forEach(name => allTags.push(name));
            selectedNew.forEach(name => allTags.push(name));

            if (allTags.length > 0) {
                allTags.forEach(name => {
                    const span = document.createElement('span');
                    span.className = 'inline-flex items-center border border-line-strong bg-paper px-2.5 py-1 font-mono text-xs font-bold text-ink';
                    span.textContent = '#' + name;
                    tagsContainer.appendChild(span);
                });
                tagsWrapper.classList.remove('hidden');
            } else {
                tagsWrapper.classList.add('hidden');
            }

            // Open modal
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        };

        const closePreview = () => {
            if (!modal) return;
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        };

        btnOpen?.addEventListener('click', openPreview);
        btnClose?.addEventListener('click', closePreview);
        btnCloseFooter?.addEventListener('click', closePreview);

        modal?.addEventListener('click', (e) => {
            if (e.target === modal) {
                closePreview();
            }
        });

        // Delete Modal Handlers (for existing post in form)
        const btnDeletePost = document.getElementById('btn-delete-post');
        const deleteModal = document.getElementById('delete-modal');
        const btnCancelDelete = document.getElementById('btn-cancel-delete');

        btnDeletePost?.addEventListener('click', () => {
            if (!deleteModal) return;
            deleteModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        });

        const closeDeleteModal = () => {
            if (!deleteModal) return;
            deleteModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        };

        btnCancelDelete?.addEventListener('click', closeDeleteModal);
        deleteModal?.addEventListener('click', (e) => {
            if (e.target === deleteModal) closeDeleteModal();
        });

        // Request Modal Handlers (for published post in form)
        const btnOpenRequest = document.getElementById('btn-open-request-form');
        const requestModal = document.getElementById('request-modal');
        const btnCloseRequest = document.getElementById('btn-close-request');
        const btnCancelRequest = document.getElementById('btn-cancel-request');
        const requestForm = document.getElementById('request-modal-form');

        btnOpenRequest?.addEventListener('click', () => {
            if (!requestModal) return;
            requestModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        });

        const closeRequestModal = () => {
            if (!requestModal) return;
            requestModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        };

        btnCloseRequest?.addEventListener('click', closeRequestModal);
        btnCancelRequest?.addEventListener('click', closeRequestModal);
        requestModal?.addEventListener('click', (e) => {
            if (e.target === requestModal) closeRequestModal();
        });

        requestForm?.addEventListener('submit', (e) => {
            if (!requestForm.action || requestForm.action === window.location.href || requestForm.action.endsWith('#')) {
                e.preventDefault();
                alert('Yêu cầu gỡ / sửa bài viết đã được ghi nhận. Ban biên tập sẽ xem xét và phản hồi trong thời gian sớm nhất!');
                closeRequestModal();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                if (modal && !modal.classList.contains('hidden')) closePreview();
                if (deleteModal && !deleteModal.classList.contains('hidden')) closeDeleteModal();
                if (requestModal && !requestModal.classList.contains('hidden')) closeRequestModal();
            }
        });
    });
</script>
@endsection
