<?php

namespace App\Exceptions;

use App\Helpers\ApiHelpers;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
        'private_key',
        'secret_key',
        'user_secret_key',
        'new_private_key',
        'api_key',
        'token',
    ];

    /**
     * API routes must never render Laravel debug pages with request query parameters.
     */
    public function render($request, Throwable $e)
    {
        if ($request->expectsJson() || $this->isApiRoute($request)) {
            if ($e instanceof HttpExceptionInterface) {
                $status = $e->getStatusCode();
                if ($status < 500) {
                    $message = $status === 429 ? 'Too many requests' : 'Request error';
                    return response()->json(ApiHelpers::error($message), $status);
                }
            }

            return response()->json(ApiHelpers::error('Server error'), 500);
        }

        return parent::render($request, $e);
    }

    private function isApiRoute($request): bool
    {
        return in_array($request->path(), [
            'getSocial',
            'getCategories',
            'getTypes',
            'createOrder',
            'getOrder',
            'orders',
            'setLanguage',
            'getUser',
            'ping',
            'create',
            'error',
            'get',
            'update',
            'rotatePrivateKey',
            'delete',
            'getSettings',
        ], true);
    }

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
