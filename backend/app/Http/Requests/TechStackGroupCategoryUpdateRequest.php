<?php

namespace App\Http\Requests;

use App\Models\TechStackCategory;
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
            'category_id' => $this->route('techStackGroupCategory')->category_id,
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
            'group_id' => [
                'sometimes',
                'integer',
                'exists:tech_stack_groups,id',
                Rule::unique('tech_stack_group_category', 'group_id')
                    ->where('category_id', $this->route('techStackGroupCategory')->category_id)
                    ->ignore($this->route('techStackGroupCategory')->group_id, 'group_id'),
            ],
            'sort_order' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'group_id.unique' => 'The selected category is already attached to this group.'
        ];
    }
}
