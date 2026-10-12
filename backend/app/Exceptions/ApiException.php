<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiException extends Exception
{
    /**
     * @param  array<string, mixed>  $errors
     */
    public function __construct(
        string $message = 'Error',
        protected int $status = Response::HTTP_UNPROCESSABLE_ENTITY,
        protected array $errors = [],
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $status, $previous);
    }

    public function status(): int
    {
        return $this->status;
    }

    /**
     * @return array<string, mixed>
     */
    public function errors(): array
    {
        return $this->errors;
    }

    public function render(): JsonResponse
    {
        return self::response(
            message: $this->getMessage(),
            status: $this->status,
            errors: $this->errors,
        );
    }

    /**
     * @param  array<string, mixed>  $errors
     */
    public static function response(string $message, int $status, array $errors = []): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}
