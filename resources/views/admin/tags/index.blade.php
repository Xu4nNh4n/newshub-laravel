@extends('layouts.dashboard', ['title' => 'Quản lý thẻ'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b-2 border-line-strong pb-5">
        <div>
            <span class="font-mono text-xs font-bold uppercase tracking-wider text-ink-muted block mb-1">Hệ thống từ khóa</span>
            <h1 class="text-2xl font-black uppercase tracking-tight text-ink sm:text-3xl font-heading">Quản lý thẻ (Tags)</h1>
            <p class="text-xs text-ink-muted mt-1">Nhãn bổ sung phân loại chi tiết và liên kết nội dung cho bài viết.</p>
        </div>
        <button type="button"
                id="btn-open-create-tag-modal"
                class="inline-flex min-h-10 items-center gap-1.5 border-2 border-line-strong bg-lime px-4 py-1.5 font-mono text-xs font-bold uppercase text-ink shadow-brutal hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all cursor-pointer">
            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="size-3.5"><path d="M12 5v14M5 12h14"/></svg>
            <span>Thêm thẻ</span>
        </button>
    </div>

    <div class="overflow-x-auto border-2 border-line-strong bg-surface shadow-brutal">
        <table class="w-full text-left text-xs">
            <thead class="border-b-2 border-line-strong bg-paper font-mono uppercase text-ink">
                <tr>
                    <th scope="col" class="px-5 py-3 font-bold">Tên thẻ</th>
                    <th scope="col" class="px-5 py-3 font-bold">Slug</th>
                    <th scope="col" class="px-5 py-3 font-bold">Số bài viết</th>
                    <th scope="col" class="px-5 py-3 font-bold text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @forelse ($tags as $tag)
                    <tr class="transition-colors hover:bg-paper/50">
                        <td class="px-5 py-4 font-bold text-ink">
                            <span class="border border-line-strong bg-paper px-2 py-1 font-mono text-xs shadow-brutal-sm">#{{ $tag->name }}</span>
                        </td>
                        <td class="px-5 py-4 text-ink-muted font-mono">
                            {{ $tag->slug }}
                        </td>
                        <td class="px-5 py-4 font-mono text-ink tabular-nums">
                            {{ $tag->posts_count }} bài
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <button type="button"
                                        data-tag-action="edit"
                                        data-id="{{ $tag->id }}"
                                        data-name="{{ $tag->name }}"
                                        data-slug="{{ $tag->slug }}"
                                        data-action="{{ route('admin.tags.update', $tag) }}"
                                        class="inline-flex min-h-7 cursor-pointer items-center border border-line-strong bg-surface px-2.5 py-1 font-mono text-[11px] font-bold uppercase text-ink shadow-brutal-sm hover:bg-lime transition-all">
                                    Sửa
                                </button>
                                <button type="button"
                                        data-tag-action="delete"
                                        data-id="{{ $tag->id }}"
                                        data-name="{{ $tag->name }}"
                                        data-posts-count="{{ $tag->posts_count }}"
                                        data-action="{{ route('admin.tags.destroy', $tag) }}"
                                        class="inline-flex min-h-7 cursor-pointer items-center border border-line-strong bg-paper px-2.5 py-1 font-mono text-[11px] font-bold uppercase text-ink shadow-brutal-sm hover:border-danger hover:text-danger transition-all">
                                    Xóa
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-12 text-center font-mono text-xs text-ink-muted">Chưa có thẻ.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $tags->links() }}
    </div>
</div>

<!-- Tag Form Modal (Create / Edit) -->
<div id="tag-form-modal"
     class="fixed inset-0 z-50 hidden overflow-y-auto bg-ink/75 p-4 sm:p-6 flex items-center justify-center"
     role="dialog"
     aria-modal="true"
     aria-labelledby="tag-modal-title">
    <div class="relative w-full max-w-md border-2 border-line-strong bg-surface p-6 sm:p-8 shadow-brutal transition-all">
        <!-- Close button -->
        <button type="button"
                data-tag-modal-close
                class="absolute right-4 top-4 border border-line-strong bg-paper p-1 text-ink hover:bg-lime transition-colors cursor-pointer"
                aria-label="Đóng">
            <svg class="size-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </button>

        <div class="mb-5 border-b-2 border-line-strong pb-4">
            <h2 id="tag-modal-title" class="font-heading text-lg font-black uppercase text-ink">Thêm thẻ mới</h2>
            <p id="tag-modal-subtitle" class="font-mono text-xs text-ink-muted mt-1">Thiết lập tên nhãn và đường dẫn tĩnh tương ứng.</p>
        </div>

        <form id="tag-modal-form" method="POST" action="{{ route('admin.tags.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="_method" id="tag-form-method" value="POST">

            <div>
                <label for="tag-name-input" class="block font-mono text-xs font-bold uppercase tracking-wider text-ink">
                    Tên thẻ <span class="text-danger">*</span>
                </label>
                <input type="text"
                       id="tag-name-input"
                       name="name"
                       required
                       placeholder="Ví dụ: AI, Công nghệ số, Khởi nghiệp..."
                       class="mt-1.5 w-full border-2 border-line bg-paper px-3.5 py-2.5 text-xs text-ink outline-none focus:border-ink">
            </div>

            <div>
                <label for="tag-slug-input" class="block font-mono text-xs font-bold uppercase tracking-wider text-ink">
                    Slug (để trống sẽ tự sinh từ tên)
                </label>
                <input type="text"
                       id="tag-slug-input"
                       name="slug"
                       placeholder="vi-du-the"
                       class="mt-1.5 w-full border-2 border-line bg-paper px-3.5 py-2.5 text-xs font-mono text-ink outline-none focus:border-ink">
            </div>

            <div class="flex items-center justify-end gap-3 pt-5 border-t-2 border-line-strong">
                <button type="button"
                        data-tag-modal-close
                        class="border-2 border-line bg-surface px-4 py-2 font-mono text-xs font-bold uppercase text-ink hover:border-line-strong transition-colors cursor-pointer">
                    Hủy
                </button>
                <button type="submit"
                        id="tag-btn-submit"
                        class="border-2 border-line-strong bg-lime px-5 py-2 font-mono text-xs font-bold uppercase text-ink shadow-brutal-sm hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all cursor-pointer">
                    Lưu thẻ
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Tag Delete Confirmation Modal -->
<div id="tag-delete-modal"
     class="fixed inset-0 z-50 hidden overflow-y-auto bg-ink/75 p-4 sm:p-6 flex items-center justify-center"
     role="dialog"
     aria-modal="true"
     aria-labelledby="tag-delete-title">
    <div class="relative w-full max-w-md border-2 border-line-strong bg-surface p-6 shadow-brutal transition-all">
        <!-- Close button -->
        <button type="button"
                data-tag-delete-modal-close
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
                <h3 id="tag-delete-title" class="font-heading text-lg font-black uppercase text-ink">Xác nhận xóa thẻ</h3>
                <p class="text-xs text-ink-muted mt-1">
                    Bạn có chắc chắn muốn xóa thẻ <span id="tag-delete-target-name" class="font-bold text-danger"></span>?
                </p>
                <div id="tag-delete-warning-box" class="mt-2.5 border border-line bg-paper p-2.5 font-mono text-[11px] text-danger hidden">
                    <span id="tag-delete-warning-text"></span>
                </div>
                <p class="mt-2 font-mono text-[11px] text-ink-muted">
                    Lưu ý: Thao tác này sẽ gỡ thẻ khỏi các bài viết liên quan (bài viết sẽ không bị xóa).
                </p>
            </div>
        </div>

        <form id="tag-delete-form" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex items-center justify-end gap-3 pt-5 border-t-2 border-line-strong">
                <button type="button"
                        data-tag-delete-modal-close
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
        // --- 1. Tag Form Modal (Create / Edit) Logic ---
        const tagFormModal = document.getElementById('tag-form-modal');
        const tagForm = document.getElementById('tag-modal-form');
        const tagModalTitle = document.getElementById('tag-modal-title');
        const tagModalSubtitle = document.getElementById('tag-modal-subtitle');
        const tagFormMethod = document.getElementById('tag-form-method');
        const tagNameInput = document.getElementById('tag-name-input');
        const tagSlugInput = document.getElementById('tag-slug-input');
        const tagBtnSubmit = document.getElementById('tag-btn-submit');

        const closeTagFormModal = () => {
            tagFormModal?.classList.add('hidden');
        };

        const openTagCreateModal = () => {
            if (!tagFormModal) return;
            tagForm.reset();
            tagForm.action = "{{ route('admin.tags.store') }}";
            tagFormMethod.value = 'POST';

            tagModalTitle.textContent = 'Thêm thẻ mới';
            tagModalSubtitle.textContent = 'Thiết lập tên nhãn và đường dẫn tĩnh tương ứng.';
            tagBtnSubmit.textContent = 'Tạo thẻ';

            tagFormModal.classList.remove('hidden');
            tagNameInput?.focus();
        };

        const openTagEditModal = (btn) => {
            if (!tagFormModal) return;
            const name = btn.getAttribute('data-name');
            const slug = btn.getAttribute('data-slug');
            const action = btn.getAttribute('data-action');

            tagForm.action = action;
            tagFormMethod.value = 'PUT';

            tagModalTitle.textContent = `Sửa thẻ: #${name}`;
            tagModalSubtitle.textContent = 'Cập nhật tên hoặc đường dẫn tĩnh của thẻ.';

            tagNameInput.value = name;
            tagSlugInput.value = slug;

            tagBtnSubmit.textContent = 'Lưu thay đổi';
            tagFormModal.classList.remove('hidden');
            tagNameInput?.focus();
        };

        document.getElementById('btn-open-create-tag-modal')?.addEventListener('click', (e) => {
            e.preventDefault();
            openTagCreateModal();
        });

        document.querySelectorAll('[data-tag-modal-close]').forEach((btn) => {
            btn.addEventListener('click', closeTagFormModal);
        });
        tagFormModal?.addEventListener('click', (e) => {
            if (e.target === tagFormModal) closeTagFormModal();
        });

        // --- 2. Tag Delete Confirmation Modal Logic ---
        const tagDeleteModal = document.getElementById('tag-delete-modal');
        const tagDeleteForm = document.getElementById('tag-delete-form');
        const tagDeleteTargetName = document.getElementById('tag-delete-target-name');
        const tagDeleteWarningBox = document.getElementById('tag-delete-warning-box');
        const tagDeleteWarningText = document.getElementById('tag-delete-warning-text');

        const closeTagDeleteModal = () => {
            tagDeleteModal?.classList.add('hidden');
        };

        const openTagDeleteModal = (btn) => {
            if (!tagDeleteModal) return;
            const name = btn.getAttribute('data-name');
            const action = btn.getAttribute('data-action');
            const postsCount = parseInt(btn.getAttribute('data-posts-count') || '0', 10);

            tagDeleteForm.action = action;
            tagDeleteTargetName.textContent = `#${name}`;

            if (postsCount > 0) {
                tagDeleteWarningBox.classList.remove('hidden');
                tagDeleteWarningText.textContent = `Thẻ này đang được gán cho ${postsCount} bài viết. Nếu xóa, nhãn sẽ được gỡ khỏi tất cả bài viết này.`;
            } else {
                tagDeleteWarningBox.classList.add('hidden');
                tagDeleteWarningText.textContent = '';
            }

            tagDeleteModal.classList.remove('hidden');
        };

        document.querySelectorAll('[data-tag-delete-modal-close]').forEach((btn) => {
            btn.addEventListener('click', closeTagDeleteModal);
        });
        tagDeleteModal?.addEventListener('click', (e) => {
            if (e.target === tagDeleteModal) closeTagDeleteModal();
        });

        document.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-tag-action]');
            if (!btn) return;
            e.preventDefault();
            e.stopPropagation();

            const actionType = btn.getAttribute('data-tag-action');
            if (actionType === 'edit') {
                openTagEditModal(btn);
            } else if (actionType === 'delete') {
                openTagDeleteModal(btn);
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                if (tagFormModal && !tagFormModal.classList.contains('hidden')) {
                    closeTagFormModal();
                }
                if (tagDeleteModal && !tagDeleteModal.classList.contains('hidden')) {
                    closeTagDeleteModal();
                }
            }
        });
    });
</script>
@endsection
