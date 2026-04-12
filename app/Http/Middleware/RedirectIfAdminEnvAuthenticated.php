<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfAdminEnvAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->session()->get('admin_env_auth')) {
            return redirect()->intended('/');
        }

        return $next($request);
    }
}
