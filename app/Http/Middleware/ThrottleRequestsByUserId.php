<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ThrottleRequestsByUserId
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user_secret_key = $request->query('user_secret_key');

        // Проверяем, есть ли вообще параметр user_id
        if ($user_secret_key) {
            // Генерируем ключ для кэша на основе user_id
            $cacheKey = 'last_request_user_' . $user_secret_key;

            // Если есть запись о предыдущем запросе
            if (Cache::has($cacheKey)) {
                $lastRequestTime = Cache::get($cacheKey);

                // Проверяем, прошло ли более 4 секунд
                if (time() - $lastRequestTime < 4) {
                    // Если нет, возвращаем ответ с ошибкой
                    return response('Слишком частые запросы. Попробуйте позже.', 429);
                }
            }

            // Обновляем время последнего запроса
            Cache::put($cacheKey, time(), 60); // Храним метку времени не менее минуты для надежности
        }

        return $next($request);
    }
}
