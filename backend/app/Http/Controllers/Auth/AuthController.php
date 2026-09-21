<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Resources\AuthResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse|AuthResource
    {
        try {
            $key = 'login-attempts:' . $request->ip();

            if (RateLimiter::tooManyAttempts($key, 5)) {
                $seconds = RateLimiter::availableIn($key);
                return $this->error(
                    message: __(key: 'auth.login.too_many_attempts'),
                    errors: [
                        'seconds' => $seconds
                    ],
                    status: Response::HTTP_TOO_MANY_REQUESTS
                );
            }

            if (!Auth::attempt($request->only(keys: ['email', 'password']))) {
                RateLimiter::hit($key); // Block for 60 seconds after 5 attempts
                return $this->error(
                    message: __(key: 'auth.login.invalid_credentials'),
                    status: Response::HTTP_UNAUTHORIZED
                );
            }

            RateLimiter::clear($key);

            $user = Auth::user();

            $token = $user->createToken(
                name: 'vue-app',
                expiresAt: config('sanctum.expiration')
            )->plainTextToken;

            return new AuthResource([
                'user' => $user,
                'token' => $token
            ]);

        } catch (\Exception $e) {
            return $this->error(
                message: __(key: 'auth.login.failed'),
                errors: [
                    'info' => $e->getMessage(),
                ]
            );
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return $this->success(
                message: __(key: 'auth.logout.success')
            );

        } catch (\Exception $e) {
            return $this->error(
                message: __(key: 'auth.logout.failed'),
                errors: [
                    'info' => $e->getMessage(),
                ]
            );
        }
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            $status = Password::sendResetLink(
                $request->only(keys: ['email'])
            );

            return match($status) {
                Password::RESET_LINK_SENT => $this->success(message: __(key: 'auth.password_reset.link_sent')),
                Password::RESET_THROTTLED => $this->error(message: __(key: 'auth.password_reset.too_many_attempts')),
                default => $this->error(message: __(key: 'auth.password_reset.request_failed')),
            };

        } catch (\Exception $e) {
            return $this->error(
                message: __(key: 'auth.password_reset.request_failed'),
                errors: [
                    'info' => $e->getMessage(),
                ]
            );
        }
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $status = DB::transaction(function () use ($request) {
                return Password::reset(
                    credentials: $request->only(keys: ['email', 'password', 'token']),
                    callback: function (User $user) use ($request) {
                        $user->forceFill([
                            'password' => Hash::make($request->input(key: 'password')),
                        ])->save();

                        $user->tokens()->delete();
                    }
                );
            });

            return match ($status) {
                Password::PASSWORD_RESET => $this->success(message: __(key: 'auth.password_reset.success')),
                Password::INVALID_TOKEN => $this->error(message: __(key: 'auth.password_reset.invalid_token')),
                default => $this->error(message: __(key: 'auth.password_reset.failed')),
            };

        } catch (\Exception $e) {
            return $this->error(
                message: __(key: 'auth.password_reset.failed'),
                errors: [
                    'info' => $e->getMessage(),
                ]
            );
        }
    }

}