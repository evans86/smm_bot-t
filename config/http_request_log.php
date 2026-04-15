<?php

return [

    'enabled' => env('HTTP_REQUEST_LOG_ENABLED', true),

    /** Префиксы пути (без ведущего /), для которых запись не ведётся */
    'exclude_path_prefixes' => array_values(array_filter(array_map(
        static fn (string $s): string => trim($s, '/'),
        explode(',', (string) env('HTTP_REQUEST_LOG_EXCLUDE_PREFIXES', '_debugbar'))
    ))),

    'retention_days' => (int) env('HTTP_REQUEST_LOG_RETENTION_DAYS', 30),
];
