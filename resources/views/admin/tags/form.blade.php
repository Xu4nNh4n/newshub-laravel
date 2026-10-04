@extends('layouts.dashboard', ['title' => isset($tag) ? 'Sửa thẻ' : 'Thêm thẻ', 'breadcrumbs' => $breadcrumbs ?? null])

@section('content')
<div class="mx-auto max-w-2xl space-y-6">
    <div class="border-b-2 border-line-strong pb-5">
        <span class="font-mono text-xs font-bold uppercase tracking-wider text-ink-muted block mb-1">Hệ thống từ khóa</span>
        <h1 class="text-2xl font-black uppercase tracking-tight text-ink sm:text-3xl font-heading">
            {{ isset($tag) ? 'Sửa thẻ' : 'Thêm thẻ mới' }}
        </h1>
        <p class="text-xs text-ink-muted mt-1">Thiết lập tên nhãn và đường dẫn tĩnh tương ứng.</p>
    </div>

    <form method="POST" action="{{ isset($tag) ? route('admin.tags.update', $tag) : route('admin.tags.store') }}" class="space-y-5 border-2 border-line-strong bg-surface p-6 sm:p-8 shadow-brutal">
        @csrf
        @isset($tag) @method('PUT') @endisset

        <div>
            <label for="name" class="block font-mono text-xs font-bold uppercase tracking-wider text-ink">
                Tên thẻ <span class="text-danger">*</span>
            </label>
            <input id="name" name="name" value="{{ old('name', $tag->name ?? '') }}" required placeholder="Ví dụ: Công nghệ AI, Khởi nghiệp..." class="mt-1.5 w-full border-2 border-line bg-paper px-3.5 py-2.5 text-xs text-ink outline-none focus:border-ink">
        </div>

        <div>
            <label for="slug" class="block font-mono text-xs font-bold uppercase tracking-wider text-ink">
                Slug (để trống sẽ tự sinh từ tên)
            </label>
            <input id="slug" name="slug" value="{{ old('slug', $tag->slug ?? '') }}" placeholder="cong-nghe-ai" class="mt-1.5 w-full border-2 border-line bg-paper px-3.5 py-2.5 text-xs font-mono text-ink outline-none focus:border-ink">
        </div>

        <div class="flex items-center gap-3 pt-5 border-t-2 border-line-strong">
            <button type="submit" class="border-2 border-line-strong bg-lime px-5 py-2 font-mono text-xs font-bold uppercase text-ink shadow-brutal-sm hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all cursor-pointer">
                Lưu thẻ
            </button>
            <a href="{{ route('admin.tags.index') }}" class="border-2 border-line bg-surface px-4 py-2 font-mono text-xs font-bold uppercase text-ink hover:border-line-strong transition-colors">
                Hủy
            </a>
        </div>
    </form>
</div>
@endsection
