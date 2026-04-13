<?php

namespace App\Http\Middleware;

use App\Services\Admin\AdminBasicAuthTelegramNotifier;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
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
            // Пустые строки от движка ≠ null: считаем попытку только если передан логин или пароль.
            $hasAttempt = $givenUserStr !== '' || $givenPassStr !== '';
            $attempted = $givenUserStr !== '' ? $givenUserStr : null;

            if ($hasAttempt) {
                App::terminating(static function () use ($request, $attempted): void {
                    app(AdminBasicAuthTelegramNotifier::class)->notifyFailure($request, $attempted, 'invalid');
                });
            }

            return response('Unauthorized', 401, [
                'WWW-Authenticate' => 'Basic realm="SMM"',
            ]);
        }

        $basicUsername = $givenUserStr;
        $response = $next($request);

        if (! $request->hasSession()) {
            Log::warning('Admin HTTP Basic: сессия недоступна, уведомление об успехе в Telegram не ставится в очередь', [
                'path' => $request->getPathInfo(),
            ]);

            return $response;
        }

        if (! $request->session()->get(AdminBasicAuthTelegramNotifier::SESSION_KEY_SUCCESS_NOTIFIED)) {
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
