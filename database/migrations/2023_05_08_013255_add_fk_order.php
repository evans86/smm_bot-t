<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFkOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order', function (Blueprint $table) {
            $table->foreign('user_id', 'order_user-proxy_fk')->on('user')->references('id')->onDelete('cascade');

            $table->foreign('country_id', 'order_country-proxy_fk')->on('country')->references('id')->onDelete('cascade');

            $table->foreign('proxy_id', 'order_proxy-proxy_fk')->on('proxy')->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
