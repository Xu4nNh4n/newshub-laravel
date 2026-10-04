<?php

namespace App\Http\Requests\Author;

use App\Enums\PostRequestPriority;
use App\Enums\PostRequestType;
use App\Models\Post;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePostWorkflowRequest extends FormRequest
{
    public function authorize(): bool
    {
        $post = $this->route('post');

        return $post instanceof Post && ($this->user()?->can('requestChange', $post) ?? false);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(PostRequestType::class)],
            'priority' => ['required', Rule::enum(PostRequestPriority::class)],
            'reason' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
