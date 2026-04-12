<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureHttpBasicFromEnv
{
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
            return response('Unauthorized', 401, [
                'WWW-Authenticate' => 'Basic realm="SMM"',
            ]);
        }

        return $next($request);
    }

    public static function isConfigured(): bool
    {
        $u = (string) config('http_basic.username', '');
        $p = (string) config('http_basic.password', '');

        return $u !== '' && $p !== '';
    }
}
