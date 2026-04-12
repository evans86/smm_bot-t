<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAdminEnvAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (! self::credentialsConfigured()) {
            return redirect()
                ->route('admin.login')
                ->with('warning', 'Задайте в .env переменные ADMIN_USERNAME и пароль (ADMIN_PASSWORD_BCRYPT или ADMIN_PASSWORD).');
        }

        if (! $request->session()->get('admin_env_auth')) {
            return redirect()->guest(route('admin.login'));
        }

        return $next($request);
    }

    public static function credentialsConfigured(): bool
    {
        $user = (string) config('admin.username', '');
        $hash = (string) config('admin.password_bcrypt', '');
        $plain = (string) config('admin.password_plain', '');

        return $user !== '' && ($hash !== '' || $plain !== '');
    }
}
