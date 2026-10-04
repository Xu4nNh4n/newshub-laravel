@extends('layouts.dashboard', ['title' => isset($category) ? 'Sửa chuyên mục' : 'Thêm chuyên mục', 'breadcrumbs' => $breadcrumbs ?? null])

@section('content')
<div class="mx-auto max-w-2xl space-y-6">
    <div class="border-b-2 border-line-strong pb-5">
        <span class="font-mono text-xs font-bold uppercase tracking-wider text-ink-muted block mb-1">Cấu trúc tòa soạn</span>
        <h1 class="text-2xl font-black uppercase tracking-tight text-ink sm:text-3xl font-heading">
            {{ isset($category) ? 'Sửa chuyên mục' : 'Thêm chuyên mục' }}
        </h1>
        <p class="text-xs text-ink-muted mt-1">Thiết lập thông tin tên, đường dẫn tĩnh và trạng thái hiển thị.</p>
    </div>

    <form method="POST" action="{{ isset($category) ? route('admin.categories.update', $category) : route('admin.categories.store') }}" class="space-y-5 border-2 border-line-strong bg-surface p-6 sm:p-8 shadow-brutal">
        @csrf
        @isset($category) @method('PUT') @endisset

        <div>
            <label for="name" class="block font-mono text-xs font-bold uppercase tracking-wider text-ink">
                Tên chuyên mục <span class="text-danger">*</span>
            </label>
            <input id="name" name="name" value="{{ old('name', $category->name ?? '') }}" required placeholder="Ví dụ: Công nghệ, Khoa học..." class="mt-1.5 w-full border-2 border-line bg-paper px-3.5 py-2.5 text-xs text-ink outline-none focus:border-ink">
        </div>

        <div>
            <label for="slug" class="block font-mono text-xs font-bold uppercase tracking-wider text-ink">
                Slug (để trống sẽ tự sinh từ tên)
            </label>
            <input id="slug" name="slug" value="{{ old('slug', $category->slug ?? '') }}" placeholder="cong-nghe" class="mt-1.5 w-full border-2 border-line bg-paper px-3.5 py-2.5 text-xs font-mono text-ink outline-none focus:border-ink">
        </div>

        <div>
            <label for="description" class="block font-mono text-xs font-bold uppercase tracking-wider text-ink">Mô tả</label>
            <textarea id="description" name="description" rows="3" placeholder="Mô tả tóm tắt..." class="mt-1.5 w-full border-2 border-line bg-paper px-3.5 py-2.5 text-xs text-ink outline-none focus:border-ink">{{ old('description', $category->description ?? '') }}</textarea>
        </div>

        <div>
            <label for="parent_id" class="block font-mono text-xs font-bold uppercase tracking-wider text-ink">Danh mục cha (Tùy chọn)</label>
            <select id="parent_id" name="parent_id" class="mt-1.5 w-full border-2 border-line bg-paper px-3.5 py-2.5 text-xs text-ink outline-none focus:border-ink">
                <option value="">-- Không có (Đây là danh mục chính) --</option>
                @foreach ($parentCategories as $parent)
                    @if (!isset($category) || $category->id !== $parent->id)
                        <option value="{{ $parent->id }}" @selected((string) old('parent_id', $category->parent_id ?? '') === (string) $parent->id)>
                            {{ $parent->name }}
                        </option>
                    @endif
                @endforeach
            </select>
            <p class="mt-1 font-mono text-[11px] text-ink-muted">
                Nếu chọn danh mục cha, chuyên mục này sẽ trở thành danh mục con hiển thị trong menu xổ xuống.
            </p>
            @error('parent_id')
                <p class="mt-1 font-mono text-xs text-danger font-bold">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="status" class="block font-mono text-xs font-bold uppercase tracking-wider text-ink">Trạng thái</label>
            <select id="status" name="status" class="mt-1.5 w-full border-2 border-line bg-paper px-3.5 py-2.5 text-xs text-ink outline-none focus:border-ink">
                @foreach (\App\Enums\CategoryStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected(old('status', ($category->status ?? \App\Enums\CategoryStatus::Active)->value) === $status->value)>
                        {{ $status->value }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-3 pt-5 border-t-2 border-line-strong">
            <button type="submit" class="border-2 border-line-strong bg-lime px-5 py-2 font-mono text-xs font-bold uppercase text-ink shadow-brutal-sm hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all cursor-pointer">
                Lưu chuyên mục
            </button>
            <a href="{{ route('admin.categories.index') }}" class="border-2 border-line bg-surface px-4 py-2 font-mono text-xs font-bold uppercase text-ink hover:border-line-strong transition-colors">
                Hủy
            </a>
        </div>
    </form>
</div>
@endsection
