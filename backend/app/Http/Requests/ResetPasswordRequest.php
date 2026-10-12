<?php

namespace App\Http\Requests;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @throws AuthorizationException
     */
    public function authorize(): bool
    {
        if (! URL::hasValidSignature($this)) {
            throw new AuthorizationException(
                message: __(key: 'auth.signature.invalid_url_or_expired'),
                code: Response::HTTP_BAD_REQUEST
            );
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string', 'min:8'],
        ];
    }
}
