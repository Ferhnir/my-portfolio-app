<?php

namespace App\Http\Requests;

use App\Models\CaseStudyBlock;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CaseStudyBlockUpdateRequest extends FormRequest
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
            'layout' => ['sometimes', 'required', 'string', Rule::in([CaseStudyBlock::LAYOUT_CARD, CaseStudyBlock::LAYOUT_GROUP])],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
