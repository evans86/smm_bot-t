<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Middleware\EnsureAdminEnvAuthenticated;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AdminEnvAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.admin-login', [
            'adminConfigured' => EnsureAdminEnvAuthenticated::credentialsConfigured(),
        ]);
    }

    public function login(Request $request)
    {
        if (! EnsureAdminEnvAuthenticated::credentialsConfigured()) {
            throw ValidationException::withMessages([
                'username' => 'Учётные данные администратора не заданы в .env',
            ]);
        }

        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $expectedUser = (string) config('admin.username');
        $bcrypt = (string) config('admin.password_bcrypt');
        $plain = (string) config('admin.password_plain');

        if (! hash_equals($expectedUser, $request->username)) {
            throw ValidationException::withMessages([
                'username' => __('Неверный логин или пароль'),
            ]);
        }

        $passwordOk = false;
        if ($bcrypt !== '') {
            $passwordOk = password_verify($request->password, $bcrypt);
        } elseif ($plain !== '') {
            $passwordOk = hash_equals($plain, $request->password);
        }

        if (! $passwordOk) {
            throw ValidationException::withMessages([
                'username' => __('Неверный логин или пароль'),
            ]);
        }

        $request->session()->regenerate();
        $request->session()->put('admin_env_auth', true);

        return redirect()->intended('/');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('admin_env_auth');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
