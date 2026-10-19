<?php

namespace App\Http\Requests;

use App\Services\ContactMessageService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ContactMessageStoreRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'form_token' => ['required', 'string', $this->freshToken()],
            'company_website' => ['nullable', 'string', 'max:255'],
        ];
    }

    private function freshToken(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            $issued = app(ContactMessageService::class)->issuedAt(is_string($value) ? $value : '');
            $maxSeconds = (int) config('contact.max_seconds');

            if ($issued === null || $issued->lt(now()->subSeconds($maxSeconds))) {
                $fail('The form has expired. Refresh the page and try again.');
            }
        };
    }
}
