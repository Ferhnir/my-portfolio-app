<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TechStackGroupCategoryStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'category_id' => $this->route('techStackCategory')->id
        ]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => [
                'required',
                Rule::unique('tech_stack_group_category', 'category_id')->where(
                    'group_id',
                    $this->route('techStackGroup')->id,
                ),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.unique' => 'The selected category is already attached to this group.'
        ];
    }
}
