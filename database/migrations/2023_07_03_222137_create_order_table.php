<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->bigInteger('bot_id')->unsigned()->nullable();
            $table->string('type')->nullable();
            $table->string('type_name')->nullable();
            $table->bigInteger('type_id')->nullable();
            $table->string('link')->nullable();
            $table->string('price')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();

            $table->index('user_id', 'order_user_idx');
            $table->foreign('user_id', 'order_user-proxy_fk')->on('user')->references('id')->onDelete('cascade');

            $table->index('bot_id', 'order_bot_idx');
            $table->foreign('bot_id', 'order_bot-proxy_fk')->on('bot')->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order');
    }
}
