<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApiExceptionRenderer
{
    public function register(Exceptions $exceptions): void
    {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $this->wantsJson($request),
        );

        $exceptions->dontReport(ApiException::class);

        $exceptions->render(function (ApiException $e, Request $request) {
            if (! $this->wantsJson($request)) {
                return null;
            }

            return $e->render();
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if (! $this->wantsJson($request)) {
                return null;
            }

            $message = $e->getPrevious() instanceof ModelNotFoundException
                ? __('exceptions.model_not_found')
                : __('exceptions.route_not_found');

            return ApiException::response(message: $message, status: $e->getStatusCode());
        });

        $exceptions->render(function (AuthorizationException|AccessDeniedHttpException $e, Request $request) {
            if (! $this->wantsJson($request)) {
                return null;
            }

            $status = $e instanceof AccessDeniedHttpException
                ? $e->getStatusCode()
                : Response::HTTP_FORBIDDEN;

            return ApiException::response(
                message: __('exceptions.access_restricted'),
                status: $status,
            );
        });
    }

    private function wantsJson(Request $request): bool
    {
        return $request->is('api/*') || $request->expectsJson();
    }
}
