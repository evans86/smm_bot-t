<?php

namespace App\Http\Middleware;

use App\Services\Admin\AdminBasicAuthTelegramNotifier;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class AdminHttpBasicAuth
{
    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        if (! self::isConfigured()) {
            if (app()->environment('testing', 'local')) {
                return $next($request);
            }

            abort(503, 'Задайте в .env HTTP_BASIC_USERNAME и HTTP_BASIC_PASSWORD');
        }

        $expectedUser = (string) config('http_basic.username');
        $expectedPass = (string) config('http_basic.password');

        $givenUser = $request->getUser();
        $givenPassword = $request->getPassword();

        if (
            ($givenUser === null || $givenUser === '')
            && ($givenPassword === null || $givenPassword === '')
            && $request->headers->get('Php-Auth-User')
        ) {
            $givenUser = (string) $request->server->get('PHP_AUTH_USER', '');
            $givenPassword = (string) $request->server->get('PHP_AUTH_PW', '');
        }

        $givenUserStr = $givenUser !== null ? (string) $givenUser : '';
        $givenPassStr = $givenPassword !== null ? (string) $givenPassword : '';

        $okUser = hash_equals($expectedUser, $givenUserStr);
        $okPass = hash_equals($expectedPass, $givenPassStr);

        if (! $okUser || ! $okPass) {
            $reason = ($givenUser === null || $givenPassword === null) ? 'missing' : 'invalid';
            $attempted = $givenUser !== null ? (string) $givenUser : null;

            // Как в vpn: только неверный логин/пароль; без Authorization — не шлём.
            if ($reason === 'invalid') {
                App::terminating(static function () use ($request, $attempted, $reason): void {
                    app(AdminBasicAuthTelegramNotifier::class)->notifyFailure($request, $attempted, $reason);
                });
            }

            return response('Unauthorized', 401, [
                'WWW-Authenticate' => 'Basic realm="SMM"',
            ]);
        }

        $basicUsername = $givenUserStr;
        $response = $next($request);

        if ($request->hasSession() && ! $request->session()->get(AdminBasicAuthTelegramNotifier::SESSION_KEY_SUCCESS_NOTIFIED)) {
            $request->session()->put(AdminBasicAuthTelegramNotifier::SESSION_KEY_SUCCESS_NOTIFIED, true);
            App::terminating(static function () use ($request, $basicUsername): void {
                app(AdminBasicAuthTelegramNotifier::class)->notifySuccess($request, $basicUsername);
            });
        }

        return $response;
    }

    public static function isConfigured(): bool
    {
        $u = (string) config('http_basic.username', '');
        $p = (string) config('http_basic.password', '');

        return $u !== '' && $p !== '';
    }
}
