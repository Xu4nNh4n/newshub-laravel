@extends('layouts.dashboard', ['title' => 'Quản lý chuyên mục'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b-2 border-line-strong pb-5">
        <div>
            <span class="font-mono text-xs font-bold uppercase tracking-wider text-ink-muted block mb-1">Cấu trúc tòa soạn</span>
            <h1 class="text-2xl font-black uppercase tracking-tight text-ink sm:text-3xl font-heading">Quản lý chuyên mục</h1>
            <p class="text-xs text-ink-muted mt-1">Tổ chức phân cấp bài viết theo danh mục cha và các danh mục con trực quan.</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <button type="button"
                    id="admin-cat-expand-all"
                    class="inline-flex min-h-10 items-center gap-1.5 border-2 border-line-strong bg-surface px-3.5 py-1.5 font-mono text-xs font-bold uppercase text-ink shadow-brutal-sm hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all cursor-pointer select-none">
                <span>[+] Mở tất cả</span>
            </button>
            <button type="button"
                    id="admin-cat-collapse-all"
                    class="inline-flex min-h-10 items-center gap-1.5 border-2 border-line-strong bg-surface px-3.5 py-1.5 font-mono text-xs font-bold uppercase text-ink shadow-brutal-sm hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all cursor-pointer select-none">
                <span>[-] Thu gọn</span>
            </button>
            <button type="button"
                    id="btn-open-create-category-modal"
                    class="inline-flex min-h-10 items-center gap-1.5 border-2 border-line-strong bg-lime px-4 py-1.5 font-mono text-xs font-bold uppercase text-ink shadow-brutal hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all cursor-pointer">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="size-3.5"><path d="M12 5v14M5 12h14"/></svg>
                <span>Thêm chuyên mục</span>
            </button>
        </div>
    </div>

    <div class="overflow-x-auto border-2 border-line-strong bg-surface shadow-brutal">
        <table class="w-full text-left text-xs">
            <thead class="border-b-2 border-line-strong bg-paper font-mono uppercase text-ink">
                <tr>
                    <th scope="col" class="px-5 py-3 font-bold w-5/12">Chuyên mục</th>
                    <th scope="col" class="px-5 py-3 font-bold w-2/12">Trạng thái</th>
                    <th scope="col" class="px-5 py-3 font-bold w-2/12">Số bài viết</th>
                    <th scope="col" class="px-5 py-3 font-bold text-right w-3/12">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @forelse ($parentCategories as $parent)
                    @php
                        $children = $parent->children;
                        $hasChildren = $children->isNotEmpty();
                    @endphp
                    <!-- Parent Category Row -->
                    <tr class="transition-colors hover:bg-paper/50 group/row {{ $hasChildren ? 'cursor-pointer select-none' : '' }}"
                        data-admin-cat-row="{{ $parent->id }}">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                @if ($hasChildren)
                                    <button type="button"
                                            data-admin-cat-toggle="{{ $parent->id }}"
                                            aria-expanded="false"
                                            aria-label="Đóng mở danh mục con của {{ $parent->name }}"
                                            class="grid size-6 shrink-0 place-items-center border border-line-strong bg-paper text-ink hover:bg-lime transition-all cursor-pointer shadow-brutal-sm">
                                        <svg class="size-3 transition-transform duration-200" data-admin-cat-icon="{{ $parent->id }}" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                @else
                                    <span class="grid size-6 shrink-0 place-items-center font-mono text-xs text-ink-muted">●</span>
                                @endif

                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <p class="font-bold text-ink text-sm group-hover/row:text-danger transition-colors">
                                            {{ $parent->name }}
                                        </p>
                                        <span class="border border-line-strong bg-paper px-1.5 py-0.5 font-mono text-[10px] font-bold uppercase text-ink">
                                            Danh mục cha
                                        </span>
                                        @if ($hasChildren)
                                            <span class="border border-line bg-surface px-1.5 py-0.5 font-mono text-[10px] text-ink-muted">
                                                {{ $children->count() }} mục con
                                            </span>
                                        @endif
                                    </div>
                                    <p class="font-mono text-[11px] text-ink-muted mt-0.5">{{ $parent->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span @class([
                                'inline-flex border px-2 py-0.5 font-mono text-[11px] font-bold uppercase',
                                'border-line-strong bg-lime text-ink' => $parent->status === \App\Enums\CategoryStatus::Active,
                                'border-line bg-paper text-ink-muted' => $parent->status !== \App\Enums\CategoryStatus::Active,
                            ])>
                                {{ $parent->status->value }}
                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-ink tabular-nums">
                            @php
                                $totalPosts = $parent->posts_count + $children->sum('posts_count');
                            @endphp
                            <span class="font-bold text-ink font-mono">{{ $parent->posts_count }} bài</span>
                            @if ($hasChildren)
                                <span class="font-mono text-[10px] text-ink-muted block font-normal">(Tổng {{ $totalPosts }} bài)</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-right" onclick="event.stopPropagation()">
                            <div class="flex items-center justify-end gap-1.5">
                                <button type="button"
                                        data-cat-action="add-sub"
                                        data-parent-id="{{ $parent->id }}"
                                        data-parent-name="{{ $parent->name }}"
                                        title="Thêm danh mục con cho {{ $parent->name }}"
                                        class="inline-flex min-h-7 cursor-pointer items-center gap-1 border border-line-strong bg-paper px-2.5 py-1 font-mono text-[11px] font-bold uppercase text-ink shadow-brutal-sm hover:bg-lime transition-all">
                                    <span>+ Con</span>
                                </button>
                                <button type="button"
                                        data-cat-action="edit"
                                        data-id="{{ $parent->id }}"
                                        data-name="{{ $parent->name }}"
                                        data-slug="{{ $parent->slug }}"
                                        data-description="{{ $parent->description ?? '' }}"
                                        data-parent-id="{{ $parent->parent_id ?? '' }}"
                                        data-status="{{ $parent->status->value }}"
                                        data-action="{{ route('admin.categories.update', $parent) }}"
                                        class="inline-flex min-h-7 cursor-pointer items-center border border-line-strong bg-surface px-2.5 py-1 font-mono text-[11px] font-bold uppercase text-ink shadow-brutal-sm hover:bg-lime transition-all">
                                    Sửa
                                </button>
                                <button type="button"
                                        data-cat-action="delete"
                                        data-id="{{ $parent->id }}"
                                        data-name="{{ $parent->name }}"
                                        data-action="{{ route('admin.categories.destroy', $parent) }}"
                                        data-has-children="{{ $hasChildren ? '1' : '0' }}"
                                        data-posts-count="{{ $parent->posts_count }}"
                                        class="inline-flex min-h-7 cursor-pointer items-center border border-line-strong bg-paper px-2.5 py-1 font-mono text-[11px] font-bold uppercase text-ink shadow-brutal-sm hover:border-danger hover:text-danger transition-all">
                                    Xóa
                                </button>
                            </div>
                        </td>
                    </tr>

                    @if ($hasChildren)
                        <!-- Collapsible Child Categories Container -->
                        <tr class="p-0 border-none">
                            <td colspan="4" class="p-0 border-none">
                                <div id="admin-children-{{ $parent->id }}"
                                     data-admin-children-drawer="{{ $parent->id }}"
                                     class="grid transition-[grid-template-rows] duration-200 ease-in-out grid-rows-[0fr]">
                                    <div class="overflow-hidden">
                                        <div class="border-y border-line-strong bg-paper divide-y divide-line">
                                            @foreach ($children as $child)
                                                <div class="flex items-center text-xs hover:bg-surface transition-colors py-3 px-5">
                                                    <div class="w-5/12 flex items-center gap-2 pl-9">
                                                        <span class="font-mono text-sm text-ink-muted leading-none shrink-0">↳</span>
                                                        <div class="min-w-0">
                                                            <div class="flex items-center gap-2 flex-wrap">
                                                                <p class="font-bold text-ink">{{ $child->name }}</p>
                                                                <span class="border border-line bg-surface px-1.5 py-0.2 font-mono text-[10px] text-ink-muted">
                                                                    thuộc {{ $parent->name }}
                                                                </span>
                                                            </div>
                                                            <p class="font-mono text-[11px] text-ink-muted mt-0.5 truncate">{{ $child->slug }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="w-2/12 whitespace-nowrap">
                                                        <span @class([
                                                            'inline-flex border px-2 py-0.5 font-mono text-[10px] font-bold uppercase',
                                                            'border-line-strong bg-lime text-ink' => $child->status === \App\Enums\CategoryStatus::Active,
                                                            'border-line bg-surface text-ink-muted' => $child->status !== \App\Enums\CategoryStatus::Active,
                                                        ])>
                                                            {{ $child->status->value }}
                                                        </span>
                                                    </div>
                                                    <div class="w-2/12 whitespace-nowrap font-mono text-ink tabular-nums">
                                                        {{ $child->posts_count }} bài
                                                    </div>
                                                    <div class="w-3/12 whitespace-nowrap text-right">
                                                        <div class="flex items-center justify-end gap-1.5">
                                                            <button type="button"
                                                                    data-cat-action="edit"
                                                                    data-id="{{ $child->id }}"
                                                                    data-name="{{ $child->name }}"
                                                                    data-slug="{{ $child->slug }}"
                                                                    data-description="{{ $child->description ?? '' }}"
                                                                    data-parent-id="{{ $child->parent_id }}"
                                                                    data-status="{{ $child->status->value }}"
                                                                    data-action="{{ route('admin.categories.update', $child) }}"
                                                                    class="inline-flex min-h-6 cursor-pointer items-center border border-line-strong bg-surface px-2.5 py-0.5 font-mono text-[10px] font-bold uppercase text-ink hover:bg-lime transition-all">
                                                                Sửa
                                                            </button>
                                                            <button type="button"
                                                                    data-cat-action="delete"
                                                                    data-id="{{ $child->id }}"
                                                                    data-name="{{ $child->name }}"
                                                                    data-action="{{ route('admin.categories.destroy', $child) }}"
                                                                    data-has-children="0"
                                                                    data-posts-count="{{ $child->posts_count }}"
                                                                    class="inline-flex min-h-6 cursor-pointer items-center border border-line bg-surface px-2.5 py-0.5 font-mono text-[10px] font-bold uppercase text-ink hover:border-danger hover:text-danger transition-all">
                                                                Xóa
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="4" class="p-12 text-center font-mono text-xs text-ink-muted">Chưa có chuyên mục.</td>
                    </tr>
                @endforelse

                @if ($orphanCategories->isNotEmpty())
                    @foreach ($orphanCategories as $orphan)
                        <tr class="transition-colors hover:bg-paper/50">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2 pl-2">
                                    <span class="font-mono text-xs font-bold text-ink-muted">?</span>
                                    <div>
                                        <p class="font-bold text-ink">{{ $orphan->name }}</p>
                                        <p class="font-mono text-[11px] text-ink-muted">{{ $orphan->slug }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="inline-flex border border-line bg-paper px-2 py-0.5 font-mono text-[11px] font-bold text-ink">
                                    {{ $orphan->status->value }}
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap font-mono text-ink tabular-nums">
                                {{ $orphan->posts_count }} bài
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button"
                                            data-cat-action="edit"
                                            data-id="{{ $orphan->id }}"
                                            data-name="{{ $orphan->name }}"
                                            data-slug="{{ $orphan->slug }}"
                                            data-description="{{ $orphan->description ?? '' }}"
                                            data-parent-id="{{ $orphan->parent_id ?? '' }}"
                                            data-status="{{ $orphan->status->value }}"
                                            data-action="{{ route('admin.categories.update', $orphan) }}"
                                            class="inline-flex min-h-7 cursor-pointer items-center border border-line-strong bg-surface px-2.5 py-1 font-mono text-[11px] font-bold uppercase text-ink shadow-brutal-sm hover:bg-lime transition-all">
                                        Sửa
                                    </button>
                                    <button type="button"
                                            data-cat-action="delete"
                                            data-id="{{ $orphan->id }}"
                                            data-name="{{ $orphan->name }}"
                                            data-action="{{ route('admin.categories.destroy', $orphan) }}"
                                            data-has-children="0"
                                            data-posts-count="{{ $orphan->posts_count }}"
                                            class="inline-flex min-h-7 cursor-pointer items-center border border-line-strong bg-paper px-2.5 py-1 font-mono text-[11px] font-bold uppercase text-ink shadow-brutal-sm hover:border-danger hover:text-danger transition-all">
                                        Xóa
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>

<!-- Category Form Modal (Create / Edit) -->
<div id="cat-form-modal"
     class="fixed inset-0 z-50 hidden overflow-y-auto bg-ink/75 p-4 sm:p-6 flex items-center justify-center"
     role="dialog"
     aria-modal="true"
     aria-labelledby="cat-modal-title">
    <div class="relative w-full max-w-lg border-2 border-line-strong bg-surface p-6 sm:p-8 shadow-brutal transition-all">
        <!-- Close button -->
        <button type="button"
                data-cat-modal-close
                class="absolute right-4 top-4 border border-line-strong bg-paper p-1 text-ink hover:bg-lime transition-colors cursor-pointer"
                aria-label="Đóng">
            <svg class="size-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </button>

        <div class="mb-5 border-b-2 border-line-strong pb-4">
            <h2 id="cat-modal-title" class="font-heading text-lg font-black uppercase text-ink">Thêm chuyên mục mới</h2>
            <p id="cat-modal-subtitle" class="font-mono text-xs text-ink-muted mt-1">Thiết lập thông tin tên, đường dẫn tĩnh và danh mục cha.</p>
        </div>

        <form id="cat-modal-form" method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="_method" id="cat-form-method" value="POST">

            <div>
                <label for="cat-name-input" class="block font-mono text-xs font-bold uppercase tracking-wider text-ink">
                    Tên chuyên mục <span class="text-danger">*</span>
                </label>
                <input type="text"
                       id="cat-name-input"
                       name="name"
                       required
                       placeholder="Ví dụ: Công nghệ, Khoa học..."
                       class="mt-1.5 w-full border-2 border-line bg-paper px-3.5 py-2.5 text-xs text-ink outline-none focus:border-ink">
            </div>

            <div>
                <label for="cat-slug-input" class="block font-mono text-xs font-bold uppercase tracking-wider text-ink">
                    Đường dẫn tĩnh (Slug)
                </label>
                <input type="text"
                       id="cat-slug-input"
                       name="slug"
                       placeholder="Để trống hệ thống sẽ tự sinh từ tên"
                       class="mt-1.5 w-full border-2 border-line bg-paper px-3.5 py-2.5 text-xs font-mono text-ink outline-none focus:border-ink">
            </div>

            <div>
                <label for="cat-desc-input" class="block font-mono text-xs font-bold uppercase tracking-wider text-ink">
                    Mô tả (Tùy chọn)
                </label>
                <textarea id="cat-desc-input"
                          name="description"
                          rows="2"
                          placeholder="Mô tả tóm tắt về chuyên mục này..."
                          class="mt-1.5 w-full border-2 border-line bg-paper px-3.5 py-2.5 text-xs text-ink outline-none focus:border-ink"></textarea>
            </div>

            <div>
                <label for="cat-parent-input" class="block font-mono text-xs font-bold uppercase tracking-wider text-ink">
                    Danh mục cha
                </label>
                <select id="cat-parent-input"
                        name="parent_id"
                        class="mt-1.5 w-full border-2 border-line bg-paper px-3.5 py-2.5 text-xs text-ink outline-none focus:border-ink">
                    <option value="">-- Không có (Đây là danh mục chính) --</option>
                    @foreach ($parentCategories as $parent)
                        <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                    @endforeach
                </select>
                <p class="mt-1 font-mono text-[11px] text-ink-muted">
                    Chọn danh mục cha để tạo cấp bậc con hiển thị dạng menu xổ xuống.
                </p>
            </div>

            <div>
                <label for="cat-status-input" class="block font-mono text-xs font-bold uppercase tracking-wider text-ink">Trạng thái</label>
                <select id="cat-status-input"
                        name="status"
                        class="mt-1.5 w-full border-2 border-line bg-paper px-3.5 py-2.5 text-xs text-ink outline-none focus:border-ink">
                    @foreach (\App\Enums\CategoryStatus::cases() as $status)
                        <option value="{{ $status->value }}">{{ $status->value }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center justify-end gap-3 pt-5 border-t-2 border-line-strong">
                <button type="button"
                        data-cat-modal-close
                        class="border-2 border-line bg-surface px-4 py-2 font-mono text-xs font-bold uppercase text-ink hover:border-line-strong transition-colors cursor-pointer">
                    Hủy
                </button>
                <button type="submit"
                        id="cat-btn-submit"
                        class="border-2 border-line-strong bg-lime px-5 py-2 font-mono text-xs font-bold uppercase text-ink shadow-brutal-sm hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all cursor-pointer">
                    Lưu chuyên mục
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Category Delete Confirmation Modal -->
<div id="cat-delete-modal"
     class="fixed inset-0 z-50 hidden overflow-y-auto bg-ink/75 p-4 sm:p-6 flex items-center justify-center"
     role="dialog"
     aria-modal="true"
     aria-labelledby="cat-delete-title">
    <div class="relative w-full max-w-md border-2 border-line-strong bg-surface p-6 shadow-brutal transition-all">
        <!-- Close button -->
        <button type="button"
                data-cat-delete-modal-close
                class="absolute right-4 top-4 border border-line-strong bg-paper p-1 text-ink hover:bg-lime transition-colors cursor-pointer"
                aria-label="Đóng">
            <svg class="size-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </button>

        <div class="flex items-start gap-4 mb-4">
            <div class="grid size-12 shrink-0 place-items-center border-2 border-line-strong bg-danger text-paper font-mono font-black text-xl shadow-brutal-sm">
                !
            </div>
            <div class="min-w-0">
                <h3 id="cat-delete-title" class="font-heading text-lg font-black uppercase text-ink">Xác nhận xóa</h3>
                <p class="text-xs text-ink-muted mt-1">
                    Bạn có chắc chắn muốn xóa chuyên mục <span id="cat-delete-target-name" class="font-bold text-danger"></span>?
                </p>
                <div id="cat-delete-warning-box" class="mt-2.5 border border-line bg-paper p-2.5 font-mono text-[11px] text-danger hidden">
                    <span id="cat-delete-warning-text"></span>
                </div>
                <p class="mt-2 font-mono text-[11px] text-ink-muted">
                    Lưu ý: Hệ thống chỉ cho phép xóa nếu chuyên mục không có bài viết và không có danh mục con.
                </p>
            </div>
        </div>

        <form id="cat-delete-form" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex items-center justify-end gap-3 pt-5 border-t-2 border-line-strong">
                <button type="button"
                        data-cat-delete-modal-close
                        class="border-2 border-line bg-surface px-4 py-2 font-mono text-xs font-bold uppercase text-ink hover:border-line-strong transition-colors cursor-pointer">
                    Hủy bỏ
                </button>
                <button type="submit"
                        class="border-2 border-line-strong bg-danger px-5 py-2 font-mono text-xs font-bold uppercase text-paper shadow-brutal-sm hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all cursor-pointer">
                    Xác nhận xóa
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- 1. Collapsible Tree Drawer Logic ---
        const toggleParent = (parentId) => {
            const drawer = document.getElementById(`admin-children-${parentId}`);
            const icon = document.querySelector(`[data-admin-cat-icon="${parentId}"]`);
            const btn = document.querySelector(`[data-admin-cat-toggle="${parentId}"]`);
            if (!drawer) return;

            const isOpen = drawer.classList.contains('grid-rows-[1fr]');
            if (isOpen) {
                drawer.classList.remove('grid-rows-[1fr]');
                drawer.classList.add('grid-rows-[0fr]');
                icon?.classList.remove('rotate-90');
                btn?.setAttribute('aria-expanded', 'false');
            } else {
                drawer.classList.remove('grid-rows-[0fr]');
                drawer.classList.add('grid-rows-[1fr]');
                icon?.classList.add('rotate-90');
                btn?.setAttribute('aria-expanded', 'true');
            }
        };

        // Clicking toggle button
        document.querySelectorAll('[data-admin-cat-toggle]').forEach((btn) => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const parentId = btn.getAttribute('data-admin-cat-toggle');
                toggleParent(parentId);
            });
        });

        // Clicking parent row
        document.querySelectorAll('[data-admin-cat-row]').forEach((row) => {
            row.addEventListener('click', (e) => {
                if (e.target.closest('a, button, form, input, select')) return;
                const parentId = row.getAttribute('data-admin-cat-row');
                toggleParent(parentId);
            });
        });

        // Expand All button
        document.getElementById('admin-cat-expand-all')?.addEventListener('click', () => {
            document.querySelectorAll('[data-admin-children-drawer]').forEach((drawer) => {
                drawer.classList.remove('grid-rows-[0fr]');
                drawer.classList.add('grid-rows-[1fr]');
            });
            document.querySelectorAll('[data-admin-cat-icon]').forEach((icon) => {
                icon.classList.add('rotate-90');
            });
            document.querySelectorAll('[data-admin-cat-toggle]').forEach((btn) => {
                btn.setAttribute('aria-expanded', 'true');
            });
        });

        // Collapse All button
        document.getElementById('admin-cat-collapse-all')?.addEventListener('click', () => {
            document.querySelectorAll('[data-admin-children-drawer]').forEach((drawer) => {
                drawer.classList.remove('grid-rows-[1fr]');
                drawer.classList.add('grid-rows-[0fr]');
            });
            document.querySelectorAll('[data-admin-cat-icon]').forEach((icon) => {
                icon.classList.remove('rotate-90');
            });
            document.querySelectorAll('[data-admin-cat-toggle]').forEach((btn) => {
                btn.setAttribute('aria-expanded', 'false');
            });
        });

        // --- 2. Category Form Modal (Create / Edit) Logic ---
        const catFormModal = document.getElementById('cat-form-modal');
        const catForm = document.getElementById('cat-modal-form');
        const catModalTitle = document.getElementById('cat-modal-title');
        const catModalSubtitle = document.getElementById('cat-modal-subtitle');
        const catFormMethod = document.getElementById('cat-form-method');
        const catNameInput = document.getElementById('cat-name-input');
        const catSlugInput = document.getElementById('cat-slug-input');
        const catDescInput = document.getElementById('cat-desc-input');
        const catParentInput = document.getElementById('cat-parent-input');
        const catStatusInput = document.getElementById('cat-status-input');
        const catBtnSubmit = document.getElementById('cat-btn-submit');

        const closeCatFormModal = () => {
            catFormModal?.classList.add('hidden');
        };

        const openCatCreateModal = (parentId = '', parentName = '') => {
            if (!catFormModal) return;
            catForm.reset();
            catForm.action = "{{ route('admin.categories.store') }}";
            catFormMethod.value = 'POST';

            if (catParentInput) {
                Array.from(catParentInput.options).forEach(opt => opt.disabled = false);
                catParentInput.value = parentId || '';
            }

            if (parentId && parentName) {
                catModalTitle.textContent = 'Thêm danh mục con';
                catModalSubtitle.textContent = `Thêm chuyên mục con trực thuộc: "${parentName}".`;
            } else {
                catModalTitle.textContent = 'Thêm chuyên mục mới';
                catModalSubtitle.textContent = 'Thiết lập thông tin tên, đường dẫn tĩnh và danh mục cha.';
            }

            catBtnSubmit.textContent = 'Tạo chuyên mục';
            catFormModal.classList.remove('hidden');
            catNameInput?.focus();
        };

        const openCatEditModal = (btn) => {
            if (!catFormModal) return;
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            const slug = btn.getAttribute('data-slug');
            const desc = btn.getAttribute('data-description') || '';
            const parentId = btn.getAttribute('data-parent-id') || '';
            const status = btn.getAttribute('data-status') || 'Active';
            const action = btn.getAttribute('data-action');

            catForm.action = action;
            catFormMethod.value = 'PUT';

            catModalTitle.textContent = `Sửa chuyên mục: ${name}`;
            catModalSubtitle.textContent = 'Cập nhật thông tin chuyên mục.';

            catNameInput.value = name;
            catSlugInput.value = slug;
            catDescInput.value = desc;

            if (catParentInput) {
                Array.from(catParentInput.options).forEach(opt => {
                    opt.disabled = (opt.value === id);
                });
                catParentInput.value = parentId;
            }

            if (catStatusInput) {
                catStatusInput.value = status;
            }

            catBtnSubmit.textContent = 'Lưu thay đổi';
            catFormModal.classList.remove('hidden');
            catNameInput?.focus();
        };

        document.getElementById('btn-open-create-category-modal')?.addEventListener('click', (e) => {
            e.preventDefault();
            openCatCreateModal();
        });

        document.querySelectorAll('[data-cat-modal-close]').forEach((btn) => {
            btn.addEventListener('click', closeCatFormModal);
        });
        catFormModal?.addEventListener('click', (e) => {
            if (e.target === catFormModal) closeCatFormModal();
        });

        // --- 3. Category Delete Confirmation Modal Logic ---
        const catDeleteModal = document.getElementById('cat-delete-modal');
        const catDeleteForm = document.getElementById('cat-delete-form');
        const catDeleteTargetName = document.getElementById('cat-delete-target-name');
        const catDeleteWarningBox = document.getElementById('cat-delete-warning-box');
        const catDeleteWarningText = document.getElementById('cat-delete-warning-text');

        const closeCatDeleteModal = () => {
            catDeleteModal?.classList.add('hidden');
        };

        const openCatDeleteModal = (btn) => {
            if (!catDeleteModal) return;
            const name = btn.getAttribute('data-name');
            const action = btn.getAttribute('data-action');
            const hasChildren = btn.getAttribute('data-has-children') === '1';
            const postsCount = parseInt(btn.getAttribute('data-posts-count') || '0', 10);

            catDeleteForm.action = action;
            catDeleteTargetName.textContent = name;

            if (hasChildren || postsCount > 0) {
                catDeleteWarningBox.classList.remove('hidden');
                const warnings = [];
                if (hasChildren) warnings.push('đang có danh mục con trực thuộc');
                if (postsCount > 0) warnings.push(`đang có ${postsCount} bài viết`);
                catDeleteWarningText.textContent = `Cảnh báo: Chuyên mục này ${warnings.join(' và ')}. Hệ thống sẽ từ chối xóa nếu chưa di dời các dữ liệu liên quan.`;
            } else {
                catDeleteWarningBox.classList.add('hidden');
                catDeleteWarningText.textContent = '';
            }

            catDeleteModal.classList.remove('hidden');
        };

        document.querySelectorAll('[data-cat-delete-modal-close]').forEach((btn) => {
            btn.addEventListener('click', closeCatDeleteModal);
        });
        catDeleteModal?.addEventListener('click', (e) => {
            if (e.target === catDeleteModal) closeCatDeleteModal();
        });

        document.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-cat-action]');
            if (!btn) return;
            e.preventDefault();
            e.stopPropagation();

            const actionType = btn.getAttribute('data-cat-action');
            if (actionType === 'add-sub') {
                const parentId = btn.getAttribute('data-parent-id');
                const parentName = btn.getAttribute('data-parent-name');
                openCatCreateModal(parentId, parentName);
            } else if (actionType === 'edit') {
                openCatEditModal(btn);
            } else if (actionType === 'delete') {
                openCatDeleteModal(btn);
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                if (catFormModal && !catFormModal.classList.contains('hidden')) {
                    closeCatFormModal();
                }
                if (catDeleteModal && !catDeleteModal.classList.contains('hidden')) {
                    closeCatDeleteModal();
                }
            }
        });
    });
</script>
@endsection
