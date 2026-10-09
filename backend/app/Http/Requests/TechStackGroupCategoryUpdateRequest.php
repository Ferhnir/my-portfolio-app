<?php

namespace App\Http\Requests;

use App\Models\TechStackGroupCategory;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TechStackGroupCategoryUpdateRequest extends FormRequest
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
            TechStackGroupCategory::LINKABLE_MODEL_KEY => $this->route('techStackGroupCategory')->category_id,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            TechStackGroupCategory::LINKABLE_MODEL_PARENT_KEY => [
                'sometimes',
                'integer',
                'exists:tech_stack_groups,id',
                Rule::unique('tech_stack_group_category', TechStackGroupCategory::LINKABLE_MODEL_PARENT_KEY)
                    ->where('category_id', $this->route('techStackGroupCategory')->category_id)
                    ->ignore($this->route('techStackGroupCategory')->id),
            ],
            TechStackGroupCategory::SORTABLE_COLUMN => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            TechStackGroupCategory::LINKABLE_MODEL_PARENT_KEY.'.unique'
                => 'The selected category is already attached to this group.',
        ];
    }
}
