<?php

namespace App\Http\Middleware;

use App\Models\HttpRequestLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class LogHttpRequests
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('http_request_log.enabled', true) || $this->shouldSkip($request)) {
            return $next($request);
        }

        $started = microtime(true);

        $response = $next($request);

        $durationMs = (int) round((microtime(true) - $started) * 1000);
        $status = $response->getStatusCode();
        $userId = Auth::id();

        App::terminating(static function () use ($request, $durationMs, $status, $userId): void {
            try {
                HttpRequestLog::query()->create([
                    'method' => strtoupper($request->getMethod()),
                    'path' => Str::limit($request->path(), 2048, '…'),
                    'ip' => $request->ip(),
                    'user_agent' => Str::limit((string) $request->userAgent(), 65000, '…'),
                    'user_id' => $userId,
                    'status_code' => $status,
                    'duration_ms' => $durationMs,
                    'created_at' => now(),
                ]);
            } catch (\Throwable $e) {
                Log::warning('LogHttpRequests: не удалось записать запрос', [
                    'message' => $e->getMessage(),
                ]);
            }
        });

        return $response;
    }

    private function shouldSkip(Request $request): bool
    {
        $path = trim($request->path(), '/');
        foreach (config('http_request_log.exclude_path_prefixes', []) as $prefix) {
            if ($prefix !== '' && Str::startsWith($path, $prefix)) {
                return true;
            }
        }

        return false;
    }
}
