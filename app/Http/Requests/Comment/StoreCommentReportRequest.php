<?php

namespace App\Http\Requests\Comment;

use App\Enums\CommentReportReason;
use App\Models\Comment;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCommentReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        $comment = $this->route('comment');

        return $comment instanceof Comment && ($this->user()?->can('report', $comment) ?? false);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'reason' => ['required', Rule::enum(CommentReportReason::class)],
            'description' => [
                'nullable',
                'string',
                'max:1000',
                Rule::requiredIf($this->input('reason') === CommentReportReason::Other->value),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $description = trim($this->string('description')->toString());
        $this->merge(['description' => $description === '' ? null : $description]);
    }
}
