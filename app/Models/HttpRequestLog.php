<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HttpRequestLog extends Model
{
    public const CREATED_AT = 'created_at';

    public $timestamps = false;

    protected $table = 'http_request_logs';

    protected $fillable = [
        'method',
        'path',
        'ip',
        'user_agent',
        'user_id',
        'status_code',
        'duration_ms',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
