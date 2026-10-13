<?php

namespace App\Http\Requests;

use App\Models\CaseStudy;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CaseStudyUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('case_studies', 'slug')->ignore($this->route('caseStudy')),
            ],
            'client_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'tagline' => ['sometimes', 'nullable', 'string', 'max:255'],
            'summary' => ['sometimes', 'nullable', 'string'],
            'introduction' => ['sometimes', 'nullable', 'string'],
            'status' => ['sometimes', 'string', Rule::in([CaseStudy::STATUS_DRAFT, CaseStudy::STATUS_PUBLISHED])],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
