<?php

namespace App\Http\Requests;

use App\Models\CaseStudy;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CaseStudyStoreRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('case_studies', 'slug')],
            'client_name' => ['nullable', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'introduction' => ['nullable', 'string'],
            'status' => ['sometimes', 'string', Rule::in([CaseStudy::STATUS_DRAFT, CaseStudy::STATUS_PUBLISHED])],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
