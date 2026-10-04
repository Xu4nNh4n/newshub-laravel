<?php

namespace App\Http\Requests\Admin;

use App\Enums\CategoryStatus;
use App\Models\Category;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('category')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Category $category */
        $category = $this->route('category');

        return [
            'parent_id' => [
                'nullable',
                Rule::exists(Category::class, 'id')
                    ->whereNull('parent_id')
                    ->whereNull('deleted_at'),
                Rule::notIn([$category->id]),
                function (string $attribute, mixed $value, \Closure $fail) use ($category): void {
                    if ($value !== null && $category->children()->exists()) {
                        $fail('Không thể đặt chuyên mục đang có danh mục con làm chuyên mục con.');
                    }
                },
            ],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique(Category::class)->ignore($this->route('category'))],
            'description' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', Rule::enum(CategoryStatus::class)],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['slug' => Str::slug($this->string('slug')->value() ?: $this->string('name')->value())]);
    }
}
