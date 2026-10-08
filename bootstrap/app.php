<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Every /api/* error uses the same JSON shape as successful responses.
        $respond = function (string $message, int $status, ?array $errors = null) {
            $body = ['success' => false, 'data' => null, 'message' => $message];
            if ($errors) {
                $body['errors'] = $errors;
            }

            return response()->json($body, $status);
        };

        $exceptions->shouldRenderJsonWhen(fn (Request $request) => $request->is('api/*') || $request->expectsJson());

        $exceptions->render(function (ValidationException $e, Request $request) use ($respond) {
            if ($request->is('api/*')) {
                return $respond('Validation failed.', 422, $e->errors());
            }
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) use ($respond) {
            if ($request->is('api/*')) {
                return $respond('Unauthenticated.', 401);
            }
        });

        $exceptions->render(function (AuthorizationException $e, Request $request) use ($respond) {
            if ($request->is('api/*')) {
                return $respond('You are not allowed to perform this action.', 403);
            }
        });

        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) use ($respond) {
            if ($request->is('api/*')) {
                return $respond('You are not allowed to perform this action.', 403);
            }
        });

        $exceptions->render(function (ModelNotFoundException $e, Request $request) use ($respond) {
            if ($request->is('api/*')) {
                return $respond('Resource not found.', 404);
            }
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) use ($respond) {
            if ($request->is('api/*')) {
                return $respond('Resource not found.', 404);
            }
        });
    })->create();
