<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class Controller
{
    protected function success(
        array $data = [],
        string $message = 'Success',
        int $status = Response::HTTP_OK
    ): JsonResponse
    {
        return response()->json([
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    protected function error(
        string $message = 'Error',
        array $errors = [],
        int $status = Response::HTTP_UNPROCESSABLE_ENTITY
    ): JsonResponse
    {
        return response()->json([
            'status'  => 'error',
            'message' => $message,
            'errors'  => $errors,
        ], $status);
    }
}
