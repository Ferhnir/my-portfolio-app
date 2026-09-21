<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): JsonResponse
    {
        $user = $request->user();

        $token = $user->createToken('vue-app')->plainTextToken;

        return new JsonResponse([
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }
}