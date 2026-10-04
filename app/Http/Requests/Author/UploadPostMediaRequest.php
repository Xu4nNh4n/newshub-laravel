<?php

namespace App\Http\Requests\Author;

use App\Models\Post;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UploadPostMediaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Post::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'image' => [
                'required',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp,gif',
                'extensions:jpg,jpeg,png,webp,gif',
                'max:'.config('images.post_media.max_size_kb'),
                Rule::dimensions()
                    ->maxWidth((int) config('images.max_input_dimension'))
                    ->maxHeight((int) config('images.max_input_dimension')),
            ],
        ];
    }
}
