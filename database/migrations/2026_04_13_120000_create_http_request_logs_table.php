<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHttpRequestLogsTable extends Migration
{
    public function up(): void
    {
        Schema::create('http_request_logs', function (Blueprint $table) {
            $table->id();
            $table->string('method', 16);
            $table->string('path', 2048);
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->unsignedSmallInteger('status_code')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('http_request_logs');
    }
}
