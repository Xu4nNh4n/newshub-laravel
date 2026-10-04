<?php

namespace App\Http\Requests\Author;

use App\Models\Post;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('post')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $maxImageDimension = (int) config('images.max_input_dimension');

        return [
            'title' => ['required', 'string', 'max:255'],
            'meta_title' => ['nullable', 'string', 'max:60'],
            'slug' => ['required', 'string', 'max:255', Rule::unique(Post::class)->ignore($this->route('post'))],
            'summary' => ['nullable', 'string', 'max:1000'],
            'meta_description' => ['nullable', 'string', 'max:160'],
            'content' => ['required', 'string'],
            'show_thumbnail_in_post' => ['nullable', 'boolean'],
            'thumbnail' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'extensions:jpg,jpeg,png,webp',
                'max:4096',
                Rule::dimensions()->maxWidth($maxImageDimension)->maxHeight($maxImageDimension),
            ],
            'remove_thumbnail' => ['sometimes', 'boolean'],
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')->whereNull('deleted_at')],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'distinct', Rule::exists('tags', 'id')],
            'tag_names' => ['nullable', 'array', 'max:20'],
            'tag_names.*' => ['string', 'max:50'],
            'action' => ['nullable', 'string', Rule::in(['draft', 'publish'])],
            'published_at' => ['nullable', 'date'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['slug' => Str::slug($this->string('slug')->value() ?: $this->string('title')->value())]);
    }
}
