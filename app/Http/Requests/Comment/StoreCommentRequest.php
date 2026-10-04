<?php

namespace App\Http\Requests\Comment;

use App\Enums\CommentStatus;
use App\Models\Comment;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Comment::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:2000'],
            'reply_to_id' => [
                'nullable',
                'integer',
                Rule::exists(Comment::class, 'id')
                    ->where('status', CommentStatus::Visible->value)
                    ->whereNull('deleted_at'),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['content' => trim($this->string('content')->toString())]);
    }
}
