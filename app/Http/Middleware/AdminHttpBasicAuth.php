<?php

namespace App\Http\Middleware;

use App\Services\Admin\AdminBasicAuthTelegramNotifier;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class AdminHttpBasicAuth
{
    private const SESSION_KEY_SUCCESS_NOTIFIED = 'admin_basic_telegram_success_notified';

    public function handle(Request $request, Closure $next)
    {
        if (! self::isConfigured()) {
            if (app()->environment('testing', 'local')) {
                return $next($request);
            }

            abort(503, 'Задайте в .env HTTP_BASIC_USERNAME и HTTP_BASIC_PASSWORD');
        }

        $expectedUser = (string) config('http_basic.username');
        $expectedPass = (string) config('http_basic.password');
        $givenUser = (string) $request->getUser();
        $givenPass = (string) $request->getPassword();

        if ($givenUser === '' && $givenPass === '' && $request->headers->get('Php-Auth-User')) {
            $givenUser = (string) $request->server->get('PHP_AUTH_USER', '');
            $givenPass = (string) $request->server->get('PHP_AUTH_PW', '');
        }

        $okUser = hash_equals($expectedUser, $givenUser);
        $okPass = hash_equals($expectedPass, $givenPass);

        if (! $okUser || ! $okPass) {
            $hasAttempt = ($givenUser !== '' || $givenPass !== '');
            if ($hasAttempt) {
                $this->queueInvalidTelegram($request, $givenUser);
            }

            return response('Unauthorized', 401, [
                'WWW-Authenticate' => 'Basic realm="SMM"',
            ]);
        }

        $response = $next($request);

        $notifier = app(AdminBasicAuthTelegramNotifier::class);
        $session = $request->getSession();
        if (
            $notifier->isEnabled()
            && $session !== null
            && ! $session->get(self::SESSION_KEY_SUCCESS_NOTIFIED)
        ) {
            $session->put(self::SESSION_KEY_SUCCESS_NOTIFIED, true);
            $login = $givenUser;
            App::terminating(static function () use ($request, $login): void {
                app(AdminBasicAuthTelegramNotifier::class)->notifySuccess($request, $login);
            });
        }

        return $response;
    }

    private function queueInvalidTelegram(Request $request, string $attemptedLogin): void
    {
        $notifier = app(AdminBasicAuthTelegramNotifier::class);
        if (! $notifier->isEnabled()) {
            return;
        }

        App::terminating(static function () use ($request, $attemptedLogin): void {
            app(AdminBasicAuthTelegramNotifier::class)->notifyInvalid($request, $attemptedLogin);
        });
    }

    public static function isConfigured(): bool
    {
        $u = (string) config('http_basic.username', '');
        $p = (string) config('http_basic.password', '');

        return $u !== '' && $p !== '';
    }
}
