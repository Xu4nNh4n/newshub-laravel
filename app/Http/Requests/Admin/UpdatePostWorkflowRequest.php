<?php

namespace App\Http\Requests\Admin;

use App\Enums\PostRequestStatus;
use App\Models\PostRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePostWorkflowRequest extends FormRequest
{
    public function authorize(): bool
    {
        $postRequest = $this->route('postRequest');

        return $postRequest instanceof PostRequest && ($this->user()?->can('update', $postRequest) ?? false);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in([
                PostRequestStatus::Approved->value,
                PostRequestStatus::Rejected->value,
            ])],
            'admin_notes' => [
                'nullable',
                'string',
                'max:2000',
                Rule::requiredIf($this->string('status')->toString() === PostRequestStatus::Rejected->value),
            ],
        ];
    }
}
